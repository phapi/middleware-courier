<?php
namespace Phapi\Middleware\Courier;

abstract class Output
{
    public static $headers = array();
    public static $body;

    public static function reset()
    {
        self::$headers = array();
        self::$body = null;
    }
}

function headers_sent()
{
    return false;
}

function header($value)
{
    Output::$headers[] = $value;
}

function printf($text)
{
    Output::$body .= $text;
}
