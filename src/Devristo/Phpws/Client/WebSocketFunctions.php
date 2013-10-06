<?php
/**
 * Created by JetBrains PhpStorm.
 * User: chris
 * Date: 10/6/13
 * Time: 6:33 PM
 * To change this template use File | Settings | File Templates.
 */
namespace Devristo\Phpws\Client;

use Devristo\Phpws\Client\HixieKey;

class WebSocketFunctions
{

    /**
     * Parse a HTTP HEADER 'Cookie:' value into a key-value pair array
     *
     * @param string $line Value of the COOKIE header
     * @return array Key-value pair array
     */
    public static function cookie_parse($line)
    {
        $cookies = array();
        $csplit = explode(';', $line);

        foreach ($csplit as $data) {

            $cinfo = explode('=', $data);
            $key = trim($cinfo[0]);
            $val = urldecode($cinfo[1]);

            $cookies[$key] = $val;
        }

        return $cookies;
    }

    /**
     * Parse HTTP request into an array
     *
     * @param string $header HTTP request as a string
     * @return array Headers as a key-value pair array
     */
    public static function parseHeaders($header)
    {
        $retVal = array();
        $fields = explode("\r\n", preg_replace('/\x0D\x0A[\x09\x20]+/', ' ', $header));
        foreach ($fields as $field) {
            if (preg_match('/([^:]+): (.+)/m', $field, $match)) {
                $match[1] = preg_replace_callback('/(?<=^|[\x09\x20\x2D])./', function ($m) {
                    return strtoupper($m[0]);
                }, strtolower(trim($match[1])));
                if (isset($retVal[$match[1]])) {
                    $retVal[$match[1]] = array($retVal[$match[1]], $match[2]);
                } else {
                    $retVal[$match[1]] = trim($match[2]);
                }
            }
        }

        if (preg_match("/GET (.*) HTTP/", $header, $match)) {
            $retVal['GET'] = $match[1];
        }

        return $retVal;
    }

    public static function calcHybiResponse($challenge)
    {
        return base64_encode(sha1($challenge . '258EAFA5-E914-47DA-95CA-C5AB0DC85B11', true));
    }

    public static function randHybiKey()
    {
        return base64_encode(
            chr(rand(0, 255)) . chr(rand(0, 255)) . chr(rand(0, 255)) . chr(rand(0, 255))
            . chr(rand(0, 255)) . chr(rand(0, 255)) . chr(rand(0, 255)) . chr(rand(0, 255))
            . chr(rand(0, 255)) . chr(rand(0, 255)) . chr(rand(0, 255)) . chr(rand(0, 255))
            . chr(rand(0, 255)) . chr(rand(0, 255)) . chr(rand(0, 255)) . chr(rand(0, 255))
        );
    }

    /**
     * Output a line to stdout
     *
     * @param string $msg Message to output to the STDOUT
     */
    public static function say($msg = "")
    {
        echo date("Y-m-d H:i:s") . " | " . $msg . "\n";
    }

    // mamta
    public static function genKey3()
    {
        return "" . chr(rand(0, 255)) . chr(rand(0, 255)) . chr(rand(0, 255)) . chr(rand(0, 255))
        . chr(rand(0, 255)) . chr(rand(0, 255)) . chr(rand(0, 255)) . chr(rand(0, 255));
    }

    public static function randHixieKey()
    {
        $_MAX_INTEGER = (1 << 32) - 1;
        #$_AVAILABLE_KEY_CHARS = range(0x21, 0x2f + 1) + range(0x3a, 0x7e + 1);
        #$_MAX_CHAR_BYTE = (1<<8) -1;
        # $spaces_n = 2;
        $spaces_n = rand(1, 12); // random.randint(1, 12)
        $max_n = $_MAX_INTEGER / $spaces_n;
        # $number_n = 123456789;
        $number_n = rand(0, $max_n); // random.randint(0, max_n)
        $product_n = $number_n * $spaces_n;
        $key_n = "" . $product_n;
        # $range = 3; //
        $range = rand(1, 12);
        for ($i = 0; $i < $range; $i++) {
            #i in range(random.randint(1, 12)):
            if (rand(0, 1) > 0) {
                $c = chr(rand(0x21, 0x2f + 1)); #random.choice(_AVAILABLE_KEY_CHARS)
            } else {
                $c = chr(rand(0x3a, 0x7e + 1)); #random.choice(_AVAILABLE_KEY_CHARS)
            }
            # $c = chr(65);
            $len = strlen($key_n);
            # $pos = 2;
            $pos = rand(0, $len);
            $key_n1 = substr($key_n, 0, $pos);
            $key_n2 = substr($key_n, $pos);
            $key_n = $key_n1 . $c . $key_n2;
        }
        for ($i = 0; $i < $spaces_n; $i++) {
            $len = strlen($key_n);
            # $pos = 2;
            $pos = rand(1, $len - 1);
            $key_n1 = substr($key_n, 0, $pos);
            $key_n2 = substr($key_n, $pos);
            $key_n = $key_n1 . " " . $key_n2;
        }

        return new HixieKey($number_n, $key_n);
    }

}