<?php
$enable_debug = true;

function LOG_ARRAY($array) {
    if (!$array) {
        return "NULL";
    };
    $text = '[';
    foreach ($array as $k=>$v) {
        if (is_array($v)) {
            $text .= LOG_MESSAGE("\t".$k);
            $text .= LOG_ARRAY($v);
        } else if (is_bool($v) && $v)
            $text .= "\t{$k}: TRUE, ";
        else if (is_bool($v) && !$v)
            $text .= "\t{$k}: FALSE, ";
        else if ($v === '')
            $text .= "\t{$k}: '',";
        else 
            $text .= "\t{$k}: {$v},";
    } 
    $text .= "]";
    return $text;
}

function LOG_MESSAGE(...$args)
{
    global $enable_debug;
    $text = '';
    if ($enable_debug) {
        foreach($args as $arg) {
            if (is_string($arg) || is_numeric($arg))
                $text .= $arg;
            else if (is_bool($arg) && !$arg)
                $text .= "FALSE";
            else if (is_bool($arg) && $arg)
                $text .= "TRUE";
            else {
                $text .= LOG_ARRAY($arg);
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