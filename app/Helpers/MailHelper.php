<?php

use Illuminate\Support\Facades\Log;

if (!function_exists('send_email')) {
    function send_email($email, $name, $subject, $body, $attachments = [], $fromName = null, $fromAddress = null) {
        $result = send_email_detailed($email, $name, $subject, $body, $attachments, $fromName, $fromAddress);

        return $result['success'];
    }
}

if (!function_exists('send_email_detailed')) {
    /**
     * @return array{success: bool, error: ?string}
     */
    function send_email_detailed($email, $name, $subject, $body, $attachments = [], $fromName = null, $fromAddress = null): array
    {
        try {
            \Illuminate\Support\Facades\Mail::html($body, function ($message) use ($email, $name, $subject, $attachments, $fromName, $fromAddress) {
                $message->to($email, $name)
                    ->subject($subject)
                    ->from(
                        $fromAddress ?: config('mail.from.address'),
                        $fromName ?: config('mail.from.name')
                    );

                if (! empty($attachments)) {
                    foreach ($attachments as $attachment) {
                        if (file_exists($attachment)) {
                            $message->attach($attachment);
                        }
                    }
                }
            });

            return ['success' => true, 'error' => null];
        } catch (\Throwable $e) {
            Log::error('send_email failed', [
                'to' => $email,
                'subject' => $subject,
                'error' => $e->getMessage(),
            ]);

            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}

if (!function_exists('send_email_with_attachments')) {
    function send_email_with_attachments($email, $name, $subject, $body, $attachments = [], $fromName = null, $fromAddress = null) {
        return send_email_detailed($email, $name, $subject, $body, $attachments, $fromName, $fromAddress)['success'];
    }
}
