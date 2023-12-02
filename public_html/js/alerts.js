$(document).ready(function() {
   
    if( $("#table-alerts").length ){
        getAlerts();
    }

});

function getAlerts(){
    let site_id = $("#site").val();

    $.getJSON( "/api/alerts?site_id="+site_id, function( data ) {
        
        const Item = (alert) => `<tr>
                             <td>${alert.measure_timestamp}</td>
                            <td>${alert.site_name}</td>
                            <td>${alert.sensor_name}</td>
                            <td>${alert.alertrule_name}</td>
                            <td>${alert.alertrule_field_name} ${alert.alertrule_field_operator} ${alert.alertrule_field_threshold}</td>
                            <td>${alert.measure_value}</td>
                            <td><button disabled class='btn btn-${alert.alertrule_level} btn-sm btn-circle'><i class='fas fa-exclamation-triangle'></i></button></td>
                           
                        </tr>`;

        $('#table-alerts tbody').html(data.map(Item).join(''));
        myDataTable = $('#table-alerts').DataTable({
            //dom: 'Bfrtip',
            dom: "<'row'<'col-sm-12 col-md-6'B><'col-sm-12 col-md-6'f>>" +
                "<'row'<'col-sm-12'rt>>" +
                "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            buttons: [
                'csv', 'excel'
            ]
        });
    });
}
