<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="border-bottom title-part-padding card-header">
                <h4 class="card-title mb-0">Roles</h4>
                <a href="roles/new"><i class="ti ti-plus"></i> Add New Role</a>
            </div>
            <div class="card-body">
                <table class="table border text-nowrap customize-table mb-0 align-middle">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Date Created</th>
                        </tr>
                        <?php foreach ($roles as $row) { ?>
                        <tr>
                            <td>
                                <a href="roles/new/<?= $row['ID'] ?>"><b><?= $row['name'] ?></b></a>

                            </td>
                            <td>
                                <p class="mb-0 fw-normal"><?= utilities::date_format($row['date']) ?? $row['date'] ?></p>
                            </td>
                        </tr>
                        <?php } ?>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>