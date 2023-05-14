<?php

include 'config.php';

//Si click sur Register=====================================================
// le form reviens a la meme page php 
if(isset($_POST['submit'])){

   //Recuperer les valeur du form =======
   $name = $_POST['name'];
   $email = $_POST['email'];
   $pass = md5($_POST['pass']); //crypter le pass
   $cpass = md5($_POST['cpass']);

   //Image====
   $image = $_FILES['image']['name'];
   $image_size = $_FILES['image']['size'];
   $image_tmp_name = $_FILES['image']['tmp_name'];
   $image_folder = 'uploaded_img/'.$image;
   //======================================

   //Requette checker si le User Exists===========================
   $select = $conn->prepare("SELECT * FROM `users` WHERE email = ?");
   $select->execute([$email]);
   //=====
   if($select->rowCount() > 0){
      $message[] = 'l email existe deja!';
   }else{
      if($pass != $cpass){
         $message[] = 'le mots de pass de confirmation est incorrect!';
      }else{
               
         if($image_size > 2000000){
            $message[] = 'la taille de l image est trop large!';
         }else{

            //Apres avoir checker les validations > Insert
            $insert = $conn->prepare("INSERT INTO `users`(name, email, password, image) VALUES(?,?,?,?)");
            $insert->execute([$name, $email, $pass, $image]);

            if($insert){
               //Apres validaiton, garder l'image dans le dossier : uploaded_img/
               move_uploaded_file($image_tmp_name, $image_folder);
               
               $message[] = 'Felicitation , Vous etes Membre SantéFood maintenant!';

               header('location:login.php');
            }else{
               $message[] = 'Un Probleme est surveneu , Veuillez reessayer votre inscription!';
            }    
         }   
      }
   }
}
//=========================================================================

?>


<!-- ===============================HTML D'ENREGISTREMENT============================ -->
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>register</title>

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
   <form action="" enctype="multipart/form-data" method="POST">
      <h3>s'enregistrer maintenant</h3>
      <input type="text" name="name" class="box" placeholder="entrez votre nom" required>
      <input type="email" name="email" class="box" placeholder="entrez votre email" required>
      <input type="password" name="pass" class="box" placeholder="entrez votre mots de pass" required>
      <input type="password" name="cpass" class="box" placeholder="confirmer votre mots de pass" required>
      <input type="file" name="image" class="box" required accept="image/jpg, image/jpeg, image/png">
      <input type="submit" value="register now" class="btn" name="submit">
      <p>Etes vous deja membre avec nous? ? <a href="login.php">Se Connecter</a></p>
   </form>

</section>


</body>
</html>