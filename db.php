<?php 
try {
     $db = new PDO("mysql:host=localhost;dbname=todos", "root", "root"); //db connection
} catch ( PDOException $e ){
     print $e->getMessage();
}
?>