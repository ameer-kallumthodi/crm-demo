<?php

if (!function_exists('send_email')) {
    function send_email($email, $name, $subject, $body, $attachments = [], $fromName = null, $fromAddress = null) {
        try {
            \Illuminate\Support\Facades\Mail::html($body, function ($message) use ($email, $name, $subject, $attachments, $fromName, $fromAddress) {
                $message->to($email, $name)
                        ->subject($subject)
                        ->from($fromAddress ?: config('mail.from.address'), $fromName ?: config('mail.from.name'));
                
                // Add attachments if provided
                if (!empty($attachments)) {
                    foreach ($attachments as $attachment) {
                        if (file_exists($attachment)) {
                            $message->attach($attachment);
                        }
                    }
                }
            });
            
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}

if (!function_exists('send_email_with_attachments')) {
    function send_email_with_attachments($email, $name, $subject, $body, $attachments = [], $fromName = null, $fromAddress = null) {
        try {
            \Illuminate\Support\Facades\Mail::html($body, function ($message) use ($email, $name, $subject, $attachments, $fromName, $fromAddress) {
                $message->to($email, $name)
                        ->subject($subject)
                        ->from($fromAddress ?: config('mail.from.address'), $fromName ?: config('mail.from.name'));
                
                // Add attachments if provided
                if (!empty($attachments)) {
                    foreach ($attachments as $attachment) {
                        if (file_exists($attachment)) {
                            $message->attach($attachment);
                        }
                    }
                }
            });
            
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
