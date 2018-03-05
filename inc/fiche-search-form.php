<?php
/*
 * Add a vdn_fiche_search_form shortcode for /category/fiches-thematiques/...
 * Add an ajax target for tags autosuggest
 */

add_shortcode( 'vdn_fiche_search_form', 'vdn_fiche_search_form_shortcode' );
function vdn_fiche_search_form_shortcode() {
    ob_start();
    vdn_fiche_search_form_process();
    vdn_fiche_search_form_html();
    return ob_get_clean();
}
function vdn_fiche_search_form_process() {
    global $wp_query;
    if ( isset( $_POST['vdn_fiche_search_form_submitted'] ) ) {
        $search_args = vdn_build_wp_search_args_from_criteria($_POST);
        $wp_query = new WP_Query($search_args);
    }
}


function vdn_build_wp_search_args_from_criteria($criteria) {

    //die('get_query_var='.get_query_var('cat'));
    $public_search_args = array('relation' => 'OR' );
    foreach($criteria['Public'] as $k=>$v){  //array('enfants', 'adultes'),
        array_push($public_search_args, array(
            'key'	 	=> 'public',
            'value'	  	=> $criteria['Public'][$k],
            'compare' 	=> 'LIKE',
        ));
    }
    $search_args = array(
        'vdn_search_fiche' => true,
        'posts_per_page'=> -1,
        'post_type'		=> 'fiche',
        's'				=> $criteria['search_text'],
        'cat'		    => $criteria['search_category_id'],
        'meta_query'	=> array(
            'relation'		=> 'AND',
            $public_search_args,
            array(
                'key'	  	=> 'niveau',
                'value'	  	=> $criteria['Niveau'], //array('debutant', 'confirme'),
                'compare' 	=> 'IN',
            ),
            array(
                'key'	  	=> 'type',
                'value'	  	=> $criteria['Type'], //array('activite', 'outil'),
                'compare' 	=> 'IN',
            ),
        )
    );
    if(isset($criteria['search_only_bsf'])){
        $search_args['author_name']='BSF';
    }
    return $search_args;
}

function vdn_fiche_search_form_html() {
    ?>
    <form method="post">
        <?php
        $field_group = vdn_get_fiche_fields_group();
        $acfFields = ($field_group!=null)?$field_group['fields']:array();
        ?>
        <div class="row">
            <?php if( $acfFields ) foreach( $acfFields as $field ){
                if(in_array($field['type'], array('checkbox', 'select'))){
                    ?>
                    <div class="col-lg-3 col-sm-3">
                        <h3><?php echo $field['label']?></h3>
                        <?php
                        $fieldSlug = $field['label'];
                        $optionCount = 0;
                        foreach($field['choices'] as $k=>$v){
                            $checked = ((isset($_POST[$fieldSlug]) && in_array($k, $_POST[$fieldSlug]))||!isset($_POST[$fieldSlug]))?'checked':'';
                            $disabled = ''; //($fieldSlug=='Public')?'disabled':'';
                            echo "<input type='checkbox' $checked $disabled name='{$fieldSlug}[]' value='$k' id='search_{$fieldSlug}_{$k}'>&nbsp;";
                            echo "<label for='search_{$fieldSlug}_{$k}' $disabled>$v</label><br>";
                            $optionCount++;
                        }
                        ?>
                    </div>
                    <?php
                }
            }?>
            <div class="col-lg-3 col-sm-3">
                <h3>Contenu</h3>
                <?php
                $search_text_value = isset($_POST['search_text'])?$_POST['search_text']:'';
                $search_category_value = vdn_get_searched_category_id();
                echo "<input type='text' style='float:right;' name='search_text' id='search_text' value='$search_text_value'>";
                echo "<input type='hidden' name='search_category_id' value='$search_category_value'>";
                ;
                echo "<input type='checkbox' ".(isset($_POST['search_only_bsf'])?'checked':'')." name='search_only_bsf' value='search_only_bsf' id='search_only_bsf'>&nbsp;";
                echo "<label for='search_only_bsf' >Uniquement BSF</label><br>";
                ?>
                <input type="submit" style='float:right;' name="vdn_fiche_search_form_submitted" value="Rechercher">
                <!--<script>
                    // TODO : add ajax autocomplete from tags list
                    $("#search_text").autoSuggest(
                        "/?get_tags=1",
                        {selectedItemProp: "name", searchObjProps: "name"}
                    );
                </script>-->
                <script>
                    jQuery( function($) {
                        function split( val ) {
                            return val.split( /,\s*/ );
                        }
                        function extractLast( term ) {
                            return split( term ).pop();
                        }
                        $( "#search_text" )
                            // don't navigate away from the field on tab when selecting an item
                            .on( "keydown", function( event ) {
                                if ( event.keyCode === $.ui.keyCode.TAB &&
                                    $( this ).autocomplete( "instance" ).menu.active ) {
                                    event.preventDefault();
                                }
                            })
                            .autocomplete({
                                source: function( request, response ) {
                                    $.getJSON( ".?get_tags", {
                                        term: extractLast( request.term )
                                    }, response );
                                },
                                search: function() {
                                    // custom minLength
                                    var term = extractLast( this.value );
                                    if ( term.length < 1 ) {
                                        return false;
                                    }
                                },
                                focus: function() {
                                    // prevent value inserted on focus
                                    return false;
                                },
                                select: function( event, ui ) {
                                    var terms = split( this.value );
                                    // remove the current input
                                    terms.pop();
                                    // add the selected item
                                    terms.push( ui.item.value );
                                    // add placeholder to get the comma-and-space at the end
                                    terms.push( "" );
                                    this.value = terms.join( ", " );
                                    return false;
                                }
                            });
                    } );
                </script>
            </div>
        </div><!--/.row-->

    </form>
    <?php
}

/*
 * Gets a list of tags for ajax use in /category/fiches-thematiques/...
 */
if(isset($_GET['get_tags']) && isset($_GET['term'])){
    $output = array();
    $search_term = sanitize_title($_GET['term']);
    foreach(get_terms('post_tag') as $key => $term){
        if(strpos(sanitize_title($term->name), $search_term) !== false){
            //$output[$key]['value'] = $key;
            //$output[$key]['name'] = $term->name;
            array_push($output, $term->name);
        }
    }

    header("Content-type: application/json");
    echo json_encode($output);
    die();
}
