<?php

@include 'config.php';

session_start();

//Apres Click sur Login=========================
// le form reviens a la meme page php
if(isset($_POST['submit'])){

   //Recuperer les donnees
   $email = $_POST['email'];
   $pass = md5($_POST['pass']);

   //requete sql : check if user exists
   $sql = "SELECT * FROM `users` WHERE email = ? AND password = ?";
   $stmt = $conn->prepare($sql);
   $stmt->execute([$email, $pass]);
   $rowCount = $stmt->rowCount();  

   $row = $stmt->fetch(PDO::FETCH_ASSOC);

   if($rowCount > 0){

      //check si le login est fait par un User ou un Admin
      //Rediriger vers la page Adequate
      if($row['user_type'] == 'admin'){

         $_SESSION['admin_id'] = $row['id'];
         header('location:admin_page.php');

      }elseif($row['user_type'] == 'user'){

         $_SESSION['user_id'] = $row['id'];
         header('location:home.php');

      }else{
         $message[] = 'ce compte n existe pas';
      }

   }else{
      $message[] = 'email ou password incorrect!';
   }
   
}
?>
<!-- =========================================================== -->

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>login</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <link rel="stylesheet" href="css/components.css">

</head>
<body>

<!-- AFFICHER MESSAGE DE VALIDATION SI Il y'en a -->
<?php
if(isset($message)){
   foreach($message as $message){
      echo '
      <div class="message">
         <span>'.$message.'</span>
         <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
      </div>
      ';
   }
}
?>
<!-- -------------------------------------------- -->

   
<section class="form-container">
   <!-- le form reviens a la meme page php -->
   <form action="login.php" method="POST">
      <h3>Bienvenue chez SantéFood.</h3>
      <input type="email" name="email" class="box" placeholder="entrez votre email" required>
      <input type="password" name="pass" class="box" placeholder="entrez votre password" required>
      <input type="submit" value="se connecter" class="btn" name="submit">
      <p>Vous n'etes pas encore membre avec nous ? <a href="register.php">Inscrivez vous maintenant!!</a></p>
   </form>

</section>


</body>
</html>