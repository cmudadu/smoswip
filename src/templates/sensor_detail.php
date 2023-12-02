<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800"><?php echo $sensor["name"] ?></h1>
    <div class="justify-content-end">
        <button class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm" id="btn-add-sensor" data-site-id="<?php echo $site["id"] ?>" data-toggle="modal" data-target="#uploadSensorModal">
            <i class="fas fa-upload fa-sm text-white-50"></i> Upload data
        </button>

        <button class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm" id="btn-edit-sensor" data-sensor-id="<?php echo $sensor["id"] ?>" data-site-id="<?php echo $sensor["site_id"] ?>">
            <i class="fas fa-edit fa-sm text-white-50"></i> Edit Sensor
        </button>

        <button class="d-none d-sm-inline-block btn btn-sm btn-danger shadow-sm" id="btn-delete-sensor" data-sensor-id="<?php echo $sensor["id"] ?>" data-site-id="<?php echo $sensor["site_id"] ?>">
            <i class="fas fa-trash fa-sm text-white-50"></i> Delete Sensor
        </button>
    </div>
</div>
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
            <nav>
                <div class="nav nav-tabs nav-fill" id="nav-tab" role="tablist">
                    <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#nav-home" role="tab" aria-controls="nav-home" aria-selected="true">Detail</a>
                    <a class="nav-item nav-link" id="nav-measurements-tab" data-toggle="tab" href="#nav-measurements" role="tab" aria-controls="nav-measurements" aria-selected="false">Measurements</a>
                    <a class="nav-item nav-link" id="nav-alertrule-tab" data-toggle="tab" href="#nav-alertrule" role="tab" aria-controls="nav-alertrule" aria-selected="false">Alert rules</a>
                    <a class="nav-item nav-link" id="nav-datasource-tab" data-toggle="tab" href="#nav-datasource" role="tab" aria-controls="nav-datasource" aria-selected="false">Data source</a>
                </div>
            </nav>
            <div class="tab-content" id="nav-tabContent">
                <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
                    
                        <div class="row pt-4 pb-4 pl-2 pr-2">
                            <div class="col-md-4">

                                <div id="map" style="width: 100%; height: 15rem" data-lat="<?php echo $sensor["lat"] ?>" data-lon="<?php echo $sensor["lon"] ?>"></div>

                            </div>
                            <div class="col-md-8">
                                <p class="card-text"><?php echo $sensor["description"] ?></p>
                                <table class="table table-borderless">
                                    <tbody>
                                        <tr>
                                            <th scope="row" class="col-md-2">Type</th>
                                            <td class="col-md-10"><?php echo $sensor["sensor_type"] ?></td>

                                        </tr>
                                        <tr>
                                            <th scope="row">Latitude</th>
                                            <td><?php echo $sensor["lat"] ?></td>

                                        </tr>
                                        <tr>
                                            <th scope="row">Longitute</th>
                                            <td><?php echo $sensor["lon"] ?></td>

                                        </tr>
                                        <tr>
                                            <th scope="row">Altitude</th>
                                            <td><?php echo $sensor["alt"] ?></td>

                                        </tr>
                                    </tbody>
                                </table>

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
                                                        echo "<button disabled class='btn btn-".$rule["alertrule_level"]." btn-circle' data-trigger='hover' data-toggle='popover' data-title='Warning' data-content='" . $rule["alertrule_name"] . "'><i class='fas fa-exclamation-triangle'></i></button>";
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
                        </div>
                 
                </div>
                <div class="tab-pane fade" id="nav-measurements" role="tabpanel" aria-labelledby="nav-measurements-tab">
                    
                        <div class="row pt-4 pb-4 pl-2 pr-2">

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
                                        <button id="btn-charts" class="btn btn-info btn-circle btn-sm active"
                                                data-toggle="collapse" 
                                                data-target="#charts" 
                                                aria-expanded="true" 
                                                aria-controls="charts">
                                            <i class="fas fa-chart-line"></i>
                                        </button>
                                        <button id="btn-tabular" class="btn btn-info btn-circle btn-sm"
                                                data-toggle="collapse" 
                                                data-target="#tabular" 
                                                aria-expanded="false" 
                                                aria-controls="tabular">
                                            <i class="fas fa-table"></i>
                                        </button>
                                    </div>

                                </div>
                                <!-- Card Body -->
                                <div id="data-accordion" class="card-body">
                                    <div id="charts" class="collapse show" aria-labelledby="btn-charts"  data-parent="#data-accordion">
                                        <canvas id="chart-m1"></canvas>
                                        <?php if ($sensor["sensor_number_values"] > 1) { ?>
                                            <canvas id="chart-m2"></canvas>
                                        <?php } ?>
                                        <?php if ($sensor["sensor_number_values"] > 2) { ?>
                                            <canvas id="chart-m3"></canvas>
                                        <?php } ?>
                                        
                                        
                                    </div>
                                    <div id="tabular" class="collapse" aria-labelledby="btn-tabular" data-parent="#data-accordion" >
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
                <div class="tab-pane fade" id="nav-alertrule" role="tabpanel" aria-labelledby="nav-alertrule-tab">
                   
                        <div class="row pt-4 pb-4 pl-2 pr-2">
                            <div class="col-md-12">
                                <table id="table-alert-rules" class="table table-hover mt-2">
                                    <thead>
                                        <tr>
                                            <th scope="col">Name</th>
                                            <th scope="col">Field</th>
                                            <th scope="col">Operator</th>
                                            <th scope="col">Threshold</th>
                                            <th scope="col">Level</th>
                                            <th scope="col">Recipients</th>
                                            <th scope="col"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <th scope="row" colspan="5">Alert rule not found</th>
                                        </tr>
                                    </tbody>
                                </table>
                                <button class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm" data-toggle="modal" data-target="#addAlertRuleModal">
                                    <i class="fas fa-plus fa-sm text-white-50"></i> Add Alert Rule
                                </button>
                            </div>
                        </div>
                   
                </div>
                <div class="tab-pane fade" id="nav-datasource" role="tabpanel" aria-labelledby="nav-datasource-tab">
                  
                        <div class="row pt-4 pb-4 pl-2 pr-2">
                            <div class="col-md-12">
                                <table id="table-datasource" class="table table-hover mt-2">
                                    <thead>
                                        <tr>
                                            <th scope="col" class="col-md-2">Description</th>
                                            <th scope="col" class="col-md-2">Cron</th>
                                            <th scope="col" class="col-md-1">Enable</th>
                                            <th scope="col" class="col-md-4">Data source</th>
                                            <th scope="col" class="col-md-1">Last sync</th>
                                            <th scope="col" class="col-md-2"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <th scope="row" colspan="5">Data source not found</th>
                                        </tr>
                                    </tbody>
                                </table>
                                <button class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm" data-toggle="modal" data-target="#addDataSourceModal">
                                    <i class="fas fa-plus fa-sm text-white-50"></i> Add Data Source
                                </button>
                            </div>
                        </div>
                    
                </div>
            </div>
        </div>
    </div>

</section>


<div class="modal fade" id="uploadSensorModal" tabindex="-1" aria-labelledby="uploadSensorModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="#" method="POST">

                <div class="modal-header">
                    <h5 class="modal-title" id="uploadSensorModalLabel">Upload data</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="previews"></div>
                    <div id="myDropzone" class="dropzone"></div>
                    <ul>
                        <li>Fields:
                            <ul>
                                <li>timestamp</li>
                                <li>measure 1</li>
                                <li>measure 2</li>
                                <li>measure 3</li>
                            </ul>
                        </li>
                        <li><a href="/sample_data.csv" target="blank">Sample csv file</a></li>
                    </ul>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <!--<button type="submit" class="btn btn-primary" id="btn-upload-sensor-data">Upload</button>-->
                </div>
            </form>
        </div>
    </div>
</div>


<div class="modal fade" id="addAlertRuleModal" tabindex="-1" aria-labelledby="addAlertRuleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addAlertRuleModalLabel">Add new alert rule</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="form-save-alertrule" class="needs-validation" novalidate>
                    <input type="hidden" id="sensor_id" value="<?php echo $sensor["id"] ?>">
                    <div class="form-group">
                        <label for="name" class="col-form-label">Name *:</label>
                        <input type="text" class="form-control" id="name" required>
                    </div>
                    <div class="form-group">
                        <label for="name" class="col-form-label">Level *:</label>
                        <select class="form-control" id="level">
                                <option value="warning">Warning</option>
                                <option value="danger">Danger</option>
                            </select>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label for="field_name">Field name *</label>
                            <select class="form-control" id="field_name">
                                <option value="m1"><?php echo $sensor["sensor_label_m1"] ?></option>
                                <?php if ($sensor["sensor_number_values"] > 1) { ?>
                                <option value="m2"><?php echo $sensor["sensor_label_m2"] ?></option>
                                <?php } ?>
                                <?php if ($sensor["sensor_number_values"] > 2) { ?>
                                <option value="m3"><?php echo $sensor["sensor_label_m3"] ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="field_operator">Field operator *</label>
                            <select class="form-control" id="field_operator">
                                <option value="1">&lt;</option>
                                <option value="2">&lt;=</option>
                                <option value="3">=</option>
                                <option value="4">&gt;=</option>
                                <option value="5">&gt;</option>
                            </select>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="field_threshold">Field threshold *</label>
                            <input type="number" class="form-control" id="field_threshold" required>
                        </div>
                    </div>                    
                    <h5>Recipients</h5>
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label for="recipient_email">Email *</label>
                        </div>
                        <div class="form-group col-md-8">
                            <input type="email" class="form-control" id="recipient_email" required placeholder="email address" value="<?php echo $user_email ?>" required>
                    
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label for="recipient_telegram">Telegram</label>
                        </div>
                        <div class="form-group col-md-8">
                            <input type="text" class="form-control" id="recipient_telegram" placeholder="telegram chat id">
                            <span class="small">TELEGRAM CHAT_ID example -1233 (add @smoswip_bot as administrator to your channel)</span>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label for="recipient_sms">SMS</label>
                        </div>
                        <div class="form-group col-md-8">
                            <input type="text" class="form-control" id="recipient_sms" placeholder="cellular number" readonly>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="btn-save-alertrule">Save</button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="addDataSourceModal" tabindex="-1" aria-labelledby="addDataSourceModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addDataSourceModalLabel">Add Data Source</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="form-save-datasource" class="needs-validation" novalidate>
                    <input type="hidden" id="sensor_id" value="<?php echo $sensor["id"] ?>">
                    
                    
                    <div class="form-row">
                        <div class="form-group col-md-6">
                        <label for="datasource_type" class="col-form-label">Type *:</label>
                        </div>
                        <div class="form-group col-md-6">
                        <select class="form-control form-control-sm" id="datasource_type">
                                <option value="HTTP">HTTP</option>
                                <option value="FTP" disabled>FTP</option>
                                <option value="MQTT" disabled>MQTT</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                        <label for="datasource_description" class="col-form-label">Description *:</label>
                        </div>
                        <div class="form-group col-md-6">
                            <input type="text" class="form-control form-control-sm" id="datasource_description" required>                   
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group col-md-6">
                        <label for="datasource_cron" class="col-form-label">CRON *:</label>
                        </div>
                        <div class="form-group col-md-6">
                        <input type="text" class="form-control form-control-sm" id="datasource_cron" required >
                        <span class="small">example every 5 minutes: */5 * * * *</span>                    
                        </div>
                    </div>
                   
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="datasource_enable" value="false">
                        <label class="custom-control-label" for="datasource_enable">Enable</label>
                    </div>
                    <hr>
                    <!-- dynamic fields -->
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="datasource_url">URL *</label>
                        </div>
                        <div class="form-group col-md-6">
                            <input type="url" class="form-control form-control-sm" id="datasource_url" required>                    
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="datasource_format">Format *</label>
                        </div>
                        <div class="form-group col-md-6">
                            <select class="form-control form-control-sm" id="datasource_format">
                                <option value="csv">CSV</option>
                                <option value="json" disabled>JSON</option>
                                <option value="xml" disabled>XML</option>
                            </select>               
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="datasource_delimiter">Delimiter *</label>
                        </div>
                        <div class="form-group col-md-6">
                            <input type="text" maxlength="1" class="form-control form-control-sm" id="datasource_delimiter" value="," required>                    
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="datasource_indexTimestamp">Column index timestamp *</label>
                        </div>
                        <div class="form-group col-md-6">
                            <input type="number" class="form-control form-control-sm" id="datasource_indexTimestamp" required>                    
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="datasource_indexM1">Column index <?php echo $sensor["sensor_label_m1"] ?> *</label>
                        </div>
                        <div class="form-group col-md-6">
                            <input type="number" class="form-control form-control-sm" id="datasource_indexM1" required>                    
                        </div>
                    </div>
                    <?php if ($sensor["sensor_number_values"] > 1) { ?>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="datasource_indexM2">Column index <?php echo $sensor["sensor_label_m2"] ?> *</label>
                            </div>
                            <div class="form-group col-md-6">
                                <input type="number" class="form-control form-control-sm" id="datasource_indexM2">                    
                            </div>
                        </div>
                    <?php } ?>
                    <?php if ($sensor["sensor_number_values"] > 2) { ?>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="datasource_indexM3">Column index <?php echo $sensor["sensor_label_m3"] ?> *</label>
                            </div>
                            <div class="form-group col-md-6">
                                <input type="number" class="form-control form-control-sm" id="datasource_indexM3">                    
                            </div>
                        </div>
                    <?php } ?>
                    
                    
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="datasource_header" value="false">
                        <label class="custom-control-label" for="datasource_header">Has Header</label>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="btn-save-datasource">Save</button>
            </div>
        </div>
    </div>
</div>