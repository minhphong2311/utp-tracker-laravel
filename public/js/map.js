(function($) {

var geocoder;
var map;
var marker;

/*
 * Google Map with marker
 */
function initialize() {
    var initialLat = $(".search_latitude").val();
    var initialLong = $(".search_longitude").val();

    initialLat = initialLat ? initialLat : 36.169648;
    initialLong = initialLong ? initialLong : -115.141;

    var latlng = new google.maps.LatLng(initialLat, initialLong);
    var options = {
        zoom: 16,
        center: latlng,
        mapTypeId: google.maps.MapTypeId.ROADMAP,
    };

    map = new google.maps.Map(document.getElementById("geomap"), options);

    geocoder = new google.maps.Geocoder();

    marker = new google.maps.Marker({
        map: map,
        draggable: true,
        position: latlng,
    });

    google.maps.event.addListener(marker, "dragend", function () {
        var point = marker.getPosition();
        map.panTo(point);
        geocoder.geocode(
            { latLng: marker.getPosition() },
            function (results, status) {
                if (status == google.maps.GeocoderStatus.OK) {
                    map.setCenter(results[0].geometry.location);
                    marker.setPosition(results[0].geometry.location);
                    $(".search_addr").val(results[0].formatted_address);
                    $(".search_latitude").val(marker.getPosition().lat());
                    $(".search_longitude").val(marker.getPosition().lng());
                }
            }
        );
    });
}

var $ = jQuery;
jQuery(document).ready(function ($) {
    //load google map
    initialize();

    /*
     * autocomplete location search
     */
    var PostCodeid = "#search_location";
    $(function () {
        const input = document.getElementById("search_location");
        const autocomplete = new google.maps.places.Autocomplete(input, {
            fields: ["place_id", "geometry", "formatted_address", "name"],
        });
        autocomplete.bindTo("bounds", map);
        const infowindow = new google.maps.InfoWindow();

        autocomplete.addListener("place_changed", () => {
            infowindow.close();
            jQuery(".get_map").trigger("click");
        });
    });

    /*
     * Point location on google map
     */
    $(".get_map").click(function (e) {
        var address = $(PostCodeid).val();
        geocoder.geocode({ address: address }, function (results, status) {
            if (status == google.maps.GeocoderStatus.OK) {
                map.setCenter(results[0].geometry.location);
                marker.setPosition(results[0].geometry.location);
                $(".search_addr").val(results[0].formatted_address);
                $(".search_latitude").val(marker.getPosition().lat());
                $(".search_longitude").val(marker.getPosition().lng());
            } else {
                alert(
                    "Geocode was not successful for the following reason: " +
                        status
                );
            }
        });
        e.preventDefault();
    });

    //Add listener to marker for reverse geocoding
    google.maps.event.addListener(marker, "drag", function () {
        geocoder.geocode(
            { latLng: marker.getPosition() },
            function (results, status) {
                if (status == google.maps.GeocoderStatus.OK) {
                    if (results[0]) {
                        $(".search_addr").val(results[0].formatted_address);
                        $(".search_latitude").val(marker.getPosition().lat());
                        $(".search_longitude").val(marker.getPosition().lng());
                    }
                }
            }
        );
    });
});

}(jQuery));
