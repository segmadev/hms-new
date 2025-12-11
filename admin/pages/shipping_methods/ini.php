<?php
    if(isset($_GET['ID'])) {
        $id = htmlspecialchars($_GET['ID']);
        $shipping_method = $d->getall("shipping_methods", "ID = ?", [$id]);
        if(!is_array($shipping_method)) die("Shipping method not found");
        $s->form['input_data'] = $shipping_method;
    }

    if($page == ("home" || "list")) {
        $shipping_methods = $d->getall("shipping_methods", "ID != ? order by date desc", [""], fetch: "all");
    }