$(document).ready(function () {
   
  
    $("#table-sensors").DataTable({
        bAutoWidth: false,
        paging: false,
        aoColumns: [
            { sWidth: '20%' },
            { sWidth: '20%' },
            { sWidth: '20%' },
            { sWidth: '20%' },
            { sWidth: '10%' },
            { sWidth: '5%' },
            { sWidth: '5%' }
        ],
        "columnDefs": [
            { "orderable": false, "targets": 3 },
            { "orderable": false, "targets": 4 },
            { "orderable": false, "targets": 5 },
            { "orderable": false, "targets": 6 }
        ],
        dom: "<'row'<'col-sm-12 col-md-6'B><'col-sm-12 col-md-6'f>>" +
            "<'row'<'col-sm-12'rt>>" +
            "<'row'<'col-sm-12 col-md-5'><'col-sm-12 col-md-7'p>>",
        buttons: [
            'csv', 'excel'
        ]
    });

    $(".btn-sensor-preview").on("click", previewSensor);
    
    loadSensorsChart();
});


function loadSensorsChart() {
    $('.card-sensor').each(function (i, obj) {
        getSensorMeasurementsTrends($(obj).data("sensor-id"), $(obj).data("num-values"));
    });
}
function getSensorMeasurementsTrends(sensor_id, num_values) {
    let myChart1;
    let myChart2;
    let myChart3;
    let url = "/api/sensors/" + sensor_id + "/measurementstrend";

    $.getJSON(url, function (data) {

        myChart1 = getReferenceChart('chart-m1-' + sensor_id);
        myChart1.data.labels = data.map(row => row.timestamp);
        myChart1.data.datasets.push({
            data: data.map(row => row.m1),
            barThickness: 6,
            maxBarThickness: 6,
            categoryPercentage: 0.5,
            barPercentage: 1
        });
        myChart1.update();

        if (num_values > 1) {
           
            myChart2 = getReferenceChart('chart-m2-' + sensor_id);
            myChart2.data.labels = data.map(row => row.timestamp);
            myChart2.data.datasets.push({
                data: data.map(row => row.m2),
                barThickness: 6,
                maxBarThickness: 6,
                categoryPercentage: 0.5,
                barPercentage: 1
            });
            myChart2.update();
        }


        if (num_values > 2) {
          
            myChart3 = getReferenceChart('chart-m3-' + sensor_id);
            myChart3.data.labels = data.map(row => row.timestamp);
            myChart3.data.datasets.push({
                data: data.map(row => row.m3),
                barThickness: 6,
                maxBarThickness: 6,
                categoryPercentage: 0.5,
                barPercentage: 1
            });
            myChart3.update();
        }



    });
}

function getReferenceChart(chart_id) {
    let options = {
        plugins: {
            legend: {
                display: false
            }
        },
        layout: {
            padding: 0
        },
        scales: {
            y: {
                beginAtZero: true,
                display: false
            },
            x: {
                beginAtZero: true,
                display: false,
            }
        }
    };

    var chartInstance = Chart.instances[chart_id];

    if (chartInstance) {
        // Distruggi il grafico
        chartInstance.destroy();
    
        // Rimuovi il canvas dalla lista di CanvasChart
        Chart.helpers.unbindCanvas(chartInstance);
    
        // Rimuovi il listener di ridimensionamento, se presente
        Chart.helpers.removeResizeListener(chartInstance.canvas);
    }

    return new Chart(
        document.getElementById(chart_id),
        {
            type: 'bar',
            options: options
        }
    );
}

function previewSensor(event){
    event.preventDefault();
    let href = $(this).attr("href");
    $($(this).data("target")+' .modal-title').text("Sensor overview: " + $(this).data("sensor-name"));
    $($(this).data("target") + ' .modal-body').load(href);     
}