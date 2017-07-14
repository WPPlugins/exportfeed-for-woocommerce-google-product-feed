<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly
if (!is_admin()) {
    die('Permission Denied!');
}

define('XMLRPC_REQUEST', true);
//ob_start(null, 0, PHP_OUTPUT_HANDLER_FLUSHABLE | PHP_OUTPUT_HANDLER_CLEANABLE);
ob_start(null);

function gcpf_safeGetPostData($index)
{
    if (isset($_POST[$index]))
        return $_POST[$index];
    else
        return '';
}

function gcpf_doOutput($output)
{
    ob_clean();
    echo json_encode($output);
}

require_once dirname(__FILE__) . '/../../../google-feed-wpincludes.php';

do_action('load_cpf_modifiers');
global $gfcore;
$gfcore->trigger('cpf_init_feeds');

add_action('gcpf_get_feed_main_hook', 'gcpf_get_feed_main');
do_action('gcpf_get_feed_main_hook');

function gcpf_get_feed_main()
{

    $requestCode = gcpf_safeGetPostData('provider');
    $file_name = gcpf_safeGetPostData('file_name');
    $feedIdentifier = gcpf_safeGetPostData('feed_identifier');
    $saved_feed_id = gcpf_safeGetPostData('feed_id');
    $feed_list = gcpf_safeGetPostData('feed_ids'); //For Aggregate Feed Provider
    $feedLimit = gcpf_safeGetPostData('feedLimit');
    $output = new stdClass();
    $output->url = '';

    if (!($file_name)) {
        $output->errors = 'Error: Please mention file name for the feed';
        gcpf_doOutput($output);
        return;
    }


    // Check if form was posted and select task accordingly
    $dir = GCPF_PFeedFolder::uploadRoot();
    if (!is_writable($dir)) {
        $output->errors = "Error: $dir should be writeable";
        gcpf_doOutput($output);
        return;
    }
    $dir = GCPF_PFeedFolder::uploadFolder();
    if (!is_dir($dir)) {
        mkdir($dir);
    }
    if (!is_writable($dir)) {
        $output->errors = "Error: $dir should be writeable";
        gcpf_doOutput($output);
        return;
    }

    $providerFile = 'feeds/' . strtolower($requestCode) . '/feed.php';

    if (!file_exists(dirname(__FILE__) . '/../../' . $providerFile))
        if (!class_exists('GCPF_P' . $requestCode . 'Feed')) {
            $output->errors = 'Error: Provider file not found.';
            gcpf_doOutput($output);
            return;
        }

    $providerFileFull = dirname(__FILE__) . '/../../' . $providerFile;
    if (file_exists($providerFileFull))
        require_once $providerFileFull;

    //Load form data
    $file_name = sanitize_title_with_dashes($file_name);
    if ($file_name == '')
        $file_name = 'feed' . rand(10, 1000);

    $saved_feed = null;
    if ((strlen($saved_feed_id) > 0) && ($saved_feed_id > -1)) {
        require_once dirname(__FILE__) . '/../../data/savedfeed.php';
        $saved_feed = new GCPF_PSavedFeed($saved_feed_id);
    }

    $providerClass = 'GCPF_P' . $requestCode . 'Feed';
    $x = new $providerClass;

    $x->feed_list = $feed_list; //For Aggregate Provider only
    if (strlen($feedIdentifier) > 0)
        $x->activityLogger = new GCPF_PFeedActivityLog($feedIdentifier);
    $x->getCustomFeedData($file_name, $saved_feed);

    if ($x->success)
        $output->url = GCPF_PFeedFolder::uploadURL() . $x->providerName . '/' . $file_name . '.' . $x->fileformat;
    $output->errors = $x->getErrorMessages();

    gcpf_doOutput($output);
}