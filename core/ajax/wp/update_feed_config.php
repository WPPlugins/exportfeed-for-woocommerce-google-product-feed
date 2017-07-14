<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly
if (!is_admin()) {
    die('Permission Denied!');
}
require_once dirname(__FILE__) . '/../../classes/cron.php';
require_once dirname(__FILE__) . '/../../../google-feed-wpincludes.php';

function gcpf_safeGetPostData($index)
{
    if (isset($_POST[$index]))
        return $_POST[$index];
    else
        return '';
}

add_action('gcpf_get_feed_config_hook', 'gcpf_get_feed_config');
do_action('gcpf_get_feed_config_hook');

function gcpf_get_feed_config()
{
    //print_r(array_slice(($_POST['arr']) , 4 ));
    global $gfcore;

    $feedid = gcpf_safeGetPostData('feedid');
    $setting = (gcpf_safeGetPostData('setting'));
    $merchant_attr = (gcpf_safeGetPostData('cpf_merchant_attr'));
    $cpf_prefix = (gcpf_safeGetPostData('cpf_feed_prefix'));
    $cpf_suffix = gcpf_safeGetPostData('cpf_feed_suffix');
    $cpf_feed_type = gcpf_safeGetPostData('cpf_feed_type');
    $cpf_feed_value_default = gcpf_safeGetPostData('cpf_feed_value_default');
    $cpf_feed_value_custom = gcpf_safeGetPostData('cpf_feed_value_custom');

    $arr = array();
    foreach ($merchant_attr as $key => $value) {
        $arr[$key]['merchant_attr'] = $value;
    }
    foreach ($cpf_prefix as $key => $value) {
        $arr[$key]['cpf_prefix'] = $value;
    }

    foreach ($cpf_suffix as $key => $value) {
        $arr[$key]['cpf_suffix'] = $value;
    }
    foreach ($cpf_feed_type as $key => $value) {
        $arr[$key]['cpf_feed_type'] = $value;
    }
    foreach ($cpf_feed_value_default as $key => $value) {
        $arr[$key]['cpf_feed_value_default'] = $value;
    }

    foreach ($cpf_feed_value_custom as $key => $value) {
        $arr[$key]['cpf_feed_value_custom'] = $value;
    }

    /*
        1.to add the default value to the products
            setAttributeDefault attribute_name as "value"
            Example: setAttributeDefault brand as "Studio Lilesadi"
        2.to map the value of one attribute to another
            mapAttribute attribute1 to attribute2
            Example: mapAttribute brand to Brand_name
        3. delete the attribute from the feed which you donot want to include
            deleteAttribute attribute_name
            example: deleteAttribute regular_price
        4. set google_exact_title to true  //for google only
        5. $max_custom_field = 500
        6. limitOutput FROM low TO high
            example: limitOutput FROM 0 TO 5000
        7.  mapTaxonomy source as attribute
            example: mapTaxonomy brand as g:brand
     */


    if (strpos($setting, 'cp_advancedFeedSetting') !== false) {

        //$value may get truncated on an & because $_POST can't parse
        //so pull value manually
        $postdata = file_get_contents("php://input");
        $i = strpos($postdata, '&value=');
        if ($i !== false)
            $postdata = substr($postdata, $i + 7);
        //Strip the provider name out of the setting
        $target = substr($setting, strpos($setting, '-') + 1);
        //Save new advanced setting
        if (strlen($feedid) == 0)
            update_option($target . '-google-feed-settings-custom', $arr);
        else {
            global $wpdb;
            $feed_table = $wpdb->prefix . 'gcp_feeds';
            $sql = "
				UPDATE $feed_table 
				SET
					`own_overrides`=1,
					`feed_overrides`='$postdata'
				WHERE `id`=$feedid";
            $wpdb->query($sql);
        }
    }

    echo 'Updated.';
}




