<?php
  //HOCAM EK OLARAK PROFİL GÜNCELLEMESİ EKLEMEK İSTEDİK SİZİN ÖRNEK KODUNUZU İNCELEYEREK.
  session_start() ;
  require_once "./protect.php" ;
  // only authenticated users.

  $id = $_SESSION["user"]["id"] ;
  
   if ( !empty($_POST)) {
     extract($_POST) ;
     extract($_FILES);
     require_once "./db.php" ;
     require_once "./Upload.php" ;
     $upload = new Upload("profile", "images");

     $error = [] ;
      $emailReg ='/^(([^<>()\[\]\.,;:\s@\"]+(\.[^<>()\[\]\.,;:\s@\"]+)*)|(\".+\"))@(([^<>()[\]\.,;:\s@\"]+\.)+[^<>()[\]\.,;:\s@\"]{2,})$/';
      $usernameReg='/[a-zA-Z][a-zA-Z0-9-_]{3,32}/';
   
       //username
     if(preg_match($usernameReg,$_POST['username'])===0 ){
      $error['username']="invalid username";
     
    }

    //email validation
    if(preg_match($emailReg,$email)===0 ){
      $error['email']="invalid email";
    }
        //profile photo empty check
    if(empty($_FILES['profile']['name'])){
      $error['profile']="file can not be empty";
    }
    
    
    if(empty($error)){
     if ( $upload->file()) {
         $rs = $db->prepare("update user set name=?,email=?,profile=? where id = ?") ;
         $rs->execute([$username,$email ,$upload->file(), $id]) ;

         // Delete old profile image
         $oldProfileImage = $_SESSION["user"]["profile"] ;
         if ( $oldProfileImage) {
             unlink("images/$oldProfileImage") ; // delete the previous profile image.
         }


         $_SESSION["user"]["profile"] = $upload->file() ;
     } else {
        $rs = $db->prepare("update user set name=?,email=?,where id = ?") ;
        $rs->execute([$username,$email,$id]) ;
     }

     $_SESSION["user"]["name"] = $username ;
     $_SESSION["user"]["email"] = $email ;
     header("Location: main.php") ;
    }
  
    
   }


  $user = $_SESSION["user"] ;
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
    <div style="background-color: lightblue; font-weight:bold;" class="nav-wrapper">
      <a href="index.php" class="brand-logo center">Profile Edit</a>
      <ul id="nav-mobile" class="right">
         <li><a href="main.php">Back Todo list</a></li>
       </ul>
    </div>
  </nav>
  <div class="container">
   <div class="center">
    <?php
        $profile = $user["profile"] ?? "avatar.png" ;
        echo "<img src='images/$profile' width='80' class='circle' > " ;
        ?>
   </div>
    

    <form action="" method="post" enctype="multipart/form-data">
        <div class="input-field">
          <input name="username" id="username" type="text" class="validate" placeholder="name should not be start with digit" 
            value="<?=isset($username)?filter_var($username,FILTER_SANITIZE_STRING): "" ?>"
          >
          <label for="username">Name Lastname</label>
        </div>

        <div class="input-field">
          <input name="email" id="email" type="text" class="validate" placeholder="please enter valid email"
          value="<?=isset($email)?filter_var($email,FILTER_SANITIZE_STRING): "" ?>" 
          >
          <label for="email">Email</label>
        </div>


        <div class="file-field input-field">
          <div class="btn">
            <span>File</span>
            <input type="file" name="profile">
          </div>
          <div class="file-path-wrapper">
            <input class="file-path validate" type="text">
          </div>
        </div>

        <div class="center">
          <button class="btn waves-effect waves-light" type="submit" name="action">Update
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
  </body>
</html>