<?php

/**
 * The clsx function is used to join different classes.
 * This works similar to the npm package 'clsx' (see https://www.npmjs.com/package/clsx).
 * If this function is called with multiline strings, that contain tabs or newline characters, these characters are removed.
 * Warning: using this function increases the readability of the php code but
 * will remove the Tailwind CSS Intellisense support of these classes.
 * 
 * To enable enable Tailwind CSS Intellij support for a clsx(" ... "), add the follwing entry to settings.json:
 *   "tailwindCSS.experimental.classRegex": [
 *     "clsx\\(\"([^\\)]*)\"\\)", // enable TailwindCSS in clsx functions
 *   ],
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

/**
 * Converts a single value into a class string.
 * This function also removes unnecessary whitespaces.
 */
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

	// Remove whitespace
	$str = trim(preg_replace('/\s+/', ' ', $str));

	return $str;
}

?>
