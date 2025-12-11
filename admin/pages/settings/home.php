<?php require_once "content/textarea.php";
?>
<!-- logo settings -->



<?php if ($r->validate_action(["settings" => "seo"])) { ?>

<div id="seopage" class="card col-12 p-3">
    <div class="border-bottom title-part-padding">
        <h4 class="card-title mb-0">SEO Details</h4>
    </div>
    <div class="card-body">
        <form class="mt-4" action="" id="foo" novalidate="">
            <div class="row">
                <?php
                    echo $c->create_form($settings_seo); ?>
                <input type="hidden" name="updatesettings" value="" id="">
                <input type="hidden" name="page" value="settings" id="">
                <input type="hidden" name="settings" value="seo">
            </div>
            <div id="custommessage"></div>
            <input type="submit" value="Submit" class="btn btn-primary col-3">
        </form>
    </div>
</div>
<?php } ?>

<?php if ($r->validate_action(["settings" => "settings"])) { ?>

<!-- main settings -->
<div id="settingspage" class="card col-12 p-3">
    <div class="border-bottom title-part-padding">
        <h4 class="card-title mb-0">Settings</h4>
    </div>
    <div class="card-body">
        <form class="mt-4" action="" id="foo" novalidate="">
            <div class="row">
                <?php
                    echo $c->create_form($settings_form); ?>
                <input type="hidden" name="updatesettings" value="" id="">
                <input type="hidden" name="page" value="settings" id="">
                <input type="hidden" name="settings" value="settings">
            </div>
            <div id="custommessage"></div>
            <input type="submit" value="Submit" class="btn btn-primary col-3">
        </form>
    </div>
</div>
<?php } ?>
<?php if ($r->validate_action(["settings" => "payment"])) { ?>
<!--  deposit  -->
<div id="paymentpage" class="card col-12 p-3">
    <div class="border-bottom title-part-padding">
        <h4 class="card-title mb-0">Payment Settings</h4>
        <small class="text-danger">Some sensitive details won't be shown or can be replace with something else to
            protect your infomation.</small>
    </div>
    <div class="card-body">
        <form action="" id="foo" enctype="multipart/form-data">
            <div class="row">
                <?php echo $c->create_form($settings_deposit_form); ?>
                <input type="hidden" name="updatesettings" value="" id="">
                <input type="hidden" name="page" value="settings" id="">
                <input type="hidden" name="settings" value="payment">
            </div>
            <div id="custommessage"></div>
            <input type="submit" value="Update" class="btn btn-primary  col-3">

        </form>
    </div>
</div>
<?php } ?>

<?php if ($r->validate_action(["settings" => "social_media"])) { ?>
<!-- social media links -->

<div id="socialpage" class="card col-12 p-3">
    <div class="border-bottom title-part-padding">
        <h4 class="card-title mb-0">Social Media Links</h4>
    </div>
    <div class="card-body">
        <form class="mt-4" action="" id="foo" novalidate="">
            <div class="row">
                <?php
                    echo $c->create_form($settings_social_media); ?>
                <input type="hidden" name="updatesettings" value="" id="">
                <input type="hidden" name="page" value="settings" id="">
                <input type="hidden" name="settings" value="social_media">
            </div>
            <div id="custommessage"></div>
            <input type="submit" value="Submit" class="btn btn-primary col-3">
        </form>
    </div>
</div>
<?php } ?>
<?php if ($r->validate_action(["settings" => "term_and_policy_condition"])) { ?>
<!-- term_and_policy_condition -->
<div id="termspage" class="card col-12 p-3">
    <div class="border-bottom title-part-padding">
        <h4 class="card-title mb-0">Term and condition with policy Details</h4>
    </div>
    <div class="card-body">
        <form action="" id="foo">
            <div class="row">
                <?php echo $c->create_form($term_and_policy_condition); ?>
                <input type="hidden" name="updatesettings" value="" id="">
                <input type="hidden" name="page" value="settings" id="">
                <input type="hidden" name="settings" value="term_and_policy_condition">

            </div>
            <div id="custommessage"></div>
            <input type="submit" value="Update" class="btn-primary btn  col-3">

        </form>
    </div>
</div>
<?php } ?>