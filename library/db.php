<?php
try { 
  $db = new PDO('mysql:host=' . $config['dbhost'] . ';dbname=' . $config['dbschema']
                , $config['dbuser']
                , $config['dbpass']
                , array(PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION)
                ); 
}
catch (PDOException $e) { 
  echo json_response(array(), "db_error", "資料庫連線錯誤");
  exit();
}
$db->query("SET NAMES 'utf8'"); 
?>