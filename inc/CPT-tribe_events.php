<?php
/**
 * Created by PhpStorm.
 * User: phil
 * Date: 12/03/2018
 * Time: 16:54
 */

register_field_group(array (
    'id' => 'acf_options-tribe_events',
    'title' => 'Options Events',
    'fields' => array (
        array (
            //'key' => 'field_1a8d6ba117075',
            'label' => 'Type',
            'name' => 'type',
            'type' => 'select',
            'choices' => array (
                'formation_animateurs' => 'formation d\'animateurs VDN',
                'atelier_recurrent' => 'atelier récurrent',
                'atelier_ponctuel' => 'atelier ponctuel',
                'apero_VDN' => 'apéro VDN',
                'evenement' => 'événement',
                'tour_de_france' => 'Tour de France',
            ),
            'default_value' => '',
            'allow_null' => 0,
            'multiple' => 0,
        ),
        array (
            'key' => 'field_5a899e7827c4f',
            'label' => '',//'Coordonnées GPS',
            'name' => 'coordonnees_gps',
            'type' => 'hidden',//'text',
            'instructions' => '',//'Coordonnées utilisées sur la carte pour situer le club (laisser vide si non-connu)',
            'required' => 0,
            'default_value' => '',
            'placeholder' => '48.85109, 2.41967',
            'prepend' => '',
            'append' => '',
            'formatting' => 'none',
            'maxlength' => '',
        ),
    ),
    'location' => array (
        array (
            array (
                'param' => 'post_type',
                'operator' => '==',
                'value' => 'tribe_events',
                'order_no' => 0,
                'group_no' => 0,
            ),
        ),
    ),
    'options' => array (
        'position' => 'acf_after_title',
        //'position' => 'normal',
        'layout' => 'default',
        'hide_on_screen' => array (
            0 => 'categories',
        ),
    ),
    'menu_order' => 0,
));


/**
 * Fill GPS coordinates when a Event is updated
 */
add_action( 'save_post', 'add_gps_coordinates_for_event' );
function add_gps_coordinates_for_event($post_id){
    $post_type = get_post_type($post_id);
    if ( "tribe_events" == $post_type && (get_post_status($post_id)=='publish')) {
        $location_ids = get_post_meta($post_id, '_EventVenueID');
        if( !empty($location_ids)){
            $location_id = $location_ids[0];
            $location = get_post_meta($location_id);
            $address = '';
            @$address .= $location['_VenueAddress'][0].' ,';
            @$address .= $location['_VenueZip'][0].' ,';
            @$address .= $location['_VenueCity'][0].' ,';
            @$address .= $location['_VenueCountry'][0].' ';
            // Fill GPS coordinates when a Club is updated
            $geocoder_response = geocoding_sync($address, get_option('vdn_companion_google_api_key'));
            if(is_array($geocoder_response)){
                $coord = $geocoder_response['lat'].', '.$geocoder_response['lng'];
                update_post_meta($post_id, 'coordonnees_gps',$coord);
            }
        }
    }
}
