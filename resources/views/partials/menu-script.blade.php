<script>
var AK_MENU_IDS = ['acct-menu', 'notif-menu', 'catalog-mega', 'contact-menu', 'language-menu'];
function akToggleMenu(e, id){
    e.stopPropagation();
    var menu = document.getElementById(id);
    if (!menu) return;
    var willOpen = !menu.classList.contains('show');
    AK_MENU_IDS.forEach(function(otherId){
        var el = document.getElementById(otherId);
        if (el) el.classList.remove('show');
    });
    if (willOpen) menu.classList.add('show');
}
document.addEventListener('click', function(e){
    AK_MENU_IDS.forEach(function(id){
        var menu = document.getElementById(id);
        if (menu && menu.classList.contains('show') && !menu.contains(e.target)) {
            menu.classList.remove('show');
        }
    });
});
document.addEventListener('keydown', function(e){
    if (e.key === 'Escape') {
        AK_MENU_IDS.forEach(function(id){
            var menu = document.getElementById(id);
            if (menu) menu.classList.remove('show');
        });
    }
});
</script>
