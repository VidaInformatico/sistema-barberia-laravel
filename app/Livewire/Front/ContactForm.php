<?php

namespace App\Livewire\Front;

use App\Livewire\Admin\Settings;
use App\Mail\ContactMessage;
use App\Models\Company;
use Illuminate\Mail\Mailer;
use Livewire\Component;
use Illuminate\Support\Facades\Mail;

class ContactForm extends Component
{
    public $name = '';
    public $phone = '';
    public $subject = '';
    public $message = '';

    protected $rules = [
        'name' => 'required|min:3',
        'phone' => 'required',
        'subject' => 'required',
        'message' => 'required|min:10',
    ];

    public function send()
    {
        $this->validate();

        $setting = Company::first();

        if ($setting->email) {
            Mail::to($setting->email)->send(new ContactMessage([
                'name' => $this->name,
                'phone' => $this->phone,
                'subject' => $this->subject,
                'message' => $this->message,
            ]));

            $this->reset();
            session()->flash('message', '¡Mensaje enviado con éxito!');
        }
    }

    public function render()
    {
        return view('livewire.front.contact-form');
    }
}
