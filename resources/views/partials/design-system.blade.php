:root{
  --bg:oklch(0.17 0.014 264); --bg-elevated:oklch(0.205 0.014 264);
  --surface:oklch(0.23 0.015 264); --surface-hover:oklch(0.27 0.017 264);
  --border:oklch(0.30 0.016 264); --border-strong:oklch(0.40 0.02 264);
  --text:oklch(0.96 0.004 264); --text-muted:oklch(0.72 0.014 264); --text-faint:oklch(0.52 0.014 264);
  --accent:oklch(0.56 0.19 264); --accent-strong:oklch(0.49 0.20 264);
  --accent-soft:oklch(0.56 0.19 264 / 0.14); --accent-text:oklch(0.99 0 0);
  --success:oklch(0.75 0.15 145); --success-soft:oklch(0.75 0.15 145 / 0.14);
  --danger:oklch(0.68 0.19 25); --danger-soft:oklch(0.68 0.19 25 / 0.14);
  --warning:oklch(0.80 0.15 85); --warning-soft:oklch(0.80 0.15 85 / 0.14);
  --scrim:oklch(0.17 0.014 264 / 0.7);
  --pattern-dot:oklch(1 0 0 / 0.035);
  --font:'Nunito',system-ui,-apple-system,sans-serif;
  color-scheme: dark;
}
:root[data-theme="light"]{
  --bg:oklch(0.955 0.003 264); --bg-elevated:oklch(0.995 0.002 264);
  --surface:oklch(1 0 0); --surface-hover:oklch(0.95 0.008 264);
  --border:oklch(0.89 0.01 264); --border-strong:oklch(0.78 0.014 264);
  --text:oklch(0.22 0.02 264); --text-muted:oklch(0.44 0.02 264); --text-faint:oklch(0.58 0.016 264);
  --accent:oklch(0.50 0.19 264); --accent-strong:oklch(0.44 0.20 264);
  --accent-soft:oklch(0.50 0.19 264 / 0.10); --accent-text:oklch(0.99 0 0);
  --success:oklch(0.48 0.14 145); --success-soft:oklch(0.48 0.14 145 / 0.12);
  --danger:oklch(0.55 0.19 25); --danger-soft:oklch(0.55 0.19 25 / 0.10);
  --warning:oklch(0.55 0.15 80); --warning-soft:oklch(0.55 0.15 80 / 0.12);
  --scrim:oklch(0.22 0.02 264 / 0.5);
  --pattern-dot:oklch(0.22 0.02 264 / 0.055);
  color-scheme: light;
}
*{box-sizing:border-box;}
html{transition:background .2s ease;}
html,body{
  margin:0;background-color:var(--bg);color:var(--text);font-family:var(--font);
  background-image:radial-gradient(var(--pattern-dot) 1.4px, transparent 1.4px);
  background-size:25px 25px;
}
a{color:var(--accent);text-decoration:none;}
a:hover{color:var(--accent-strong);}
button{font-family:var(--font);cursor:pointer;}
input,textarea,select{font-family:var(--font);}
::placeholder{color:var(--text-faint);}
.wrap{max-width:1320px;margin:0 auto;padding:0 24px;}
.pcard{position:relative;background:var(--surface);border:1px solid var(--border);border-radius:16px;overflow:hidden;display:flex;flex-direction:column;transition:background .2s ease,border-color .2s ease,transform .22s ease,box-shadow .22s ease;}
.pcard:hover{transform:translateY(-3px);border-color:var(--border-strong);box-shadow:0 16px 32px rgba(0,0,0,.22);}
:root[data-theme="light"] .pcard:hover{box-shadow:0 16px 32px rgba(20,20,30,.1);}
.pcard-img{position:relative;aspect-ratio:1/1.12;background:radial-gradient(circle at 35% 30%, var(--surface-hover) 0%, var(--bg-elevated) 70%);display:flex;align-items:center;justify-content:center;color:var(--text-faint);overflow:hidden;}
.pcard-img x-icon,.pcard-img svg{transition:transform .3s ease;}
.pcard:hover .pcard-img svg{transform:scale(1.08);}
.pcard-quick{width:34px;height:34px;padding:0;border-radius:10px;background:oklch(0.17 0.014 264 / 0.62);backdrop-filter:blur(8px);border:1px solid oklch(1 0 0 / 0.16);color:#fff;display:grid;place-items:center;line-height:0;cursor:pointer;transition:transform .2s ease,background .2s ease,color .2s ease,box-shadow .2s ease;}
.pcard-quick svg{display:block;margin:0;flex:none}.wishlist-toggle-form{margin:0;padding:0;height:34px}.wishlist-toggle svg{fill:transparent;transition:fill .2s ease,transform .25s cubic-bezier(.2,.8,.2,1)}.wishlist-toggle.on svg{fill:currentColor}.icon-btn.wishlist-toggle.on{color:#e44876;background:color-mix(in oklch,#e44876 10%,var(--surface))!important}.wishlist-toggle.is-busy{pointer-events:none;opacity:.7}.wishlist-toggle.is-pop svg{animation:wishlist-pop .4s cubic-bezier(.2,.85,.3,1.35)}@keyframes wishlist-pop{0%{transform:scale(.72)}55%{transform:scale(1.3)}100%{transform:scale(1)}}
.pcard-quick:hover{background:var(--accent);color:var(--accent-text);}
.pcard-quick.on{background:var(--accent);color:var(--accent-text);}
.wishlist-toast{position:fixed;left:50%;bottom:28px;z-index:200;transform:translate(-50%,18px);opacity:0;pointer-events:none;padding:11px 16px;border-radius:12px;background:var(--text);color:var(--bg);font-size:13px;font-weight:700;box-shadow:0 16px 36px rgba(0,0,0,.2);transition:.25s ease}.wishlist-toast.show{opacity:1;transform:translate(-50%,0)}
.cart-ajax-form{margin-top:6px}.cart-card-state{height:40px}.cart-add-button{width:100%;height:40px;transition:transform .2s ease,background .2s ease}.cart-add-button:active{transform:scale(.97)}.cart-qty-control{height:40px;border-radius:11px;background:linear-gradient(135deg,#16835b,#20a875);color:#fff;display:grid;grid-template-columns:42px 1fr 42px;align-items:center;box-shadow:0 8px 18px rgba(22,131,91,.2);animation:cart-state-in .3s ease}.cart-qty-control button{height:100%;border:0;background:transparent;color:#fff;display:grid;place-items:center;cursor:pointer}.cart-qty-control button:hover{background:rgba(255,255,255,.12)}.cart-qty-control span{text-align:center;display:flex;align-items:center;justify-content:center;gap:7px}.cart-qty-control small{font-size:11px;opacity:.9}.cart-qty-control b{font-size:15px}.cart-ajax-form.is-busy{pointer-events:none;opacity:.75}@keyframes cart-state-in{from{opacity:0;transform:scale(.94)}to{opacity:1;transform:scale(1)}}
.cart-ajax-large .cart-card-state,.cart-ajax-large .cart-add-button,.cart-ajax-large .cart-qty-control{height:52px}.cart-ajax-large .cart-qty-control small{font-size:13px}.cart-ajax-large .cart-qty-control b{font-size:17px}
.cart-drawer-backdrop{position:fixed;inset:0;z-index:180;background:rgba(8,15,30,.42);backdrop-filter:blur(3px);opacity:0;visibility:hidden;transition:.28s}.cart-drawer{position:fixed;right:0;top:0;bottom:0;z-index:181;width:min(430px,100%);background:var(--surface);box-shadow:-24px 0 60px rgba(5,12,30,.2);transform:translateX(102%);transition:transform .36s cubic-bezier(.22,.8,.25,1);display:flex;flex-direction:column}.cart-drawer-backdrop.show{opacity:1;visibility:visible}.cart-drawer.show{transform:translateX(0)}.cart-drawer-head{padding:20px 22px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between}.cart-drawer-title{font-size:19px;font-weight:700;display:flex;align-items:center;gap:9px}.cart-drawer-close{width:36px;height:36px;border:1px solid var(--border);border-radius:10px;background:var(--bg);display:grid;place-items:center;cursor:pointer}.cart-drawer-body{padding:8px 20px;overflow:auto;flex:1}.cart-drawer-item{display:grid;grid-template-columns:64px 1fr auto;gap:12px;align-items:center;padding:14px 0;border-bottom:1px solid var(--border)}.cart-drawer-image{width:64px;height:64px;border-radius:12px;background:var(--bg) center/contain no-repeat;display:grid;place-items:center;color:var(--text-faint)}.cart-drawer-name{font-size:13px;font-weight:650;line-height:1.35}.cart-drawer-qty{font-size:11px;color:var(--text-faint);margin-top:5px}.cart-drawer-price{font-size:13px;font-weight:750;white-space:nowrap}.cart-drawer-foot{padding:18px 22px 22px;border-top:1px solid var(--border);background:var(--bg-elevated)}.cart-drawer-total{display:flex;justify-content:space-between;align-items:baseline;margin-bottom:15px}.cart-drawer-total span{font-size:13px;color:var(--text-muted)}.cart-drawer-total strong{font-size:22px}.cart-drawer-actions{display:grid;grid-template-columns:1fr 1.3fr;gap:9px}.cart-drawer-empty{text-align:center;padding:70px 15px;color:var(--text-faint)}
.badge{font-size:11px;font-weight:800;padding:4px 8px;border-radius:8px;display:inline-flex;align-items:center;gap:4px;}
.icon-btn{position:relative;width:44px;height:44px;border-radius:10px;background:transparent;border:1px solid transparent;color:var(--text);display:flex;align-items:center;justify-content:center;text-decoration:none;}
.icon-btn:hover{background:var(--surface);border-color:var(--border);}
.count{position:absolute;top:1px;right:1px;background:var(--accent);color:var(--accent-text);font-size:10px;font-weight:800;min-width:16px;height:16px;border-radius:8px;display:flex;align-items:center;justify-content:center;padding:0 3px;}
.btn-accent{height:40px;border-radius:10px;background:var(--accent);color:var(--accent-text);border:none;font-weight:800;font-size:14px;display:flex;align-items:center;justify-content:center;gap:6px;text-decoration:none;padding:0 16px;transition:background .15s ease,transform .1s ease;}
.btn-accent:hover{background:var(--accent-strong);color:var(--accent-text);}
.btn-accent:active{transform:scale(.97);}
.btn-ghost{height:40px;border-radius:10px;background:var(--surface);border:1px solid var(--border);color:var(--text);font-weight:700;font-size:13px;display:flex;align-items:center;justify-content:center;gap:6px;text-decoration:none;padding:0 16px;transition:background .15s ease,border-color .15s ease;}
.btn-ghost:hover{background:var(--surface-hover);border-color:var(--border-strong);}
.stars{display:flex;align-items:center;gap:2px;color:var(--warning);}
.rating-chip{display:inline-flex;align-items:center;gap:5px;padding:3px 8px 3px 6px;border-radius:8px;background:var(--warning-soft);color:var(--text);font-size:11.5px;font-weight:800;}
.rating-chip .stars{gap:1px;}
.input{width:100%;height:46px;background:var(--surface);border:1px solid var(--border);border-radius:12px;padding:0 16px;color:var(--text);font-family:var(--font);font-size:14px;outline:none;transition:border-color .15s ease,box-shadow .15s ease;}
.input:focus{border-color:var(--accent);box-shadow:0 0 0 3px var(--accent-soft);}
.toolbar{display:flex;align-items:center;gap:10px;flex-wrap:wrap;}
.input:hover:not(:focus){border-color:var(--border-strong);}
.label{font-size:13px;font-weight:800;margin-bottom:8px;display:block;}
.alert{padding:12px 16px;border-radius:10px;font-size:13px;font-weight:700;margin-bottom:16px;}
.alert-success{background:var(--success-soft);color:var(--success);}
.alert-danger{background:var(--danger-soft);color:var(--danger);}
.theme-toggle{position:relative;width:44px;height:44px;border-radius:10px;background:transparent;border:1px solid transparent;color:var(--text);display:flex;align-items:center;justify-content:center;flex-shrink:0;}
.theme-toggle:hover{background:var(--surface);border-color:var(--border);}
.theme-toggle span{display:flex;align-items:center;}
.theme-toggle .icon-sun{display:none;}
:root[data-theme="light"] .theme-toggle .icon-moon{display:none;}
:root[data-theme="light"] .theme-toggle .icon-sun{display:flex;}
.theme-toggle-on-dark{color:#fff;}
.theme-toggle-on-dark:hover{background:oklch(1 0 0 / 0.15);border-color:oklch(1 0 0 / 0.2);}
.ak-logo-dark{display:block;}
.ak-logo-light{display:none;}
:root[data-theme="light"] .ak-logo-dark{display:none;}
:root[data-theme="light"] .ak-logo-light{display:block;}
.dropdown-panel{
    opacity:0;visibility:hidden;pointer-events:none;
    transform:translateY(-6px) scale(.98);
    transition:opacity .16s cubic-bezier(.16,1,.3,1),transform .16s cubic-bezier(.16,1,.3,1),visibility .16s;
}
.dropdown-panel.show{opacity:1;visibility:visible;pointer-events:auto;transform:translateY(0) scale(1);}
.review-card{padding:16px;border-radius:14px;background:var(--surface);border:1px solid var(--border);}
.rating-picker label{transition:transform .12s ease;}
.rating-picker label:hover{transform:scale(1.18);}
.input-icon-wrap{position:relative;}
.input-icon-wrap .input{padding-left:44px;}
.input-icon-wrap .input.has-toggle{padding-right:44px;}
.input-icon-wrap .input-icon{position:absolute;left:14px;top:50%;transform:translateY(-50%);color:var(--text-faint);display:flex;pointer-events:none;}
.input-icon-wrap .input-toggle{position:absolute;right:5px;top:50%;transform:translateY(-50%);width:34px;height:34px;border-radius:8px;background:transparent;border:none;color:var(--text-faint);display:flex;align-items:center;justify-content:center;cursor:pointer;transition:background .15s ease,color .15s ease;padding:0;}
.input-icon-wrap .input-toggle:hover{background:var(--surface-hover);color:var(--text);}
@keyframes authCardIn{from{opacity:0;transform:translateY(16px);}to{opacity:1;transform:translateY(0);}}
.auth-card{animation:authCardIn .5s cubic-bezier(.16,1,.3,1);}
.auth-tab{flex:1;height:42px;border-radius:9px;font-weight:800;font-size:14px;display:flex;align-items:center;justify-content:center;transition:background .18s ease,color .18s ease;}
.auth-tab.active{background:var(--accent);color:var(--accent-text);}
.auth-tab:not(.active){color:var(--text-muted);}
.auth-tab:not(.active):hover{color:var(--text);}
.popular-searches{padding:13px 14px 15px}.popular-searches-head{display:flex;align-items:center;gap:7px;margin-bottom:10px;font-size:11px;font-weight:700;color:var(--text)}.popular-searches-head svg{color:var(--accent)}.popular-searches-list{display:flex;flex-wrap:wrap;gap:7px}.popular-search-chip{display:inline-flex;align-items:center;gap:6px;padding:8px 10px;border-radius:9px;background:var(--bg);border:1px solid var(--border);color:var(--text-muted);font-size:11px;font-weight:600;transition:.17s}.popular-search-chip:hover{background:var(--accent-soft);border-color:color-mix(in oklch,var(--accent) 28%,var(--border));color:var(--accent);transform:translateY(-1px)}
.language-wrap{position:relative}.language-trigger{height:38px;padding:0 10px;display:flex;align-items:center;gap:6px;border:1px solid var(--border);border-radius:11px;background:var(--surface);color:var(--text-muted);font:600 11px var(--font);cursor:pointer}.language-trigger:hover{color:var(--accent);border-color:var(--border-strong)}.language-menu{position:absolute;right:0;top:46px;width:205px;padding:7px;background:var(--surface);border:1px solid var(--border);border-radius:14px;box-shadow:0 20px 45px rgba(10,22,50,.18);z-index:100}.language-head{padding:7px 9px 9px;font-size:10px;color:var(--text-faint);text-transform:uppercase;letter-spacing:.06em}.language-option{width:100%;height:42px;padding:0 9px;border:0;border-radius:9px;background:transparent;color:var(--text-muted);display:grid;grid-template-columns:25px 1fr 16px;align-items:center;text-align:left;font:600 12px var(--font);cursor:pointer}.language-option:hover{background:var(--surface-hover);color:var(--text)}.language-option.active{background:var(--accent-soft);color:var(--accent)}.language-flag{font-size:18px}
.auth-visual{background-color:#0d1730!important;background-image:linear-gradient(180deg,rgba(6,13,31,.12),rgba(6,13,31,.38) 45%,rgba(6,13,31,.94)),url('{{ asset('images/auth-stationery.png') }}')!important;background-position:center!important;background-size:cover!important}
