<script>
(() => {
    if (window.akCartReady) return;
    window.akCartReady = true;
    const drawer = document.getElementById('cart-drawer');
    const backdrop = document.querySelector('.cart-drawer-backdrop');
    const openDrawer = () => { drawer?.classList.add('show'); backdrop?.classList.add('show'); drawer?.setAttribute('aria-hidden','false'); document.body.style.overflow='hidden'; };
    const closeDrawer = () => { drawer?.classList.remove('show'); backdrop?.classList.remove('show'); drawer?.setAttribute('aria-hidden','true'); document.body.style.overflow=''; };
    document.querySelectorAll('[data-cart-close]').forEach(el => el.addEventListener('click', closeDrawer));
    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeDrawer(); });

    const icon = (type) => type === 'minus'
        ? '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14"/></svg>'
        : '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>';
    const renderState = (form, quantity) => {
        const state = form.querySelector('.cart-card-state');
        if (!state) return;
        state.innerHTML = quantity > 0
            ? `<div class="cart-qty-control"><button type="submit" data-cart-action="decrement">${icon('minus')}</button><span><small>В корзине</small><b>${quantity}</b></span><button type="submit" data-cart-action="increment">${icon('plus')}</button></div>`
            : '<button type="submit" data-cart-action="increment" class="btn-accent cart-add-button"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.5 2.5h3l2.7 12.4a2 2 0 0 0 2 1.6h8.6a2 2 0 0 0 2-1.6L23 6.5H6"/></svg><span>В корзину</span></button>';
    };

    document.addEventListener('submit', async event => {
        const form = event.target.closest('.cart-ajax-form');
        if (!form) return;
        event.preventDefault();
        if (form.classList.contains('is-busy')) return;
        const submitter = event.submitter;
        const action = submitter?.dataset.cartAction || 'increment';
        form.classList.add('is-busy');
        const data = new FormData(form);
        data.set('action', action);
        try {
            const response = await fetch(form.action, {method:'POST',body:data,headers:{'Accept':'application/json','X-Requested-With':'XMLHttpRequest'},credentials:'same-origin'});
            if (response.status === 401) { window.location.href='{{ route('login') }}'; return; }
            if (response.status === 419) { window.location.reload(); return; }
            const payload = await response.json();
            if (!response.ok) throw new Error(payload.message || 'Не удалось обновить корзину');
            document.querySelectorAll(`.cart-ajax-form[data-product-id="${form.dataset.productId}"]`).forEach(item => renderState(item, payload.quantity));
            document.querySelectorAll('[data-cart-count]').forEach(counter => { counter.textContent=payload.count; counter.hidden=payload.count<1; });
            const body = document.querySelector('[data-cart-drawer-body]');
            if (body) body.innerHTML = payload.drawer;
            const total = document.querySelector('[data-cart-subtotal]');
            if (total) total.textContent = new Intl.NumberFormat('ru-RU').format(payload.subtotal) + ' TMT';
            if (action === 'increment') openDrawer();
        } catch (error) {
            let toast=document.querySelector('.wishlist-toast');
            if(!toast){toast=document.createElement('div');toast.className='wishlist-toast';document.body.appendChild(toast)}
            toast.textContent=error.message;toast.classList.add('show');setTimeout(()=>toast.classList.remove('show'),2200);
        } finally { form.classList.remove('is-busy'); }
    });

    const expressBtn = document.getElementById('cart-express-btn');
    if (expressBtn) {
        expressBtn.addEventListener('click', async () => {
            if (expressBtn.disabled) return;
            if (!confirm('Оформить быстрый заказ по адресу из последнего заказа? К сумме добавится 30 TMT за срочность.')) return;
            expressBtn.disabled = true;
            try {
                const response = await fetch(expressBtn.dataset.expressUrl, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                    },
                    credentials: 'same-origin'
                });
                if (response.status === 419) { window.location.reload(); return; }
                const payload = await response.json();
                if (!response.ok) throw new Error(payload.message || 'Не удалось оформить быстрый заказ');
                window.location.href = payload.redirect;
            } catch (error) {
                let toast = document.querySelector('.wishlist-toast');
                if (!toast) { toast = document.createElement('div'); toast.className = 'wishlist-toast'; document.body.appendChild(toast); }
                toast.textContent = error.message; toast.classList.add('show'); setTimeout(() => toast.classList.remove('show'), 2600);
                expressBtn.disabled = false;
            }
        });
    }
})();
</script>
