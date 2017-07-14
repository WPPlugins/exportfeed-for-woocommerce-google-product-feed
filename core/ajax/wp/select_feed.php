<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly
if (!is_admin()) {
    die('Permission Denied!');
}

require_once dirname(__FILE__) . '/../../data/feedcore.php';
require_once dirname(__FILE__) . '/../../classes/dialogbasefeed.php';
require_once dirname(__FILE__) . '/../../classes/providerlist.php';

do_action('load_cpf_modifiers');

global $gfcore;
$gfcore->trigger('cpf_init_feeds');

add_action('gcpf_select_feed_main_hook', 'gcpf_select_feed_main');
do_action('gcpf_select_feed_main_hook');

function gcpf_select_feed_main()
{

    $feedType = $_POST['feedtype'];

    if (strlen($feedType) == 0)
        return;

    $inc = dirname(__FILE__) . '/../../feeds/' . strtolower($feedType) . '/dialognew.php';
    $feedObjectName = $feedType . 'Dlg_GCPF';

    if (file_exists($inc))
        include_once $inc;
    $f = new $feedObjectName();
    echo $f->mainDialog();
}
