<script>
    const notyf = window.notyf ?? (typeof Notyf !== 'undefined' ?
        new Notyf({
            duration: 3000,
            position: {
                x: 'right',
                y: 'top'
            },
            dismissible: true,
            ripple: true
        }) :
        {
            success: message => console.info(message),
            error: message => alert(message)
        });
    window.notyf = notyf;
    let globalCartBusy = false;

    function cartError(data, fallback) {
        return data.message || Object.values(data.errors ?? {}).flat()[0] || fallback;
    }

    async function cartFetch(url, body = {}, method = 'POST') {
        const response = await fetch(url, {
            method,
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? ''
            },
            body: JSON.stringify(body)
        });
        const data = await response.json();
        if (!response.ok || data.status !== true) {
            const error = new Error(cartError(data, 'Unable to update your cart.'));
            error.data = data;
            throw error;
        }
        return data;
    }

    function updateCartUI(data) {
        // Partial payloads must not reset the bag count to zero.
        if (data.cart_count !== undefined) {
            document.querySelectorAll('[data-cart-count]').forEach(element => element.textContent = data.cart_count);
        }
        const content = document.getElementById('side-cart-content');
        if (content && typeof data.side_cart_html === 'string') {
            content.innerHTML = data.side_cart_html;
        }
        // Tier changes may reprice every option, so refresh the full cart page.
        if (document.getElementById('cart-page')) window.location.reload();
        document.dispatchEvent(new CustomEvent('cart:updated', {
            detail: data
        }));
    }
    window.updateCartUI = updateCartUI;

    // Retail product-card add handler. Wholesale forms use their dedicated endpoint.
    document.addEventListener('click', async function(event) {
        const button = event.target.closest('.add_to_cart[data-product-id]');
        if (!button) return;
        event.preventDefault();
        if (globalCartBusy || button.classList.contains('cart-processing')) return;
        const url = button.dataset.cartUrl;
        if (!url) {
            notyf.error('The cart action is unavailable.');
            return;
        }
        const icon = button.querySelector('.cart-icon');
        const loader = button.querySelector('.cart-loading');
        globalCartBusy = true;
        button.classList.add('cart-processing');
        icon?.classList.add('d-none');
        loader?.classList.remove('d-none');
        try {
            const data = await cartFetch(url, {
                product_id: button.dataset.productId,
                quantity: 1
            });
            updateCartUI(data);
            notyf.success(data.message || 'Product added to cart.');
            const cart = document.getElementById('shoppingCart');
            if (cart && typeof bootstrap !== 'undefined') bootstrap.Offcanvas.getOrCreateInstance(cart)
                .show();
        } catch (error) {
            notyf.error(error.message || 'Unable to add product to cart.');
        } finally {
            globalCartBusy = false;
            button.classList.remove('cart-processing');
            icon?.classList.remove('d-none');
            loader?.classList.add('d-none');
        }
    });

    function sideQuantity(input) {
        const value = Number(input.value);
        return input.value.trim() && Number.isInteger(value) && input.checkValidity() ? value : null;
    }

    async function updateCartQuantity(cartId, quantity, input = null, previous = null) {
        if (!cartId || globalCartBusy) return;
        globalCartBusy = true;
        if (input) input.disabled = true;
        try {
            const data = await cartFetch(@json(route('cart.update')), {
                id: String(cartId),
                quantity
            });
            updateCartUI(data);
        } catch (error) {
            if (input && previous !== null) input.value = previous;
            notyf.error(error.message || 'Unable to update cart.');
        } finally {
            globalCartBusy = false;
            if (input) input.disabled = false;
        }
    }

    // Capture clicks to stop the theme from changing quantities a second time.
    document.addEventListener('click', function(event) {
        const button = event.target.closest('#side-cart-content .shop-up, #side-cart-content .shop-down');
        if (!button) return;
        event.preventDefault();
        event.stopImmediatePropagation();
        if (globalCartBusy) return;
        const wrapper = button.closest('.shop-quantity');
        const input = wrapper?.querySelector('input[type="number"]');
        const cartId = wrapper?.dataset.cartId;
        if (!input || !cartId) return;
        const previous = input.value;
        const current = sideQuantity(input);
        if (current === null) {
            input.reportValidity();
            return;
        }
        const next = current + (button.classList.contains('shop-up') ? 1 : -1);
        if (next < Number(input.min || 1) || next > Number(input.max || 4294967295)) {
            notyf.error('This quantity would violate the minimum order or supported quantity limit.');
            return;
        }
        input.value = next;
        updateCartQuantity(cartId, next, input, previous);
    }, true);

    document.addEventListener('change', function(event) {
        const input = event.target.closest('#side-cart-content .shop-quantity input[type="number"]');
        if (!input) return;
        if (globalCartBusy) {
            input.value = input.defaultValue;
            return;
        }
        const quantity = sideQuantity(input);
        if (quantity === null) {
            input.reportValidity();
            input.value = input.defaultValue;
            return;
        }
        updateCartQuantity(input.closest('.shop-quantity')?.dataset.cartId, quantity, input, input
        .defaultValue);
    });

    document.addEventListener('click', async function(event) {
        const button = event.target.closest('#side-cart-content .clear-product');
        if (!button) return;
        event.preventDefault();
        if (globalCartBusy || !button.dataset.removeUrl) return;
        globalCartBusy = true;
        button.style.pointerEvents = 'none';
        button.style.opacity = '0.5';
        try {
            const data = await cartFetch(button.dataset.removeUrl, {}, 'DELETE');
            updateCartUI(data);
            notyf.success(data.message || 'Item removed from your bag.');
        } catch (error) {
            notyf.error(error.message || 'Unable to remove item.');
        } finally {
            globalCartBusy = false;
            button.style.pointerEvents = '';
            button.style.opacity = '';
        }
    });

    // One delegated handler covers every currency selector, including dynamic content.
    document.addEventListener('click', async function(event) {
        const button = event.target.closest('.currency-option');
        if (!button) return;
        event.preventDefault();
        if (globalCartBusy || !button.dataset.currency) return;
        globalCartBusy = true;
        button.classList.add('currency-loading');
        try {
            await cartFetch(@json(route('currency.switch')), {
                currency: button.dataset.currency
            });
            window.location.reload();
        } catch (error) {
            // Keep the page open so the wholesale restriction remains visible.
            notyf.error(error.message || 'Unable to change currency.');
            if (error.data?.currency_locked) {
                document.querySelectorAll('.currency-option').forEach(option => {
                    option.setAttribute('aria-pressed', String(option.dataset.currency === 'NGN'));
                });
            }
        } finally {
            globalCartBusy = false;
            button.classList.remove('currency-loading');
        }
    });

    document.addEventListener('DOMContentLoaded', function() {
        /*
        |--------------------------------------------------------------------------
        | Compare Notification
        |--------------------------------------------------------------------------
        */
        function compareNotify(message, type = 'success') {
            // Toastr
            if (typeof toastr !== 'undefined') {
                if (type === 'success') {
                    toastr.success(message);
                } else if (type === 'error') {
                    toastr.error(message);
                } else {
                    toastr.info(message);
                }
                return;
            }
            // Notify.js
            if (typeof $ !== 'undefined' && typeof $.notify === 'function') {
                $.notify(message, {
                    className: type,
                    globalPosition: 'top right'
                });
                return;
            }
            /*
            |--------------------------------------------------------------------------
            | Bootstrap Fallback
            |--------------------------------------------------------------------------
            */
            const oldAlert = document.querySelector('.compare-notification-alert');
            if (oldAlert) {
                oldAlert.remove();
            }
            const alertType = type === 'error' ? 'danger' : type;
            const alert = document.createElement('div');
            alert.className =
                `alert alert-${alertType} alert-dismissible fade show compare-notification-alert position-fixed`;
            alert.style.cssText = `
                top: 90px;
                right: 20px;
                z-index: 99999;
                min-width: 280px;
                max-width: 400px;
                box-shadow: 0 .5rem 1rem rgba(0,0,0,.15);
            `;
            alert.innerHTML = `
                <div>${message}</div>
                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                    aria-label="Close">
                </button>
            `;
            document.body.appendChild(alert);
            setTimeout(function() {
                if (alert.parentNode) {
                    alert.classList.remove('show');
                    setTimeout(function() {
                        alert.remove();
                    }, 300);
                }
            }, 3000);
        }
        /*
        |--------------------------------------------------------------------------
        | Compare Toggle
        |--------------------------------------------------------------------------
        */
        document.addEventListener('click', async function(event) {
            const compareButton = event.target.closest('.compare-toggle');
            if (!compareButton) {
                return;
            }
            event.preventDefault();
            /*
            |--------------------------------------------------------------------------
            | Prevent Multiple Requests
            |--------------------------------------------------------------------------
            */
            if (compareButton.classList.contains('compare-loading')) {
                return;
            }
            const productId = compareButton.dataset.productId;
            const url = compareButton.dataset.url;
            const csrfToken = compareButton.dataset.csrf;
            if (!productId || !url) {
                console.error('Compare configuration is missing.');
                compareNotify(
                    'Unable to update compare. Please try again.',
                    'error'
                );
                return;
            }
            compareButton.classList.add('compare-loading');
            try {
                /*
                |--------------------------------------------------------------------------
                | Send Request
                |--------------------------------------------------------------------------
                */
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    credentials: 'same-origin'
                });
                /*
                |--------------------------------------------------------------------------
                | Read JSON Response
                |--------------------------------------------------------------------------
                */
                const data = await response.json();
                /*
                |--------------------------------------------------------------------------
                | Handle Compare Limit / Errors
                |--------------------------------------------------------------------------
                */
                if (!response.ok || data.success === false) {
                    compareNotify(
                        data.message ||
                        'Unable to update product comparison.',
                        data.limit ? 'info' : 'error'
                    );
                    return;
                }
                /*
                |--------------------------------------------------------------------------
                | Update Every Copy Of This Product
                |--------------------------------------------------------------------------
                */
                document
                    .querySelectorAll(
                        `.compare-toggle[data-product-id="${productId}"]`
                    )
                    .forEach(function(button) {
                        if (data.compared) {
                            /*
                            |--------------------------------------------------------------------------
                            | Product Is Now Compared
                            |--------------------------------------------------------------------------
                            */
                            button.classList.add('compare-active');
                            button.setAttribute(
                                'data-bs-title',
                                'Remove From Compare'
                            );
                            button.setAttribute(
                                'aria-label',
                                'Remove From Compare'
                            );
                        } else {
                            /*
                            |--------------------------------------------------------------------------
                            | Product Removed From Compare
                            |--------------------------------------------------------------------------
                            */
                            button.classList.remove('compare-active');
                            button.setAttribute(
                                'data-bs-title',
                                'Add To Compare'
                            );
                            button.setAttribute(
                                'aria-label',
                                'Add To Compare'
                            );
                        }
                        /*
                        |--------------------------------------------------------------------------
                        | Refresh Bootstrap Tooltip
                        |--------------------------------------------------------------------------
                        */
                        if (typeof bootstrap !== 'undefined') {
                            const tooltip =
                                bootstrap.Tooltip.getInstance(button);
                            if (tooltip) {
                                tooltip.dispose();
                            }
                            new bootstrap.Tooltip(button);
                        }
                    });
                /*
                |--------------------------------------------------------------------------
                | Update Header Compare Count
                |--------------------------------------------------------------------------
                */
                document
                    .querySelectorAll('.compare-count')
                    .forEach(function(counter) {
                        counter.textContent = data.count;
                        if (data.count > 0) {
                            counter.classList.remove('d-none');
                        } else {
                            counter.classList.add('d-none');
                        }
                    });
                /*
                |--------------------------------------------------------------------------
                | Notification
                |--------------------------------------------------------------------------
                */
                compareNotify(
                    data.message ||
                    (
                        data.compared ?
                        'Product added to compare.' :
                        'Product removed from compare.'
                    ),
                    'success'
                );
            } catch (error) {
                console.error('Compare Error:', error);
                compareNotify(
                    'Something went wrong. Please try again.',
                    'error'
                );
            } finally {
                compareButton.classList.remove('compare-loading');
            }
        });
    });
</script>
