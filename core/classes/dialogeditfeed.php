<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly
class GCPF_PEditFeedDialog {

	public static function pageBody($feed_id , $feed_type) {

		require_once dirname(__FILE__) . '/../data/savedfeed.php';
		require_once 'dialogbasefeed.php';

		if ($feed_id == 0)
			return;

        $feed = new GCPF_PSavedFeed($feed_id);

            //Figure out the dialog for the provider
            $dialog_file = dirname(__FILE__) . '/../feeds/' . strtolower($feed->provider) . '/dialognew.php';
            if (file_exists($dialog_file))
                require_once $dialog_file;

            //Instantiate the dialog
            $provider = $feed->provider . 'Dlg_GCPF';
            $provider_dialog = new $provider();
            echo $provider_dialog->mainDialog($feed , $feed_type);
    }

}