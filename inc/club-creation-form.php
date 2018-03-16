<?php
/*
 * Add a vdn_club_creation_form shortcode for /fonder-un-club/
 */

add_shortcode( 'vdn_club_creation_form', 'vdn_club_creation_form_shortcode' );
function vdn_club_creation_form_shortcode() {
    global $VDN_CONFIG;
    $coordinateurs_bsf = $VDN_CONFIG['coordinateurs_bsf'];
    ob_start();
    $mail_sent = vdn_club_creation_form_process($coordinateurs_bsf);
    if($mail_sent) {
        vdn_club_creation_confirmed_html();
    }else{
        if(is_user_logged_in()){
            vdn_club_creation_form_html($coordinateurs_bsf);
        }else{
            vdn_club_creation_notloggedin_html();
        }
    }
    return ob_get_clean();
}

function vdn_create_draft_club($args) {
    $current_user = wp_get_current_user();
    $title = $args['nom_club'];
    $slug = sanitize_title($title);
    $welcome_text = "
        Ce club vient d'être créé !
    ";
    if( vdn_get_club_by_slug($slug)!=null ) {
        $count = 1;
        while( vdn_get_club_by_slug($slug.(++$count))!=null ) {
        }
        $slug = $slug.$count;
    } 
    
    $post_id = wp_insert_post(array(
        'comment_status'	=>	'closed',
        'ping_status'		=>	'closed',
        'post_author'		=>	$current_user->ID,
        'post_name'	        =>	$slug,
        'post_title'		=>	$title,
        'post_status'		=>	'draft',
        'post_type'		    =>	'club'
    ));
    update_post_meta($post_id, 'presentation', $welcome_text);
    update_post_meta($post_id, 'structure', $args['structure']);
    update_post_meta($post_id, 'ville', $args['ville']);
    update_post_meta($post_id, 'adresse', $args['adresse']);
    update_post_meta($post_id, 'code_postal', $args['code_postal']);
    update_post_meta($post_id, 'nom_du_referent', $args['nom_du_referent']);
    update_post_meta($post_id, 'prenom_du_referent', $args['prenom_du_referent']);
    update_post_meta($post_id, 'contact_du_referent', $args['email_du_referent']);
    update_post_meta($post_id, 'telephone_du_referent', $args['telephone_du_referent']);
    update_post_meta($post_id, 'regularite_des_ateliers', $args['regularite_des_ateliers']);
    update_post_meta($post_id, 'site_web_structure', $args['site_web_structure']);

    // update profile if information missing / TODO : not working
    $userdata = get_userdata($current_user->ID);
    if($userdata->first_name=='' && $args['prenom_du_referent']!=''){
        //update_user_meta($current_user->ID, 'first_name', $args['prenom_du_referent']);
        wp_update_user( array( 'ID' => $current_user->ID, 'first_name' => $args['prenom_du_referent'] ) );
    }
    if($userdata->last_name=='' && $args['nom_du_referent']!='') {
        //update_user_meta($current_user->ID, 'last_name', $args['nom_du_referent']);
        wp_update_user( array( 'ID' => $current_user->ID, 'last_name' => $args['nom_du_referent'] ) );
    }
    if(get_user_meta($current_user->ID, 'code_postal', true)=='' && $args['code_postal']!='') {
        update_user_meta($current_user->ID, 'code_postal', $args['code_postal']);
    }
    return $post_id;
}

/**
 * @return bool : true if mail sent (for confirmation message), false otherwise (for form)
 */
function vdn_club_creation_form_process($coordinateurs_bsf) {
    global $wp_query;

    if ( isset( $_POST['vdn_club_creation_form_submitted'] ) ) {
        //echo ('<pre>'.print_r($_POST, true).'</pre>');
        $post_id = vdn_create_draft_club($_POST);
        $email_content = vdn_get_club_creation_email_content($post_id, $_POST['complement']);
        @$coordinateur = $coordinateurs_bsf[$_POST['zone_geo']];
        $to = ($coordinateur!=null)?$coordinateur['email'] : get_bloginfo('admin_email');
        wp_mail( //$to, $subject, $message
            $to,
            "[VDN] Demande de création de club",
            $email_content,
            array('Content-Type: text/html; charset=UTF-8')
        );
        return true;
    }
    return false;
}
function vdn_get_club_creation_email_content($club_id, $extra_msg) {
    $club = get_post($club_id);
    $code_postal = get_post_meta($club_id, 'code_postal', true);
    $author_id = get_post_field ('post_author', $club_id);
    $author = get_userdata($author_id);
    $msg = "
    Un utilisateur vient de faire une demande de création de club.<br>
    Le club a été créé sous forme de brouillon sur le site des Voyageurs du Numérique.<br>
    Pour devenir actif, ce club nécésite <b>une action de publication de votre part</b><br><br>
    Utilisateur : <a href='".get_site_url(null, '/user/'.$author->user_nicename)."'>".$author->display_name."</a><br>
    Code postal : $code_postal<br>
    Club à valider (lien vers l'administration du site) : <a href='".get_site_url(null, '/wp-admin/post.php?post='.$club_id.'&action=edit')."'>{$club->post_title}</a><br>
    ";
    if($extra_msg!=''){
        $msg .= "L'utilisateur à laissé ce message : <br><pre>$extra_msg</pre>";
    }
    $msg .= "<br><br>Cet email à été généré automatiquement par le site Voyageurs Du Numérique. Merci de ne pas y répondre.";
    
    return $msg;
}


function vdn_get_club_validation_email_content($club_id) {
    $club = get_post($club_id);
    $author_id = get_post_field ('post_author', $club_id);
    $author = get_userdata($author_id);
    $msg = "
Bonjour {$author->display_name}, <br>
Félicitation, votre demande de création de club sur <a href='".get_site_url(null, '/')."'>Voyageurs Du Numérique</a> a été validée ! <br>
<br>
    Votre club est visible depuis ce lien : <strong><a href='".get_site_url(null, '/club/'.$club->post_name)."'>".get_site_url(null, '/club/'.$club->post_name)."</a></strong><br>
    <br>
    <b>Vous êtes désormais référent(e) club</b> : Une fois <a href='".get_site_url(null, '/login')."'>connecté(e) avec votre compte</a>, Vous avez accès à 
    <a href='".get_site_url(null, '/les-ressources')."'>l'ensemble des ressources des Voyageurs du Numérique</a>
    pour préparer des ateliers.
    <br><br>
    Nous vous invitons à référencer <a href='".get_site_url(null, '/events')."'>les ateliers et événements</a> organisés par votre structure, 
    à raconter vos activités sur <a href='".get_site_url(null, '/events')."'>le blog</a> et à partager vos expériences 
    sur <a href='https://www.facebook.com/lesvoyageursdunumerique/'>le groupe Facebook des Voyageurs du Numériques</a>.
    <br><br>
    Le tableau de bord, bientôt disponible, vous permettra de suivre les activités de tous les membres du club. <br>
    <br><br>
    N'hésitez pas à <a href='".get_site_url(null, '/ecrivez-nous')."'>nous contacter</a> pour être accopagné(e) dans la vie de votre club. 
    <br>Nous restons à votre entière disposition si vous avez des questions.
    <br>Bien cordialement,
    <br><br>
    L'équipe des Voyageurs Du Numérique.
    ";
    
    return $msg;
}


function vdn_club_creation_confirmed_html() {
    ?>
    <div style="text-align:center; font-size:17px;color:#666">
        <strong style="font-size:20px">Merci !</strong><br><br>
        Votre demande de création de club a bien été envoyée. <br>
        Notre coordinateur régional référent va revenir vers vous pour échanger sur le projet de
        votre club et voir comment nous pouvons vous accompagner dans la mise en place de vos
        activités. <br><br>
        <a href="<?php echo site_url()?>" class="" style="color:#5171b6">Retour à l'accueil</a>
    </div>
    <?php
}
function vdn_club_creation_notloggedin_html() {
    ?>
    <div style="text-align:center; font-size:17px;color:#666">
        Pour faire une demande de création de club, <br>
        vous devez auparavant <a href="<?php echo site_url('/login')?>" style="color:#ea5951">vous connecter</a>, <br>
        ou <a href="<?php echo site_url('/register')?>" style="color:#ea5951">créer un compte</a> si vous n'en possédez pas. <br>
      
    </div>
    <?php
}


function vdn_club_creation_form_html($coordinateurs_bsf) {
    // DEBUG
    //    $current_user = wp_get_current_user();
    //    $userdata = get_userdata($current_user->ID);
    //    //$res = update_user_meta($current_user->ID, 'first_name', 'Test Prenom 4');
    //    $res = wp_update_user( array( 'ID' => $current_user->ID, 'first_name' =>'Test Prenom 5' ) );
    //    $res = get_user_meta($current_user->ID, 'first_name', false);
    //    $res = $userdata->first_name;
    //    print_r($res);
    // /DEBUG
    
    $form_values = $_POST;
    $current_user = wp_get_current_user();
    $userdata = get_userdata($current_user->ID);
    if(get_user_meta($current_user->ID, 'first_name', true)!='' && $form_values['prenom_du_referent']=='') {
        $form_values['prenom_du_referent'] = get_user_meta($current_user->ID, 'first_name', true);
    }
    if(get_user_meta($current_user->ID, 'last_name', true)!='' && $form_values['nom_du_referent']=='') {
        $form_values['nom_du_referent'] = get_user_meta($current_user->ID, 'last_name', true);
    }
    if($form_values['email_du_referent']=='') {
        $form_values['email_du_referent'] = $userdata->user_email;
    }
    if(get_user_meta($current_user->ID, 'structure', true)!='' && $form_values['structure']=='') {
        $form_values['structure'] = get_user_meta($current_user->ID, 'structure', true);
    }
    if(get_user_meta($current_user->ID, 'code_postal', true)!='' && $form_values['code_postal']=='') {
        $form_values['code_postal'] = get_user_meta($current_user->ID, 'code_postal', true);
    }
    if(get_user_meta($current_user->ID, 'ville', true)!='' && $form_values['ville']=='') {
        $form_values['ville'] = get_user_meta($current_user->ID, 'ville', true);
    }
    if(get_user_meta($current_user->ID, 'adresse', true)!='' && $form_values['adresse']=='') {
        $form_values['adresse'] = get_user_meta($current_user->ID, 'adresse', true);
    }
    
    ?>
    <form method="post" class="vdn_club_creation_form">

        <fieldset>
            <legend>Votre club</legend><br>
            <p><label> Nom souhaité pour le club <small>(obligatoire)</small><br>
                    <span class=" nom_club">
                        <input name="nom_club" value="<?php echo $form_values['nom_club']?>" required="required" type="text">
                    </span>
                </label>
            </p>
            <p><label> Régularité envisagée pour les ateliers<br>
                    <span class=" regularite_des_ateliers">
                        <input name="regularite_des_ateliers" value="<?php echo $form_values['regularite_des_ateliers']?>" type="text">
                    </span>
                </label>
            </p>
            <!--
            <p><label> Coordinateur BSF à contacter<br>
                    <span class=" coordinateur">
                        <select name="coordinateur">
                            <option value="Je ne sais pas">Je ne sais pas</option>
                            <option value="Région grand-Est">Région grand-Est</option>
                            <option value="Région parisienne">Région parisienne</option>
                            <option value="liste... (à venir)">liste... (à venir)</option>
                            <option value="Coordinateur national">Coordinateur national</option>
                        </select>
                    </span>
                </label>
            </p>-->
        </fieldset>
        <fieldset>
            <legend>Référent <small>(c'est vous !)</small></legend><br>
    
            <p><label> Prénom du référent<br>
                    <span class=" prenom_referent">
                        <input name="prenom_du_referent" value="<?php echo $form_values['prenom_du_referent']?>"  type="text">
                    </span> </label></p>
            <p><label> Nom du référent <small>(obligatoire)</small><br>
                    <span class=" nom_du_referent">
                        <input name="nom_du_referent" value="<?php echo $form_values['nom_du_referent']?>" required="required" type="text">
                    </span> </label></p>
            <p><label> Adresse mail <small>(obligatoire)</small><br>
                    <span class=" email_du_referent">
                        <input name="email_du_referent" value="<?php echo $form_values['email_du_referent']?>" required="required" type="email">
                    </span>
                    <br>
                </label>
            </p>
            <p><label> Téléphone<br>
                    <span class=" telephone_du_referent">
                        <input name="telephone_du_referent" value="<?php echo $form_values['telephone_du_referent']?>" type="text">
                    </span>
                </label>
            </p>
        </fieldset>
        <fieldset>
            <legend>Structure de rattachement </legend><br>
            
            <p>
                <label> Structure <small>(obligatoire, l'association ou autre pour laquelle vous intervenez)</small>
                    <br>
                    <span class=" nom_structure">
                        <input name="structure" value="<?php echo $form_values['structure']?>" required="required" type="text"></span>
                </label>
            </p>
            <p><label> Ville <small>(obligatoire)</small><br>
                    <span class=" ville">
                        <input name="ville" value="<?php echo $form_values['ville']?>" required="required" type="text">
                    </span>
                </label>
            </p>
            <p><label> Code postal <small>(obligatoire)</small><br>
                    <span class=" code_postal">
                        <input name="code_postal" value="<?php echo $form_values['code_postal']?>" required="required" type="text">
                    </span>
                </label></p>
            <p><label> Adresse <br>
                    <span class=" adresse">
                        <input name="adresse" value="<?php echo $form_values['adresse']?>" type="text">
                    </span>
                </label>
            </p>
            <p><label> Site web<br>
                    <span class=" site_web_structure">
                        <input name="site_web_structure" value="<?php echo $form_values['site_web_structure']?>" type="text">
                    </span>
                </label>
            </p>
        </fieldset>
        <fieldset>
            <p><label> Si vous le souhaitez, vous pouvez nous laisser plus d'informations :<br>
                    <span class=" complement">
                        <textarea name="complement" cols="40" rows="10"><?php echo $form_values['complement']?></textarea>
                    </span>
                </label>
            </p>
            <p><label> Zone géographique <small>(obligatoire, pour vous attribuer un coordinateur régional)</small><br>
                    <span class=" complement">
                        <select name="zone_geo" required="required">
                            <?php 
                            foreach($coordinateurs_bsf as $k=>$coordinateur){
                                echo "<option value='$k'>{$coordinateur['label']}</option>";
                            }
                            ?>
                        </select>
                    </span>
                </label>
            </p>
            <p>
                <input value="Envoyer" name="vdn_club_creation_form_submitted" type="submit">
                
            </p>
        </fieldset>
    </form>
    <?php
}


