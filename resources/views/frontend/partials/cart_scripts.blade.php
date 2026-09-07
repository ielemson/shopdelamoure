<script>

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

function showToast(message, type = 'success') {
    let bgColor = '#28a745';

    if (type === 'error') bgColor = '#dc3545';
    if (type === 'info') bgColor = '#17a2b8';

    Toastify({
        text: message,
        duration: 3000,
        gravity: "top",
        position: "right",
        close: true,
        style: {
            background: bgColor
        }
    }).showToast();
}

function formatNaira(amount) {
    amount = Number(amount) || 0;

    return '₦' + amount.toLocaleString('en-NG', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
}

function isCartPage() {
    return $('.cart-table-content').length > 0;
}

function refreshSideCart(response, openCart = false) {
    if (response.side_cart_html) {
        $("#ec-side-cart").replaceWith(response.side_cart_html);
    }

    if (openCart) {
        $(".ec-side-cart-overlay").fadeIn();
        $("#ec-side-cart").addClass("ec-open");
    }
}

function refreshCartCount(response) {
    $(".ec-cart-count, .cart-count-lable").text(response.cart_count ?? 0);
}

function refreshCartPageSummary(response) {
    if (!isCartPage()) return;

    let cartCount = Number(response.cart_count ?? 0);

    $('.cart-page-subtotal').text(formatNaira(response.subtotal ?? response.cart_total ?? 0));
    $('.cart-page-delivery').text(formatNaira(response.delivery ?? 0));
    $('.cart-page-discount').text(formatNaira(response.discount ?? 0));
    $('.cart-page-total').text(formatNaira(response.total ?? response.cart_total ?? 0));

    if (cartCount === 0) {
        $('.cart-table-content tbody').html(`
            <tr>
                <td colspan="5" class="text-center">
                    Your cart is empty.
                </td>
            </tr>
        `);

        $('.ec-cart-update-bottom').html(`
            <a class="btn_underline" href="/shop">
                Continue Shopping
            </a>
        `);
    }
}

function refreshCartPageItem(itemId, quantity, response) {
    if (!isCartPage()) return;

    let row = $('#cart-page-item-' + itemId);

    if (!row.length) return;

    row.find('.cart-page-qty-input').val(quantity);

    if (response.item_total !== undefined) {
        row.find('.ec-cart-pro-subtotal').text(formatNaira(response.item_total));
    }
}

function removeCartPageItem(itemId, response) {
    if (!isCartPage()) return;

    $('#cart-page-item-' + itemId).fadeOut(200, function () {
        $(this).remove();
        refreshCartPageSummary(response);
    });
}

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
        showToast("Cart button is not properly configured.", "error");
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
            quantity: 1
        },

        success: function (response) {
            refreshCartCount(response);
            refreshSideCart(response, false);
            refreshCartPageSummary(response);

            showToast(response.message || "Product added to cart.");
        },

        error: function (xhr) {
            let message = xhr.responseJSON?.message || "Unable to add product to cart.";

            if (xhr.responseJSON?.errors) {
                message = Object.values(xhr.responseJSON.errors).flat()[0];
            }

            showToast(message, "error");
        },

        complete: function () {
            button
                .css("pointer-events", "")
                .html(originalHtml);
        }
    });

});

$(document).on("click", ".remove-cart-item, .cart-page-remove-item", function (e) {

    e.preventDefault();

    let button = $(this);
    let url = button.data("url");
    let itemId = button.data("id");
    let sideRow = button.closest("li");

    if (!url || !itemId) {
        showToast("Remove button is not properly configured.", "error");
        return;
    }

    button.css("pointer-events", "none");

    $.ajax({
        url: url,
        type: "POST",
        dataType: "json",
        data: {
            id: itemId
        },

        success: function (response) {
            refreshCartCount(response);
            removeCartPageItem(itemId, response);

            if (sideRow.length) {
                sideRow.fadeOut(200, function () {
                    refreshSideCart(response, true);
                });
            } else {
                refreshSideCart(response, false);
            }

            refreshCartPageSummary(response);

            showToast(response.message || "Item removed from cart.");
        },

        error: function (xhr) {
            button.css("pointer-events", "");

            let message = xhr.responseJSON?.message || "Unable to remove product.";
            showToast(message, "error");
        }
    });

});

$(document).on("click", ".cart-qty-increase, .cart-qty-decrease, .cart-page-qty-increase, .cart-page-qty-decrease", function (e) {

    e.preventDefault();

    let button = $(this);
    let url = button.data("url");
    let itemId = button.data("id");

    let isCartPageButton =
        button.hasClass("cart-page-qty-increase") ||
        button.hasClass("cart-page-qty-decrease");

    let input = isCartPageButton
        ? button.closest("tr").find(".cart-page-qty-input")
        : button.closest(".custom-cart-qty").find(".qty-input");

    let currentQty = parseInt(input.val()) || 1;
    let newQty = currentQty;

    if (
        button.hasClass("cart-qty-increase") ||
        button.hasClass("cart-page-qty-increase")
    ) {
        newQty = currentQty + 1;
    }

    if (
        button.hasClass("cart-qty-decrease") ||
        button.hasClass("cart-page-qty-decrease")
    ) {
        newQty = currentQty - 1;
    }

    if (newQty < 1) return;

    input.val(newQty);
    button.css("pointer-events", "none");

    $.ajax({
        url: url,
        type: "POST",
        dataType: "json",
        data: {
            id: itemId,
            quantity: newQty
        },

        success: function (response) {
            refreshCartCount(response);
            refreshCartPageItem(itemId, newQty, response);
            refreshCartPageSummary(response);
            refreshSideCart(response, !isCartPageButton);

            showToast(response.message || "Cart updated successfully.");
        },

        error: function () {
            input.val(currentQty);
            showToast("Unable to update cart.", "error");
        },

        complete: function () {
            button.css("pointer-events", "");
        }
    });

});

$(document).on('click', '.ajax-checkout-auth', function (e) {

    e.preventDefault();

    $.ajax({
        url: $(this).data('url'),
        type: 'POST',

        success: function (response) {
            showToast(response.message, "info");

            setTimeout(function () {
                window.location.href = response.redirect;
            }, 2500);
        }
    });

});
</script>