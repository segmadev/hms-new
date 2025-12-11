<div class="row">
    <div class="col-lg-6 col-12">
        <div class="card">
            <div class="border-bottom title-part-padding card-header">
                <h4 class="card-title mb-0">Edit: <?= $shipping_method['title'] ?></h4>
            </div>
            <div class="card-body">
                <div id="custommessage"></div> <!-- Placeholder for success/error messages -->
                <form id="foo" method="POST" action="passer.php">
                   <div class="row">
                   <?= $c->create_form($s->form) ?>
                   <input type="hidden" name="edit_shipping" value="">
                   <input type="hidden" name="page" value="shipping_methods">
                   </div>
                    <button type="submit" class="btn btn-primary">Update Shipping Method</button>
                    <a href="shipping_methods/" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>