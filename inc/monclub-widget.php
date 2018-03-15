<?php
/**
 * Add a Club widget for sidebar
 */

class monclub_widget extends WP_Widget {

    function __construct() {
        parent::__construct(

        // Base ID of your widget
            'monclub_widget',

            // Widget name will appear in UI
            __('Widget Mon club', 'monclub_widget_domain'),

            // Widget description
            array( 'description' => __( 'Affiches les liens club et profil pour les utilisateurs connectés', 'monclub_widget_domain' ), )
        );
    }

    // Creating widget front-end
    public function widget( $args, $instance ) {
        $title = apply_filters( 'widget_title', $instance['title'] );

        // before and after widget arguments are defined by themes
        echo $args['before_widget'];
        if ( ! empty( $title ) ){
            echo $args['before_title'] . $title . $args['after_title'];
        }
        // This is where you run the code and display the output
        $output = '<ul>';
        $current_user = wp_get_current_user();
        if( is_user_logged_in() ){
            $output .= '<li><a href="'.get_site_url(null, '/user/').'">Mon profil ('.$current_user->display_name.')</a>'
                .'<br><small><a href="'.get_site_url(null, '/logout/').'">Me déconnecter</a></small>'
                .'</li>';
        }else{
            $output .= '<li><a href="'.get_site_url(null, '/login/').'">Connexion</a></li>';
        }
        if( is_user_logged_in() ){
            $club_slug = get_user_club();
            if($club_slug != null){
                $club = vdn_get_club_by_slug($club_slug);
                $output .= '<li><a href="'.get_site_url(null, '/club/'.$club_slug).'">Mon club ('.$club->post_title.')</a></li>';
            }else{
                $output .= '<li><a href="'.get_site_url(null, '/les-clubs/').'">Trouver un club</a></li>';

            }
            //$output .= '<li></li>';   
        }else{
        }
        $output .= '</ul>';
        echo $output;
        echo $args['after_widget'];
        $cu = get_current_user_id ();
        $um = get_user_meta ($cu);
        //var_dump ($um);
    }

    // Widget Backend 
    public function form( $instance ) {
        if ( isset( $instance[ 'title' ] ) ) {
            $title = $instance[ 'title' ];
        }
        else {
            $title = __( 'New title', 'monclub_widget_domain' );
        }
        // Widget admin form
        ?>
        <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
        </p>
        <?php
    }

    // Updating widget replacing old instances with new
    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
        return $instance;
    }
}

function monclub_load_widget() {
    register_widget( 'monclub_widget' );
}
add_action( 'widgets_init', 'monclub_load_widget' );
