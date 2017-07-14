<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly
class GCPF_PFeedActivityLog
{

    function __construct($feedIdentifier = '')
    {
        //When instantiated (as opposed to static calls) it means we need to log the phases
        //therefore, save the feedIdentifier
        $this->feedIdentifier = $feedIdentifier;
    }

    function __destruct()
    {
        global $gfcore;
        if (!empty($gfcore) && (strlen($gfcore->callSuffix) > 0)) {
            $deleteLogData = 'deleteLogData' . $gfcore->callSuffix;
            $this->$deleteLogData();
        }
    }

    /********************************************************************
     * Add a record to the activity log for "Manage Feeds"
     ********************************************************************/

    private static function addNewFeedData($category, $remote_category, $file_name, $file_path, $providerName, $productCount, $miinto_country_code = null)
    {
        global $gfcore;
        $addNewFeedData = 'addNewFeedData' . $gfcore->callSuffix;
        GCPF_PFeedActivityLog::$addNewFeedData($category, $remote_category, $file_name, $file_path, $providerName, $productCount, $miinto_country_code);
    }

    private static function addNewFeedDataJ($category, $remote_category, $file_name, $file_path, $providerName, $productCount)
    {
        $date = JFactory::getDate();
        $user = JFactory::getUser();
        $db = JFactory::getDBO();

        $sql = 'SELECT COUNT(*) FROM #__cartproductfeed_feeds';
        $db->setQuery($sql);
        $db->query();
        $ordering = $db->loadResult() + 1;

        $newData = new stdClass();
        $newData->title = $file_name;
        $newData->category = $category;
        $newData->remote_category = $remote_category;
        $newData->filename = $file_name;
        $newData->url = $file_path;
        $newData->type = $providerName;
        $newData->product_count = $productCount;
        $newData->ordering = $ordering;
        $newData->created = $date->toSql();
        $newData->created_by = $user->get('id');
        //$newData->catid int,
        $newData->modified = $date->toSql();
        $newData->modified_by = $user->get('id');
        //$productCount
        $db->insertObject('#__cartproductfeed_feeds', $newData, 'id');
    }

    private static function addNewFeedDataJH($category, $remote_category, $file_name, $file_path, $providerName, $productCount)
    {
        GCPF_PFeedActivityLog::addNewFeedDataJ($category, $remote_category, $file_name, $file_path, $providerName, $productCount);
    }

    private static function addNewFeedDataJS($category, $remote_category, $file_name, $file_path, $providerName, $productCount)
    {

        global $gfcore;
        $shopID = $gfcore->shopID;

        $date = JFactory::getDate();
        $user = JFactory::getUser();
        $db = JFactory::getDBO();

        $sql = 'SELECT COUNT(*) FROM #__cartproductfeed_feeds';
        $db->setQuery($sql);
        $db->query();
        $ordering = $db->loadResult() + 1;

        $newData = new stdClass();
        $newData->title = substr($file_name, 3);
        $newData->category = $category;
        $newData->remote_category = $remote_category;
        $newData->filename = $file_name;
        $newData->url = $file_path;
        $newData->type = $providerName;
        $newData->product_count = $productCount;
        $newData->ordering = $ordering;
        $newData->created = $date->toSql();
        $newData->created_by = $user->get('id');
        //$newData->catid int,
        $newData->modified = $date->toSql();
        $newData->modified_by = $user->get('id');
        $newData->shop_id = $shopID;
        //$productCount
        $db->insertObject('#__cartproductfeed_feeds', $newData, 'id');
    }

    private static function addNewFeedDataW($category, $remote_category, $file_name, $file_path, $providerName, $productCount, $miinto_country_code = null)
    {
        global $wpdb;
        global $gfcore;
        /*
         * $gfcore->feed_type == 1 Custom product feed type
         * $gfcore->feed_type == 0 Default product feed type
         * */
        $product_details = '';
        if ($gfcore->feedType == 1) {
            $feed_type = 1;
            $sql = "SELECT * from {$wpdb->prefix}gcpf_custom_products";
            $product_details = serialize($wpdb->get_results($sql, ARRAY_A));
        }
        if ($gfcore == 0) {
            $feed_type = 0;
            $product_details = NULL;
        }
        $feed_table = $wpdb->prefix . 'gcp_feeds';
        $sql = "INSERT INTO $feed_table(`category`, `remote_category`, `filename`, `url`, `type`, `product_count`,`feed_type`,`product_details` ) VALUES ('$category','$remote_category','$file_name','$file_path','$providerName', '$productCount','$feed_type','$product_details' )";
        if ($wpdb->query($sql)) {
            $sql_custom = "TRUNCATE {$wpdb->prefix}gcpf_custom_products";
            $wpdb->query($sql_custom);

        }
    }

    private static function addNewFeedDataWe($category, $remote_category, $file_name, $file_path, $providerName, $productCount)
    {
        GCPF_PFeedActivityLog::addNewFeedDataW($category, $remote_category, $file_name, $file_path, $providerName, $productCount);
    }

    /********************************************************************
     * Search the DB for a feed matching filename / providerName
     ********************************************************************/

    public static function feedDataToID($file_name, $providerName)
    {
        global $gfcore;
        $feedDataToID = 'feedDataToID' . $gfcore->callSuffix;
        return GCPF_PFeedActivityLog::$feedDataToID($file_name, $providerName);
    }

    private static function feedDataToIDJ($file_name, $providerName)
    {
        $db = JFactory::getDBO();
        $query = "
			SELECT id
			FROM #__cartproductfeed_feeds
			WHERE filename='$file_name' AND type='$providerName'";
        $db->setQuery($query);
        $db->query();
        $result = $db->loadObject();
        if (!$result)
            return -1;

        return $result->id;

    }

    private static function feedDataToIDJH($file_name, $providerName)
    {

        return GCPF_PFeedActivityLog::feedDataToIDJ($file_name, $providerName);

    }

    private static function feedDataToIDJS($file_name, $providerName)
    {

        global $gfcore;
        $shopID = $gfcore->shopID;

        $db = JFactory::getDBO();
        $db->setQuery('
			SELECT id
			FROM #__cartproductfeed_feeds
			WHERE (filename=' . $db->quote($file_name) . ') AND (type=' . $db->quote($providerName) . ') AND (shop_id = ' . (int)$shopID . ')');
        $result = $db->loadObject();
        if (!$result)
            return -1;

        return $result->id;

    }

    private static function feedDataToIDW($file_name, $providerName)
    {
        global $wpdb;
        $feed_table = $wpdb->prefix . 'gcp_feeds';
        $sql = "SELECT * from $feed_table WHERE `filename`='$file_name' AND `type`='$providerName'";
        $list_of_feeds = $wpdb->get_results($sql, ARRAY_A);
        if ($list_of_feeds) {
            return $list_of_feeds[0]['id'];
        } else {
            return -1;
        }
    }

    private static function feedDataToIDWe($file_name, $providerName)
    {
        return GCPF_PFeedActivityLog::feedDataToIDW($file_name, $providerName);
    }

    /********************************************************************
     * Called from outside... this class has to make sure the feed shows under "Manage Feeds"
     ********************************************************************/

    public static function updateFeedList($category, $remote_category, $file_name, $file_path, $providerName, $productCount, $miinto_country_code = null)
    {
        $id = GCPF_PFeedActivityLog::feedDataToID($file_name, $providerName);
        if ($id == -1)
            GCPF_PFeedActivityLog::addNewFeedData($category, $remote_category, $file_name, $file_path, $providerName, $productCount, $miinto_country_code);
        else
            GCPF_PFeedActivityLog::updateFeedData($id, $category, $remote_category, $file_name, $file_path, $providerName, $productCount, $miinto_country_code);
    }

    public static function updateCustomFeedList($category, $remote_category, $file_name, $file_path, $providerName, $productCount, $miinto_country_code = null)
    {
        $category = implode(',', $category);
        $remote_category = implode('::', $remote_category);
        $id = GCPF_PFeedActivityLog::feedDataToID($file_name, $providerName);
        if ($id == -1)
            GCPF_PFeedActivityLog::addNewFeedData($category, $remote_category, $file_name, $file_path, $providerName, $productCount, $miinto_country_code);
        else
            GCPF_PFeedActivityLog::updateFeedData($id, $category, $remote_category, $file_name, $file_path, $providerName, $productCount, $miinto_country_code);
    }

    /********************************************************************
     * Update a record in the activity log
     ********************************************************************/

    private static function updateFeedData($id, $category, $remote_category, $file_name, $file_path, $providerName, $productCount, $miinto_country_code = null)
    {
        global $gfcore;
        $updateFeedData = 'updateFeedData' . $gfcore->callSuffix;
        GCPF_PFeedActivityLog::$updateFeedData($id, $category, $remote_category, $file_name, $file_path, $providerName, $productCount, $miinto_country_code);
    }

    private static function updateFeedDataJ($id, $category, $remote_category, $file_name, $file_path, $providerName, $productCount)
    {

        $date = JFactory::getDate();
        $user = JFactory::getUser();
        $db = JFactory::getDBO();

        $newData = new stdClass();
        $newData->id = $id;
        $newData->category = $category;
        $newData->remote_category = $remote_category;
        $newData->filename = $file_name;
        $newData->url = $file_path;
        $newData->type = $providerName;
        $newData->product_count = $productCount;
        $newData->modified = $date->toSql();
        $newData->modified_by = $user->get('id');
        //$productCount
        $db->updateObject('#__cartproductfeed_feeds', $newData, 'id');
    }

    private static function updateFeedDataJH($id, $category, $remote_category, $file_name, $file_path, $providerName, $productCount)
    {

        GCPF_PFeedActivityLog::updateFeedDataJ($id, $category, $remote_category, $file_name, $file_path, $providerName, $productCount);

    }

    private static function updateFeedDataJS($id, $category, $remote_category, $file_name, $file_path, $providerName, $productCount)
    {

        $date = JFactory::getDate();
        $user = JFactory::getUser();
        $db = JFactory::getDBO();

        //global $gfcore;
        //$shopID = $gfcore->shopID;

        $newData = new stdClass();
        $newData->id = $id;
        $newData->category = $category;
        $newData->remote_category = $remote_category;
        $newData->filename = $file_name;
        $newData->url = $file_path;
        $newData->type = $providerName;
        $newData->product_count = $productCount;
        $newData->modified = $date->toSql();
        $newData->modified_by = $user->get('id');
        //$newData->shop_id = $shopID;
        //$productCount

        $db->updateObject('#__cartproductfeed_feeds', $newData, 'id');

    }

    private static function updateFeedDataW($id, $category, $remote_category, $file_name, $file_path, $providerName, $productCount, $miinto_country_code = null)
    {
        global $wpdb;
        global $gfcore;
        /*
         * $gfcore->feed_type == 1 Custom product feed type
         * $gfcore->feed_type == 0 Default product feed type
         * */
        $product_details = '';
        if ($gfcore->feedType == 1) {
            $feed_type = 1;
            $sql = "SELECT * from {$wpdb->prefix}gcpf_custom_products";
            $product_details = serialize($wpdb->get_results($sql, ARRAY_A));
        }
        if ($gfcore == 0) {
            $feed_type = 0;
            $product_details = NULL;
        }
        $feed_table = $wpdb->prefix . 'gcp_feeds';
        $sql = "
			UPDATE $feed_table 
			SET 
				`category`='$category',
				`remote_category`='$remote_category',
				`filename`='$file_name',
				`url`='$file_path',
				`type`='$providerName',
				`product_count`='$productCount',
				`feed_type` = '$feed_type',
				`product_details` = '$product_details'
			WHERE `id`=$id";
        $wpdb->query($sql);
    }

    private static function updateFeedDataWe($id, $category, $remote_category, $file_name, $file_path, $providerName, $productCount)
    {
        GCPF_PFeedActivityLog::updateFeedDataW($id, $category, $remote_category, $file_name, $file_path, $providerName, $productCount);
    }

    /********************************************************************
     * Save a Feed Phase
     ********************************************************************/

    function logPhase($activity)
    {
        global $gfcore;
        $gfcore->settingSet('gcpf_feedActivity_' . $this->feedIdentifier, $activity);
    }

    /********************************************************************
     * Remove Log info
     ********************************************************************/

    function deleteLogDataJ()
    {

    }

    function deleteLogDataJH()
    {

    }

    function deleteLogDataJS()
    {

    }

    function deleteLogDataW()
    {
        delete_option('gcpf_feedActivity_' . $this->feedIdentifier);
    }

    function deleteLogDataWe()
    {
        delete_option('gcpf_feedActivity_' . $this->feedIdentifier);
    }

}