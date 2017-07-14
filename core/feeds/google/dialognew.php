<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly

class GoogleDlg_GCPF extends GCPF_PBaseFeedDialog {

	function __construct() {
		parent::__construct();
		$this->service_name = 'Google';
		$this->service_name_long = 'Google Products XML Export';
	}

	function convert_option($option) {
		return strtolower(str_replace(" ", "_", $option));
	}

}
