<?php

namespace App\Listeners;

use App\Events\UserVerified;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class UserVerifiedListener
{
    /**
     * Handle the event.
     *
     * @param  UserVerified  $event
     * @return void
     */
    public function handle(UserVerified $event)
    {
        try {
            $user = $event->user;

            // Get user plan information
            $planName = $user->plan ? $user->plan->name : 'N/A';
            $phone = $user->mobile ? ($user->dial_code . $user->mobile) : 'N/A';

            // Prepare email data
            $data = [
                'plan' => $planName,
                'email' => $user->email,
                'phone' => $phone,
            ];

            // Send alert email (non-blocking)
            Mail::raw($this->buildEmailBody($data), function ($message) use ($data) {
                $message->to('hafizfahadhassan@gmail.com')
                        ->subject('[Butik Cloud] New sign up: ' . $data['email']);
            });

        } catch (\Exception $e) {
            // Log the error but don't block signup
            Log::error('Signup alert email failed: ' . $e->getMessage(), [
                'user_id' => $user->id ?? null,
                'user_email' => $user->email ?? null,
            ]);
        }
    }

    /**
     * Build the email body exactly as specified
     *
     * @param array $data
     * @return string
     */
    private function buildEmailBody(array $data): string
    {
        return "Pricing plan: {$data['plan']}\nEmail: {$data['email']}\nPhone: {$data['phone']}";
    }
}
