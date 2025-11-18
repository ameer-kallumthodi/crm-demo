<?php

return [
    'key_id' => env('RAZORPAY_KEY_ID'),
    'key_secret' => env('RAZORPAY_KEY_SECRET'),
    'default_currency' => env('RAZORPAY_DEFAULT_CURRENCY', 'INR'),
    'payment_link' => [
        'notify_customer' => env('RAZORPAY_NOTIFY_CUSTOMER', true),
        'reminder_enable' => env('RAZORPAY_PAYMENT_LINK_REMINDERS', false),
        'expire_minutes' => env('RAZORPAY_PAYMENT_LINK_EXPIRE_MINUTES', 0), // 0 = no expiry
    ],
];

