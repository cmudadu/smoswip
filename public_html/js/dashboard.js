$(document).ready(function() {
 
   
    if( $(".map-dashboard").length ){
        showSitesOnMap();
    }
    
    //sse();
});
function showSitesOnMap(){
    
    $.getJSON( "/api/sites", function( data ) {
        //console.log(data);
        var sites = L.layerGroup();

        var greenIcon = L.icon({
            iconUrl: '/img/marker-icon-green.png'
        });
        var yellowIcon = L.icon({
            iconUrl: '/img/marker-icon-yellow.png'
        });
        var redIcon = L.icon({
            iconUrl: '/img/marker-icon-red.png'
        });

        if(data.length>0){
            let bounds = [];
            $.each(data, function( index, site ) {
                var myIcon = greenIcon;
                if( site.alert_level == "warning"){
                    myIcon = yellowIcon;
                }else if( site.alert_level == "danger"){
                    myIcon = redIcon;
                }
                var msgalert = site.alertrule_name != null ? `${site.alertrule_name ?? ''}<br><br>` : "";
                var popup = `<b>${site.name}</b><br><br>
                             ${msgalert}
                            <a href="/sites/${site.id}/overview">view site</a>`;
                L.marker( [site.lat, site.lon], {icon: myIcon})
                    .bindPopup(popup)
                    .addTo(sites);
                    bounds.push([site.lat, site.lon]);
                
            });
            L.latLngBounds(bounds);
            //var bounds = L.marker().getBounds();
            const osm = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
            });

            const map = L.map('map', {
                center: [data[0].lat, data[0].lon],
                zoom: 5,
                layers: [osm, sites]
            });
            console.log(sites)
            map.fitBounds(bounds);
        }
    });
}

