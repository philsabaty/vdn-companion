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
    $event_types = array(
        'evenement'=>array(
            'label'=>'événement',
            'slug'=>'evenement',
            'color'=>'e95a51',
            'letter'=>'E',
        ),
        'formation_animateurs'=>array(
            'label'=>'formation d animateurs VDN',
            'slug'=>'formation_animateurs',
            'color'=>'5171b6',
            'letter'=>'F',
        ),
        'atelier_ponctuel'=>array(
            'label'=>'atelier ponctuel',
            'slug'=>'atelier_ponctuel',
            'color'=>'00ff00',
            'letter'=>'P',
        ),
        'atelier_recurrent'=>array(
            'label'=>'atelier récurrent',
            'slug'=>'atelier_recurrent',
            'color'=>'708090',
            'letter'=>'R',
        ),
        'apero_VDN'=>array(
            'label'=>'apéro VDN',
            'slug'=>'apero_VDN',
            'color'=>'ff0000',
            'letter'=>'A',
        ),
        'tour_de_france'=>array(
            'label'=>'Tour de France',
            'slug'=>'tour_de_france',
            'color'=>'0000ff',
            'letter'=>'T',
        ),
    );
    ?>

    <div class="row" style="background-color:#ddd;padding:4px;">
        <div class="col-sm-3">Filtrer par type : </div>
        <div class="col-sm-9">
            <select style="width:100%" onchange="return update_vdn_events(this)">
                <option value='all' selected="selected">Tous</option>
            <?php
            foreach($event_types as $et){
                echo "<option style='background-color:#{$et['color']}' value='{$et['slug']}'>{$et['label']}</option>";
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
        var infowindow;
        var map ;
        var locations = [
        <?php
        $events = get_posts(array('post_type' => 'tribe_events'));
        $coord_avg = [0,0];
        $coord_count = 0;
        foreach($events as $event){
            $evt_type = get_post_meta($event->ID, 'type', true);
            $location_id = vdn_get_event_location_id($event->ID);
            $coords = explode(',', get_post_meta($location_id, 'coordonnees_gps', true));
            if(count($coords)==2){
                $coord_avg[0] += $coords[0];
                $coord_avg[1] += $coords[1];
                $coord_count++;
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
        if($coord_count>1){
            $coord_avg[0] = $coord_avg[0] / $coord_count;
            $coord_avg[1] = $coord_avg[1] / $coord_count;
        }
        ?>
        ];

        function initMap() {
            geocoder = new google.maps.Geocoder();

            map = new google.maps.Map(document.getElementById('map'), {
                zoom: 5,
                center: new google.maps.LatLng(<?php echo $coord_avg[0]; ?>, <?php echo $coord_avg[1]; ?>)
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
                    map: map,
                });
                google.maps.event.addListener(marker, 'click', (function(marker, i) {
                    return function() {
                        infowindow.setContent(locations[i].html);
                        infowindow.setOptions({maxWidth: 200});
                        infowindow.open(map, marker);
                    }
                }) (marker, i));
                locations[i].marker = marker;
            }

        }
    </script>
    <script async defer
            src="https://maps.googleapis.com/maps/api/js?key=<?php echo get_option('vdn_companion_google_api_key'); ?>&callback=initMap">
    </script>
    <?php
}


