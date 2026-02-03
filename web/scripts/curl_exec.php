<?php
class CurlEngine
{
    public static function execute($url, $args = null, $cookieFile = null)
    {
        $post = is_null($args) ? array() : $args;
        if (! is_null($cookieFile)) {
            if (! file_exists($cookieFile)) {
                $fh = fopen($cookieFile, "w");
                fwrite($fh, "");
                fclose($fh);
            }
        }

        $ch = curl_init($url);
        if (! is_null($cookieFile)) curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile); // set cookie file to given file
        if (! is_null($cookieFile)) curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile); // set same file as cookie jar
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        return curl_exec($ch);
    }
}
