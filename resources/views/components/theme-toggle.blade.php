@props(['light' => false])
<button type="button" onclick="akToggleTheme()" class="theme-toggle {{ $light ? 'theme-toggle-on-dark' : '' }}" title="Сменить тему">
    <span class="icon-moon"><x-icon name="moon" :size="19" /></span>
    <span class="icon-sun"><x-icon name="sun" :size="19" /></span>
</button>
<script>
function akToggleTheme(){
    var html = document.documentElement;
    var isLight = html.getAttribute('data-theme') === 'light';
    if (isLight) {
        html.removeAttribute('data-theme');
        try { localStorage.setItem('ak-theme', 'dark'); } catch (e) {}
    } else {
        html.setAttribute('data-theme', 'light');
        try { localStorage.setItem('ak-theme', 'light'); } catch (e) {}
    }
}
</script>
