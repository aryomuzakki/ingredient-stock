<?php include_once 'connection/config.php';?>
<?php
    // For PDO
    class DB{

        private static function connect(){
            try {
                global $db_host;
                global $db_username;
                global $db_password;
                global $db_name;
    
                $pdo = new PDO('mysql:host='.$db_host.'; dbname='.$db_name.'; charset=utf8', $db_username, $db_password);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                return $pdo;
            } catch (\Throwable $th) {
                function isLocalhost($whitelist = ['127.0.0.1', '::1']) {
                    // return in_array($_SERVER['REMOTE_ADDR'], $whitelist);
                    return false;
                }
                if (isLocalhost()) throw $th;
                else throw new Exception('Error Database', 500);
            }
        }

        public static function query($query, $params = array()){
            $statement = self::connect()->prepare($query);
            $statement->execute($params);

            if (explode(' ', $query)[0] == 'SELECT'){

                $data = $statement->fetchAll();
                return $data;

            }
        }

    }
?>