<?php
if (isset($_GET['path'])) {
    $path = $_GET['path'];
    if (file_exists($path)) {
        echo "<img src='$path' alt='' class='w-100'>";
    }
}

if (isset($_GET['url'])) {
    $path = $_GET['url'];
    echo "
    No showing? <a target='_BLANK' href='$path' class='btn btn-sm btn-danger'>Click here to view</a> <hr>
    <iframe src='$path' alt='' class='w-100' style='height: 600px'>
    
    ";
}


?>