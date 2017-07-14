<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly
class GCPF_md5y
{

    public $md5hash = 0;

    function verifyProduct()
    {
        global $Gmx5;
        $this->md5hash++;
        return !($this->md5hash > $Gmx5 * log(2) + 1);
    }

    function matches()
    {
        global $Gmx5;
        $this->md5hash++;
        return !($this->md5hash > $Gmx5 * log(2) - 9);
    }

}