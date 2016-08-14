<?php

/**
 * @param array $data
 * @return array
 */
function webhooksCleanData(array $data) {
	return array_diff_key($data, array_flip(['password', 'secret']));
}

/**
 * @param $object
 * @return array
 */
function webhooksGetData($object) {
	$data = method_exists($object, 'data') ? $object->data() : $object->toArray();
	return webhooksCleanData($data);
}

/**
 * @param array $array1
 * @param array $array2
 * @return array
 */
function webhooksCalculateDiff(array $array1, array $array2) {
	foreach ($array1 as $key => $value) {
		if (is_array($value)) {
			if (!array_key_exists($key, $array2)) {
				$difference[$key] = $value;
			} else if (!is_array($array2[$key])) {
				$difference[$key] = $value;
			} else if($diff = webhooksCalculateDiff($value, $array2[$key])) {
				$difference[$key] = $diff;
			}
		} else if (!array_key_exists($key, $array2) || $array2[$key] !== $value) {
			$difference[$key] = $value;
		}
	}

	return !isset($difference) ? null : $difference;
}

/**
 * @param array $array1
 * @param array $array2
 * @return array
 */
function webhooksGetDiff(array $array1, array $array2) {
	return webhooksCalculateDiff($array2, $array1);
}

/**
 * @param $hook
 * @param array $filters
 * @return bool
 */
function webhooksFilterHook($hook, array $filters) {
	foreach($filters as $filter) {
		if($filter === $hook || strpos($hook, $filter . '.') === 0) {
			return true;
		}
	}

	return false;
}
