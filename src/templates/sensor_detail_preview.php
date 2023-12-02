<input type="hidden" value="<?php echo $sensor["id"] ?>" id="sensor_id">
<input type="hidden" value="<?php echo $sensor["sensor_number_values"] ?>" id="sensor_number_values">
<input type="hidden" value="<?php echo $sensor["sensor_um_label"] ?>" id="sensor_um_label">
<input type="hidden" value="<?php echo $sensor["sensor_um"] ?>" id="sensor_um">
<input type="hidden" value="<?php echo $sensor["sensor_label_m1"] ?>" id="sensor_label_m1">
<input type="hidden" value="<?php echo $sensor["sensor_label_m2"] ?>" id="sensor_label_m2">
<input type="hidden" value="<?php echo $sensor["sensor_label_m3"] ?>" id="sensor_label_m3">



<section id="tabs" class="section-tab">

    <div class="row">
        <div class="col-md-12">

            <table class="table table-hover mt-2">
                <thead>
                    <tr>
                        <th scope="col"></th>
                        <th scope="col">First measure (<?php echo $sensor["sensor_um"] ?>)</th>
                        <th scope="col">Last measure (<?php echo $sensor["sensor_um"] ?>)</th>
                        <th scope="col"></th>
                    </tr>
                </thead>
                <tbody>

                    <tr>
                        <th scope="row"><?php echo $sensor["sensor_label_m1"] ?></th>
                        <td><?php echo $sensor["m1"] ?></td>
                        <td><?php echo $sensor["m1_last"] ?></td>
                        <td>
                            <?php
                            $verified = false;
                            foreach ($rules_verified as $rule) {
                                if ($rule["field_name"] == "m1") {
                                    $verified = true;
                                    echo "<button disabled class='btn btn-" . $rule["alertrule_level"] . " btn-circle' data-trigger='hover' data-toggle='popover' data-title='Warning' data-content='" . $rule["alertrule_name"] . "'><i class='fas fa-exclamation-triangle'></i></button>";
                                }
                            }
                            if ($verified == false) {
                                echo "<button disabled class='btn btn-success btn-circle'><i class='fas fa-check'></i></button>";
                            }
                            ?>
                        </td>
                    </tr>
                    <?php if ($sensor["sensor_number_values"] > 1) { ?>
                        <tr>
                            <th scope="row"><?php echo $sensor["sensor_label_m2"] ?></th>
                            <td><?php echo $sensor["m2"] ?></td>
                            <td><?php echo $sensor["m2_last"] ?></td>
                            <td>
                                <?php
                                $verified = false;
                                foreach ($rules_verified as $rule) {
                                    if ($rule["field_name"] == "m2") {
                                        $verified = true;
                                        echo "<button disabled class='btn btn-".$rule["alertrule_level"]." btn-circle' data-trigger='hover' data-toggle='popover' data-title='Warning' data-content='" . $rule["alertrule_name"] . "'><i class='fas fa-exclamation-triangle'></i></button>";
                                    }
                                }
                                if ($verified == false) {
                                    echo "<button disabled class='btn btn-success btn-circle'><i class='fas fa-check'></i></button>";
                                }
                                ?>
                            </td>
                        </tr>
                    <?php } ?>
                    <?php if ($sensor["sensor_number_values"] > 2) { ?>
                        <tr>
                            <th scope="row"><?php echo $sensor["sensor_label_m3"] ?></th>
                            <td><?php echo $sensor["m3"] ?></td>
                            <td><?php echo $sensor["m3_last"] ?></td>
                            <td>
                                <?php
                                $verified = false;
                                foreach ($rules_verified as $rule) {
                                    if ($rule["field_name"] == "m3") {
                                        $verified = true;
                                        echo "<button disabled class='btn btn-".$rule["alertrule_level"]." btn-circle' data-trigger='hover' data-toggle='popover' data-title='Warning' data-content='" . $rule["alertrule_name"] . "'><i class='fas fa-exclamation-triangle'></i></button>";
                                    }
                                }
                                if ($verified == false) {
                                    echo "<button disabled class='btn btn-success btn-circle'><i class='fas fa-check'></i></button>";
                                }
                                ?>
                            </td>
                        </tr>
                    <?php } ?>


                </tbody>
            </table>

        </div>

        <div class="col-md-12">

            <!-- Form Orizzontale -->
            <form class="w-100">
                <div class="form-row">
                    <div class="form-group col-md-4">

                        <input type="date" class="form-control" id="date-from" name="date-from" placeholder="date from">

                    </div>
                    <div class="form-group col-md-4">

                        <input type="date" class="form-control" id="date-to" name="date-to" placeholder="date to">

                    </div>
                    <div class="form-group col-md-4">

                        <button type="button" id="btn-filter-sensor-data" class="btn btn-primary">Go</button>

                    </div>
                </div>

            </form>

            <div class="card shadow mb-4" style="width:100%">
                <!-- Card Header - Dropdown -->
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary"><?php echo $sensor["sensor_um_label"] ?> <span id="chartTitle"></span></h6>
                    <div>
                        <button id="btn-charts" class="btn btn-info btn-circle btn-sm active" data-toggle="collapse" data-target="#charts" aria-expanded="true" aria-controls="charts">
                            <i class="fas fa-chart-line"></i>
                        </button>
                        <button id="btn-tabular" class="btn btn-info btn-circle btn-sm" data-toggle="collapse" data-target="#tabular" aria-expanded="false" aria-controls="tabular">
                            <i class="fas fa-table"></i>
                        </button>
                    </div>

                </div>
                <!-- Card Body -->
                <div id="data-accordion" class="card-body">
                    <div id="charts" class="collapse show" aria-labelledby="btn-charts" data-parent="#data-accordion">
                        <canvas id="chart-m1"></canvas>
                        <?php if ($sensor["sensor_number_values"] > 1) { ?>
                            <canvas id="chart-m2"></canvas>
                        <?php } ?>
                        <?php if ($sensor["sensor_number_values"] > 2) { ?>
                            <canvas id="chart-m3"></canvas>
                        <?php } ?>


                    </div>
                    <div id="tabular" class="collapse" aria-labelledby="btn-tabular" data-parent="#data-accordion">
                        <table id="table-measurements" class="table">
                            <thead>
                                <tr>
                                    <th scope="col">Timestamp</th>

                                    <th scope="col"><?php echo $sensor["sensor_label_m1"] ?> (<?php echo $sensor["sensor_um"] ?>)</th>
                                    <?php if ($sensor["sensor_number_values"] > 1) { ?>
                                        <th scope="col"><?php echo $sensor["sensor_label_m2"] ?> (<?php echo $sensor["sensor_um"] ?>)</th>
                                    <?php } ?>
                                    <?php if ($sensor["sensor_number_values"] > 2) { ?>
                                        <th scope="col"><?php echo $sensor["sensor_label_m3"] ?> (<?php echo $sensor["sensor_um"] ?>)</th>
                                    <?php } ?>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>

                    </div>


                </div>
            </div>
        </div>
    </div>

    </div>



</section>