<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <?php echo $site["name"] ?>
    </h1>
    <div class="justify-content-end">
        <button class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm" id="btn-edit-site" data-site-id="<?php echo $site["id"] ?>">
            <i class="fas fa-edit fa-sm text-white-50"></i> Edit Site
        </button>
        <button class="d-none d-sm-inline-block btn btn-sm btn-danger shadow-sm" id="btn-delete-site" data-site-id="<?php echo $site["id"] ?>">
            <i class="fas fa-trash fa-sm text-white-50"></i> Delete Site
        </button>
    </div>
</div>

<div id="site-detail" data-site-id="<?php echo $site["id"] ?>">
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div id="map" style="width: 100%; height: 15rem" data-lat="<?php echo $site["lat"] ?>" data-lon="<?php echo $site["lon"] ?>">
                            </div>
                        </div>
                        <div class="col-md-8">
                            <p class="card-text">
                                <?php echo $site["description"] ?>
                            </p>
                            <table class="table table-borderless">
                                <tbody>
                                    <tr>
                                        <th scope="row" class="col-md-2">Lat :</th>
                                        <td class="col-md-10"><?php echo $site["lat"] ?></td>

                                    </tr>
                                    <tr>
                                        <th scope="row" class="col-md-2">Lon :</th>
                                        <td class="col-md-10"><?php echo $site["lon"] ?></td>

                                    </tr>
                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">Sensors</div>
                <div class="card-body">
                    <div class="row">
                        <table id="table-sensors" class="table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Type</th>
                                    <th>Description</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>