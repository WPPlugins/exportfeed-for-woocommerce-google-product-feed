<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly
require_once 'core/classes/cron.php';
require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
//callback function
function gcpf_activate_plugin()
{

    global $wpdb;
    $activation_date = date('Y-m-d');
    update_option('gcpf-installation-date', $activation_date);

    $table_name = $wpdb->prefix . "gcp_feeds";
    $sql = "CREATE TABLE IF NOT EXISTS `$table_name` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `category` varchar(250) DEFAULT NULL,
              `remote_category` text,
              `filename` varchar(250) DEFAULT NULL,
              `url` text,
              `type` varchar(100) DEFAULT NULL,
              `own_overrides` int(11) DEFAULT NULL,
              `feed_overrides` text,
              `product_count` int(11) DEFAULT NULL,
              `feed_errors` text,
              `feed_title` varchar(250) DEFAULT NULL,
              `feed_type` int(11) DEFAULT NULL,
              `product_details` text,
              PRIMARY KEY (`id`)
            )";
    //)" ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;
    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
        $wpdb->query($sql);
    }
    // ading custom table

    $table_name = $wpdb->prefix . "gcpf_custom_products";
        $sql = "CREATE TABLE IF NOT EXISTS `$table_name` (
              `id` INT(11) NOT NULL AUTO_INCREMENT,
              `product_title` TEXT,
              `category_name` VARCHAR(255) DEFAULT NULL,
              `product_type` VARCHAR(255) DEFAULT NULL,
              `product_attributes` TEXT,
              `product_variation_ids` VARCHAR(255) DEFAULT NULL,
              `remote_category` TEXT,
              `category` INT(11) DEFAULT NULL,
              `product_id` INT(11) DEFAULT NULL,
              `own_overides` TEXT,
              `feed_overides` TEXT,
              PRIMARY KEY (`id`),
              UNIQUE KEY `cpf_custom` (`id`,`remote_category`(100),`product_id`)
            )";
    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
        $wpdb->query($sql);
    }
}


function gcpf_deactivate_plugin()
{

    $next_refresh = wp_next_scheduled('gcpf_update_cartfeeds_hook');
    if ($next_refresh)
        wp_unschedule_event($next_refresh, 'gcpf_update_cartfeeds_hook');

}