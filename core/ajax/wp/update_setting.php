<?php

/********************************************************************
 * Version 2.1
 * AJAX script updates a setting
 * Copyright 2014 Purple Turtle Productions. All rights reserved.
 * license    GNU General Public License version 3 or later; see GPLv3.txt
 * By: Keneto 2014-05-09
 * 2014-08 Included code to support Google trusted stores
 ********************************************************************/
if (!defined('ABSPATH')) exit; // Exit if accessed directly
if (!is_admin()) {
    die('Permission Denied!');
}
if (!isset($_POST['setting']) || !isset($_POST['value'])) {
    echo 'Error in setting';
    return;
}

require_once dirname(__FILE__) . '/../../classes/cron.php';
require_once dirname(__FILE__) . '/../../data/feedcore.php';

$setting = $_POST['setting'];
if (isset($_POST['feedid']))
    $feedid = $_POST['feedid'];
else
    $feedid = '';
$value = $_POST['value'];

//Don't update here - security issue would allow any option to be updated
//Only update within an if()
if ($setting == 'gcp_feed_delay') {

    update_option($setting, $value);

    //Is this event scheduled?
    $next_refresh = wp_next_scheduled('gcpf_update_cartfeeds_hook');
    if ($next_refresh)
        wp_unschedule_event($next_refresh, 'gcpf_update_cartfeeds_hook');
    wp_schedule_event(time(), 'refresh_interval', 'gcpf_update_cartfeeds_hook');
}

if ($setting == 'gts_licensekey')
    update_option($setting, $value);
if ($setting == 'gcp_licensekey'){
	global $gfcore;
	$license_key_id = $gfcore->checkLicense($value);
	if($license_key_id){
		echo "License key already in use";
		return;
	}else{
		update_option($setting, $value);
	}
}


//Some PHPs don't return the post correctly when it's long data
if (strlen($setting) == 0) {
    $lines = explode('&', file_get_contents("php://input"));
    foreach ($lines as $line) {
        if ((strpos($line, 'feedid') == 0) && (strlen($feedid) == 0))
            $feedid = substr($line, 7);
        if ((strpos($line, 'setting') == 0) && (strlen($setting) == 0))
            $setting = substr($line, 8);
    }
}

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
        update_option($target . '-google-feed-settings', $postdata);
    else {
        global $wpdb;
        $feed_table = $wpdb->prefix . 'gcp_feeds';
        $data = array(
            'own_overrides'=> 1,
            'feed_overrides'=>$postdata
        );
        $wpdb->update($feed_table,$data,['id'=>$feedid]);
    }
}

echo 'Updated.';
