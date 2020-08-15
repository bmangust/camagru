<?php
$enable_debug = true;

function LOG_ARRAY($array) {
    if (!$array) {
        return "NULL";
    };
    $text = '';
    foreach ($array as $k=>$v) {
        if (is_array($v)) {
            $text .= LOG_MESSAGE("\t".$k);
            $text .= LOG_ARRAY($v);
        } else if (is_bool($v) && $v)
            $text .= "\t{$k}: TRUE\n";
        else if (is_bool($v) && !$v)
            $text .= "\t{$k}: FALSE\n";
        else if ($v === '')
            $text .= "\t{$k}: ''\n";
        else 
            $text .= "\t{$k}: {$v}\n";
    } 
    return $text;
}

function LOG_MESSAGE(...$args)
{
    global $enable_debug;
    $text = '';
    if ($enable_debug) {
        foreach($args as $arg) {
            if (is_string($arg) || is_numeric($arg))
                $text .= "<b>{$arg}</b><br>";
            else if (is_bool($arg) && !$arg)
                $text .= "<pre>\tFALSE\n</pre>";
            else if (is_bool($arg) && $arg)
                $text .= "<pre>\tTRUE\n</pre>";
            else {
                $text .= '<pre>';
                $text .= LOG_ARRAY($arg);
                $text .= '</pre>';
            }
        }
    }
    return $text;
}

function LOG_M(...$args) {
    global $enable_debug;
    echo LOG_ARRAY($args);
}

function LOG_F(...$args) {
    global $enable_debug;
    file_put_contents('logs\log.log', LOG_MESSAGE($args));
}
?>