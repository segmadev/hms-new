<?php 
    if(isset($_POST['new_shipping'])) {
        echo $s->newShipping_method();
    }

    if(isset($_POST['edit_shipping'])) {
        echo $s->editShipping_method();
    }