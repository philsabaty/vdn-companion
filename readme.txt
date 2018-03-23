VDN-Companion

This wordpress plugin mainly deals with custom post types (CPT) used in VDN website. 

This plugin is meant to be used in association with VDN-theme :
https://github.com/philsabaty/vdn-theme


CPT defined in this plugin use custom fields, managed by ACF Plugin (embedded in /inc/libs). 
More informations here :
https://www.advancedcustomfields.com/resources/register-fields-via-php/

This plugin also relies on Ultimate Members plugin. It should be installed on your WP instance.
Settings are stored in ./ultimate-member-settings-backup.txt and should be imported to UM plugin.
https://ultimatemember.com/

Finally, it uses Groups plugin. No prior settings required
https://www.itthinx.com/plugins/groups/

-- V1.1 Changelog --
* Fixed possible division by zero in clubs map
* Added events map shortcode
* Added custom field "type" for CPT tribe_events
* Added custom field "GPS" for CPT tribe_venues
* Added event types support ad selector in event map
* Added html list of clubs in club map
* Fixed error preventing sending club validation email
* Made regular posts visible to everyone
* Improved club category management
* Club membership managed by user_meta (no more UM)


-- V1.2 Changelog --
* Added autocenter and multiple markers support on maps
* Added a config.php for misc settings
* New feature : Send notification email when a post is published by a non-admin
* Many settings moved to vdn-companion/config.php
* Improved PDF rendering
* Added coordinateurs BSF dispatch in contact form


-- V1.3 Changelog --
* Fixed bug sending undue club validation emails
* Added missing ACF keys in register_field_group()
* Added support for empty or single-location on events map
* Added new colors for events and fiches

-- v1.4 Changelog --
* Added various gps lookup fallbacks
* Added addslashes in clubs and events titles
* Google API pins moved from http to https
