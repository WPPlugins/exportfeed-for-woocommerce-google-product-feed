<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly
class GCPF_PAProduct
{

    public $id = 0;
    public $title = '';
    public $taxonomy = '';
    public $imgurls;
    public $attributes;

    function __construct()
    {
        $this->imgurls = array();
        $this->attributes = array();
    }


}

class GCPF_PProductEntry
{
    public $taxonomyName;
    public $ProductID;
    public $Attributes;

    function __construct()
    {
        $this->Attributes = array();
    }

    function GetAttributeList()
    {
        $result = '';
        foreach ($this->Attributes as $ThisAttribute) {
            $result .= $ThisAttribute . ', ';
        }
        return '[' . $this->Name . '] ' . substr($result, 0, -2);
    }
}

global $gfcore;
$productListScript = 'productlist' . strtolower($gfcore->callSuffix) . '.php';
require_once $productListScript;