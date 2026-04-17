<?php

namespace App\Services\Payment;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Stripe\Refund;
use Stripe\Exception\ApiErrorException;

class StripeService
{
    protected ?string $secretKey;
    protected ?string $webhookSecret;

    public function __construct()
    {
        $this->secretKey = config('services.stripe.secret');
        $this->webhookSecret = config('services.stripe.webhook_secret');
        
        if ($this->secretKey) {
            Stripe::setApiKey($this->secretKey);
        }
    }

    /**
     * Create a Stripe Checkout Session
     */
    public function createCheckoutSession(array $data): array
    {
        if (!$this->secretKey) {
            return [
                'success' => false,
                'error' => 'Stripe is not configured',
            ];
        }

        try {
            $sessionData = [
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => $data['currency'] ?? 'eur',
                        'product_data' => [
                            'name' => $data['plan_name'] ?? 'LexOmnis Subscription',
                            'description' => $data['plan_description'] ?? '',
                        ],
                        'unit_amount' => (int)($data['amount'] * 100), // Convert to cents
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment', // Change to 'subscription' if using Stripe Billing
                'success_url' => $data['success_url'] ?? route('checkout.success') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => $data['cancel_url'] ?? route('checkout.cancel'),
                'customer_email' => $data['customer_email'] ?? null,
                'metadata' => array_merge($data['metadata'] ?? [], [
                    'plan_id' => $data['plan_id'] ?? null,
                    'billing_period' => $data['billing_period'] ?? null,
                    'tenant_email' => $data['customer_email'] ?? null,
                ]),
            ];

            // If it's a subscription, adjust mode and add recurring data
            if (isset($data['is_subscription']) && $data['is_subscription']) {
                $sessionData['mode'] = 'subscription';
                $sessionData['line_items'][0]['price_data']['recurring'] = [
                    'interval' => ($data['billing_period'] ?? 'monthly') === 'yearly' ? 'year' : 'month',
                ];
            }

            $session = Session::create($sessionData);

            return [
                'success' => true,
                'session_id' => $session->id,
                'checkout_url' => $session->url,
            ];
        } catch (ApiErrorException $e) {
            Log::error('Stripe checkout session creation failed', [
                'error' => $e->getMessage(),
                'data' => $data,
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Handle Stripe Webhook
     */
    public function handleWebhook(Request $request): bool
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload,
                $sigHeader,
                $this->webhookSecret
            );
        } catch (\Exception $e) {
            Log::error('Stripe webhook signature verification failed', [
                'error' => $e->getMessage(),
            ]);
            return false;
        }

        Log::info('Stripe webhook received', ['type' => $event->type]);

        try {
            switch ($event->type) {
                case 'checkout.session.completed':
                    $this->handleCheckoutCompleted($event->data->object);
                    break;
                case 'invoice.payment_succeeded':
                    // Handle recurring payment
                    break;
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Stripe webhook processing failed', [
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    protected function handleCheckoutCompleted($session): void
    {
        // Here we would normally update the tenant's subscription status
        Log::info('Stripe checkout completed successfully', [
            'session_id' => $session->id,
            'email' => $session->customer_email,
            'metadata' => $session->metadata
        ]);
        
        // TODO: Update Tenant/Subscription in DB
    }
}
