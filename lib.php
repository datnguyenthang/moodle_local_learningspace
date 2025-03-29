<?php
/**
 * Convert an array to a comma-separated string, filtering out empty values.
 *
 * @param array $arr The input array.
 * @return string The resulting comma-separated string.
 */
function convert_array_to_str($arr) {
    // Remove empty strings.
    $arr = array_filter($arr, 'strlen');
    // Convert array to comma-separated string.
    return implode(',', $arr);
}