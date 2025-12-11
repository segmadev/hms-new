<?php 
$script[] = "fetcher";
$script[] = "textarea";
$script[] = "sweetalert";
?>
<div class="card">
    <div class="card-header">
        <h3>About <?= company_name ?></h3>
    </div>
    <div class="card-body">
        <form action="" id="foo">
        <?= $c->create_form($about); ?>
        <input type="hidden" name="updatesettings" value="" id="">
                <input type="hidden" name="page" value="settings" id="">
                <input type="hidden" name="settings" value="about">
        <input type="submit" value="Update details" class="btn btn-primary">
        </form>
    </div>
<hr>
</div>
<div class="card card-body">
        <h3>Manage Gallary</h3>
        <div><a href="gallery/new/about?ref=about_company" class="btn btn-sm btn-outline-primary">Add Gallery</a></div>
        <hr>
        <div 
            class="row" 
            data-load="gallery" 
            data-page="gallery" 
            data-limit="100" 
            data-path="passer?p=gallery&get=gallery&ref=about_company&forID=about" 
            data-displayId="loadaccount" id="loadaccount"></div>
    </div>