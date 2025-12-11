<?php 
    require_once ROOT."/functions/stripe.php";
    class payment extends database {
        protected $stripe;
        protected $base_url;
        function __construct() {
            parent::__construct();
            $this->stripe = new StripeService(
                $this->get_settings("stripe_private"),
                $this->get_settings("default_currency")
            );
            $this->base_url = $this->get_settings("website_url");
        }
        function iniPayment(array | string $order) {
            if(!is_array($order)) $order = $this->getall("orders", "ID = ?", [$order]);
            if(!isset($order['ID'])) return false;
            
            // Get product and user information
            if(!isset($order['product'])) $order['product'] = $this->getall("products", "ID = ?", [$order['productID']]);
            if(!isset($order['user'])) $order['user'] = $this->getall("users", "ID = ?", [$order['userID']]);
            $payID = 'PAY-' . uniqid();
            // Initialize Stripe checkout session
            $iniCheckout = $this->stripe->createCheckoutSession($order, 
                $this->base_url."/pay/verify?isSuccess=yes&id=$payID",
                $this->base_url."/pay/verify?isSuccess=no&id=$payID"
            );
            
            if (!$iniCheckout) {
                return false;
            }
            
            // Prepare payment data
            $paymentData = [
                'ID' => $payID,
                'orderID' => $order['ID'],
                'userID' => $order['userID'],
                'productID' => $order['productID'],
                'sessionID' => $iniCheckout->id,
                'amount' => $iniCheckout->amount_total / 100, // Convert from cents to dollars
                'currency' => $iniCheckout->currency,
                'status' => $iniCheckout->payment_status,
                'payment_intent' => $iniCheckout->payment_intent,
                'customer_email' => $iniCheckout->customer_email,
                'checkout_url' => $iniCheckout->url,
                'metadata' => json_encode([
                    'order_id' => $iniCheckout->metadata->order_id,
                    'product_id' => $iniCheckout->metadata->product_id
                ])
            ];
            
            // Insert payment record
            if ($this->quick_insert("payments", $paymentData)) {
               return $paymentData;
            }
            
            return false;
        }
        function validatePayment($sessionId) {
            try {
                // Get payment record
                $payment = $this->getall("payments", "sessionID = ?", [$sessionId]);
                if (!$payment) {
                    return [
                        'success' => false,
                        'message' => 'Payment session not found'
                    ];
                }

                // Check payment status with Stripe
                $status = $this->stripe->checkPaymentStatusBySession($sessionId);
                
                if (!$status) {
                    return [
                        'success' => false,
                        'message' => 'Failed to verify payment status'
                    ];
                }

                // Update payment status
                $updateData = [
                    'status' => $status['status'],
                    'payment_intent' => $status['payment_intent'] ?? $payment['payment_intent'],
                ];

                // Update payment record
                $update = $this->update("payments", $updateData, "sessionID = '$sessionId'");
                if (!$update) {
                    return [
                        'success' => false,
                        'message' => 'Error Updating the status'
                    ];
                }
                // Update order status based on payment status
                // $orderStatus = $this->getOrderStatusFromPaymentStatus($status['status']);
                // $this->update("orders", [
                //     "status" => $orderStatus
                // ], "ID = ?", [$payment['orderID']]);

                $pay = $this->getall("payments", "sessionID = ?", [$sessionId]);
                if(!is_array($pay)) {
                    return [
                        'success' => false,
                        'message' => 'Error getting your payment'
                    ];
                }
                unset($pay['sessionID']);
                return [
                    'success' => true,
                    'payment' => $pay,
                    'status'=>$status,
                    // 'order' => $this->getall("orders", "ID = ?", [$payment['orderID']])
                ];

            } catch (\Exception $e) {
                error_log($e);
                return [
                    'success' => false,
                    'message' => "Int Error."
                ];
            }
        }

        
        
        private function getOrderStatusFromPaymentStatus($paymentStatus) {
            switch ($paymentStatus) {
                case 'succeeded':
                    return 'paid';
                case 'processing':
                    return 'processing_payment';
                case 'requires_payment_method':
                case 'requires_confirmation':
                case 'requires_action':
                case 'requires_capture':
                    return 'pending_payment';
                case 'canceled':
                    return 'payment_cancelled';
                default:
                    return 'payment_failed';
            }
        }
        function getPaymentStatus($sessionId) {
            try {
                $payment = $this->getall("payments", "sessionID = ?", [$sessionId]);
                if (!$payment) {
                    return [
                        'success' => false,
                        'message' => 'Payment session not found'
                    ];
                }

                return [
                    'success' => true,
                    'payment' => $payment,
                    'order' => $this->getall("orders", "ID = ?", [$payment['orderID']])
                ];

            } catch (\Exception $e) {
                return [
                    'success' => false,
                    'message' => $e->getMessage()
                ];
            }
        }

         function getPaymentDetails($paymentID, $userID = null, $select = "*") {

            $where = "ID = ? ";
            $data = [$paymentID];
            if($userID !=  null) {
                $where .= "and userID  = ?";
                $data[] = $userID;
            }
            return  $this->getall("payments", $where, $data, $select);
         }

        function getallpayments($start, $limit, $userID = null, $orderID = null, $status = null, $select = "*") {
            $limit = ($limit > 50) ? 50 : $limit;
            $query = "ID != ?";
            $params = [""];
            
            if ($start == null || !is_numeric($start)) {
                $start = 0;
            }

            if($userID != null) {
                $query .= "AND userID = ? ";
                $params[] = $userID;
            }
            if($orderID != null) {
                $query .= " AND  orderID = ? ";
                $params[] = $orderID;
            }
           
    
            if ($status != null) {
                $query .= " AND status = ?";
                $params[] = $status;
            }
    
            $query .= " ORDER BY created_at DESC LIMIT $start, $limit";
            return $this->getall("payments", $query, $params, select: $select, fetch: "all");
        }
    }