<?php

namespace App\Livewire\Admin;

use App\Models\Company;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

class Settings extends Component
{
    use WithFileUploads;

    public $company_id;
    public $name, $email, $phone, $address, $currency_code, $timezone, $hours;
    public $socials = [];
    public $logo;
    public $extra_config = [
        'map_url' => '',
        'message' => '',
        'mercadopago' => [
            'key' => '',
            'token' => '',
            'currency' => 'PEN',
            'locale' => 'es-PE',
        ],
        'paypal' => [
            'mode' => 'sandbox',
            'currency' => 'USD',
            'client_id' => '',
            'secret' => '',
        ]
    ];

    public function mount()
    {
        $company = Company::first() ?? new Company();
        $this->company_id = $company->id;
        $this->name = $company->name;
        $this->email = $company->email;
        $this->phone = $company->phone;
        $this->address = $company->address;
        $this->currency_code = $company->currency_code;
        $this->timezone = $company->timezone;
        $this->hours = $company->hours;
        
        $socialsData = $company->social_links ?? [];
    
        if (array_keys($socialsData) !== range(0, count($socialsData) - 1)) {
            $this->socials = [];
            foreach ($socialsData as $key => $url) {
                $this->socials[] = [
                    'name' => ucfirst($key),
                    'url' => $url,
                    'icon' => 'fa-brands fa-' . strtolower($key),
                    'color1' => '#000000',
                    'color2' => '#000000',
                ];
            }
        } else {
            $this->socials = $socialsData;
        }

        if (!empty($company->extra_config)) {
            $this->extra_config = array_merge($this->extra_config, $company->extra_config);
        }
    }

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'currency_code' => 'required|string|max:3',
            'timezone' => 'required|string',
            'hours' => 'required|string',
            'logo' => 'nullable|image|max:2048',
            'extra_config.map_url' => 'nullable|string',
            'extra_config.message' => 'nullable|string|max:500',
            'extra_config.mercadopago.key' => 'nullable|string',
            'extra_config.mercadopago.token' => 'nullable|string',
            'extra_config.mercadopago.currency' => 'nullable|string|max:3',
            'extra_config.mercadopago.locale' => 'nullable|string|max:10',
            'extra_config.paypal.mode' => 'nullable|string|in:sandbox,live',
            'extra_config.paypal.currency' => 'nullable|string|max:3',
            'extra_config.paypal.client_id' => 'nullable|string',
            'extra_config.paypal.secret' => 'nullable|string',
        ];
    }

    public function save()
    {
        $this->validate();

        $company = Company::find($this->company_id) ?? new Company();

        $company->fill([
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
            'currency_code' => $this->currency_code,
            'timezone' => $this->timezone,
            'hours' => $this->hours,
            'social_links' => $this->socials,
            'extra_config' => $this->extra_config,
        ]);

        if ($this->logo) {
            if ($company->logo_path) {
                Storage::disk('public')->delete($company->logo_path);
            }
            $company->logo_path = $this->logo->store('logos', 'public');
        }

        $company->save();

        $this->company_id = $company->id;

        $this->dispatch('swal', [
            'title' => '¡Éxito!',
            'text' => 'Datos guardados correctamente.',
            'icon' => 'success'
        ]);
    }

    public function addSocial()
    {
        $this->socials[] = ['name' => '', 'url' => '', 'icon' => 'fa-brands fa-instagram', 'color1' => '#000000', 'color2' => '#000000'];
    }

    public function removeSocial($index)
    {
        unset($this->socials[$index]);
        $this->socials = array_values($this->socials);
    }

    public function render()
    {
        return view('livewire.admin.settings');
    }
}