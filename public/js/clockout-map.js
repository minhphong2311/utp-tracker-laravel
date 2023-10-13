(function($) {

    var geocoder;
    var map;
    var marker;

    /*
     * Google Map with marker
     */
    function initialize1() {
        var initialLat = $(".clockout_latitude").val();
        var initialLong = $(".clockout_longitude").val();
        initialLat = initialLat ? initialLat : 36.169648;
        initialLong = initialLong ? initialLong : -115.141;

        var latlng = new google.maps.LatLng(initialLat, initialLong);
        var options = {
            zoom: 16,
            center: latlng,
            mapTypeId: google.maps.MapTypeId.ROADMAP,
        };

        map = new google.maps.Map(document.getElementById("clockout_geomap"), options);

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
                        $(".clockout_address").val(results[0].formatted_address);
                        $(".clockout_latitude").val(marker.getPosition().lat());
                        $(".clockout_longitude").val(marker.getPosition().lng());
                    }
                }
            );
        });
    }

    var $ = jQuery;
    jQuery(document).ready(function ($) {
        //load google map
        initialize1();

        /*
         * autocomplete location search
         */
        var PostCodeid = "#clockout_location";
        $(function () {
            const input = document.getElementById("clockout_location");
            const autocomplete = new google.maps.places.Autocomplete(input, {
                fields: ["place_id", "geometry", "formatted_address", "name"],
            });
            autocomplete.bindTo("bounds", map);
            const infowindow = new google.maps.InfoWindow();

            autocomplete.addListener("place_changed", () => {
                infowindow.close();
                jQuery(".clockout_get_map").trigger("click");
            });
        });

        /*
         * Point location on google map
         */
        $(".clockout_get_map").click(function (e) {
            var address = $(PostCodeid).val();
            geocoder.geocode({ address: address }, function (results, status) {
                if (status == google.maps.GeocoderStatus.OK) {
                    map.setCenter(results[0].geometry.location);
                    marker.setPosition(results[0].geometry.location);
                    $(".clockout_address").val(results[0].formatted_address);
                    $(".clockout_latitude").val(marker.getPosition().lat());
                    $(".clockout_longitude").val(marker.getPosition().lng());
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
                            $(".clockout_address").val(results[0].formatted_address);
                            $(".clockout_latitude").val(marker.getPosition().lat());
                            $(".clockout_longitude").val(marker.getPosition().lng());
                        }
                    }
                }
            );
        });
    });

    }(jQuery));
