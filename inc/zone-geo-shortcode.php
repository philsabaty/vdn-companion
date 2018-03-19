<?php
/*
 * Add a zone_geo_selector shortcode for /ecrivez-nous/
 */

add_shortcode( 'zone_geo_selector', 'zone_geo_selector_shortcode' );
function zone_geo_selector_shortcode() {
    global $VDN_CONFIG;
    $coordinateurs_bsf = $VDN_CONFIG['coordinateurs_bsf'];
    ob_start();
    ?>
    <script type="text/javascript">
        jQuery(function() {
            var newOptions = {
                <?php
                foreach($coordinateurs_bsf as $k=>$coordinateur){
                    echo "\"{$coordinateur['label']}\": \"{$coordinateur['email']}\", ";
                }
                ?>
            };
            
            var zone_geo_select = jQuery("select[name='zone_geo']");
            zone_geo_select.empty(); // remove old options
            jQuery.each(newOptions, function(key,value) {
                zone_geo_select.append(jQuery("<option></option>")
                    .attr("value", value).text(key));
            });
        });
    </script>
    <?php
    
    return ob_get_clean();
}
