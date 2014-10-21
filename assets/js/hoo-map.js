jQuery(function($) {
    var map_options = {
        center: {
            lat: -34.397,
            lng: 150.644
        },
        zoom: 8
    },
        map = new google.maps.Map(document.getElementById('map-canvas'),
                                  map_options);
    console.log("MAP!");
});
