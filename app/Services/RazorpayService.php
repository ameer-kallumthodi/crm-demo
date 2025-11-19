<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Razorpay\Api\Api;

class RazorpayService
{
    protected ?Api $client = null;

    public function __construct()
    {
        $keyId = config('razorpay.key_id');
        $keySecret = config('razorpay.key_secret');

        if ($keyId && $keySecret) {
            $this->client = new Api($keyId, $keySecret);
        }
    }

    public function isConfigured(): bool
    {
        return $this->client !== null;
    }

    public function createPaymentLink(array $payload): array
    {
        if (!$this->client) {
            throw new \RuntimeException('Razorpay credentials are not configured.');
        }

        $response = $this->client->paymentLink->create($payload);

        return $response->toArray();
    }

    public function fetchPaymentLink(string $paymentLinkId): array
    {
        if (!$this->client) {
            throw new \RuntimeException('Razorpay credentials are not configured.');
        }

        $response = $this->client->paymentLink->fetch($paymentLinkId);

        return $response->toArray();
    }

    public function safeFetchPaymentLink(string $paymentLinkId): ?array
    {
        try {
            return $this->fetchPaymentLink($paymentLinkId);
        } catch (\Throwable $exception) {
            Log::error('Failed to fetch Razorpay payment link', [
                'payment_link_id' => $paymentLinkId,
                'message' => $exception->getMessage(),
            ]);
        }

        return null;
    }

    public function cancelPaymentLink(string $paymentLinkId): array
    {
        if (!$this->client) {
            throw new \RuntimeException('Razorpay credentials are not configured.');
        }

        $response = $this->client->paymentLink->cancel($paymentLinkId);

        return $response->toArray();
    }

    public function fetchPayment(string $paymentId): array
    {
        if (!$this->client) {
            throw new \RuntimeException('Razorpay credentials are not configured.');
        }

        $response = $this->client->payment->fetch($paymentId);

        return $response->toArray();
    }
}

