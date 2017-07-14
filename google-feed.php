<?php
/***********************************************************
 * Plugin Name: Sync WooCommerce Product feed to Google Shopping
 * Plugin URI: www.exportfeed.com
 * Description: Google's Feed of WooCommerce Product Feed Export :: <a target="_blank" href="http://www.exportfeed.com/tos/">How-To Click Here</a>
 * Author: ExportFeed.com
 * Version: 1.2.1
 * Author URI: www.exportfeed.com
 * License:  GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: google-merchant-strings
 * Authors: sabinthapa8, roshanbh
 * Note: The "core" folder is shared to the Joomla component.
 * Changes to the core, especially /core/data, should be considered carefully
 * license GNU General Public License version 3 or later; see GPLv3.txt
 ***********************************************************/
// Create a helper function for easy SDK access.
if (!defined('ABSPATH')) exit; // Exit if accessed directly

require_once ABSPATH . 'wp-admin/includes/plugin.php';
$plugin_version_data = get_plugin_data(__FILE__);
//current version: used to show version throughout plugin pages
define('GCPF_FEED_PLUGIN_VERSION', $plugin_version_data['Version']);
define('GCPF_PLUGIN_BASENAME', plugin_basename(__FILE__)); //cart-product-feed/cart-product-feed.php
define('GCPF_PATH', realpath(dirname(__FILE__)));
define('GCPF_URL', plugins_url() . '/' . basename(dirname(__FILE__)) . '/');
//functions to display cart-product-feed version and checks for updates
include_once('google-feed-information.php');

//action hook for plugin activation
register_activation_hook(__FILE__, 'gcpf_activate_plugin');
register_deactivation_hook(__FILE__, 'gcpf_deactivate_plugin');

global $gcp_feed_order, $gcp_feed_order_reverse;

require_once 'core/classes/cron.php';
require_once 'core/data/feedfolders.php';

if (get_option('gcp_feed_order_reverse') == '')
    add_option('gcp_feed_order_reverse', false);

if (get_option('gcp_feed_order') == '')
    add_option('gcp_feed_order', "id");

if (get_option('gcp_feed_delay') == '')
    add_option('gcp_feed_delay', "43200");

if (get_option('gcp_licensekey') == '')
    add_option('gcp_licensekey', "none");

if (get_option('gcp_localkey') == '')
    add_option('gcp_localkey', "none");
//***********************************************************
// cron schedules for Feed Updates
//***********************************************************

GCPF_PCPCron::doSetup();
GCPF_PCPCron::scheduleUpdate();

//***********************************************************
// Update Feeds (Cron)
//   2014-05-09 Changed to now update all feeds... not just Google Feeds
//***********************************************************

add_action('gcpf_update_cartfeeds_hook', 'gcpf_update_all_cart_feeds');

function gcpf_update_all_cart_feeds($doRegCheck = true, $feed_id = array())
{

    require_once 'google-feed-wpincludes.php'; //The rest of the required-files moved here
    require_once 'core/data/savedfeed.php';

    $reg = new GCPF_PLicense();
    if ($doRegCheck && ($reg->results["status"] != "Active"))
        return;

    do_action('load_cpf_modifiers');
    add_action('gcpf_get_feed_main_hook', 'gcpf_update_all_cart_feeds_step_2');
    do_action('gcpf_get_feed_main_hook', $feed_id);
}

function gcpf_update_all_cart_feeds_step_2($feed_id)
{
    global $wpdb;
    $feed_table = $wpdb->prefix . 'gcp_feeds';
    $where = '';
    if (is_array($feed_id) && !empty($feed_id)) {
        $feed_id = implode(',', $feed_id);
        $where = ' WHERE id IN ' . '(' . $feed_id . ') ';
    }
    $sql = 'SELECT id, type, filename FROM ' . $feed_table . $where;
    $feed_ids = $wpdb->get_results($sql);
    $savedProductList = null;

    //***********************************************************
    //Build stack of aggregate providers
    //***********************************************************
    $aggregateProviders = array();

    //***********************************************************
    //Main
    //***********************************************************
    foreach ($feed_ids as $index => $this_feed_id) {

        $saved_feed = new GCPF_PSavedFeed($this_feed_id->id);

        $providerName = $saved_feed->provider;

        //Skip any Aggregate Types
        if ($providerName == 'AggXml' || $providerName == 'AggXmlGoogle' || $providerName == 'AggCsv' || $providerName == 'AggTxt' || $providerName == 'AggTsv')
            continue;

        //Make sure someone exists in the core who can provide the feed
        $providerFile = 'core/feeds/' . strtolower($providerName) . '/feed.php';
        if (!file_exists(dirname(__FILE__) . '/' . $providerFile))
            continue;
        require_once $providerFile;

        //Initialize provider data
        $providerClass = 'GCPF_P' . $providerName . 'Feed';
        $x = new $providerClass();
        $x->aggregateProviders = $aggregateProviders;
        $x->savedFeedID = $saved_feed->id;

        $x->productList = $savedProductList;
        $x->getFeedData($saved_feed->category_id, $saved_feed->remote_category, $saved_feed->filename, $saved_feed);

        $savedProductList = $x->productList;
        $x->products = null;

    }

    foreach ($aggregateProviders as $thisAggregateProvider)
        $thisAggregateProvider->finalizeAggregateFeed();

}

//***********************************************************
// Links From the Install Plugins Page (WordPress)
//***********************************************************

if (is_admin()) {

    require_once 'google-feed-admin.php';
    $plugin = plugin_basename(__FILE__);
    add_filter("plugin_action_links_" . $plugin, 'gcpf_manage_feeds_link');

}

//***********************************************************
//Function to create feed generation link  in installed plugin page
//***********************************************************
function gcpf_manage_feeds_link($links)
{

    $settings_link = '<a href="admin.php?page=gcpf-feed-manage-page">Manage Feeds</a>';
    array_unshift($links, $settings_link);
    return $links;

}