<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly
if (!is_admin()) {
    die('Permission Denied!');
}
require_once dirname(__FILE__) . '/../../data/feedcore.php';

global $wpdb;
$providerName = sanitize_text_field($_POST['service_name']);

$sql = $wpdb->prepare("
			SELECT * FROM $wpdb->options
			WHERE $wpdb->options.option_name LIKE '%s'", like_escape($providerName) . '_cp_%');

$mappings = $wpdb->get_results($sql);
foreach ($mappings as $this_option)
    delete_option($this_option->option_name);

echo "1";
