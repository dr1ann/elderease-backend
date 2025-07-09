<?php
    class DbConnect {
        
        private $server = 'localhost';
        private $dbname = 'seniorgovernment';
        private $user = 'root';
        private $password = '';
        private $port = 3306;

        public function connect() {
            try {
                $conn = new PDO('mysql:host=;port=3306;dbname=' . $this->dbname, $this->user, $this->password);
                $conn-> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);;
                return $conn;
            } catch (\Exception $e) {
                echo 'Database Error: '.$e->getMessage();
            }
        }

    }
?>