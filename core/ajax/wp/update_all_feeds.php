<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly
if (!is_admin()) {
    die('Permission Denied!');
}
$feed_id = isset($_POST['feed_id']) ? sanitize_text_field($_POST['feed_id'] ): '';
gcpf_update_all_cart_feeds(false, $feed_id);

echo 'Update successful';
