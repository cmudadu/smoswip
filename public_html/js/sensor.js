
Dropzone.autoDiscover = false;
var chart1 = null;
var chart2 = null;
var chart3 = null;
var myDataTable;

$(document).ready(function() {
    let sensor_id = $("#sensor_id").val();
    
    $('#btn-delete-sensor').on("click", deleteSensor);
    $('#table-datasource').on("click", '.btn-delete-datasource', deleteDataSource);
    $('#btn-filter-sensor-data').on("click", filterSensorData);
    $('#btn-save-alertrule').on('click', saveAlertRule);
    $('#btn-save-datasource').on('click', saveDataSource);
    $('#table-alert-rules').on('click', '.btn-delete-alertrule', deleteAlertRule);
    $('[data-toggle="popover"]').popover();

      if($("div#myDropzone").length){
        var myDropzone = new Dropzone("div#myDropzone", { 
            url: '/api/sensors/'+sensor_id+'/measurements/uploadCSV',
            createImageThumbnails: false,
            acceptedFiles: 'text/csv'
         });
      }

      if($("#map").length){
        getSensorDetails();
      }
    

    
    getSensorMeasurements(sensor_id, '', '');

    if($('#table-alert-rules').length){
        getAlertRulesBySensor(sensor_id);
    }
    if($('#table-datasource').length){
        getDataSourcesBySensor(sensor_id);
    }

});

function getSensorDetails(){    
    if($("#map").data("lat") !== ""){
        // Creating map options
        var mapOptions = {
            center: [$("#map").data("lat"), $("#map").data("lon")],
            zoom: 15
        }
        
        // Creating a map object
        var map = new L.map('map', mapOptions);
        var layer = new L.TileLayer('http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',{
            attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
        });
        map.addLayer(layer);

    }
}

function filterSensorData(event){
    event.preventDefault();
    let sensor_id = $("#sensor_id").val();
    let date_from = $("#date-from").val();
    let date_to = $("#date-to").val();
    //console.log(date_from, date_to)
    getSensorMeasurements(sensor_id, date_from, date_to);
    
}

function getSensorMeasurements(sensor_id, date_from, date_to){
    
    let url = "/api/sensors/"+sensor_id+"/measurements?";
    if(date_from != ""){
        url = url + "&date_from=" + date_from;
    }
    if(date_to != ""){
        url = url + "&date_to=" + date_to;
    }

    $.getJSON( url, function( data ) {
        let sensor_number_values = $("#sensor_number_values").val();
        let sensor_label_m1 = $("#sensor_label_m1").val();
        let sensor_label_m2 = $("#sensor_label_m2").val();
        let sensor_label_m3 = $("#sensor_label_m3").val();
        let myChart1 = getReferenceChart('chart-m1', $('#sensor_um').val());
        myChart1.data.labels = []; 
        myChart1.data.datasets = []; 
        myChart1.update(); 
       
        myChart1.data.labels = data.map(row => row.timestamp); 
        myChart1.data.datasets.push({ label: sensor_label_m1, data : data.map(row => row.m1), fill: false, borderColor: '#FF0000', tension: 0.1}); 
        
        myChart1.update(); 

        if(sensor_number_values > 1){
            let myChart2 = getReferenceChart('chart-m2', $('#sensor_um').val());
            myChart2.data.labels = []; 
            myChart2.data.datasets = []; 
            myChart2.update(); 
        
            myChart2.data.labels = data.map(row => row.timestamp); 
            myChart2.data.datasets.push({ label: sensor_label_m2, data : data.map(row => row.m2), fill: false, borderColor: '#00FF00', tension: 0.1 }); 
            myChart2.update(); 
        }

        if(sensor_number_values > 2){
            let myChart3 = getReferenceChart('chart-m3', $('#sensor_um').val());
            myChart3.data.labels = []; 
            myChart3.data.datasets = []; 
            myChart3.update(); 
        
            myChart3.data.labels = data.map(row => row.timestamp); 
            myChart3.data.datasets.push({ label: sensor_label_m3, data : data.map(row => row.m3), fill: false, borderColor: '#0000FF', tension: 0.1 }); 
            myChart3.update(); 
        }

        if(data.length > 0){
            
            const Item1 = (measure) => `
            <tr>
                <td>${measure.timestamp}</td>
                <td>${measure.m1}</td>
            </tr>
            `;
            const Item2 = (measure) => `
            <tr>
                <td>${measure.timestamp}</td>
                <td>${measure.m1}</td>
                <td>${measure.m2}</td>
            </tr>
            `;
            const Item3 = (measure) => `
            <tr>
                <td>${measure.timestamp}</td>
                <td>${measure.m1}</td>
                <td>${measure.m2}</td>
                <td>${measure.m3}</td>
            </tr>
            `;
            
            if($.fn.DataTable.isDataTable('#table-measurements')){
                console.log("destroy datatable")
                myDataTable.destroy();
            }
            if(sensor_number_values == 1)
                $('#table-measurements tbody').html(data.map(Item1).join(''));
            else if(sensor_number_values == 2)
                $('#table-measurements tbody').html(data.map(Item2).join(''));
            else
                $('#table-measurements tbody').html(data.map(Item3).join(''));

            
            myDataTable = $('#table-measurements').DataTable({
                //dom: 'Bfrtip',
                dom: "<'row'<'col-sm-12 col-md-6'B><'col-sm-12 col-md-6'f>>" +
                    "<'row'<'col-sm-12'rt>>" +
                    "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
                buttons: [
                    'csv', 'excel'
                ]
            });
        }

  });
}

function getReferenceChart(chart_id, labelY){
    let options = {
        scales: {
            y: {
                beginAtZero: true, // Imposta l'asse Y per iniziare da zero
                title: {
                    display: true,
                    text: labelY
                }
            },
            x: {
                type: 'time',
                time: {
                    displayFormats: {
                        quarter: 'MMM YYYY'
                    }
                }
            }
        }
    }

    var chartInstance = Chart.instances[chart_id];

    if (chartInstance) {
        // Distruggi il grafico
        chartInstance.destroy();
    
        // Rimuovi il canvas dalla lista di CanvasChart
        Chart.helpers.unbindCanvas(chartInstance);
    
        // Rimuovi il listener di ridimensionamento, se presente
        Chart.helpers.removeResizeListener(chartInstance.canvas);
    }

    if(chart_id == "chart-m1" ){
        
        chart1 = new Chart(
            document.getElementById(chart_id),
            {
                type: 'line',
                options: options
            }

        );

        return chart1;
    }else if(chart_id == "chart-m2" ){
        
        chart2 = new Chart(
            document.getElementById(chart_id),
            {
                type: 'line',
                options: options,
            }

        );

        return chart2;
    }else {
       
        chart3 = new Chart(
            document.getElementById(chart_id),
            {
                type: 'line',
                options: options,
            }

        );

        return chart3;
    }
    
}

function deleteSensor(event){
    let id = $(event.target).data("sensor-id");
    let site_id = $(event.target).data("site-id");
        
    if( window.confirm("Delete sensor #" + id + " ?") ){
        
        $.ajax({
            url: "/api/sensors/" + id,
            type: 'DELETE'
        }).done(function(data) {
            alert("Sensor deleted");            
            window.location.href = "/sites/" + site_id;
        });
        
    }
}

function getAlertRulesBySensor(sensor_id){
    $.getJSON( "/api/sensors/" + sensor_id + "/alertrules", function( data ) {
        //console.log(data);

        if(data.length > 0){
            const FieldLabel = (alertrule) =>{ switch(alertrule.field_name){
                case "m1": return alertrule.sensor_label_m1;break;
                case "m2": return alertrule.sensor_label_m2;break;
                case "m3": return alertrule.sensor_label_m3;break;
            }}
            const Operator = (op) =>{ switch(op){
                case 1: return "<";break;
                case 2: return "<=";break;
                case 3: return "=";break;
                case 4: return ">=";break;
                case 5: return ">";break;
            }}
            const Item = (alertrule) => `
            <tr>
                <td>${alertrule.name}</td>
                <td>${FieldLabel(alertrule)}</td>
                <td>${Operator(alertrule.field_operator)}</td>
                <td>${alertrule.field_threshold}</td>
                <td>${alertrule.level}</td>
                <td>${alertrule.recipients}</td>
                <td>
                    <button class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm btn-edit-alertrule" data-alertrule-id="${alertrule.id}" >
                        <i class="fas fa-edit fa-sm text-white-50"></i> Edit
                    </button>
                    <button class="d-none d-sm-inline-block btn btn-sm btn-danger shadow-sm btn-delete-alertrule" data-alertrule-id="${alertrule.id}" >
                        <i class="fas fa-trash fa-sm text-white-50"></i> Delete
                    </button>
                </td>
            </tr>
            `;
    
            $('#nav-alertrule-tab').text(`Alert rules (${data.length})`)
            $('#table-alert-rules tbody').html(data.map(Item).join(''));

        }
    });
}

function getDataSourcesBySensor(sensor_id){
    $.getJSON( "/api/sensors/" + sensor_id + "/datasources", function( data ) {
        //console.log(data);

        if(data.length > 0){
            
            const Item = (ds) => `
            <tr>
                <td>${ds.description}</td>
                <td>${ds.cron}</td>
                <td>${ds.enable}</td>
                <td><pre><code>${ds.datasource}</code></pre></td>
                <td>${ds.status} : ${ds.execution_time}</td>
                <td>
                    <button class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm btn-edit-datasource" data-datasource-id="${ds.id}" >
                        <i class="fas fa-edit fa-sm text-white-50"></i> Edit
                    </button>
                    <button class="d-none d-sm-inline-block btn btn-sm btn-danger shadow-sm btn-delete-datasource" data-datasource-id="${ds.id}" >
                        <i class="fas fa-trash fa-sm text-white-50"></i> Delete
                    </button>
                </td>
            </tr>
            `;
            
            $('#nav-datasource-tab').text(`Data sources (${data.length})`)
            $('#table-datasource tbody').html(data.map(Item).join(''));

        }
    });
}

function saveAlertRule(event){
    event.preventDefault();

    if ($('#form-save-alertrule')[0].checkValidity() === false) {
        event.stopPropagation();
    
    }else{
        let data ={
            sensor_id: $("#sensor_id").val(), 
            name : $("#name").val(),
            level : $("#level").val(),
            field_name : $("#field_name").val(),
            field_operator : $("#field_operator").val(),
            field_threshold : $("#field_threshold").val(),
            recipient_email : $("#recipient_email").val(),
            recipient_telegram : $("#recipient_telegram").val(),
        }
        
        $.post( "/api/alertrules", data, function( res ) {
            getAlertRulesBySensor($("#sensor_id").val());
            $("#name").val(""),
            $("#field_name").val(""),
            $("#field_operator").val(""),
            $("#field_threshold").val(""),
            $("#recipient").val("")
            $('#addAlertRuleModal').modal('toggle');
        });
    }
    

    $('#form-save-alertrule').addClass('was-validated');
}

function saveDataSource(event){
    event.preventDefault();

    if ($('#form-save-datasource')[0].checkValidity() === false) {
        event.stopPropagation();
    
    }else{
        let data = {
            sensor_id: $("#sensor_id").val(), 
            datasource_type : $("#datasource_type").val(),
            datasource_description : $("#datasource_description").val(),
            datasource_cron : $("#datasource_cron").val(),
            datasource_enable : $("#datasource_enable").is(":checked"),
            datasource_url : $("#datasource_url").val(),
            datasource_format : $("#datasource_format").val(),
            datasource_delimiter : $("#datasource_delimiter").val(),
            datasource_indexTimestamp : $("#datasource_indexTimestamp").val(),
            datasource_indexM1 : $("#datasource_indexM1").val(),
            datasource_indexM2 : $("#datasource_indexM2").length > 0 ? $("#datasource_indexM2").val() : null,
            datasource_indexM3 : $("#datasource_indexM3").length > 0 ? $("#datasource_indexM3").val() : null,
            datasource_header : $("#datasource_header").is(":checked"),
        }
        //console.log(data);
        $.post( "/api/datasources", data, function( res ) {
            getDataSourcesBySensor(data.sensor_id);
            $('#addDataSourceModal').modal('toggle');
        });
    }
    

    $('#form-save-datasource').addClass('was-validated');
}

function deleteAlertRule(event){
    $button = $(event.target);
    let id = $button.data("alertrule-id");
        
    if( window.confirm("Delete alert rule #" + id + " ?") ){
        
        $.ajax({
            url: "/api/alertrules/" + id,
            type: 'DELETE'
        }).done(function(data) {
            getAlertRulesBySensor($("#sensor_id").val());
        });
    }
    
}

function deleteDataSource(event){
    $button = $(event.target);
    let id = $button.data("datasource-id");
        
    if( window.confirm("Delete data source #" + id + " ?") ){
        
        $.ajax({
            url: "/api/datasources/" + id,
            type: 'DELETE'
        }).done(function(data) {
            getDataSourcesBySensor($("#sensor_id").val());
        });
    }
}