<?php
require_once ROOT."functions/formhandler.php";
require_once ROOT."functions/utilities.php";
require_once ROOT."api/controllers/payment.php";
require_once ROOT."api/controllers/products.php";
class user extends database
{
    public $userID;
    public $userData;
    // formHandler object
    public $formHandler;
    public $user_form = [];
    public $order_form = [];
    public $payment;
    public $token;
    protected $product;
    // Constructor for ApiUser
    public function __construct()
    {
        parent::__construct(); // Call parent constructor to initialize database connection
        // Call parent constructor to set the name
        // Additional logic specific to ApiUser
        // create $this->user_form
        // formhandler
        $this->formHandler = new formHandler();
        $this->user_form =  [
            "ID"=>["is_required"=>false],
            "first_name"=>[],
            "last_name"=>[],
            "email"=>[],
            "phone_number"=>[],
        ];

        $this->order_form = [
            "ID" => ["is_required" => false],
            "userID" => ["is_required" => false],
            "productID" => [],
            "quantity" => [],
            "size_index" => [],
            "shipping_address" => [],
            "shipping_method" => [],
        ];
        $this->payment = new payment();
        $this->product = new products();
        // $token = utilities::getBearerToken();
    }

    // write user_data

    function set_user() {
        $utilities = new utilities();
        // get getBearerToken
       $token = $utilities->getBearerToken();
       $this->token = $token;
        if ($token == null || empty($token)) {
            die($utilities->apiMessage("No token passed", 401));
        }
        $user_log = $this->get_log($token);
        if (!is_array($user_log) || !isset($user_log['userID'])) {
            die($utilities->apiMessage("Invaild API Token or expired", 401));
        }
        $this->userID = $user_log['userID'];
        $this->userData = $this->user_data($this->userID);
        if (count($this->userData) == 0) {
            die($utilities->apiMessage("Invaild API Token or account not active", 401));
        }
    }

    function get_user() {
        // set user
        $this->set_user();
        // get user data
        unset($this->userData['password'], $this->userData['status'], $this->userData['reason'], $this->userData['ip_address'], $this->userData['token']);
        return utilities::apiMessage("User fetched", 200, $this->userData);

    }
    // logout
    function logout() {
        $this->set_user();
        $this->delete("user_logs", "userID = ? and token = ?", [$this->userID, $this->userData['token']]);
        return utilities::apiMessage("Logged out successfully", 200);
    }
    // get user data
    function user_data($userID = null)
    {
        if ($userID == null) {
            $userID = $this->userID;
        }
        $user = $this->getall("users", "ID = ?", [$userID]);
        if (!is_array($user)) die(utilities::apiMessage("User not found", 400));    
        return $user;
    }
    function get_order($isApi = "no", $id = null)
    {
        if ($id == null && !isset($_GET['id'])) {
            die($this->apiMessage("No ID passed", 401));
        }
        $id = $id ?? htmlspecialchars($_GET['id'] ?? $_GET['ID']);
        $order = $this->getall("orders", "ID = ? and userID = ?", [$id, $this->userID]);
        if (!is_array($order)) die($this->apiMessage("Order not found", 400));
        if ($isApi == "no") return $order;
        return $this->apiMessage("Order fetched", 200, $this->cleanOrder($order));
    } 

  
    function get_log($token)
    {
        $api_data = $this->getall("user_logs", "token = ? and expiry_date >= ? and status = ?", [$token, time(), 1]);
        return $api_data; 
    }
    function getBalance()
    {
        die($this->apiMessage("Balance fetched", 200, ["balance" => round($this->userData['balance'], 3), "total_credit" => $this->userData['total_credit'], "currency" => "NGN"]));
    }

    // create a user account
    function createAccount($user = null)
    {
        if ($user == null) {
            $user = $this->formHandler->validateForm($this->user_form, "users");
            if (!is_array($user)) return null;
        }

        // Validate base64 password if provided
        if (isset($user['password']) && !empty($user['password'])) {
            $passwordRaw = trim($user['password']);
            if (!base64_decode($passwordRaw, true)) {
                return $this->apiMessage("Invalid password format", 400);
            }
            $user['password'] = base64_decode($passwordRaw);
            
            // Validate password length
            if (strlen($user['password']) < 8) {
                return $this->apiMessage("Password must be at least 8 characters long", 400);
            }
            
            // Hash the password
            $user['password'] = password_hash($user['password'], PASSWORD_DEFAULT);
        }

        if ($this->getall("users", "email = ?", [$user['email']], fetch: "") > 0) {
            return $this->apiMessage("Email already exists", 400);
        }
        if ($this->getall("users", "phone_number = ?", [$user['phone_number']], fetch: "") > 0) {
            return $this->apiMessage("Phone number already exists", 400);
        }
        $user['ID'] = $this->validID($user['ID']);
        if (!$this->quick_insert("users", $user)) return $this->apiMessage("Unable to create account for your", 500);
        if (isset($user['password']) && $user['password'] != "") {
            $user['token'] =  $this->set_token($user['ID']);
        }
        return $this->apiMessage("Account created successfully", 200, $user);
    }

    

    // write a function to update user 

    function update_account() {
        $this->set_user();
        $info = $this->formHandler->validateForm($this->user_form);
        if (!is_array($info)) return utilities::apiMessage("Fill all required filed", 400, errors: $this->formHandler->errors);
        $checkUser = $this->getall("users", "email = ? and ID != ?", [$info['email'], $this->userID]);
        if(is_array($checkUser)) return utilities::apiMessage("Email already exits.", 401);
        unset($info['ID']);
        $userID = $this->userID;
        $update = $this->update("users", $info, "ID = '$userID'");
        if($update) return utilities::apiMessage("Account Updated", 200, $info);
        return utilities::apiMessage("Unable to Update Account.", 500);
    }

    // create new order for a user
    function createOrder($order = null)
    {
        $this->set_user();
        // return utilities::apiMessage("", 400);
        if ($order == null) {
            $order = $this->formHandler->validateForm($this->order_form, "orders");
            if (!is_array($order)) return utilities::apiMessage("Fill all required filed", 400, errors: $this->formHandler->errors);
        }
        // generate order ID
        $order['ID'] = utilities::genID("ORDER-", 6);
        if ($this->getall("orders", "ID = ?", [$order['ID']], fetch: "") > 0) {
            return utilities::apiMessage("Order already exists", 400);
        }

        $order['userID'] = $this->userID;
        // check if product exists and active
        $product = $this->getall("products", "ID = ? and status = ?", [$order['productID'], 1]);
        if (!is_array($product)) {
            return utilities::apiMessage("Product not found or not active at the moment", 404);
        }

        $shippingMethod = $this->getall("shipping_methods", "ID = ? and status = ?", [$order['shipping_method'], 1]);
        if (!is_array($shippingMethod)) {
            return utilities::apiMessage("Shipping method not found or not active at the moment", 404);
        }
        // check if shipping address is valid and active and belongs to this user
        $shippingAddress = $this->getall("shipping", "ID = ? and userID = ?", [$order['shipping_address'], $this->userID]);
        if (!is_array($shippingAddress)) {
            return utilities::apiMessage("Shipping address not found or not active at the moment", 404);
        }
        // get product size
        $product['sizes'] = json_decode($product['sizes'], true);
        $order['size_index'] = (int)$order['size_index'] - 1;
        if (!isset($product['sizes'][$order['size_index']])) {
            return utilities::apiMessage("Product size not found", 404);
        }
        $sizes = $product['sizes'][$order['size_index']];
        //    check if quantity is available for the size
        if ($sizes['quantity'] < $order['quantity']) {
            return utilities::apiMessage("Product size not available", 404);
        }
        unset($order['size_index']);
        $order = array_merge($order, [
            "size" => $sizes['size'],
            "weight" => $sizes['weight'],
            "unit_price" => $sizes['price'],
            "amount" => ($sizes['price'] * $order['quantity']),
        ]);
        // check if shipping method is valid and active
        //    create new order after 1 min of the last order created
        $lastOrder = $this->getall("orders", "userID = ? ORDER BY date DESC LIMIT 1", [$this->userID]);
        if (!empty($lastOrder) && isset($lastOrder[0]['date_time'])) {
            if (strtotime($lastOrder[0]['date_time']) > (time() - 60)) {
             return utilities::apiMessage("You can only create one order per minute", 400);
            }
        }
        if (!$this->quick_insert("orders", $order)) return utilities::apiMessage("Unable to create order for your", 500);
        $paymentOrder = $order;
        $paymentOrder['product'] = $product;
        $paymentOrder['user'] = $this->userData;
        try {
            $paymentResponse = $this->payment->iniPayment($paymentOrder);
            if(!is_array($paymentResponse)) return utilities::apiMessage("Order Created but unable to create order for your", 500, $order);
            $order['paymentID'] = $paymentResponse['ID'];
            $order['payment_url'] = $paymentResponse['checkout_url'];
            return utilities::apiMessage("Order created successfully", 200, $order);
        } catch (\Exception $e) {
            return utilities::apiMessage("Order Created but unable to create payment for your", 500, $order);
        }
    }
   
    // fetch user orders using paging add fliter by status and date
    function fetchOrders($status = null, $start = 0, $limit = 50)
    {
        $this->set_user();
        // use GET parameters to get the values
        $status = isset($_GET['status']) ? htmlspecialchars($_GET['status']) : $status;
        $start = isset($_GET['start']) ? htmlspecialchars($_GET['start']) : $start;
        $limit = isset($_GET['limit']) ? htmlspecialchars($_GET['limit']) : $limit;
        $limit = ($limit > 50) ? 50 : $limit;
        $params = [];
        $query = "";
        
        if ($start == null || !is_numeric($start)) {
            $start = 0;
        }
        $query .= " userID = ? ";
        $params[] = $this->userID;

        if ($status) {
            $query .= " AND status = ?";
            $params[] = $status;
        }

        $query .= " ORDER BY date DESC LIMIT $start, $limit";
        $orders = $this->getall("orders", $query, $params, select: "*", fetch: "all");
        if($orders == "") {
            return utilities::apiMessage("No orders found", 404);
        }
        return utilities::apiMessage("Orders fetched", 200, data: ["next"=>($orders->rowCount() >= $limit) ? $start + $limit : null, "orders"=>$orders->fetchAll(PDO::FETCH_ASSOC)]);
    }

    //  fetch single order by ID for a user
    function fetchOrder($id = null) {
        $this->set_user();
        if ($id == null && !isset($_GET['ID'])) {
            return utilities::apiMessage("No ID passed", 401);
        }
        $id = $id ?? htmlspecialchars($_GET['ID']);
        $order = $this->getall("orders", "ID = ? and userID = ?", [$id, $this->userID]);
        if (!is_array($order)) {
            return utilities::apiMessage("Order not found", 404);
        }
        return utilities::apiMessage("Order fetched", 200, data: $order);
    }

    function validateUserPayment() {
        $this->set_user(); // Ensure user is authenticated
        
        $paymentID = htmlspecialchars($_GET['paymentID']);
        if (empty($paymentID)) {
            return utilities::apiMessage("No payment ID provided", 400);
        }
    
        // Check if payment session exists and belongs to user
        $payment = $this->getall("payments", "ID = ? AND userID = ?", [$paymentID, $this->userID]);
        if (!is_array($payment)) {
            return utilities::apiMessage("Payment session not found or unauthorized", 404);
        }
    
        // Validate payment using payment controller
        $validationResult = $this->payment->validatePayment($payment['sessionID']);
        
        if (!$validationResult['success']) {
            return utilities::apiMessage($validationResult['message'], 400);
        }
    
        // die(var_dump($validationResult));
        $message = "";
        if(isset($validationResult['status']) && isset($validationResult['status']['amount_received'])) {
           if(!$this->upadate_order($payment['orderID'], $validationResult['status']['amount_received'])) {
                $message = "Unable to update order status, please contact customer support for help.";
           }
        }
        // Return success response with payment and order details
        return utilities::apiMessage(
            "Payment validated successfully. $message", 
            200, 
            $validationResult['payment']
                // 'order' => $validationResult['order']
            
        );
    }

    private function upadate_order($orderID, $amount_received) {
        $order = $this->getall("orders", "ID = ? and amount <= ?", [$orderID, $amount_received]);
        if(!is_array($order)) return false;
        if($order['status'] != "initiate") return true;
        if($this->update("orders", ["status"=>"processing"], "ID = '$orderID'")) {
            $this->product->reduceSize($order['productID'], $order['size']);
            return true;
        }
        return false;
    }

    

    function getpayment($status = null, $start = 0, $limit = 50) {
        $this->set_user();
        $select = "ID, orderID, userID, productID, amount, currency, status, payment_intent, customer_email, checkout_url, metadata, created_at, updated_at";
        if(isset($_GET['paymentID'])) {
             $payment = $this->payment->getPaymentDetails(htmlspecialchars($_GET['paymentID']), $this->userID, $select);
             if(!is_array($payment)){
                return utilities::apiMessage("No payment with ID found", 404);
             }
            return utilities::apiMessage("payment fetched", 200, data: $payment);
        }

        $status = isset($_GET['status']) ? htmlspecialchars($_GET['status']) : $status;
        $start = isset($_GET['start']) ? htmlspecialchars($_GET['start']) : $start;
        $limit = isset($_GET['limit']) ? htmlspecialchars($_GET['limit']) : $limit;
        $orderID = isset($_GET['orderID']) ? htmlspecialchars($_GET['orderID']) : null;
        $payments = $this->payment->getallpayments($start, $limit, $this->userID, $orderID, $status, $select);
        if($payments->rowCount() == 0) {
            return utilities::apiMessage("No payment found", 404);
        }
        return utilities::apiMessage("payments fetched", 200, data: ["next"=>($payments->rowCount() >= $limit) ? $start + $limit : null, "payments"=>$payments->fetchAll(PDO::FETCH_ASSOC)]);
    }

    function changePassword() {
        $this->set_user(); // Ensure user is authenticated
        
        // Check if POST data exists
        if (!isset($_POST['current_password']) || !isset($_POST['new_password']) || !isset($_POST['confirm_password'])) {
            return utilities::apiMessage("Missing required password fields", 400);
        }
        
        // Get and validate input
        $currentPasswordRaw = trim($_POST['current_password']);
        $newPasswordRaw = trim($_POST['new_password']);
        $confirmPasswordRaw = trim($_POST['confirm_password']);

        // Validate base64 encoding
        if (!base64_decode($currentPasswordRaw, true) || 
            !base64_decode($newPasswordRaw, true) || 
            !base64_decode($confirmPasswordRaw, true)) {
            return utilities::apiMessage("Invalid password format", 400);
        }

        // Decode passwords
        $currentPassword = base64_decode($currentPasswordRaw);
        $newPassword = base64_decode($newPasswordRaw);
        $confirmPassword = base64_decode($confirmPasswordRaw);

        if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
            return utilities::apiMessage("All password fields are required", 400);
        }

        // Check if new password is same as current password
        if ($currentPassword === $newPassword) {
            return utilities::apiMessage("New password must be different from current password", 400);
        }

        // Verify current password
        if (!password_verify($currentPassword, $this->userData['password'])) {
            return utilities::apiMessage("Current password is incorrect", 401);
        }

        // Validate new password
        if (strlen($newPassword) < 8) {
            return utilities::apiMessage("New password must be at least 8 characters long", 400);
        }

        if ($newPassword !== $confirmPassword) {
            return utilities::apiMessage("New passwords do not match", 400);
        }

        // Hash new password
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        // Update password in database
        $update = $this->update("users", 
            ['password' => $hashedPassword], 
            "ID = '".$this->userID."'", 
        );

        if (!$update) {
            return utilities::apiMessage("Failed to update password", 500);
        }

        // Log out all other sessions for security
        $this->delete("user_logs", "userID = ? AND token != ?", 
            [$this->userID, $this->token]
        );

        return utilities::apiMessage("Password changed successfully", 200);
    }

    // clean order data for API response
}