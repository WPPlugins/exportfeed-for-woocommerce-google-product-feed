<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly
if (!is_admin()) {
    die('Permission Denied!');
}
define('XMLRPC_REQUEST', true);
ob_start(null);

function gcpf_safeGetPostData($index)
{
    if (isset($_POST[$index]))
        return $_POST[$index];
    else
        return '';
}

$feedIdentifier = gcpf_safeGetPostData('feed_identifier');

ob_clean();
echo get_option('gcpf_feedActivity_' . $feedIdentifier);