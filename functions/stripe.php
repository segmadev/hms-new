<?php
require_once ROOT.'/vendor/autoload.php';

class StripeService
{
    private $secretKey;
    private $currency;

    public function __construct($secretKey, $currency)
    {
        $this->secretKey = $secretKey;
        $this->currency = $currency;
        \Stripe\Stripe::setApiKey($this->secretKey);
    }

    public function createCheckoutSession($order, $successUrl, $cancelUrl)
    {
        try {
            // Validate required order data
            if (empty($order['product']['name'])) {
                throw new \Exception("Product name is required");
            }
            if (empty($order['unit_price'])) {
                throw new \Exception("Product price is required");
            }
            if (empty($order['quantity'])) {
                throw new \Exception("Product quantity is required");
            }

            // die(json_encode($order));
            // Prepare line items
            $lineItems = [
                [
                    'price_data' => [
                        'currency' => $this->currency,
                        'product_data' => [
                            'name' => $order['product']['name'],
                            'description' => $order['product']['description'] ?? '',
                            //'images' => $order['product_image'] ?? [], // Optional product images
                        ],
                        'unit_amount' => (int)($order['unit_price'] * 100), // Convert to cents
                    ],
                    'quantity' => (int)$order['quantity'],
                ]
            ];

            // Create session
            $session = \Stripe\Checkout\Session::create([
                'payment_method_types' => ['card'],
                'line_items' => $lineItems,
                'mode' => 'payment',
                'success_url' => $successUrl,
                'cancel_url' => $cancelUrl,
                'client_reference_id' => $order['ID'],
                'customer_email' => $order['user']['email'] ?? null,
                'metadata' => [
                    'order_id' => $order['ID'],
                    'product_id' => $order['productID'] ?? null
                ]
            ]);

            return $session;

        } catch (\Exception $e) {
            throw new \Exception("Stripe Checkout Error: " . $e->getMessage());
        }
    }

    public function checkPaymentStatusBySession($sessionId)
    {
        try {
            $session = \Stripe\Checkout\Session::retrieve($sessionId);
            
            // If payment_intent is not available, return session status
            if (empty($session->payment_intent)) {
                return [
                    'status' => $session->payment_status,
                    'amount_received' => $session->amount_total,
                    'currency' => $session->currency,
                    'order_id' => $session->metadata->order_id ?? null,
                ];
            }

            $paymentIntent = \Stripe\PaymentIntent::retrieve($session->payment_intent);

            return [
                'status' => $paymentIntent->status,
                'amount_received' => $paymentIntent->amount_received,
                'currency' => $paymentIntent->currency,
                'order_id' => $session->metadata->order_id ?? null,
            ];
        } catch (\Exception $e) {
            throw new \Exception("Status Check Error: " . $e->getMessage());
        }
    }

    public function checkPaymentStatusByIntent($paymentIntentId)
    {
        try {
            $intent = \Stripe\PaymentIntent::retrieve($paymentIntentId);
            return [
                'status' => $intent->status,
                'amount_received' => $intent->amount_received,
                'currency' => $intent->currency,
            ];
        } catch (\Exception $e) {
            throw new \Exception("Intent Status Error: " . $e->getMessage());
        }
    }
}
