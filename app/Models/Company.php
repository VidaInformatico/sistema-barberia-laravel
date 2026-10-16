<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = [
        'name', 'email', 'phone', 'address', 
        'currency_code', 'timezone', 'logo_path', 
        'hours',
        'social_links', 'extra_config'
    ];

    protected $casts = [
        'social_links' => 'array',
        'extra_config' => 'array',
    ];

    public function getMercadoPagoKeyAttribute()
    {
        return $this->extra_config['mercadopago_key'] ?? null;
    }

    public function getMercadoPagoTokenAttribute()
    {
        return $this->extra_config['mercadopago_token'] ?? null;
    }

    public function getMercadoPagoCurrencyAttribute()
    {
        return $this->extra_config['mercadopago_currency'] ?? 'PEN';
    }

    public function getMercadoPagoLocaleAttribute()
    {
        return $this->extra_config['mercadopago_locale'] ?? 'es-PE';
    }

    public function getPaypalModeAttribute()
    {
        return $this->extra_config['paypal_mode'] ?? 'sandbox';
    }

    public function getPaypalClientIdAttribute()
    {
        return $this->extra_config['paypal_client_id'] ?? null;
    }

    public function getPaypalSecretAttribute()
    {
        return $this->extra_config['paypal_secret'] ?? null;
    }

    public function getPaypalCurrencyAttribute()
    {
        return $this->extra_config['paypal_currency'] ?? 'USD';
    }
}