<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\BusinessSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
class BusinessSettingController extends Controller
{
    public function edit() { return view('admin.settings.edit',['settings'=>BusinessSetting::current()]); }
    public function update(Request $request)
    {
        if ($request->input('save_section') === 'contacts') {
            $settings=BusinessSetting::current();
            $data=$request->validate(['contact_phones'=>['nullable','array','max:10'],'contact_phones.*'=>['nullable','string','max:40','distinct']]);
            $phones=collect($data['contact_phones'] ?? [])->map(fn($phone)=>trim($phone))->filter()->unique()->values()->all();
            $settings->update(['contact_phones'=>$phones,'phone'=>$phones[0] ?? null,'contact_button_enabled'=>$request->boolean('contact_button_enabled'),'contact_phone_visible'=>$request->boolean('contact_phone_visible')]);
            BusinessSetting::forgetCache();
            return back()->with('status','Контактные номера сохранены.');
        }
        $settings=BusinessSetting::current(); $data=$this->validated($request);
        $data['enabled_locales']=collect($data['enabled_locales'])->push($data['default_locale'])->unique()->values()->all();
        $data['contact_phones']=collect($data['contact_phones'] ?? [])->map(fn($phone)=>trim($phone))->filter()->unique()->values()->all();
        $data['phone']=$data['contact_phones'][0] ?? null;
        foreach(['development_mode','email_notifications','order_notifications','seller_notifications','otp_enabled','smtp_enabled','contact_button_enabled','contact_phone_visible'] as $field) $data[$field]=$request->boolean($field);
        if(!$request->filled('smtp_password')) unset($data['smtp_password']);
        if($request->hasFile('logo')) { if($settings->logo_path) Storage::disk('public')->delete($settings->logo_path); $data['logo_path']=$request->file('logo')->store('branding','public'); }
        unset($data['logo']); $settings->update($data); BusinessSetting::forgetCache();
        return back()->with('status','Бизнес-настройки сохранены.');
    }
    public function testMail(Request $request)
    {
        $settings=BusinessSetting::current();
        $request->validate(['test_email'=>['required','email']]);
        abort_unless($settings->smtp_enabled,422,'Сначала включите и сохраните SMTP.');
        $this->configureMail($settings);
        try { Mail::raw('SMTP настроен правильно. Это тестовое письмо AK KAGYZ.',fn($mail)=>$mail->to($request->test_email)->subject('Проверка SMTP — AK KAGYZ')); }
        catch(\Throwable $e) { report($e); return back()->withErrors(['smtp'=>'Не удалось отправить письмо. Проверьте сервер, порт, логин и пароль.']); }
        return back()->with('status','Тестовое письмо отправлено на '.$request->test_email.'.');
    }
    private function validated(Request $request): array
    {
        return $request->validate([
            'site_name'=>['required','string','max:120'],'tagline'=>['nullable','string','max:180'],'company_name'=>['nullable','string','max:160'],'store_description'=>['nullable','string','max:1200'],'phone'=>['nullable','string','max:40'],'contact_phones'=>['nullable','array','max:10'],'contact_phones.*'=>['nullable','string','max:40','distinct'],'email'=>['nullable','email','max:160'],'address'=>['nullable','string','max:255'],'logo'=>['nullable','image','mimes:png,jpg,jpeg,webp,svg','max:4096'],'currency'=>['required','string','max:10'],'primary_color'=>['required','regex:/^#[0-9A-Fa-f]{6}$/'],'instagram_url'=>['nullable','url','max:255'],'telegram_url'=>['nullable','url','max:255'],'whatsapp_url'=>['nullable','url','max:255'],'support_hours'=>['nullable','string','max:120'],
            'development_title'=>['required','string','max:160'],'development_message'=>['nullable','string','max:1000'],
            'otp_channel'=>['required','in:email'],'otp_ttl'=>['required','integer','min:1','max:30'],'otp_length'=>['required','integer','min:4','max:8'],
            'smtp_host'=>['nullable','string','max:255'],'smtp_port'=>['required','integer','min:1','max:65535'],'smtp_username'=>['nullable','string','max:255'],'smtp_password'=>['nullable','string','max:500'],'smtp_encryption'=>['nullable','in:tls,ssl,none'],'smtp_from_address'=>['nullable','email','max:255'],'smtp_from_name'=>['nullable','string','max:120'],
            'default_locale'=>['required','in:ru,tk,en'],'enabled_locales'=>['required','array','min:1'],'enabled_locales.*'=>['in:ru,tk,en','distinct'],
        ]);
    }
    private function configureMail(BusinessSetting $s): void
    {
        config(['mail.default'=>'smtp','mail.mailers.smtp.host'=>$s->smtp_host,'mail.mailers.smtp.port'=>$s->smtp_port,'mail.mailers.smtp.username'=>$s->smtp_username,'mail.mailers.smtp.password'=>$s->smtp_password,'mail.mailers.smtp.scheme'=>$s->smtp_encryption==='none'?null:$s->smtp_encryption,'mail.from.address'=>$s->smtp_from_address,'mail.from.name'=>$s->smtp_from_name?:$s->site_name]);
        app('mail.manager')->forgetMailers();
    }
}
