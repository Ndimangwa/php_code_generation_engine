<?php 
class ConfigurationData {
    private $username;
    private $database;
    private $hostname;
    private $password;
    public function __construct($fconfig)   {
        if (! file_exists($fconfig)) throw new Exception("Configuration File does not exists");
        include($fconfig);
        $this->username = Config::$username;
        $this->database = Config::$database;
        $this->hostname = Config::$host;
        $this->password = Config::$password;
    }
    public function getUsername()   { return $this->username; }
    public function getPassword()   { return $this->password; }
    public function getHostname()   { return $this->hostname; }
    public function getDatabase()   { return $this->database; }
}
?>