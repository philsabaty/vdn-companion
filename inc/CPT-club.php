<?php


/*
 * Define Club CPT
 */
add_action('init', 'register_club_cpt');
function register_club_cpt()
{

    // Register Clubs
    $clubs_labels = array(
        'name' => 'Clubs',
        'singular_name' => 'Club',
        'menu_name' => 'Clubs',
        'all_items' => 'Tous les club',
        'add_new_item' => 'Ajouter un club',
        'edit_item' => 'Éditer le club',
        'new_item' => 'Nouveau club',
        'view_item' => 'Voir le club',
        'search_items' => 'Rechercher parmi les clubs',
        'not_found' => 'Pas de club trouvé',
        'not_found_in_trash' => 'Pas de club dans la corbeille'
    );
    $clubs_args = array(
        'labels' => $clubs_labels,
        'public' => true,
        //'capability_type' => 'page',
        'capability_type' => 'post',
        'has_archive' => true,
        'supports' => array('title', 'editor', 'excerpt', 'thumbnail', 'revisions', 'author')
    );
    register_post_type('club', $clubs_args);

}

/*
 * Register Club fields
 */
if(function_exists("register_field_group"))
{
    register_field_group(array (
        'id' => 'acf_options-clubs',
        'title' => 'Options Clubs',
        'fields' => array (
            array (
                'key' => 'field_5a6dedb08039b',
                'label' => 'Présentation',
                'name' => 'presentation',
                'type' => 'wysiwyg',
                'default_value' => '',
                'toolbar' => 'full',
                'media_upload' => 'yes',
            ),
            array (
                'key' => 'field_5a747090d6a61',
                'label' => 'Structure de rattachement',
                'name' => 'structure',
                'type' => 'text',
                'instructions' => 'Quelle est la structure qui accueille les ateliers ?',
                'required' => 1,
                'default_value' => '',
                'placeholder' => '',
                'prepend' => '',
                'append' => '',
                'formatting' => 'html',
                'maxlength' => '',
            ),
            array (
                'key' => 'field_5a673b21cea88',
                'label' => 'Ville',
                'name' => 'ville',
                'type' => 'text',
                'required' => 1,
                'default_value' => 'Ville',
                'placeholder' => '',
                'prepend' => '',
                'append' => '',
                'formatting' => 'html',
                'maxlength' => '',
            ),
            array (
                'key' => 'field_5a6dee358039d',
                'label' => 'Adresse',
                'name' => 'adresse',
                'type' => 'text',
                'required' => 0,
                'default_value' => '',
                'placeholder' => '',
                'prepend' => '',
                'append' => '',
                'formatting' => 'html',
                'maxlength' => '',
            ),
            array (
                'key' => 'field_5a6ded938039a',
                'label' => 'Code postal',
                'name' => 'code_postal',
                'type' => 'text',
                'required' => 1,
                'default_value' => '',
                'placeholder' => '',
                'prepend' => '',
                'append' => '',
                'formatting' => 'html',
                'maxlength' => '',
            ),
            array (
                'key' => 'field_5a6deccd80397',
                'label' => 'Prénom du Référent',
                'name' => 'prenom_du_referent',
                'type' => 'text',
                'required' => 1,
                'default_value' => '',
                'placeholder' => '',
                'prepend' => '',
                'append' => '',
                'formatting' => 'none',
                'maxlength' => '',
            ),
            array (
                'key' => 'field_5a6deccd80398',
                'label' => 'Nom du Référent',
                'name' => 'nom_du_referent',
                'type' => 'text',
                'required' => 1,
                'default_value' => '',
                'placeholder' => '',
                'prepend' => '',
                'append' => '',
                'formatting' => 'none',
                'maxlength' => '',
            ),
            array (
                'key' => 'field_5a6ded2880398',
                'label' => 'Email du référent',
                'name' => 'contact_du_referent',
                'type' => 'text',
                'required' => 1,
                'default_value' => '',
                'placeholder' => '',
                'prepend' => '',
                'append' => '',
                'formatting' => 'none',
                'maxlength' => '',
            ),
            array (
                'key' => 'field_5a6ded2880399',
                'label' => 'Téléphone du référent',
                'name' => 'telephone_du_referent',
                'type' => 'text',
                'required' => 0,
                'default_value' => '',
                'placeholder' => '',
                'prepend' => '',
                'append' => '',
                'formatting' => 'none',
                'maxlength' => '',
            ),
            array (
                'key' => 'field_5a747036d6a5f',
                'label' => 'Régularité des ateliers',
                'name' => 'regularite_des_ateliers',
                'type' => 'text',
                'instructions' => 'Indiquez quels jours dans le mois ou dans la semaine se tiennent les ateliers.',
                'default_value' => '',
                'placeholder' => '',
                'prepend' => '',
                'append' => '',
                'formatting' => 'html',
                'maxlength' => '',
            ),
            array (
                'key' => 'field_5a747036d6a60',
                'label' => 'Site web de la structure',
                'name' => 'site_web_structure',
                'type' => 'text',
                'instructions' => '',
                'default_value' => '',
                'placeholder' => '',
                'prepend' => '',
                'append' => '',
                'formatting' => 'text',
                'maxlength' => '',
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
                    'value' => 'club',
                    'order_no' => 0,
                    'group_no' => 0,
                ),
            ),
        ),
        'options' => array (
            'position' => 'normal',
            'layout' => 'default',
            'hide_on_screen' => array (
                0 => 'the_content',
                1 => 'excerpt',
                2 => 'discussion',
                3 => 'comments',
                4 => 'revisions',
                5 => 'format',
                //6 => 'author',
            ),
        ),
        'menu_order' => 0,
    ));
}

/**
 * Add a category club_xxxx when club with slug xxxx is published
 * Attach club to club author (owner)
 * Fill GPS coordinates when a Club is updated
 */
add_action( 'save_post', 'add_category_for_club' );
function add_category_for_club($post_id){
    $post_type = get_post_type($post_id);
    if ( "club" == $post_type && (get_post_status($post_id)=='publish')) {
        // Create category if needed
        $post = get_post($post_id);
        $club_slug = $post->post_name;
        $category_slug = "club_$club_slug";
        if(get_category_by_slug( $category_slug )===false){
            //wp_create_category($category_slug);
            $category_name = "Club \"{$post->post_title}\""; 
            wp_insert_category( array('cat_name' => $category_name, 'category_nicename' => $category_slug) );
        }
        // Attach club to club author (TODO : not working : um_profile() doesn't use user_meta)
        $author_id = get_post_field('post_author', $post_id);
        update_user_meta($author_id, 'club', $club_slug);
        // Assign author to group "ReferentsClubs"
        make_user_referentclub($author_id);

        // Fill GPS coordinates when a Club is updated
        $coords = get_post_meta($post_id, 'coordonnees_gps', true);
        if($coords==null || $coords==''){
            $address =  get_post_meta($post_id, 'code_postal', true).'  '. get_post_meta($post_id, 'ville', true);
            $geocoder_response = geocoding_sync($address, get_option('vdn_companion_google_api_key'));
            if(is_array($geocoder_response)){
                $coord = $geocoder_response['lat'].', '.$geocoder_response['lng'];
                update_post_meta($post_id, 'coordonnees_gps',$coord);
            }
        }
    }

    if ( "club" == $post_type && (get_post_status($post_id)=='draft')) {
        $post = get_post($post_id);
        // Remove author from group "ReferentsClubs" if not more published clubs
        $author_id = get_post_field('post_author', $post_id);
        $clubs_for_author = new WP_Query(array('author' => $author_id, 'post_type'=> 'club', 'post_status' => 'publish'));
        if(count($clubs_for_author->posts)==0){
            unmake_user_referentclub($author_id);
        }
        // Remove club to club author
        $club_slug = $post->post_name;
        $author_id = get_post_field('post_author', $post_id);
        delete_user_meta($author_id, 'club', $club_slug);
    }
}


/**
 * Automatically add club_xxxx category when a post or CPT is created by a club user
 */
add_action( 'save_post', 'add_club_category_for_posts' );
function add_club_category_for_posts($post_id){
    $post_type = get_post_type($post_id);
    if ( in_array($post_type, array('post', 'fiche', 'tribe_events' ))  ) {
        $club_slug = get_user_club();
        if($club_slug!=null){
            $category_slug = "club_$club_slug";
            $category_id = wp_create_category($category_slug);
            wp_set_post_categories($post_id, array($category_id), true);
            $club = vdn_get_club_by_slug($club_slug);
            $category_name = 'Club "'.$club->post_title.'"';
            wp_update_term($category_id, 'category', array('name' => $category_name));
        }
    }
}

/*
 * Send notification email to referent when club is validated
 */
add_action( 'draft_to_publish', 'vdn_send_club_validation_email', 10, 1 ); 
function vdn_send_club_validation_email($club_id){
    $author_id = get_post_field ('post_author', $club_id);
    $author = get_userdata($author_id);
    $email_content = vdn_get_club_validation_email_content($club_id);
    $headers = array();
    wp_mail( //$to, $subject, $message
        $author->user_email,
        "Voyageurs Du Numérique : Votre club est validé !",
        $email_content,
        'Content-Type: text/html; charset=UTF-8;'. "\r\n"
        ."From: Voyageurs Du Numerique <".get_bloginfo('admin_email').">". "\r\n"
        
    );
}