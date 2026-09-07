<script src="{{ asset('assets/js/vendor/jquery-3.5.1.min.js') }}"></script>
<script src="{{ asset('assets/js/vendor/popper.min.js') }}"></script>
<script src="{{ asset('assets/js/vendor/bootstrap.min.js') }}"></script>
<script src="{{ asset('assets/js/vendor/jquery-migrate-3.3.0.min.js') }}"></script>
<script src="{{ asset('assets/js/vendor/modernizr-3.11.2.min.js') }}"></script>

<!-- Plugins JS -->
<script src="{{ asset('assets/js/plugins/swiper-bundle.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/countdownTimer.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/scrollup.js') }}"></script>
<script src="{{ asset('assets/js/plugins/jquery.zoom.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/slick.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/infiniteslidev2.js') }}"></script>
<script src="{{ asset('assets/js/vendor/jquery.magnific-popup.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/jquery.sticky-sidebar.js') }}"></script>
<script src="{{ asset("assets/js/plugins/click-to-call.js") }}"></script>
<script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
<!-- Main JS -->
{{-- <script src="{{ asset("assets/js/demo-4.js") }}"></script> --}}
<script src="{{ asset('assets/js/main.js') }}"></script>
@include("frontend.partials.cart_scripts")

{{-- <script>

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

$(document).ready(function () {

    $(document).on("click", ".ec-side-toggle", function (e) {
        e.preventDefault();

        $(".ec-side-cart-overlay").fadeIn();
        $("#ec-side-cart").addClass("ec-open");
    });

    $(document).on("click", ".ec-side-cart-overlay, .ec-close", function (e) {
        e.preventDefault();

        $(".ec-side-cart-overlay").fadeOut();
        $("#ec-side-cart").removeClass("ec-open");
    });

});

$(document).on("click", ".add-to-cart", function (e) {

    e.preventDefault();

    let button = $(this);
    let url = button.data("url");
    let productId = button.data("product-id");
    let originalHtml = button.html();

    if (!url || !productId) {
        Toastify({
            text: "Cart button is not properly configured.",
            duration: 3000,
            gravity: "top",
            position: "right",
            close: true,
            style: {
                background: "#dc3545"
            }
        }).showToast();

        return;
    }

    button
        .css("pointer-events", "none")
        .html('<i class="fas fa-spinner fa-spin me-1"></i> Adding...');

    $.ajax({
        url: url,
        type: "POST",
        data: {
            product_id: productId,
            quantity: 1,
            _token: $('meta[name="csrf-token"]').attr("content")
        },

      success: function (response) {

    $(".ec-cart-count").text(response.cart_count);
    $(".cart-count-lable").text(response.cart_count);

    if (response.side_cart_html) {
        $("#ec-side-cart").replaceWith(response.side_cart_html);
    }

    Toastify({
        text: response.message,
        duration: 3000,
        gravity: "top",
        position: "right",
        close: true,
        style: {
            background: "#28a745"
        }
    }).showToast();

    $(".ec-side-cart-overlay").fadeIn();
    $("#ec-side-cart").addClass("ec-open");
},

        error: function (xhr) {

            let message = xhr.responseJSON?.message || "Unable to add product to cart.";

            if (xhr.responseJSON?.errors) {
                message = Object.values(xhr.responseJSON.errors).flat()[0];
            }

            Toastify({
                text: message,
                duration: 4000,
                gravity: "top",
                position: "right",
                close: true,
                style: {
                    background: "#dc3545"
                }
            }).showToast();
        },

        complete: function () {
            button
                .css("pointer-events", "")
                .html(originalHtml);
        }
    });

});

$(document).on("click", ".remove-cart-item", function (e) {

    e.preventDefault();

    let button = $(this);
    let url = button.data("url");
    let itemId = button.data("id");
    let row = button.closest("li");

    if (!url || !itemId) {
        Toastify({
            text: "Remove button is not properly configured.",
            duration: 3000,
            gravity: "top",
            position: "right",
            close: true,
            style: {
                background: "#dc3545"
            }
        }).showToast();

        return;
    }

    button.css("pointer-events", "none");

    $.ajax({
        url: url,
        type: "POST",
        data: {
            id: itemId,
            _token: $('meta[name="csrf-token"]').attr("content")
        },

        success: function (response) {

            $(".ec-cart-count").text(response.cart_count);
            $(".cart-count-lable").text(response.cart_count);

            row.fadeOut(200, function () {

                if (response.side_cart_html) {
                    $("#ec-side-cart").replaceWith(response.side_cart_html);
                }

                $(".ec-side-cart-overlay").fadeIn();
                $("#ec-side-cart").addClass("ec-open");

            });

            Toastify({
                text: response.message,
                duration: 3000,
                gravity: "top",
                position: "right",
                close: true,
                style: {
                    background: "#28a745"
                }
            }).showToast();
        },

        error: function (xhr) {

            button.css("pointer-events", "");

            let message = xhr.responseJSON?.message || "Unable to remove product.";

            Toastify({
                text: message,
                duration: 4000,
                gravity: "top",
                position: "right",
                close: true,
                style: {
                    background: "#dc3545"
                }
            }).showToast();
        }
    });

});

$(document).on("click", ".cart-qty-increase, .cart-qty-decrease", function (e) {

    e.preventDefault();

    let button = $(this);
    let wrapper = button.closest(".custom-cart-qty");
    let input = wrapper.find(".qty-input");

    let url = button.data("url");
    let itemId = button.data("id");
    let currentQty = parseInt(input.val());

    let newQty = currentQty;

    if (button.hasClass("cart-qty-increase")) {
        newQty = currentQty + 1;
    }

    if (button.hasClass("cart-qty-decrease")) {
        newQty = currentQty - 1;
    }

    if (newQty < 1) {
        return;
    }

    input.val(newQty);

    button.css("pointer-events", "none");

    $.ajax({
        url: url,
        type: "POST",
        data: {
            id: itemId,
            quantity: newQty,
            _token: $('meta[name="csrf-token"]').attr("content")
        },
success: function (response) {

    $(".ec-cart-count").text(response.cart_count);
    $(".cart-count-lable").text(response.cart_count);

    Toastify({
        text: response.message || "Cart updated successfully.",
        duration: 2000,
        gravity: "top",
        position: "right",
        close: true,
        style: {
            background: "#28a745"
        }
    }).showToast();

    if (response.side_cart_html) {
        $("#ec-side-cart").replaceWith(response.side_cart_html);
        $(".ec-side-cart-overlay").show();
        $("#ec-side-cart").addClass("ec-open");
    }
},

        error: function () {
            input.val(currentQty);

            Toastify({
                text: "Unable to update cart.",
                duration: 3000,
                gravity: "top",
                position: "right",
                close: true,
                style: {
                    background: "#dc3545"
                }
            }).showToast();
        },

        complete: function () {
            button.css("pointer-events", "");
        }
    });

});
</script> --}}

