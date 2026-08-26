<section id="storefront" class="card settings-section">
 <div class="settings-section-head"><span class="entity-icon"><x-icon name="image" :size="18"/></span><div><div class="settings-section-title">Витрина и мобильное приложение</div><div class="settings-section-note">Единые данные бренда для сайта, приложения, контактов и социальных сетей</div></div></div>
 <div class="form-grid">
  <div class="field full"><label>Короткий слоган</label><input class="input" name="tagline" maxlength="180" value="{{ old('tagline',$settings->tagline) }}" placeholder="Всё для работы, творчества и бизнеса"></div>
  <div class="field full"><label>Описание магазина</label><textarea class="input" name="store_description" maxlength="1200" style="height:92px;padding:12px">{{ old('store_description',$settings->store_description) }}</textarea></div>
  <div class="field"><label>Валюта</label><input class="input" name="currency" required maxlength="10" value="{{ old('currency',$settings->currency ?: 'TMT') }}"></div>
  <div class="field"><label>Основной цвет</label><input type="color" name="primary_color" value="{{ old('primary_color',$settings->primary_color ?: '#285ED6') }}" style="width:100%;height:42px;border:1px solid var(--border);border-radius:10px;background:var(--surface);padding:4px"></div>
  <div class="field"><label>Часы работы поддержки</label><input class="input" name="support_hours" value="{{ old('support_hours',$settings->support_hours) }}" placeholder="Пн–Сб, 09:00–19:00"></div>
  <div class="field"><label>Instagram</label><input class="input" type="url" name="instagram_url" value="{{ old('instagram_url',$settings->instagram_url) }}" placeholder="https://instagram.com/..."></div>
  <div class="field"><label>Telegram</label><input class="input" type="url" name="telegram_url" value="{{ old('telegram_url',$settings->telegram_url) }}" placeholder="https://t.me/..."></div>
  <div class="field"><label>WhatsApp</label><input class="input" type="url" name="whatsapp_url" value="{{ old('whatsapp_url',$settings->whatsapp_url) }}" placeholder="https://wa.me/..."></div>
 </div>
 <div style="margin-top:18px;padding:14px;border-radius:12px;background:var(--accent-soft);color:var(--text-muted);font-size:12px">После сохранения сайт увидит изменения сразу, а приложение загрузит их при запуске.</div>
</section>
