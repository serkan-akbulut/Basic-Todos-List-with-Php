<?php
   session_start() ;
   require_once "./auth.php" ;
      
   if ( !empty($_POST)) {
     extract($_POST) ;
     extract($_FILES) ;
     
     require_once "./db.php" ;
     require_once "./Upload.php" ;
     $upload = new Upload("profile", "images");

     $error = [] ;
      $emailReg ='/^(([^<>()\[\]\.,;:\s@\"]+(\.[^<>()\[\]\.,;:\s@\"]+)*)|(\".+\"))@(([^<>()[\]\.,;:\s@\"]+\.)+[^<>()[\]\.,;:\s@\"]{2,})$/';
      $usernameReg='/[a-zA-Z][a-zA-Z0-9-_]{3,32}/';
       $passwordReg = '/^\S{6,12}$/' ;
     
     //username
     if(preg_match($usernameReg,$username)===0 ){
      $error['username']="invalid username";
    }

    //email validation
    if(preg_match($emailReg,$email)===0 ){
      $error['email']="invalid email";
    }

     //password validation
     if(preg_match($passwordReg,$password)===0 ){
      $error['password']="invalid password";
    }
        //profile photo empty check
    if(empty($_FILES['profile']['name'])){
      $error['profile']="file can not be empty";
    }

  
  
  
      
      if(empty($error)){
     $rs = $db->prepare("insert into user (name, email, password, profile) values (?,?,?,?)") ;
     $rs->execute([$username, $email, password_hash($password, PASSWORD_DEFAULT), $upload->file()]);

     // redirect browser to index.php
     header("Location: index.php") ;
     exit ;
    }
   
  }
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Title of the document</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <script src="https://code.jquery.com/jquery-3.2.1.min.js" integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
    <style>
     .container { margin-top: 100px;}
     .input-field { width: 60%; margin: 30px auto;}
    </style>
  </head>
  <body>
  <nav>
    <div class="nav-wrapper"style="background-color: lightblue; font-weight:bold;">
      <a href="index.php" class="brand-logo center">Taskman</a>
    </div>
  </nav>
  <div class="container">
    <form action="" method="post" enctype="multipart/form-data">
        <div class="input-field">
          <input name="username" placeholder="name should not be start with digit" id="username" type="text" class="validate" value="<?=isset($username)?filter_var($username,FILTER_SANITIZE_STRING): "" ?>">
          <label for="username">Name Lastname</label>
        </div>

        <div class="input-field">
          <input name="email" id="email" placeholder="please enter valid email" type="text" class="validate" value="<?=isset($email)?filter_var($email,FILTER_SANITIZE_EMAIL): "" ?>">
          <label for="email">Email</label>
        </div>

        <div class="input-field">
      
          <input name="password" placeholder="password min-6 max-12" id="password" type="text" class="validate" value="<?=isset($password)?filter_var($password,FILTER_SANITIZE_STRING): "" ?>">
          <label for="password">Password</label>
        </div>

    

        <div class="file-field input-field">
          <div class="btn">
            <span>File</span>
            <input type="file" name="profile" >
          </div>
          <div class="file-path-wrapper">
            <input class="file-path validate" type="text" value="<?= isset($_FILES['profile']['name'])? $_FILES['profile']['name']:"" ?>">
          </div>
        </div>

        <div class="center">
          <button class="btn waves-effect waves-light" type="submit" name="action">Register
            <i class="material-icons right">send</i>
          </button>
        </div>
    
    
    </form>
   
  </div>

  <?php
     
      // if error exists, display with toast messages.
      if (!empty($error)) {
        echo "<script>" ;
        foreach($error as $e) {
            echo "M.toast({html: '$e'}); " ;
        }
        echo "</script>" ;
      }
  
       
    

  ?>

  <script>
    $(function(){

    })
  </script>
  </body>
</html>