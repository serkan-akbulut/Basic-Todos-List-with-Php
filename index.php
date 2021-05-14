<?php
  session_start() ;
  if( isset($_SESSION['user'])){
    header("Location:main.php");
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
      .input-field { width: 50%; margin: 30px auto;}
      .container { margin-top: 150px;}

      .nav-color{color: lightblue;}
    </style>
  </head>
  <body>
  <nav>
    <div style="background-color: lightblue; font-weight:bold;" class="nav-wrapper">
      <a href="#" class="brand-logo center">TaskMan</a>
      <ul id="nav-mobile" class="right">
        <li><i class="material-icons">person_add</i></li>
        <li><a href="register.php">Register</a></li>
      </ul>
    </div>
  </nav>

  <div class="container">
    <form action="login.php" method="post">
        <div class="input-field">
          <i class="material-icons prefix">account_circle</i>
          <input name="email" id="email" type="text" class="validate" value="<?=isset($email)?filter_var($email,FILTER_SANITIZE_EMAIL): "" ?>">
          <label for="email">Email</label>
        </div>

        <div class="input-field">
          <i class="material-icons prefix">lock</i>
          <input name="password" id="password" type="text" class="validate" value="<?=isset($password)?filter_var($password,FILTER_SANITIZE_STRING): "" ?>">
          <label for="password">Password</label>
        </div>

        <div class="center">
          <button class="btn waves-effect waves-light" type="submit" name="action">Login
          </button>
        </div>
    
    </form>
  
  </div>

  <?php
     if ( isset($_SESSION["message"])) {
         $err = $_SESSION["message"] ;
         echo "<script> M.toast({html: '$err'}) ; </script>" ;
         unset($_SESSION["message"]) ; // unset : delete from assoc. array.
     }

  ?>
  <script>
    $(function(){

    })
  </script>
  </body>
</html>