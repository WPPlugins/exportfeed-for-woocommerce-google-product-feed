<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly
if (!is_admin()) {
    die('Permission Denied!');
}
update_option(sanitize_text_field($_POST['service_name']) . '_cp_' . sanitize_text_field($_POST['attribute']), sanitize_text_field($_POST['mapto']));
