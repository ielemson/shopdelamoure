<div class="modal fade" id="quickViewModal" tabindex="-1" aria-labelledby="quickViewModalLabel" aria-hidden="true">

    <div class="modal-dialog modal-xl">
        <div class="modal-content">

            <div class="modal-header border-0 py-5">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                </button>
            </div>

            <div class="modal-body pt-0" id="quickViewContent">

                {{-- Dynamic product content will load here --}}

            </div>

        </div>
    </div>
</div>


@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            /*
            |--------------------------------------------------------------------------
            | OPEN QUICK VIEW
            |--------------------------------------------------------------------------
            */
            document.addEventListener('click', function(e) {

                const button = e.target.closest('.quick-view-btn');

                if (!button) return;

                e.preventDefault();

                const url = button.dataset.url;

                if (!url) return;

                fetch(url)
                    .then(response => {

                        if (!response.ok) {
                            throw new Error('Product could not be loaded.');
                        }

                        return response.text();

                    })
                    .then(html => {

                        const content =
                            document.getElementById('quickViewContent');

                        content.innerHTML = html;


                        /*
                        |--------------------------------------------------------------------------
                        | INITIALIZE DEFAULT VARIANT
                        |--------------------------------------------------------------------------
                        */
                        const variantSelect =
                            document.getElementById('quickViewVariant');

                        if (variantSelect) {
                            updateQuickViewVariant(variantSelect);
                        }


                        /*
                        |--------------------------------------------------------------------------
                        | MAIN PRODUCT SLIDER
                        |--------------------------------------------------------------------------
                        */
                        const slider =
                            $('#quickViewSlider');

                        const thumbs =
                            $('#quickViewSliderThumb');


                        if (slider.length) {

                            if (slider.hasClass('slick-initialized')) {
                                slider.slick('unslick');
                            }

                            slider.slick({
                                arrows: false,
                                dots: false,
                                slidesToShow: 1,
                                adaptiveHeight: true,
                                asNavFor: thumbs.length ?
                                    '#quickViewSliderThumb' :
                                    null
                            });
                        }


                        /*
                        |--------------------------------------------------------------------------
                        | THUMBNAIL SLIDER
                        |--------------------------------------------------------------------------
                        */
                        if (thumbs.length) {

                            if (thumbs.hasClass('slick-initialized')) {
                                thumbs.slick('unslick');
                            }

                            thumbs.slick({
                                arrows: false,
                                dots: false,
                                slidesToShow: 5,
                                focusOnSelect: true,
                                asNavFor: '#quickViewSlider',

                                responsive: [{
                                        breakpoint: 768,
                                        settings: {
                                            slidesToShow: 4
                                        }
                                    },
                                    {
                                        breakpoint: 576,
                                        settings: {
                                            slidesToShow: 3
                                        }
                                    }
                                ]
                            });
                        }


                        /*
                        |--------------------------------------------------------------------------
                        | OPEN MODAL
                        |--------------------------------------------------------------------------
                        */
                        const modalElement =
                            document.getElementById('quickViewModal');

                        const modal =
                            bootstrap.Modal.getOrCreateInstance(
                                modalElement
                            );

                        modal.show();


                        /*
                        |--------------------------------------------------------------------------
                        | RECALCULATE SLIDER
                        |--------------------------------------------------------------------------
                        */
                        modalElement.addEventListener(
                            'shown.bs.modal',
                            function() {

                                if (
                                    slider.length &&
                                    slider.hasClass('slick-initialized')
                                ) {
                                    slider.slick('setPosition');
                                }

                                if (
                                    thumbs.length &&
                                    thumbs.hasClass('slick-initialized')
                                ) {
                                    thumbs.slick('setPosition');
                                }

                            }, {
                                once: true
                            }
                        );

                    })
                    .catch(error => {

                        console.error(
                            'Quick View Error:',
                            error
                        );

                        if (typeof notyf !== 'undefined') {
                            notyf.error(error.message);
                        }

                    });

            });



            /*
            |--------------------------------------------------------------------------
            | QUANTITY PLUS / MINUS
            |--------------------------------------------------------------------------
            */
            document.addEventListener('click', function(e) {

                /*
                 * PLUS
                 */
                const plus =
                    e.target.closest('.quick-view-plus');

                if (plus) {

                    e.preventDefault();

                    const input =
                        document.getElementById('QuickViewNumber');

                    if (!input) return;

                    let quantity =
                        parseInt(input.value) || 1;

                    const max =
                        parseInt(input.max) || null;


                    if (!max || quantity < max) {
                        quantity++;
                    }

                    input.value = quantity;

                    return;
                }


                /*
                 * MINUS
                 */
                const minus =
                    e.target.closest('.quick-view-minus');

                if (minus) {

                    e.preventDefault();

                    const input =
                        document.getElementById('QuickViewNumber');

                    if (!input) return;

                    let quantity =
                        parseInt(input.value) || 1;


                    if (quantity > 1) {
                        quantity--;
                    }

                    input.value = quantity;
                }

            });



            /*
            |--------------------------------------------------------------------------
            | MANUAL QUANTITY VALIDATION
            |--------------------------------------------------------------------------
            */
            document.addEventListener('change', function(e) {

                if (e.target.id !== 'QuickViewNumber') {
                    return;
                }

                const input = e.target;

                let quantity =
                    parseInt(input.value) || 1;

                const max =
                    parseInt(input.max) || null;


                if (quantity < 1) {
                    quantity = 1;
                }

                if (max && quantity > max) {
                    quantity = max;
                }

                input.value = quantity;

            });



            /*
            |--------------------------------------------------------------------------
            | VARIANT CHANGE
            |--------------------------------------------------------------------------
            */
            document.addEventListener('change', function(e) {

                if (e.target.id !== 'quickViewVariant') {
                    return;
                }

                updateQuickViewVariant(e.target);

            });



            /*
            |--------------------------------------------------------------------------
            | UPDATE VARIANT
            |--------------------------------------------------------------------------
            */
            function updateQuickViewVariant(select) {

                const option =
                    select.options[select.selectedIndex];

                if (!option) return;


                const regularPrice =
                    document.getElementById(
                        'quickViewRegularPrice'
                    );

                const salePrice =
                    document.getElementById(
                        'quickViewSalePrice'
                    );

                const stockText =
                    document.getElementById(
                        'quickViewStock'
                    );

                const quantity =
                    document.getElementById(
                        'QuickViewNumber'
                    );

                const addButton =
                    document.getElementById(
                        'quickViewAddToCart'
                    );


                if (
                    !regularPrice ||
                    !salePrice ||
                    !stockText ||
                    !quantity ||
                    !addButton
                ) {
                    return;
                }


                const price =
                    parseFloat(option.dataset.price || 0);

                const sale =
                    parseFloat(option.dataset.salePrice || 0);

                const stock =
                    parseInt(option.dataset.stock || 0);

                const stockStatus =
                    option.dataset.stockStatus;

                const symbol =
                    select.dataset.symbol || '₦';


                /*
                |--------------------------------------------------------------------------
                | PRICE
                |--------------------------------------------------------------------------
                */
                regularPrice.textContent =
                    formatQuickViewPrice(
                        price,
                        symbol
                    );


                if (sale > 0) {

                    regularPrice.classList.add(
                        'text-decoration-line-through'
                    );

                    salePrice.textContent =
                        formatQuickViewPrice(
                            sale,
                            symbol
                        );

                    salePrice.style.display = '';

                } else {

                    regularPrice.classList.remove(
                        'text-decoration-line-through'
                    );

                    salePrice.textContent = '';

                    salePrice.style.display = 'none';

                }


                /*
                |--------------------------------------------------------------------------
                | STOCK
                |--------------------------------------------------------------------------
                */
                if (
                    stockStatus === 'in_stock' &&
                    stock > 0
                ) {

                    stockText.innerHTML = `
                <svg class="icon fs-5 me-4 pe-2 align-text-bottom">
                    <use xlink:href="#icon-Timer"></use>
                </svg>
                ${stock} in stock
            `;

                    quantity.max = stock;


                    if (parseInt(quantity.value) > stock) {
                        quantity.value = stock;
                    }


                    if (parseInt(quantity.value) < 1) {
                        quantity.value = 1;
                    }


                    addButton.disabled = false;
                    addButton.innerHTML = 'Add To Bag';

                } else {

                    stockText.innerHTML = `
                <svg class="icon fs-5 me-4 pe-2 align-text-bottom">
                    <use xlink:href="#icon-Timer"></use>
                </svg>
                Out of Stock
            `;

                    quantity.value = 1;
                    quantity.max = 1;

                    addButton.disabled = true;
                    addButton.innerHTML = 'Out of Stock';

                }

            }



            /*
            |--------------------------------------------------------------------------
            | FORMAT PRICE
            |--------------------------------------------------------------------------
            */
            function formatQuickViewPrice(amount, symbol) {

                return symbol +
                    Number(amount).toLocaleString(
                        undefined, {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        }
                    );

            }



            /*
            |--------------------------------------------------------------------------
            | ADD TO CART
            |--------------------------------------------------------------------------
            */
            document.addEventListener('submit', function(e) {

                const form =
                    e.target.closest('#quickViewCartForm');

                if (!form) return;

                e.preventDefault();


                const button =
                    form.querySelector(
                        '#quickViewAddToCart'
                    );

                if (!button || button.disabled) {
                    return;
                }


                const originalText =
                    button.innerHTML;


                button.disabled = true;
                button.innerHTML = 'Adding...';


                const formData =
                    new FormData(form);


                fetch(form.action, {

                        method: 'POST',

                        body: formData,

                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }

                    })
                    .then(async response => {

                        const data =
                            await response.json();


                        if (!response.ok) {

                            throw new Error(
                                data.message ||
                                'Unable to add product to bag.'
                            );

                        }

                        return data;

                    })
                    .then(data => {

                        /*
                        |--------------------------------------------------------------------------
                        | SUCCESS MESSAGE
                        |--------------------------------------------------------------------------
                        */
                        button.innerHTML =
                            'Added To Bag';


                        if (typeof notyf !== 'undefined') {

                            notyf.success(
                                data.message ||
                                'Product added to cart.'
                            );

                        }


                        /*
                        |--------------------------------------------------------------------------
                        | UPDATE GENERAL CART UI
                        |--------------------------------------------------------------------------
                        |
                        | This is the SAME function used by your normal Add To Cart.
                        |
                        | It updates:
                        |
                        | [data-cart-count]
                        | #side-cart-content
                        |
                        */
                        if (typeof updateCartUI === 'function') {
                            updateCartUI(data);
                        }


                        /*
                        |--------------------------------------------------------------------------
                        | RESET BUTTON
                        |--------------------------------------------------------------------------
                        */
                        setTimeout(function() {

                            button.innerHTML =
                                originalText;

                            button.disabled =
                                false;

                        }, 1200);

                    })
                    .catch(error => {

                        console.error(
                            'Cart Error:',
                            error
                        );


                        /*
                        |--------------------------------------------------------------------------
                        | ERROR NOTIFICATION
                        |--------------------------------------------------------------------------
                        */
                        if (typeof notyf !== 'undefined') {

                            notyf.error(
                                error.message ||
                                'Unable to add product to cart.'
                            );

                        }


                        button.innerHTML =
                            'Try Again';


                        setTimeout(function() {

                            button.innerHTML =
                                originalText;

                            button.disabled =
                                false;

                        }, 1500);

                    });

            });

        });
    </script>
@endpush
