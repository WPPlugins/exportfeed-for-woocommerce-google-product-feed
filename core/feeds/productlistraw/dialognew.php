<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly
class ProductlistrawDlg_GCPF extends GCPF_PBaseFeedDialog
{

    function __construct()
    {
        parent::__construct();
        $this->service_name = 'Productlistraw';
        $this->service_name_long = 'Product List RAW Export';
        $this->options = array();
        $this->blockCategoryList = true;
    }

}
