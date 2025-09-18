<?php
require_once 'db_credentials.php';

if(!class_exists('db_connection')){
    class db_connection{
        public $db = null;
        public $result = null;

        // *@return boolean

        function db_connect(){
            $this -> db = mysqli_connect(SERVER, USERNAME, PASSWD, DB_NAME);

            if (mysqli_connect_errno()) {
                return false;
            } else {
                return true;
            }
        }
    }
}

?>