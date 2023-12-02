<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Overview</h1>
    
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <table class="table" id="table-sensors">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Type</th>
                            <th>Timestamp</th>
                            <th>Measure</th>
                            <th>Trend</th>
                            <th>Alert</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($sensors as $sensor) { ?>
                            <tr>
                                <td><?php echo $sensor["sensor_name"] ?></th>
                                <td><?php echo $sensor["sensor_type"] ?></td>
                                <td><?php echo $sensor["measurement_timestamp_last"] ?></td>
                                <td>
                                    <p class="font-weight-bold"><?php echo $sensor["sensor_label_m1"] ?> <?php echo rtrim($sensor["m1_last"], '.0') ?> <?php echo $sensor["sensor_um"] ?></p>
                                    <?php if ($sensor["sensor_number_values"] > 1) { ?>
                                        <p class="font-weight-bold"><?php echo $sensor["sensor_label_m2"] ?> <?php echo rtrim($sensor["m2_last"], '.0') ?> <?php echo $sensor["sensor_um"] ?></p>
                                    <?php } ?>
                                    <?php if ($sensor["sensor_number_values"] > 2) { ?>
                                        <p class="font-weight-bold"><?php echo $sensor["sensor_label_m3"] ?> <?php echo rtrim($sensor["m3_last"], '.0') ?> <?php echo $sensor["sensor_um"] ?></p>
                                    <?php } ?>
                                </td>
                                <td>
                                    <div class="chart-container" style="position: relative; height:35px">
                                        <canvas id="chart-m1-<?php echo $sensor["sensor_id"] ?>" class="card-sensor" data-sensor-id="<?php echo $sensor["sensor_id"] ?>" data-num-values="<?php echo $sensor["sensor_number_values"] ?>"></canvas>
                                        <?php if ($sensor["sensor_number_values"] > 1) { ?>
                                            <canvas id="chart-m2-<?php echo $sensor["sensor_id"] ?>" class="card-sensor" data-sensor-id="<?php echo $sensor["sensor_id"] ?>" data-num-values="<?php echo $sensor["sensor_number_values"] ?>"></canvas>

                                        <?php } ?>
                                        <?php if ($sensor["sensor_number_values"] > 2) { ?>
                                            <canvas id="chart-m3-<?php echo $sensor["sensor_id"] ?>" class="card-sensor" data-sensor-id="<?php echo $sensor["sensor_id"] ?>" data-num-values="<?php echo $sensor["sensor_number_values"] ?>"></canvas>

                                        <?php } ?>
                                    </div>
                                </td>
                                <td>
                                    <?php if (isset($sensor["alertrule_name"])) { ?>
                                        <button disabled class='btn btn-<?php echo $sensor["alert_level"] ?> btn-sm btn-circle'><i class='fas fa-exclamation-triangle'></i></button>

                                    <?php } else { ?>
                                        <button disabled class='btn btn-success btn-sm btn-circle'><i class='fas fa-check'></i></button>

                                    <?php } ?>
                                </td>
                                <td>
                                    <a href="/sensors/<?php echo $sensor["sensor_id"] ?>/preview" 
                                        data-toggle="modal" data-target="#previewSensorModal"
                                        data-sensor-name="<?php echo $sensor["sensor_name"] ?>"
                                        class='btn btn-info btn-sm btn-circle btn-sensor-preview' >
                                        <i class='fas fa-eye'></i>
                                    </a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<!-- Preview sensor Modal -->
<div class="modal modal-fullscreen fade" id="previewSensorModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Sensor overview</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <iframe height="100%" width="100%"></iframe>
            </div>
        </div>
    </div>
</div>