(function($) { $(function() { 
    'use strict';
    
    
    // Init map
    var centerMap = new google.maps.LatLng(43.666667, -79.416667); // Toronto hardcoded
    
    var propertiesMap = new google.maps.Map(document.getElementById('map'), {
        center: centerMap,
        zoom: 10,
        panControl: true,
        zoomControl: true,
        zoomControlOptions: {
            style: google.maps.ZoomControlStyle.SMALL
        }
    });	


    // Properties markers
    var markers = [];
    
    Vividcrest.properties.forEach(function(property) {
        // Create the marker
        var marker = new google.maps.Marker({
            map: propertiesMap,
            position: new google.maps.LatLng(property.latitude, property.longitude),
            clickable: true
        });
        
        markers.push(marker);


        // Handle click on the marker
        var infowindow = new google.maps.InfoWindow();

        marker.addListener('click', function() {
            var isShown = property.isShown || false;

            if (!isShown) {
                // Show infowindow                
                infowindow.setContent(''
                    +'<h4>' + property.address + '</h4>'
                    +'<p>' + property.description + '</p>'
                );
                
                infowindow.open(propertiesMap, this);
            }
            else {
                // Close infowindow
                infowindow.close();


                // Mark as not shown
                address.isShown = false;
            }
        });
    }); 
    
    
    // Init clusterization
    var markerCluster = new MarkerClusterer(propertiesMap, markers);
}) })(jQuery)