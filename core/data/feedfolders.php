<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly
class GCPF_PFeedFolder
{

    /********************************************************************
     * feedURL is where the client should be sent to generate the new feed
     * It's unclear if it's still used
     ********************************************************************/

    public static function feedURL()
    {
        global $gfcore;
        $feedURL = 'feedURL' . $gfcore->callSuffix;
        return GCPF_PFeedFolder::$feedURL();
    }

    private static function feedURLJ()
    {
        global $gfcore;
        return $gfcore->siteHost . '/administrator/index.php?option=com_cartproductfeed&view=instantiatefeed';
    }

    private static function feedURLJH()
    {
        global $gfcore;
        return $gfcore->siteHost . '/administrator/index.php?option=com_cartproductfeed&view=instantiatefeed';
    }

    private static function feedURLJS()
    {
        global $gfcore;
        return $gfcore->siteHost . '/administrator/index.php?option=com_cartproductfeed&view=instantiatefeed';
    }

    private static function feedURLW()
    {
        global $gfcore;
        return $gfcore->siteHost;
    }

    private static function feedURLWe()
    {
        global $gfcore;
        return $gfcore->siteHost;
    }

    /********************************************************************
     * uploadFolder is where the plugin should make the file
     ********************************************************************/
    public static function uploadFolder()
    {
        global $gfcore;
        $uploadFolder = 'uploadFolder' . $gfcore->callSuffix;
        return GCPF_PFeedFolder::$uploadFolder();
    }

    private static function uploadFolderJ()
    {
        return JPATH_SITE . '/media/cart_product_feeds/';
    }

    private static function uploadFolderJH()
    {
        return JPATH_SITE . '/media/cart_product_feeds/';
    }

    private static function uploadFolderJS()
    {
        return JPATH_SITE . '/media/cart_product_feeds/';
    }

    private static function uploadFolderW()
    {
        $upload_dir = wp_upload_dir();
        return $upload_dir['basedir'] . '/cart_product_feeds/';
    }

    private static function uploadFolderWe()
    {
        $upload_dir = wp_upload_dir();
        return $upload_dir['basedir'] . '/cart_product_feeds/';
    }

    /********************************************************************
     * uploadRoot is where the plugin should make the file (same as uploadFolder)
     * but no "cart_product_feeds". Useful for ensuring folder exists
     ********************************************************************/

    public static function uploadRoot()
    {
        global $gfcore;
        $uploadRoot = 'uploadRoot' . $gfcore->callSuffix;
        return GCPF_PFeedFolder::$uploadRoot();
    }

    private static function uploadRootJ()
    {
        return JPATH_SITE . '/media/';
    }

    private static function uploadRootJH()
    {
        return JPATH_SITE . '/media/';
    }

    private static function uploadRootJS()
    {
        return JPATH_SITE . '/media/';
    }

    private static function uploadRootW()
    {
        $upload_dir = wp_upload_dir();
        return $upload_dir['basedir'];
    }

    private static function uploadRootWe()
    {
        $upload_dir = wp_upload_dir();
        return $upload_dir['basedir'];
    }

    /********************************************************************
     * URL we redirect the client to in order for the user to see the feed
     ********************************************************************/

    public static function uploadURL()
    {
        global $gfcore;
        $uploadURL = 'uploadURL' . $gfcore->callSuffix;
        return GCPF_PFeedFolder::$uploadURL();
    }

    private static function uploadURLJ()
    {
        return JURI::root() . 'media/cart_product_feeds/';
    }

    private static function uploadURLJH()
    {
        return JURI::root() . 'media/cart_product_feeds/';
    }

    private static function uploadURLJS()
    {
        return JURI::root() . 'media/cart_product_feeds/';
    }

    private static function uploadURLW()
    {
        $upload_dir = wp_upload_dir();
        return $upload_dir['baseurl'] . '/cart_product_feeds/';
    }

    private static function uploadURLWe()
    {
        $upload_dir = wp_upload_dir();
        return $upload_dir['baseurl'] . '/cart_product_feeds/';
    }

}