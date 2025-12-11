<?php $quickNavs = [
    "fund" => [
        "title" => "Fund",
        "a" => "fund",
        "icon" => 'f-20 ti ti-plus',
        "description" => "Fund your account.",
        "class"=> "dropdown-toggle",
        "atb"=>'data-bs-toggle="dropdown" href="javascript:void(0)" role="button" aria-haspopup="false" aria-expanded="false"',
        "body"=>'<div class="dropdown-menu dropdown-menu-end pc-h-dropdown">'.$c->countriesList('account/fund/').'</div>'
    ],
    "transfer" => [
        "title" => "Transfer",
        "a" => "transfer",
        "icon" => 'f-20 ti ti-arrows-left-right',
        "description" => "Transfer between currencies."

    ],
    "withdraw" => [
        "title" => "Withdraw",
        "a" => "withdraw",
        "icon" => 'f-20 ti ti-sort-ascending',
        "description" => "Withdraw to bank account.",
        "class"=> "dropdown-toggle",
        "atb"=>'data-bs-toggle="dropdown" href="javascript:void(0)" role="button" aria-haspopup="false" aria-expanded="false"',
        "body"=>'<div class="dropdown-menu dropdown-menu-end pc-h-dropdown">'.$c->countriesList('account/withdraw/').'</div>'
   
    ],
    "Transactions" => [
        "title" => "Transactions",
        "a" => "transactions",
        "icon" => 'f-20 ti ti-license',
        "description" => "See previous transactions."
    ],
];

?>
<div class="card bg-transparent d-none d-lg-block">
   <div class="card-body">
   <div class="d-flex align-items-center justify-content-between">
                                <h5 class="mb-0">Quick Actions</h5>
                            </div>
    <div class="d-flex gap-1">
    <?php if (is_array($quickNavs)) { 
         foreach ($quickNavs as $key => $nav) {
            $nav = $quickNavs[$key];
            $atb =  $nav['atb'] ?? 'href="' . $nav['a'] . '"';
            $class = $nav['class'] ?? '';
            $body = $nav['body'] ?? '';
    echo '<div class="list-group-item px-0 col-3 dropdown">
        <a '.$atb.' class="d-flex align-items-center bg-body p-3 shadow-sm rounded '.$class.'">
            <div class="flex-shrink-0">
                <div class="avtar avtar-s border"> <i class="' . $nav['icon'] . '"></i></div>
            </div>
            <div class="flex-grow-1 ms-3">
                <div class="row g-1">
                    <div class="col-12">
                        <h6 class="mb-0">' . $nav['title'] . '</h6>
                        <p class="text-muted mb-0"><small>' . $nav['description'] . '</small></p>
                    </div>
                </div>
            </div>
        </a>
        '.$body.'
    </div>';
     }} ?>
</d>
   </div>
</div>
</div>
<div class="col-11 d-flex align-items-center justify-content-between gap-2 m-0  mx-auto d-lg-none">
    <?php
    if (is_array($quickNavs)) {
        foreach ($quickNavs as $key => $nav) {
            $nav = $quickNavs[$key];
            $atb =  $nav['atb'] ?? 'href="' . $nav['a'] . '"';
            echo '<a '.$atb.' class="text-center align-items-center '.$class.'">
                   <i class="avtar mb-2  border-primary rounded-circle shadow ' . $nav['icon'] . '"></i>
                   <small class="text-dark mt-2">' . $nav['title'] . '</small>
                </a>'

            ;

        }
    }
    ?>

</div>