<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
class BusinessSetting extends Model
{
    protected $guarded=[];
    protected function casts(): array { return ['development_mode'=>'boolean','email_notifications'=>'boolean','order_notifications'=>'boolean','seller_notifications'=>'boolean','otp_enabled'=>'boolean','smtp_enabled'=>'boolean','contact_button_enabled'=>'boolean','contact_phone_visible'=>'boolean','contact_phones'=>'array','enabled_locales'=>'array','smtp_password'=>'encrypted']; }
    public static function current(): self
    {
        if(!Schema::hasTable('business_settings')) return new self;
        $defaults=['site_name'=>'AK KAGYZ','phone'=>'+99364005374','contact_button_enabled'=>true,'contact_phone_visible'=>true,'currency'=>'TMT','primary_color'=>'#285ED6'];
        // ->fresh() matters: right after the very first insert, the in-memory
        // model only knows the columns we passed — booleans like
        // order_notifications that rely on the migration's DB-level default
        // would read as null/false until re-fetched from the row itself.
        $resolve=fn()=>static::firstOrCreate([],$defaults)->fresh();
        if(!Schema::hasTable('cache')) return $resolve();
        return Cache::remember('business_settings.current',3600,$resolve);
    }
    public static function forgetCache(): void { Cache::forget('business_settings.current'); }
    public function getLogoUrlAttribute(): ?string { return $this->logo_path ? asset('storage/'.$this->logo_path) : null; }
}
