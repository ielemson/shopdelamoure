<script>
    (() => {
        'use strict';

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
        const notifier = typeof Notyf !== 'undefined' ?
            new Notyf({
                duration: 3000,
                position: {
                    x: 'right',
                    y: 'top'
                },
                dismissible: true,
                ripple: true
            }) :
            null;

        window.notyf = window.notyf || notifier;

        const notify = (message, type = 'success') => {
            const text = String(message || (type === 'error' ?
                'Something went wrong. Please try again.' :
                'Completed successfully.'));

            if (notifier) {
                type === 'error' ? notifier.error(text) : notifier.success(text);
            }
        };

        const isSuccessful = data =>
            data?.status === true ||
            data?.status === 1 ||
            data?.status === '1' ||
            data?.status === 'success' ||
            data?.success === true ||
            data?.success === 1 ||
            data?.success === '1';

        const requestJson = async (url, options = {}) => {
            if (!url || !csrfToken) {
                throw new Error('This request could not be completed. Please refresh and try again.');
            }

            const response = await fetch(url, {
                credentials: 'same-origin',
                ...options,
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken,
                    ...options.headers
                }
            });

            const contentType = response.headers.get('content-type') || '';
            const data = contentType.includes('application/json') ?
                await response.json().catch(() => ({})) : {};

            return {
                response,
                data
            };
        };

        const updateCartUI = data => {
            const count = Number.isFinite(Number(data.cart_count)) ?
                Math.max(0, Number(data.cart_count)) :
                0;

            document.querySelectorAll('[data-cart-count]').forEach(element => {
                element.textContent = count;
            });

            const sideCartContent = document.getElementById('side-cart-content');
            if (sideCartContent && typeof data.side_cart_html === 'string') {
                sideCartContent.innerHTML = data.side_cart_html;
            }
        };

        window.updateCartUI = updateCartUI;

        const setBusy = (element, className, busy) => {
            element.classList.toggle(className, busy);
            element.toggleAttribute('disabled', busy);
            element.setAttribute('aria-busy', String(busy));
        };

        const updateCartQuantity = async (wrapper, quantity) => {
            if (wrapper.dataset.updating === 'true') return;

            wrapper.dataset.updating = 'true';
            const input = wrapper.querySelector('input[type="number"]');
            const previousQuantity = Number(input?.dataset.savedQuantity || input?.value || 1);

            try {
                const {
                    response,
                    data
                } = await requestJson("{{ route('cart.update') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        id: wrapper.dataset.cartId,
                        quantity
                    })
                });

                if (!response.ok || !isSuccessful(data)) {
                    throw new Error('Unable to update your bag. Please try again.');
                }

                if (input) input.dataset.savedQuantity = String(quantity);
                updateCartUI(data);
            } catch {
                if (input) input.value = String(previousQuantity);
                notify('Unable to update your bag. Please try again.', 'error');
            } finally {
                delete wrapper.dataset.updating;
            }
        };

        document.addEventListener('click', async event => {
            const addButton = event.target.closest(
                '.add_to_cart[data-product-id], .add-to-cart[data-product-id], [data-add-to-cart][data-product-id]'
            );
            if (addButton) {
                event.preventDefault();
                if (addButton.classList.contains('cart-processing')) return;

                const icon = addButton.querySelector('.cart-icon');
                const loader = addButton.querySelector('.cart-loading');
                setBusy(addButton, 'cart-processing', true);
                icon?.classList.add('d-none');
                loader?.classList.remove('d-none');

                try {
                    const container = addButton.closest(
                        'form, .product-info, .product-details, .product-summary');
                    const quantityInput = container?.querySelector(
                        'input[name="quantity"], input[name="qty"], .product-quantity input[type="number"], .quantity input[type="number"]'
                    );
                    const quantity = Math.max(
                        1,
                        Number.parseInt(addButton.dataset.quantity || quantityInput?.value || '1',
                            10) || 1
                    );
                    const cartUrl = addButton.dataset.cartUrl || addButton.dataset.url;

                    const {
                        response,
                        data
                    } = await requestJson(cartUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            product_id: addButton.dataset.productId,
                            quantity
                        })
                    });

                    if (!response.ok || !isSuccessful(data)) {
                        throw new Error('Unable to add this product to your bag.');
                    }

                    updateCartUI(data);
                    notify(data.message || 'Product added to your bag.');

                    const shoppingCart = document.getElementById('shoppingCart');
                    if (shoppingCart && typeof bootstrap !== 'undefined') {
                        bootstrap.Offcanvas.getOrCreateInstance(shoppingCart).show();
                    }
                } catch {
                    notify('Unable to add this product to your bag. Please try again.', 'error');
                } finally {
                    setBusy(addButton, 'cart-processing', false);
                    icon?.classList.remove('d-none');
                    loader?.classList.add('d-none');
                }
                return;
            }

            const quantityButton = event.target.closest('.shop-up, .shop-down');
            if (quantityButton) {
                event.preventDefault();
                const wrapper = quantityButton.closest('.shop-quantity');
                const input = wrapper?.querySelector('input[type="number"]');
                if (!wrapper?.dataset.cartId || !input) return;

                const current = Math.max(1, Number.parseInt(input.value, 10) || 1);
                const quantity = quantityButton.classList.contains('shop-up') ?
                    current + 1 :
                    Math.max(1, current - 1);

                if (quantity === current) return;
                input.dataset.savedQuantity = String(current);
                input.value = String(quantity);
                await updateCartQuantity(wrapper, quantity);
                return;
            }

            const removeButton = event.target.closest('.clear-product[data-remove-url]');
            if (removeButton) {
                event.preventDefault();
                if (removeButton.classList.contains('remove-processing')) return;
                setBusy(removeButton, 'remove-processing', true);

                try {
                    const {
                        response,
                        data
                    } = await requestJson(removeButton.dataset.removeUrl, {
                        method: 'DELETE'
                    });

                    if (!response.ok || !isSuccessful(data)) {
                        throw new Error('Unable to remove this item.');
                    }

                    updateCartUI(data);
                    removeButton.closest('[data-cart-row]')?.remove();

                    if (typeof data.cart_html === 'string') {
                        const shoppingCart = document.getElementById('shoppingCart');
                        if (shoppingCart) shoppingCart.innerHTML = data.cart_html;
                    }

                    notify(data.message || 'Item removed from your bag.');
                } catch {
                    setBusy(removeButton, 'remove-processing', false);
                    notify('Unable to remove this item. Please try again.', 'error');
                }
                return;
            }

            const compareButton = event.target.closest('.compare-toggle[data-product-id]');
            if (compareButton) {
                event.preventDefault();
                if (compareButton.classList.contains('compare-loading')) return;

                const productId = compareButton.dataset.productId;
                const url = compareButton.dataset.url;
                if (!productId || !url) {
                    notify('Unable to update comparison. Please try again.', 'error');
                    return;
                }

                setBusy(compareButton, 'compare-loading', true);

                try {
                    const {
                        response,
                        data
                    } = await requestJson(url, {
                        method: 'POST'
                    });

                    if (!response.ok || data.success === false || data.status === false) {
                        notify(
                            data.limit ? (data.message ||
                                'The comparison limit has been reached.') :
                            'Unable to update comparison.',
                            'error'
                        );
                        return;
                    }

                    document.querySelectorAll('.compare-toggle[data-product-id]').forEach(button => {
                        if (button.dataset.productId !== productId) return;

                        const active = data.compared === true;
                        const label = active ? 'Remove From Compare' : 'Add To Compare';
                        button.classList.toggle('compare-active', active);
                        button.setAttribute('data-bs-title', label);
                        button.setAttribute('aria-label', label);

                        if (typeof bootstrap !== 'undefined') {
                            bootstrap.Tooltip.getInstance(button)?.dispose();
                            bootstrap.Tooltip.getOrCreateInstance(button);
                        }
                    });

                    const count = Math.max(0, Number(data.count) || 0);
                    document.querySelectorAll('.compare-count').forEach(counter => {
                        counter.textContent = count;
                        counter.classList.toggle('d-none', count === 0);
                    });

                    notify(data.message || (data.compared ?
                        'Product added to compare.' :
                        'Product removed from compare.'));
                } catch {
                    notify('Unable to update comparison. Please try again.', 'error');
                } finally {
                    setBusy(compareButton, 'compare-loading', false);
                }
                return;
            }

            const currencyButton = event.target.closest('.currency-option[data-currency]');
            if (!currencyButton) return;

            event.preventDefault();
            if (currencyButton.classList.contains('currency-loading')) return;
            setBusy(currencyButton, 'currency-loading', true);

            try {
                const {
                    response,
                    data
                } = await requestJson("{{ route('currency.switch') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        currency: currencyButton.dataset.currency
                    })
                });

                if (!response.ok || !isSuccessful(data)) {
                    throw new Error('Unable to change currency.');
                }

                window.location.reload();
            } catch {
                setBusy(currencyButton, 'currency-loading', false);
                notify('Unable to change currency. Please try again.', 'error');
            }
        });
    })();
</script>
