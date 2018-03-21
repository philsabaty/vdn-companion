<?php
/**
 * Define extra fields (based on ACF) for tribe events
 */
global $VDN_CONFIG;
$tribe_event_choices = array_map(function($v){return $v['label'];}, $VDN_CONFIG['vdn_event_types']);

register_field_group(array (
    'id' => 'acf_options-tribe_events',
    'title' => 'Options événements',
    'fields' => array (
        array (
            'key' => 'field_1a8d6ba117075',
            'label' => 'Type',
            'name' => 'type',
            'type' => 'select',
            'choices' => $tribe_event_choices,
            'default_value' => 'atelier',
            'allow_null' => 0,
            'multiple' => 0,
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


register_field_group(array (
    'id' => 'acf_options-tribe_venue',
    'title' => 'Options Event Venues',
    'fields' => array (
        array (
            //'key' => '',
            'label' => '',//'Coordonnées GPS',
            'name' => 'coordonnees_gps',
            'type' => 'text',//'hidden',
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
                'value' => 'tribe_venue',
                'order_no' => 0,
                'group_no' => 0,
            ),
        ),
    ),
    'options' => array (
        'position' => 'normal',
        'layout' => 'default',
        'hide_on_screen' => array (
            0 => 'categories',
        ),
    ),
    'menu_order' => 0,
));


/**
 * Fill GPS coordinates when a Event Venue is updated
 */
add_action( 'save_post', 'add_gps_coordinates_for_event_venue', 20,1 );
function add_gps_coordinates_for_event_venue($post_id){
    $post_type = get_post_type($post_id);
    if ( "tribe_venue" == $post_type && (get_post_status($post_id)=='publish')) {
        $location_meta = get_post_meta($post_id);
        $address = '';
        @$address .= $location_meta['_VenueAddress'][0].' ,';
        @$address .= $location_meta['_VenueZip'][0].' ,';
        @$address .= $location_meta['_VenueCity'][0].' ,';
        @$address .= $location_meta['_VenueCountry'][0].' ';

        if ($address != '') {
            $geocoder_response = geocoding_sync($address, get_option('vdn_companion_google_api_key'));
            if(is_array($geocoder_response)){
                $coord = $geocoder_response['lat'].', '.$geocoder_response['lng'];
                update_post_meta($post_id, 'coordonnees_gps',$coord);
            }
        }
    }
}

function vdn_get_event_location_id($event_id){
    $location_ids = get_post_meta($event_id, '_EventVenueID');
    return ( !empty($location_ids))?$location_ids[0]:0;
}
