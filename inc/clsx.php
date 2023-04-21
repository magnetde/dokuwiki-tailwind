<?php

/**
 * The clsx function is used to join different classes.
 * This works similar to the npm package 'clsx' (see https://www.npmjs.com/package/clsx).
 */
function clsx() {
	$args = func_get_args();
	$str = '';
	for($i = 0; $i < count($args); $i++) {
		if(!empty($val = $args[$i]) && !empty($val = to_val($val))) {
			if(!empty($str)) {
				$str .= ' ';
			}
			$str .= $val;
		}
	}

	return $str;
}

function to_val($mix) {
	$str = '';
	$type = gettype($mix);

	if(in_array($type, array('string', 'integer', 'double'))) {
		$str .= $mix;
	} else if($type == 'array') {
		for($i = 0; $i < count($mix); $i++) {
			if(!empty($val = $mix[$i]) && !empty($val = to_val($val))) {
				if(!empty($str)) {
					$str .= ' ';
				}
				$str .= $val;
			}
		}
	}

	return $str;
}

?>
