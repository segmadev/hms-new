<!-- filepath: c:\dev\xampp\htdocs\dsspice\admin\pages\shipping_methods\home.php -->
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="border-bottom title-part-padding card-header">
                <h4 class="card-title mb-0">Shipping Methods</h4>
                <a href="shipping_methods/new"><i class="ti ti-plus"></i> Add New Shipping Method</a>
            </div>
            <div class="card-body responsive-table">
                <table class="table border text-nowrap customize-table mb-0 align-middle">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Description</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th>Date Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($shipping_methods as $method) { ?>
                        <tr>
                            <td><b><?=  utilities::short_text($method['title'], 20, true)  ?></b></td>
                            <td><?= utilities::short_text($method['description'], 10, true) ?></td>
                            <td><?= $method['price'] ?></td>
                            <td><?= $method['status'] == 1 ? 'Active' : 'Inactive' ?></td>
                            <td><?= utilities::date_format($method['date']) ?? $method['date'] ?></td>
                            <td>
                                <a href="shipping_methods/edit/<?= $method['ID'] ?>" class="btn btn-sm btn-primary">Edit</a>
                                <!-- <a href="shipping_methods/delete/<?= $method['ID'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this shipping method?');">Delete</a> -->
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>