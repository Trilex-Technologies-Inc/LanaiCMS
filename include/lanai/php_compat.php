<?php

function lanai_normalize_dbtype($dbtype)
{
    $normalized = strtolower(trim((string)$dbtype));
    return $normalized === 'mysql' ? 'mysqli' : $dbtype;
}

if (!function_exists('split')) {
    function split($pattern, $string, $limit = -1)
    {
        return preg_split('~' . str_replace('~', '\\~', (string)$pattern) . '~', (string)$string, $limit);
    }
}

if (!function_exists('spliti')) {
    function spliti($pattern, $string, $limit = -1)
    {
        return preg_split('~' . str_replace('~', '\\~', (string)$pattern) . '~i', (string)$string, $limit);
    }
}

if (!function_exists('ereg')) {
    function ereg($pattern, $string, &$matches = null)
    {
        return preg_match('~' . str_replace('~', '\\~', (string)$pattern) . '~', (string)$string, $matches);
    }
}

if (!function_exists('eregi')) {
    function eregi($pattern, $string, &$matches = null)
    {
        return preg_match('~' . str_replace('~', '\\~', (string)$pattern) . '~i', (string)$string, $matches);
    }
}

if (!function_exists('ereg_replace')) {
    function ereg_replace($pattern, $replacement, $string)
    {
        return preg_replace('~' . str_replace('~', '\\~', (string)$pattern) . '~', $replacement, (string)$string);
    }
}

if (!function_exists('eregi_replace')) {
    function eregi_replace($pattern, $replacement, $string)
    {
        return preg_replace('~' . str_replace('~', '\\~', (string)$pattern) . '~i', $replacement, (string)$string);
    }
}

if (!function_exists('sql_regcase')) {
    function sql_regcase($string)
    {
        $result = '';
        $length = strlen((string)$string);

        for ($index = 0; $index < $length; $index++) {
            $char = $string[$index];
            if (ctype_alpha($char)) {
                $result .= '[' . strtoupper($char) . strtolower($char) . ']';
                continue;
            }

            $result .= $char;
        }

        return $result;
    }
}

if (!function_exists('each')) {
    function each(&$array)
    {
        if (!is_array($array)) {
            return false;
        }

        $key = key($array);
        if ($key === null) {
            return false;
        }

        $value = current($array);
        next($array);

        return array(
            0 => $key,
            1 => $value,
            'key' => $key,
            'value' => $value,
        );
    }
}