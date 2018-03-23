<?php
/*
 * Add a vdn_club_map shortcode for /category/fiches-thematiques/...
 * Add an ajax target for tags autosuggest
 */

add_shortcode( 'vdn_club_map', 'vdn_club_map_shortcode' );
function vdn_club_map_shortcode() {
    ob_start();
    vdn_club_map_html();
    return ob_get_clean();
}
function vdn_club_map_html() {
    $clubs = get_posts(array('post_type' => 'club'));
    ?>

    <div id="map" style="width:100%; height:480px;"></div>
    <div class="row" style="background-color:#ddd;padding:4px;">
        <div class="col-sm-3">Tous les clubs : </div>
        <div class="col-sm-7">
            <select id="choosen_club" style="width:100%">
                <option value='none' selected="selected" disabled="disabled">(choisir dans la liste)</option>
                <?php
                foreach($clubs as $club){
                    $address =  get_post_meta($club->ID, 'code_postal', true).'  '. get_post_meta($club->ID, 'ville', true);
                    echo "<option value='{$club->post_name}'>{$club->post_title} ($address)</option>";
                }
                ?>
            </select>
        </div>
        <div class="col-sm-2"><a href="javascript:void()" onclick="return goto_club()">Aller voir !</a></div>
    </div>
    <br>
    <script>
        function goto_club(){
            var selector = document.getElementById('choosen_club');
            var selectedClubSlug = selector.options[selector.selectedIndex].value;
            window.location = '/club/'+selectedClubSlug;
            return false;
        }
        var map;
        var markers = [];
        var infowindow;
        var locations = [
        <?php
        foreach($clubs as $club){
            $coords = explode(',', get_post_meta($club->ID, 'coordonnees_gps', true));
            if(count($coords)==2){
                $address =  addslashes(get_post_meta($club->ID, 'code_postal', true).' '. get_post_meta($club->ID, 'ville', true));
                $club_title = addslashes($club->post_title);
                echo "
                    {
                        wpid: '{$club->ID}',
                        html: '<strong>{$club_title}</strong><br><p>$address<br><strong><a href=\'".get_site_url(null, "/club/{$club->post_name}")."\'>Voir ce club.</a></strong></p>',
                        lat: {$coords[0]},
                        lng: {$coords[1]},
                        marker: null,
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

            var image = {
                url: 'https://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=%E2%80%A2|00ff00',
                size: new google.maps.Size(20, 32),
                origin: new google.maps.Point(0, 0),
                anchor: new google.maps.Point(10, 32)
            };
            infowindow = new google.maps.InfoWindow();

            for(i=0; i<locations.length; i++) {
                var position = new google.maps.LatLng(locations[i].lat, locations[i].lng);
                var marker = new google.maps.Marker({
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
                    console.log('bounds_changed : zoom was '+map.getZoom());
                    map.setZoom(map.getZoom() - 2);
                    console.log('bounds_changed : zoom now is '+map.getZoom());
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


