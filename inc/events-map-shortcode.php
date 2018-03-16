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
    global $VDN_CONFIG;
    $event_types = $VDN_CONFIG['vdn_event_types'];
    ?>

    <div class="row" style="background-color:#ddd;padding:4px;">
        <div class="col-sm-3">Filtrer par type : </div>
        <div class="col-sm-9">
            <select style="width:100%" onchange="return update_vdn_events(this)">
                <option value='all' selected="selected">Tous</option>
            <?php
            foreach($event_types as $k=>$v){
                echo "<option style='background-color:#{$v['color']}' value='{$k}'>{$v['label']}</option>";
            }
            ?>
            </select>
        </div>
    </div>
    <div id="map" style="width:100%; height:480px;"></div>
    <script>
        function update_vdn_events(selector){
            var selectedType = selector.options[selector.selectedIndex].value;
            var marker;
            for(var i in locations){
                marker = locations[i].marker;
                marker.setMap((locations[i].evt_type==selectedType || selectedType=='all')?map:null);
            }
            return false;
        }
        var map;
        var markers = [];
        var infowindow;
        var locations = [
        <?php
        $events = get_posts(array('post_type' => 'tribe_events'));
        foreach($events as $event){
            $evt_type = get_post_meta($event->ID, 'type', true);
            $location_id = vdn_get_event_location_id($event->ID);
            $coords = explode(',', get_post_meta($location_id, 'coordonnees_gps', true));
            if(count($coords)==2){
                echo "
                    {
                        wpid: '{$event->ID}',
                        html: '<strong>{$event->post_title}</strong><br><strong><a href=\'".get_site_url(null, "/event/{$event->post_name}")."\'>Voir cet événement.</a></strong></p>',
                        lat: {$coords[0]},
                        lng: {$coords[1]},
                        evt_type: '$evt_type',
                        marker: null
                    },
                ";
            }
        }
        ?>
        ];

        function initMap() {
            var bounds = new google.maps.LatLngBounds();
            map = new google.maps.Map(document.getElementById('map'));
            
            var oms = new OverlappingMarkerSpiderfier(map, {
                markersWontMove: true,
                markersWontHide: true,
                basicFormatEvents: true
            });
            var marker_icons = {
                <?php
                foreach($event_types as $et){
                    echo "
                    '{$et['slug']}':{
                        url: 'http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld={$et['letter']}|{$et['color']}',
                        size: new google.maps.Size(20, 32),
                        origin: new google.maps.Point(0, 0),
                        anchor: new google.maps.Point(10, 32)
                    },";
                }
                ?>
                
            };
            
            infowindow = new google.maps.InfoWindow();

            for(i=0; i<locations.length; i++) {
                var position = new google.maps.LatLng(locations[i].lat, locations[i].lng);
                var marker_icon = marker_icons[locations[i].evt_type];
                if(marker_icon==null){marker_icon = marker_icons['evenement']}
                var marker = new google.maps.Marker({
                    icon: marker_icon,
                    position: position,
                    //map: map,
                });
                oms.addMarker(marker);
                // use spider_click instead of click to use with OverlappingMarkerSpiderfier
                google.maps.event.addListener(marker, 'spider_click', (function(marker, i) {
                    return function() {
                        infowindow.setContent(locations[i].html);
                        infowindow.setOptions({maxWidth: 200});
                        infowindow.open(map, marker);
                    }
                }) (marker, i));
                locations[i].marker = marker;
                markers.push(marker);
                bounds.extend(marker.getPosition());
            }

            if(locations.length>1) {
                google.maps.event.addListenerOnce(map, 'bounds_changed', function (event) {
                    map.setZoom(map.getZoom() - 2);
                });
                map.fitBounds(bounds);
            }
            
            var markerCluster = new MarkerClusterer(map, markers,
                {imagePath: '<?php echo plugins_url('/vdn-companion/inc/googlemaps/m'); ?>'}
            );
            markerCluster.setMaxZoom(14);
        }
    </script>
    <script src="<?php echo plugins_url('/vdn-companion/inc/googlemaps/markerclusterer.js'); ?>"></script>
    <script src="<?php echo plugins_url('/vdn-companion/inc/googlemaps/oms.min.js'); ?>"></script>
    <script async defer
            src="https://maps.googleapis.com/maps/api/js?key=<?php echo get_option('vdn_companion_google_api_key'); ?>&callback=initMap">
    </script>
    <?php
}


