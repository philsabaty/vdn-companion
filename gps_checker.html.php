<?php
/**
 * Created by PhpStorm.
 * User: psabaty
 * Date: 22/06/18
 * Time: 17:12
 */

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ERROR | E_WARNING | E_PARSE);

define(ABSPATH, dirname(__file__).'/../../../');
require_once( ABSPATH . 'wp-load.php' );


// ------------------------------------------------------
$rows = $wpdb->get_results( $wpdb->prepare(
    "select p.ID, p.post_type, p.post_title from wp_posts as p
      where p.post_type='tribe_events'
            and  ID not in (select post_id from wp_postmeta where meta_key = '_EventVenueID');
",
    //"select * from wp_posts LIMIT 10",
    [] ), ARRAY_A);
if( $rows ) {
    echo "<h4>Evènements sans lieu :</h4>";
    echo "<ul>";
    foreach( $rows as $row ) {
        echo "<li>{$row['post_title']} <a href='/wp-admin/post.php?post={$row['ID']}&action=edit'>modifier</a></li>";
        echo "";
    }
    echo "</ul>";
}else{
    echo "Tous les évènements ont un lieu.";
}

// ------------------------------------------------------
$rows = $wpdb->get_results( $wpdb->prepare(
    "select p.ID, p.post_type, p.post_title from wp_posts as p
      where p.post_type='tribe_venue'
            and  ID not in (select post_id from wp_postmeta where meta_key = 'coordonnees_gps');
",
    //"select * from wp_posts LIMIT 10",
[] ), ARRAY_A);
if( $rows ) {
    echo "<h4>Lieu d'évènements sans coordonnées GPS :</h4>";
    echo "<ul>";
    foreach( $rows as $row ) {
        echo "<li>{$row['post_title']} <a href='/wp-admin/post.php?post={$row['ID']}&action=edit'>modifier</a></li>";
        echo "";
    }
       echo "</ul>";
}else{
    echo "Tous les lieux d'évènements ont des coordoonées GPS.";
}
