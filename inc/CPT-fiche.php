<?php
/*
 * Define Fiche CPT
 */
global $VDN_CONFIG;

add_action('init', 'register_fiche_cpt');
function register_fiche_cpt() {
    register_post_type(
        'fiche',
        array(
            'label' => 'Fiches',
            'labels' => array(
                'name' => 'Fiches',
                'singular_name' => 'Fiche',
                'all_items' => 'Toutes les fiches',
                'add_new_item' => 'Ajouter une fiche',
                'edit_item' => 'Éditer la fiche',
                'new_item' => 'Nouvelle fiche',
                'view_item' => 'Voir la fiche',
                'search_items' => 'Rechercher parmi les fiches',
                'not_found' => 'Pas de fiche trouvé',
                'not_found_in_trash'=> 'Pas de fiche dans la corbeille'
            ),
            'public' => true,
            'capability_type' => 'post',
            'taxonomies' => array( 'category', 'post_tag' ),
            'supports' => array(
                'title',
                'editor',
                'page-attributes',
                'thumbnail',
                'author',
                'excerpt',
                'comments',
                'post-formats',
            ),
            'has_archive' => false
        )
    );

    //register_taxonomy_for_object_type( 'category', 'ai1ec_event' );
}

/*
 * Register Fiche fields
 */
if(function_exists("register_field_group"))
{
    $fiche_type_choices = array_map(function($v){return $v['label'];}, $VDN_CONFIG['vdn_fiche_types']);

    register_field_group(array (
        'id' => 'acf_options-fiches',
        'title' => 'Options fiches',
        'fields' => array (
            array (
                'key' => 'field_5a8d6ba117075',
                'label' => 'Information rédacteur',
                'name' => '',
                'type' => 'message',
                'message' => '<big><b>Bienvenue sur l\'outil de création de fiches.</b>
	Vous avez à disposition <b>de nombreux champs pour préciser les points clés de votre fiche</b> : Matériel, durée, nombre de participants, etc. N\'hésitez par à remplir ces champs pour faciliter l\'utilisation de votre fiche.
	<b>A la suite de ces options, vous trouverez l\'éditeur graphique pour le contenu de votre fiche.</b>
	<br><br>
	En contribuant aux fiches de Voyageurs du numérique, <b>vous vous engagez à respecter <a target="_blank" href="'.site_url().'/la-charte-des-voyageurs-du-numerique/">notre charte</a></b>.</big>',
            ),
            array (
                'key' => 'field_5a7af15c413a4',
                'label' => 'Type',
                'name' => 'type',
                'type' => 'select',
                'choices' => $fiche_type_choices,
                'default_value' => '',
                'allow_null' => 0,
                'multiple' => 0,
            ),
            array (
                'key' => 'field_5a65f62105ff9',
                'label' => 'Niveau',
                'name' => 'niveau',
                'type' => 'select',
                'instructions' => 'Indiquer le niveau de la fiche',
                'required' => 1,
                'choices' => array (
                    'debutant' => 'Débutant',
                    'intermediaire' => 'Intermédiaire',
                    'confirme' => 'Confirmé',
                ),
                'default_value' => '',
                'allow_null' => 0,
                'multiple' => 0,
            ),
            array (
                'key' => 'field_5a65f67605ffa',
                'label' => 'Public',
                'name' => 'public',
                'type' => 'checkbox',
                'instructions' => 'Indiquez le public visé',
                'required' => 1,
                'choices' => array (
                    'enfants' => 'Enfants',
                    'ados' => 'Ados',
                    'adultes' => 'Adultes',
                    'seniors' => 'Seniors',
                ),
                'default_value' => '',
                'layout' => 'vertical',
            ),
//			array (
//				'key' => 'field_5a8c5c5b2ed8c',
//				'label' => 'Profil du public',
//				'name' => 'profil_du_public',
//				'type' => 'textarea',
//				'instructions' => 'on parle ici
//	principalement de l’âge du
//	public ciblé par l’animation
//	(enfants, adolescents, adultes).
//	Cependant, on peut également
//	mentionner dans cette
//	catégorie des spécificités
//	(handicaps, genres, situation
//	psychologique…)',
//				'default_value' => '',
//				'placeholder' => '',
//				'maxlength' => '',
//				'rows' => 4,
//				'formatting' => 'br',
//			),
            array (
                'key' => 'field_5a8c5b932ed8a',
                'label' => 'durée de préparation',
                'name' => 'duree_preparation',
                'type' => 'text',
                'instructions' => 'indiquer approximativement le temps de préparation de l’activité : l’activité nécessite peut être seulement
	                une bonne prise en main des contenus, mais peut également impliquer une préparation de
                    matériel, l’installation d’un espace, ou même la création de supports.',
                'default_value' => '',
                'placeholder' => '',
                'maxlength' => '',
                'rows' => 4,
                'formatting' => 'br',
            ),
            array (
                'key' => 'field_5a8c5c0e2ed8b',
                'label' => 'Durée de l’animation',
                'name' => 'duree_animation',
                'type' => 'text',
                'instructions' => 'indiquer une durée assez précise selon le déroulé de l’activité. 
                    Elle permettra une gestion plus fluide et dynamique par la suite.',
                'default_value' => '',
                'placeholder' => '',
                'maxlength' => '',
                'rows' => 4,
                'formatting' => 'br',
            ),
            array (
                'key' => 'field_5a8c5c822ed8d',
                'label' => 'Nombre de participants',
                'name' => 'nombre_de_participants',
                'type' => 'text',
                'instructions' => 'on peut ici conseiller une fourchette entre le nombre minimum et le nombre maximum de participants.',
                'default_value' => '',
                'placeholder' => '',
                'prepend' => '',
                'append' => '',
                'formatting' => 'html',
                'maxlength' => '',
            ),
            array (
                'key' => 'field_5a8c5cbc2ed8e',
                'label' => 'Taux d’encadrement',
                'name' => 'taux_encadrement',
                'type' => 'text',
                'instructions' => 'cette information peut être utile pour préciser si c’est une activité qui
                     nécessite plus d’attention pour chacun au sein du groupe, ou si on peut l’imaginer avec des
                     éléments du groupe plus en autonomie.',
                'default_value' => '',
                'placeholder' => '',
                'prepend' => '',
                'append' => '',
                'formatting' => 'html',
                'maxlength' => '',
            ),
            array (
                'key' => 'field_5a8c5cdc2ed8f',
                'label' => 'Matériel utilisé',
                'name' => 'materiel_utilise',
                'type' => 'textarea',
                'instructions' => 'du matériel de création, des fournitures, mais aussi des outils numériques, ou des aliments…
                    Ne pas hésiter à être précis !',
                'default_value' => '',
                'placeholder' => '',
                'maxlength' => '',
                'rows' => 4,
                'formatting' => 'br',
            ),
            array (
                'key' => 'field_5a8c5cf42ed90',
                'label' => 'Contenus utilisés',
                'name' => 'contenus_utilises',
                'type' => 'textarea',
                'instructions' => 'le titre du livre, du jeu ou de l’application utilisés. Eventuellement sa référence également.',
                'default_value' => '',
                'placeholder' => '',
                'maxlength' => '',
                'rows' => 4,
                'formatting' => 'br',
            ),
            array (
                'key' => 'field_5a8c5d332ed91',
                'label' => 'Objectifs pédagogiques',
                'name' => 'objectifs_pedagogiques',
                'type' => 'textarea',
                'instructions' => 'un objectif pédagogique doit traduire ce que l’on souhaite transmettre aux participants à
                    travers l’activité. Il est à formulé avec un verbe d’action, qui décrit un savoir, un savoir-être
                      ou un savoir-faire à acquérir. Pour s’aider, on peut commencer par « les participants doivent être en
                     capacité de … » ou « permettre aux participants de… »',
                'default_value' => '',
                'placeholder' => '',
                'maxlength' => '',
                'rows' => '',
                'formatting' => 'br',
            ),
            array (
                'key' => 'field_5a8c5d692ed92',
                'label' => 'Pré-requis',
                'name' => 'pre-requis',
                'type' => 'textarea',
                'instructions' => 'ce sont les compétences ou connaissances pré-requises des participants que l’on indiquera ici.
                    Si les participants doivent savoir lire par exemple. Ou connaitre un langage informatique.',
                'default_value' => '',
                'placeholder' => '',
                'maxlength' => '',
                'rows' => 4,
                'formatting' => 'br',
            ),
            array (
                'key' => 'field_5a8c5d7e2ed93',
                'label' => 'Compétences travaillées',
                'name' => 'competences_travaillees',
                'type' => 'textarea',
                'instructions' => 'cette catégorie permet de détailler l’objectif pédagogique et d’aller plus loin en détaillant
                    les compétences ou connaissances qui vont être stimulées chez les participants durant l’activité.',
                'default_value' => '',
                'placeholder' => '',
                'maxlength' => '',
                'rows' => 4,
                'formatting' => 'br',
            ),
//			array (
//				'key' => 'field_5a8c5dbf2ed94',
//				'label' => 'Profil animateur',
//				'name' => 'profil_animateur',
//				'type' => 'textarea',
//				'instructions' => 'on utilisera cette catégorie si certaines compétences précises sont également requises chez l’animateur.',
//				'default_value' => '',
//				'placeholder' => '',
//				'maxlength' => '',
//				'rows' => 4,
//				'formatting' => 'br',
//			),
//			array (
//				'key' => 'field_5a8c5de12ed95',
//				'label' => 'Déroulement',
//				'name' => 'deroulement',
//				'type' => 'textarea',
//				'instructions' => 'on pourra organiser le déroulé de l’activité en différentes parties et étapes afin d’aller dans une précision assez fine.',
//				'default_value' => '',
//				'placeholder' => '',
//				'maxlength' => '',
//				'rows' => '',
//				'formatting' => 'br',
//			),
        ),
        'location' => array (
            array (
                array (
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'fiche',
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
}

function vdn_get_fiche_fields_group(){
    foreach($GLOBALS['acf_register_field_group'] as $fg){
        if($fg['id']=='acf_options-fiches'){
            return $fg;
        }
    }
    return null;
}

/*
 * Allow CPT "Fiche" in category pages
 */
add_filter( 'pre_get_posts', 'vdn_add_custom_types' );
function vdn_add_custom_types( $query ) {
    if( is_category() || is_tag() && empty( $query->query_vars['suppress_filters'] ) ) {
        $query->set( 'post_type', array(
            'post', 'nav_menu_item', 'fiche'
        ));
        return $query;
    }
}

/**
 * Add custom fields for Fiche PDF export
 */
add_filter( 'the_post_export_content', 'export_full_fiche_pdf' );
function export_full_fiche_pdf( $content ){
    global $VDN_CONFIG;
    $post = get_post();
    $main_css_color = '#e95a51';
    @$main_css_color = '#'.($VDN_CONFIG['vdn_fiche_types'][get_field('type', $post->ID)]['color']);
    if ( 'fiche' === $post->post_type ){
        $fields = get_field_objects($post->ID);
        $new_content = '';
        $new_content .= '<style>
body{font-family:sans-serif;}
strong, span{color:'.$main_css_color.';}
h1, .main_info{color:#fff; background-color: '.$main_css_color.'; display:inline-block;padding:4px; margin:4px; font-weight:bold;float:left;}
h1{padding-top:16px;}
</style>';
        $new_content .= 'Publié le '.get_the_date( 'j F Y' ).' par '.get_the_author_meta('display_name', get_post_field( 'post_author', $post->ID )).'<br>';
        $new_content .= 'Catégories : <span>'.strip_tags(get_the_category_list(', ')).'</span><br>';
        $new_content .= 'Mots-clés : <span>'.strip_tags(get_the_tag_list('', ', ')).'</span><br>';

        $new_content .= '<h2>À propos de cette fiche</h2>';
        $new_content .= '<span class="main_info"> Type : '.get_selected_option_label('type').' </span> &nbsp; ';
        $new_content .= '<span class="main_info"> Niveau : '.get_selected_option_label('niveau').' </span> &nbsp; ';
        $new_content .= '<span class="main_info"> Public : '.implode(', ', get_field('public')).' </span> &nbsp; ';
        $new_content .= '<br><br><hr>';
        $new_content .= '<table cellpadding="4" cellspacing="0" border="1">';
        foreach( $fields as $field_name => $field ){
            if($field['type']=='text' && $field['value']!='' && !in_array($field['name'], array('type', 'niveau', 'public'))){
                $new_content .= '<tr><td>' . $field['label'] . ' </td><td>' . $field['value'] . '</td></tr>';
            }
        }
        $new_content .= '</table><br>';
        foreach( $fields as $field_name => $field ){
            if($field['type']=='textarea' && $field['value']!=''){
                $new_content .= '<div><strong>' . $field['label'] . ' :&nbsp;</strong><br>' . $field['value'] . '</div>';
            }
        }
        $new_content .= '<hr><br><br>';
        $new_content .= '<h2>Contenu</h2><br><br>';
        $new_content .= $post->post_content.'';
        $post_thumbnail = get_the_post_thumbnail( get_the_ID() , array(400,400));
        //$extra_content .= ( ! empty( $post_thumbnail ) )?$post_thumbnail:'';
        
        $content = $new_content;
        //die($content);
    }
    return $content;
}


/*
 *  Add a category metabox in fiche editing, with only children of "fiches thematiques"
 */
add_action( 'add_meta_boxes', 'vdn_metabox_thematiques_fiche' );
function vdn_metabox_thematiques_fiche() {
    add_meta_box( 'after-title-help', 'Catégories thématiques', 'vdn_metabox_thematiques_content', array('fiche'), 'acf_after_title', 'high' );
}
function vdn_metabox_thematiques_content($post) {
    ?>
    <div id="categories-all" class="ui-tabs-panel">
        <ul id="categorychecklist" class="list:category categorychecklist form-no-clear">
            <?php
            $parent_category_slug = 'fiches-thematiques';
            $parent_category = get_category_by_slug($parent_category_slug);
            $thematiques_categories = get_categories( array(
                'parent' => $parent_category->term_id,
                'hide_empty' => false
            ));
            foreach($thematiques_categories as $thematiques_category) {
                $checked = (in_category($thematiques_category->slug))?"checked='checked'":'';
                echo "<li id='category-{$thematiques_category->term_id}' class='popular-category'>
            <label class='selectit'><input value='{$thematiques_category->term_id}' type='checkbox' name='post_thematiques_category[]' {$checked } />{$thematiques_category->name}</label>
        </li>";
            }
            ?>
        </ul>
    </div>
    <?php
}

/*
 * Attach fiche created by non-admin to 'Registered' read-group.
 * BE AWARE of low priority (100) set in add_action() to go after regular process
 */
add_action( 'save_post', function ($post_id) {
    $post_type = get_post_type($post_id);
    if( in_array($post_type, array('fiche')) ){
        if ( ! vdn_is_admin() ){
            $registered_group = Groups_Group::read_by_name( 'Registered' );
            //die('$registered_group='.print_r($registered_group, true));
            $update_result = Groups_Post_Access::update( array( 'post_id' => $post_id, 'groups_read' => array($registered_group->group_id) ) );
            //die(' registered_group : $update_result='.print_r($update_result, true));
        }
    }
},100 );

/*
 *  Handle POST values of vdn_metabox_thematiques_fiche metabox and update fiche
 */
add_action( 'save_post', 'vdn_update_thematiques_fiche' );
function vdn_update_thematiques_fiche($post_id) {
    $post_type = get_post_type($post_id);
    if( 'fiche' == $post_type ){
        if( isset($_POST['post_thematiques_category']) ){
            //echo ('<pre>'.print_r($_POST['post_thematiques_category'], true));
            $parent_category_slug = 'fiches-thematiques';
            $parent_category = get_category_by_slug($parent_category_slug);
            $thematiques_categories = get_categories( array('parent' => $parent_category->term_id) );

            // first remove all thematiques_categories from $_POST['post_category']
            $post_categories = wp_get_post_categories( $post_id);
            foreach($thematiques_categories as $thematiques_category) {
                if (($key = array_search($thematiques_category->term_id, $post_categories)) !== false) {
                    unset($post_categories[$key]);
                }
            }

            // then add $_POST['post_thematiques_category'] to $_POST['post_category']
            foreach($_POST['post_thematiques_category'] as $thematiques_category_id) {
                array_push($post_categories, $thematiques_category_id);
            }

            unset($_POST['post_thematiques_category']);
            wp_set_post_categories( $post_id, $post_categories, false );
        }
    }
}

/*
 * Add default read group "ReferentsClubs" to new fiche
 */
add_action( 'admin_head-post-new.php', 'set_fiche_default_read_group' );
function set_fiche_default_read_group() {
    global $post_type;
    if( 'fiche' == $post_type ){
        ?>
        <script type="text/javascript">
            jQuery(function() {
                setTimeout(function(){
                    jQuery("div.selectize-input").trigger('click');
                    setTimeout(function(){
                        jQuery("div.option:contains('ReferentsClubs')").trigger('click');
                        jQuery("div.selectize-input").trigger('click');
                    }, 1000)
                }, 2000)
            });
        </script>
        <?php
    }
}


/*
 * Send notification email when a fiche (and more) is published by a non-admin
 */
add_action( 'save_post', function ($post_id) {
    global $VDN_CONFIG;
    $post_type = get_post_type($post_id);
    if( in_array($post_type, array('fiche', 'post', 'tribe_events')) ){
        if( !vdn_is_admin() && !wp_is_post_revision($post_id) && !wp_is_post_autosave($post_id) && get_post_status($post_id)=='publish'  ) {
            if ( isset($VDN_CONFIG['user_action_notification_email']) && $VDN_CONFIG['user_action_notification_email']!='' ){
                $pto = get_post_type_object( $post_type );
                $post_type_name = ($pto==null)?'contenu':$pto->labels->singular_name;
                $author_id = get_post_field ('post_author', $post_id);
                $author = get_userdata($author_id);
                $post = get_post($post_id);
                $mail_content = "Bonjour,<br>
                l' utlisateur \"<a href='".get_site_url(null, '/user/'.$author->user_nicename)."'>".$author->display_name."</a>\" 
                vient de créer ou de modifier un(e) $post_type_name sur Voyageurs du Numérique :<br>
                <a href='".get_post_permalink($post_id)."'>{$post->post_title}</a>
                <br><br>
                Ceci est un mail automatique, aucune action n'est requise de votre part.";

                wp_mail( //$to, $subject, $message
                    $VDN_CONFIG['user_action_notification_email'],
                    "[VDN] Notification de nouvel(le) $post_type_name utilisateur : {$post->post_title}",
                    $mail_content,
                    array('Content-Type: text/html; charset=UTF-8')
                );
            }
        }
    }
});
