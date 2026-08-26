<script>
(() => {
    if (window.akWishlistReady) return;
    window.akWishlistReady = true;
    let toastTimer;
    const toast = message => {
        let node = document.querySelector('.wishlist-toast');
        if (!node) {
            node = document.createElement('div');
            node.className = 'wishlist-toast';
            document.body.appendChild(node);
        }
        node.textContent = message;
        node.classList.add('show');
        clearTimeout(toastTimer);
        toastTimer = setTimeout(() => node.classList.remove('show'), 1900);
    };

    document.addEventListener('submit', async event => {
        const form = event.target.closest('.wishlist-toggle-form');
        if (!form) return;
        event.preventDefault();
        const button = form.querySelector('.wishlist-toggle');
        if (!button || button.classList.contains('is-busy')) return;
        button.classList.add('is-busy');
        try {
            const response = await fetch(form.action, {
                method: 'POST',
                body: new FormData(form),
                headers: {'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest'},
                credentials: 'same-origin'
            });
            if (response.status === 401 || response.status === 419) {
                window.location.href = '{{ route('login') }}';
                return;
            }
            if (!response.ok) throw new Error('request_failed');
            const data = await response.json();
            button.classList.toggle('on', data.active);
            button.setAttribute('aria-pressed', data.active ? 'true' : 'false');
            button.title = data.active ? 'Удалить из избранного' : 'В избранное';
            button.classList.remove('is-pop');
            void button.offsetWidth;
            button.classList.add('is-pop');
            document.querySelectorAll('[data-wishlist-count]').forEach(counter => {
                counter.textContent = data.count;
                counter.hidden = data.count < 1;
            });
            toast(data.message);
        } catch (error) {
            toast('Не удалось обновить избранное. Попробуйте ещё раз.');
        } finally {
            button.classList.remove('is-busy');
        }
    });
})();
</script>
