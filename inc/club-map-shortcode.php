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
        var Markers = {};
        var infowindow;
        var geocoder;
        var locations = [
        <?php
        $coord_avg = [0,0];
        $coord_count = 0;
        foreach($clubs as $club){
            $coords = explode(',', get_post_meta($club->ID, 'coordonnees_gps', true));
            if(count($coords)==2){
                $address =  get_post_meta($club->ID, 'code_postal', true).'  '. get_post_meta($club->ID, 'ville', true);
                //$thumbnail = wp_get_attachment_url( get_post_thumbnail_id($club->ID), '50x50' );
                $coord_avg[0] += $coords[0];
                $coord_avg[1] += $coords[1];
                $coord_count++;
                echo "
                    [
                        '{$club->post_title}',
                        '<strong>{$club->post_title}</strong><br><p>$address<br><strong><a href=\'".get_site_url(null, "/club/{$club->post_name}")."\'>Voir ce club.</a></strong></p>',
                        {$coords[0]},
                        {$coords[1]},
                        0
                    ],
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

            var map = new google.maps.Map(document.getElementById('map'), {
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
                Markers[locations[i][4]] = marker;
            }

        }
    </script>
    <script async defer
            src="https://maps.googleapis.com/maps/api/js?key=<?php echo get_option('vdn_companion_google_api_key'); ?>&callback=initMap">
    </script>
    <?php
}


