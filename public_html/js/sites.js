
$(document).ready(function() {
    if( $("#site-detail").length ){
        getSiteDetails( $("#site-detail").data("site-id") );
    }
    
    $("#btn-delete-site").on("click", deleteSite);
  
});

function getSiteDetails(site_id){
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

    
    $.getJSON( "/api/sensors/site/"+site_id, function( data ) {
        //console.log(data);

        const Item = (sensor) => `        
                <tr>
                    <td>${sensor.name}</dt>
                    <td>${sensor.sensor_type}</td>
                    <td>${sensor.description}</td>
                    <td><a href="/sensors/${sensor.id}" class="btn btn-info btn-circle btn-sm"><i class='fas fa-eye'></i></a></td>
                </tr>
        `;

        $('#table-sensors tbody').html(data.map(Item).join(''));
        
    });
    
}

function deleteSite(event){
    let id = $(event.target).data("site-id");
        
    if( window.confirm("Delete site #" + id + " ?") ){
        
        $.ajax({
            url: "/api/sites/" + id,
            type: 'DELETE'
        }).done(function(data) {
            alert("Site deleted");
            window.location.href = "/dashboard";
        });
    }
    
}








