<?php


function vdn_get_searched_category_id(){
    return (isset($_POST['search_category_id'])?$_POST['search_category_id']:get_query_var('cat'));
}

function vdn_get_user_role($id=0){
    $id = ($id==0)?get_current_user_id():$id;
    $user_meta=get_userdata($id);
    $user_roles=$user_meta->roles;
    return (empty($user_roles))?'':array_shift($user_roles);
}

function vdn_is_admin($id=0){
    $id = ($id==0)?get_current_user_id():$id;
    $user_role = vdn_get_user_role(get_current_user_id());
    return (strpos($user_role, 'admin')!==false ) ;
}


/**
 * Redirect to frontend when post is published by a non-admin user
 */
add_filter('redirect_post_location', 'redirect_to_post_on_publish_or_save');
function redirect_to_post_on_publish_or_save($location) {
    global $post;
    $post_type = get_post_type($post->ID);
    if ( in_array($post_type, array('post', 'fiche', 'club', 'tribe_events' ))  ) {
        $user_role = vdn_get_user_role(get_current_user_id());
        if (
            strpos($user_role, 'admin')===false &&
            (isset($_POST['publish']) || isset($_POST['save'])) &&
            preg_match("/post=([0-9]*)/", $location, $match) &&
            $post &&
            $post->ID == $match[1] &&
            (isset($_POST['publish']) || $post->post_status == 'publish') // Publishing draft or updating published post
        ) {
            $location = get_permalink($post->ID);
        }
    }
    return $location;
}

/**
 * Create a [loggedin] shortcode
 */
add_shortcode('loggedin', 'if_logged_in_shortcode' );
function if_logged_in_shortcode ($atts, $content = null){
    if ( is_user_logged_in() ){
        if(isset($atts['id'])){
            $p_user_id = get_userdata( $atts['id'] );
            if(get_current_user_id()==$p_user_id){
                return $content;
            }
        }else{
            return $content;
        }
    }
    return '';
}

/**
 * @return bool
 */
function user_has_club(){
    return ( um_profile('club')!=null && um_profile('club')!='-1') ;
}

add_shortcode('not_loggedin', 'if_not_logged_in_shortcode' );
function if_not_logged_in_shortcode ($atts, $content = null){
    return ( is_user_logged_in() )?'':$content;
}

add_shortcode('has_club', 'if_has_club_shortcode' );
function if_has_club_shortcode ($atts, $content = null){
    return user_has_club()?'':$content;
}

/*
 * Request google geocoding service
 */
function geocoding_sync($address, $API_KEY){
    $address = urlencode($address);
    $url = "https://maps.googleapis.com/maps/api/geocode/json?address={$address}&key={$API_KEY}";
    $resp_json = file_get_contents($url);
    $resp = json_decode($resp_json, true);
    if($resp['status']=='OK'){
        // get the important data
        $lati = isset($resp['results'][0]['geometry']['location']['lat']) ? $resp['results'][0]['geometry']['location']['lat'] : "";
        $longi = isset($resp['results'][0]['geometry']['location']['lng']) ? $resp['results'][0]['geometry']['location']['lng'] : "";
        $formatted_address = isset($resp['results'][0]['formatted_address']) ? $resp['results'][0]['formatted_address'] : "";

        return array(
            'lat' => $lati,
            'lng' => $longi,
            'formatted_address' => $formatted_address);
    }
    return false;
}
