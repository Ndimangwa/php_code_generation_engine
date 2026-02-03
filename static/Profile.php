<?php 
class Profile {
    public function getBaseURL()    {
        $baseURL = $this->baseURL;
        $serverAddress = isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : null;
        if (is_null($serverAddress)) return $baseURL;
        $len = strlen($baseURL);
        $ipAddress = ""; //can be host too
        $state = 0;
        for ($i = 0; $i < $len; $i++)   {
            $char = substr($baseURL, $i, 1);
            if ($state == 0 && $char == '/')    {
                $state = 1;
                continue;
            }
            if ($state == 1 && $char != '/')    {
                $ipAddress = $char;
                $state = 2;
                continue;
            }
            if ($state == 2 && $char == '/')    {
                $state = 3;
                break;
            }
            if ($state == 2)    {
                $ipAddress .= $char;
                continue;
            }
            
        }
        if ($state == 2) {
            $state = 3; //break at end-of-string
        }
        return ($state == 3) ? str_replace($ipAddress, $serverAddress, $baseURL) : $baseURL;
    }
}
?>