<?php 
    // $request = $d->api_call("https://cruisetechlogs.com/app/api/account/buy?accountID=66f3f8841ad8a", header: ["Authorization: Bearer 02aca5cb3282d68f1e29b9d778c08b35326ba1fe9747b1a819500c"], method: "POST", posts: ["choices"=>"69113"]);
    // $request = $d->api_call("https://cruisetechlogs.com/app/api/user/order?id=order-67839abba05f5", header: ["Authorization: Bearer 02aca5cb3282d68f1e29b9d778c08b35326ba1fe9747b1a819500c"]);
    $request = $d->api_call("https://cruisetechlogs.com/app/api/account/fetch/logins?ID=68801", header: ["Authorization: Bearer 02aca5cb3282d68f1e29b9d778c08b35326ba1fe9747b1a819500c"]);
    echo '<pre>';
        var_dump($request);
    echo '</pre>';
?>