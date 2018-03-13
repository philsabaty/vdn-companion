<?php
/*
 * Add a vdn_event_map shortcode for /category/fiches-thematiques/...
 * Add an ajax target for tags autosuggest
 */

add_shortcode( 'vdn_event_map', 'vdn_event_map_shortcode' );
function vdn_event_map_shortcode() {
    ob_start();
    vdn_event_map_html();
    return ob_get_clean();
}
function vdn_event_map_html() {
    ?>

    <div id="map" style="width:100%; height:480px;"></div>
    <script>
        var markers = [];
        var infowindow;
        var map ;
        var locations = [
        <?php
        $events = get_posts(array('post_type' => 'tribe_events'));
        $coord_avg = [0,0];
        foreach($events as $event){
        //die(print_r(get_post_custom($event->ID)));
        $coords = explode(',', get_post_meta($event->ID, 'coordonnees_gps', true));
        $address =  get_post_meta($event->ID, 'code_postal', true).'  '. get_post_meta($event->ID, 'ville', true);
        //$thumbnail = wp_get_attachment_url( get_post_thumbnail_id($event->ID), '50x50' );
        $coord_avg[0] += $coords[0];
        $coord_avg[1] += $coords[1];
        echo "
            [
                '{$event->post_title}',
                '<strong>{$event->post_title}</strong><br><strong><a href=\'".get_site_url(null, "/event/{$event->post_name}")."\'>Voir cet événement.</a></strong></p>',
                {$coords[0]},
                {$coords[1]},
                {$event->ID}
            ],
            ";
        }
        if(count($events)>0){
            $coord_avg[0]  = $coord_avg[0] / count($events);
            $coord_avg[1] += $coords[1] / count($events);
        }
        ?>
        ];

        function initMap() {
            geocoder = new google.maps.Geocoder();

            map = new google.maps.Map(document.getElementById('map'), {
                zoom: 5,
                center: new google.maps.LatLng(<?php echo $coord_avg[0]; ?>, <?php echo $coord_avg[1]; ?>)
            });

            var image = {
                url: 'http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=%E2%80%A2|00ff00',
                size: new google.maps.Size(20, 32),
                origin: new google.maps.Point(0, 0),
                anchor: new google.maps.Point(10, 32)
            };
            infowindow = new google.maps.InfoWindow();

            for(i=0; i<locations.length; i++) {
                var position = new google.maps.LatLng(locations[i][2], locations[i][3]);
                var marker = new google.maps.Marker({
                    position: position,
                    map: map,
                });
                google.maps.event.addListener(marker, 'click', (function(marker, i) {
                    return function() {
                        infowindow.setContent(locations[i][1]);
                        infowindow.setOptions({maxWidth: 200});
                        infowindow.open(map, marker);
                    }
                }) (marker, i));
                markers[locations[i][4]] = marker;
            }

        }
    </script>
    <script async defer
            src="https://maps.googleapis.com/maps/api/js?key=<?php echo get_option('vdn_companion_google_api_key'); ?>&callback=initMap">
    </script>
    <?php
}


