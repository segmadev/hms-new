<?php 
    // Initialize the router
    $router = new Router();
    // Register routes
    $router->register("/content/tcp", "FrontContent@getTCandPolicy");
    $router->register("/content/common", "FrontContent@commonDetails");
    $router->register("/product/:ID", "products@fetchProducts");
    $router->register(["/shipping/methods/:ID", "/shipping/methods"], "shipping@getShippingMethods");
    $router->register(["/shipping/edit/:ID", "/shipping/add"], "shipping@saveShipping", ["POST", "PUT"]);
    $router->register("/shipping/delete/:ID", "shipping@deleteShipping");
    $router->register("/shipping/get/:ID", "shipping@getUserShipping");
    $router->register("/shipping/get", "shipping@fetchShippingForUser");
    $router->register("/shipping/get", "shipping@fetchShippingForUser");
    $router->register("/orders/get", "user@fetchOrders", "GET");
    $router->register("/order/:ID", "user@fetchOrder", "GET");
    $router->register("/order", "user@createOrder", "POST");
    $router->register("/freshorder/create", "user@createOrder", "POST");
    $router->register("/auth/register", "auth@register", "POST");
    $router->register("/auth/login", "auth@signin", "POST");
    $router->register("/auth/forgetpassword", "auth@forget_password", ["OPTIONS", "POST"]);
    $router->register("/auth/resetpassword", "auth@resetpassword", ["OPTIONS", "POST"]);
    $router->register("/auth/logout", "user@logout", "GET");
    $router->register("/user/get", "user@get_user");
    $router->register("/user/edit", "user@update_account", "POST");
    $router->register("/user/changepassword", "user@changePassword", "POST");
    
    $router->register("/payment/validate/:ID", "user@validateUserPayment", "GET");
    $router->register("/payments/get", "user@getpayment", "GET");
    $router->register("/payment/:paymentID", "user@getpayment", "GET");
    $router->register("/payment/status/:paymentID", "user@validateUserPayment", "GET");
    $router->register("/404", "auth@page404");
    $router->register("/", "auth@page404");
    // Dispatch the current request
    $router->dispatch(PATH);