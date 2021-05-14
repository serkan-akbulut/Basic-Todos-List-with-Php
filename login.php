<?php
  session_start();

  require_once "./db.php" ;

  extract($_POST) ; // email, password

  $rs = $db->prepare("select * from user where email = ?") ;
  $rs->execute([$email]) ;

  if ( $rs->rowCount() === 1) {
      // valid email address
      $user = $rs->fetch(PDO::FETCH_ASSOC) ;
      if ( password_verify($password, $user["password"])) {
         // echo "user authenticated" ;
         $_SESSION["user"] = $user ; // login.php puts user data in global persisten data area.
         header("Location: main.php") ;
         exit ;
      }
      
  } 
  
  $_SESSION["message"] = "Login Failed!" ;
  //echo "no user with that email address" ;
  header("Location: index.php") ;
  