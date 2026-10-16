<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use Carbon\Carbon;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

#[Signature('app:send-tenant-expiration-reminders')]
#[Description('Envía correos automáticos a los inquilinos cuando su plan está por vencer (5 y 3 días) o ya ha vencido.')]
class SendTenantExpirationReminders extends Command
{
    public function handle()
    {
        $today = Carbon::today();

        $target5Days = $today->copy()->addDays(5)->toDateString();
        $tenants5Days = Tenant::whereDate('trial_ends_at', $target5Days)
            ->orWhereDate('ends_at', $target5Days)
            ->get();

        foreach ($tenants5Days as $tenant) {
            $this->sendEmail($tenant, 5);
        }

        $target3Days = $today->copy()->addDays(3)->toDateString();
        $tenants3Days = Tenant::whereDate('trial_ends_at', $target3Days)
            ->orWhereDate('ends_at', $target3Days)
            ->get();

        foreach ($tenants3Days as $tenant) {
            $this->sendEmail($tenant, 3);
        }

        $tenantsExpiredToday = Tenant::whereDate('trial_ends_at', $today->toDateString())
            ->orWhereDate('ends_at', $today->toDateString())
            ->get();

        foreach ($tenantsExpiredToday as $tenant) {
            $this->sendEmail($tenant, 0);
        }

        $this->info('¡Recordatorios de suscripción procesados exitosamente!');
    }

    private function sendEmail(Tenant $tenant, int $daysLeft)
    {
        $company = $tenant->run(function () {
            return \App\Models\Company::first();
        });

        $email = $company?->email ?? $tenant->email ?? null;

        if (!$email) {
            return;
        }

        Mail::send('emails.tenant-expiration', ['tenant' => $tenant, 'company' => $company, 'daysLeft' => $daysLeft], function ($message) use ($email, $daysLeft) {
            $message->to($email);
            if ($daysLeft > 0) {
                $message->subject("⏳ Plan por vencer ({$daysLeft}d) - Renovación pendiente");
            } else {
                $message->subject("❗ Suscripción vencida - Acceso suspendido");
            }
        });
    }
}
