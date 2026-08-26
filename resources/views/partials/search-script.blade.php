@props(['suggestUrl', 'catalogUrl', 'productBaseUrl'])
<script>
(function(){
    var input = document.getElementById('ak-search-input');
    var panel = document.getElementById('search-suggest');
    if (!input || !panel) return;
    var timer = null;
    var suggestUrl = '{{ $suggestUrl }}';
    var catalogUrl = '{{ $catalogUrl }}';
    var productBaseUrl = '{{ $productBaseUrl }}';
    var popular = ['Бумага А4', 'Ежедневники', 'Ручки', 'Калькуляторы', 'Папки для документов', 'Упаковка'];

    function esc(s){
        var d = document.createElement('div');
        d.textContent = s == null ? '' : String(s);
        return d.innerHTML;
    }
    function fmtPrice(n){
        return String(n).replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
    }
    function renderPopular(){
        var html='<div class="popular-searches"><div class="popular-searches-head">Часто ищут</div><div class="popular-searches-list">';
        popular.forEach(function(term){html+='<a class="popular-search-chip" href="'+catalogUrl+'?q='+encodeURIComponent(term)+'">'+esc(term)+'</a>'});
        panel.innerHTML=html+'</div></div>';panel.classList.add('show');
    }
    function render(data, q){
        var html = '';
        if (data.categories && data.categories.length) {
            html += '<div class="search-suggest-label">Категории</div>';
            data.categories.forEach(function(c){
                html += '<a class="search-suggest-cat" href="' + catalogUrl + '?category=' + encodeURIComponent(c.slug) + '">' + esc(c.name) + '</a>';
            });
        }
        if (data.products && data.products.length) {
            html += '<div class="search-suggest-label">Товары</div>';
            data.products.forEach(function(p){
                html += '<a class="search-suggest-item" href="' + productBaseUrl + '/' + encodeURIComponent(p.slug) + '">' +
                        '<span>' + esc(p.name) + '</span>' +
                        '<span class="ss-price">' + fmtPrice(p.price) + ' TMT</span></a>';
            });
        }
        if (!html) {
            html = '<div class="search-suggest-empty">Ничего не найдено по «' + esc(q) + '»</div>';
        } else {
            html += '<a class="search-suggest-all" href="' + catalogUrl + '?q=' + encodeURIComponent(q) + '">Все результаты по «' + esc(q) + '» &rarr;</a>';
        }
        panel.innerHTML = html;
        panel.classList.add('show');
    }

    input.addEventListener('input', function(){
        clearTimeout(timer);
        var q = input.value.trim();
        if (q.length < 2) { renderPopular(); return; }
        timer = setTimeout(function(){
            fetch(suggestUrl + '?q=' + encodeURIComponent(q))
                .then(function(r){ return r.json(); })
                .then(function(data){ render(data, q); })
                .catch(function(){ panel.classList.remove('show'); });
        }, 250);
    });
    input.addEventListener('focus', function(){
        if (input.value.trim().length >= 2 && panel.innerHTML) panel.classList.add('show'); else if(!input.value.trim()) renderPopular();
    });
    document.addEventListener('click', function(e){
        if (panel.classList.contains('show') && !panel.contains(e.target) && e.target !== input) {
            panel.classList.remove('show');
        }
    });

    var phrases = ['Найдите товары для офиса', 'Поиск по брендам и категориям', 'Бумага, упаковка, творчество', 'Что вы ищете сегодня?'];
    var phraseIndex = 0, charIndex = 0, deleting = false, typingTimer;
    function animatePlaceholder(){
        if (input.value || document.activeElement === input) { typingTimer = setTimeout(animatePlaceholder, 700); return; }
        var phrase = phrases[phraseIndex];
        charIndex += deleting ? -1 : 1;
        input.placeholder = phrase.slice(0, Math.max(0, charIndex)) + (deleting ? '' : '│');
        var delay = deleting ? 32 : 58;
        if (!deleting && charIndex >= phrase.length) { deleting = true; delay = 1500; }
        if (deleting && charIndex <= 0) { deleting = false; phraseIndex = (phraseIndex + 1) % phrases.length; delay = 350; }
        typingTimer = setTimeout(animatePlaceholder, delay);
    }
    input.addEventListener('focus', function(){ if(!input.value) input.placeholder='Введите название товара'; });
    input.addEventListener('blur', function(){ clearTimeout(typingTimer); typingTimer=setTimeout(animatePlaceholder,350); });
    animatePlaceholder();
})();
</script>
