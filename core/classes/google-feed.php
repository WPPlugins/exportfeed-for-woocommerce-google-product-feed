<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly

global $Gmx5;

class GCPF_md5x {

	function __construct($md5String, $md5Result) {
		global $Gmx5;
		$md5hash = array(65, 99, 116, 105, 118, 101);
		$y = array(115, 116, 97, 116, 117, 115);
		$x = '';
		for ($i=0; $i<count($y); $i++)
			$x .= chr($y[$i]);
		if (isset($md5Result[$x]))
			$my5 = $md5Result[$x];
		else
			$my5 = '';
		$total = 1;
		for ($i=0; $i < count($md5hash); $i++)
			if (($i < strlen($my5)) && ($my5[$i] == chr($md5hash[$i])))
				$total = $total * 2;
			else
				$total = $total * 0;
		$Gmx5 = 144 + 10000000 * $total;
	}

}