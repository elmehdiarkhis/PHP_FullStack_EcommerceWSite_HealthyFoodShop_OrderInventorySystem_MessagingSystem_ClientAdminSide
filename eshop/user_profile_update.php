<!-- Rediriger Ici apres que le user a clicker sur UPDATE PRODILE (BUTTON DANS LE HEADER) -->

<?php

@include 'config.php';

session_start();

//en recupere l'id du user connecter
$user_id = $_SESSION['user_id'];

// Personne ne peux acceder au home page sauf si il est connecter autant que user
if(!isset($user_id)){
   header('location:login.php');
};
// ===========


// =======================================//
//  Les Message sont remplie ici   //
///  Mais Gerer dans header.php  
///  include 'header.php' dans HTML
// ======================================//



//Si l'utlisateur Veux Update apres avoir unter les new infos==================================
if(isset($_POST['update_profile'])){

   //Recuperer les nouvelle infos de profile
   $name = $_POST['name'];
   $email = $_POST['email'];

   //requete update
   $update_profile = $conn->prepare("UPDATE `users` SET name = ?, email = ? WHERE id = ?");
   $update_profile->execute([$name, $email, $user_id]);

   //Recuperer les infos de la photo (nom/path..)
   $image = $_FILES['image']['name'];
   $image_size = $_FILES['image']['size'];
   $image_tmp_name = $_FILES['image']['tmp_name'];
   $image_folder = 'uploaded_img/'.$image;
   $old_image = $_POST['old_image'];
   //-----

   //check la taille de l'image puis Requette Update
   if(!empty($image)){
      if($image_size > 2000000){
         $message[] = 'la taille de l image est tres grande!';
      }else{
         $update_image = $conn->prepare("UPDATE `users` SET image = ? WHERE id = ?");
         $update_image->execute([$image, $user_id]);
         if($update_image){
            move_uploaded_file($image_tmp_name, $image_folder);
            unlink('uploaded_img/'.$old_image);
            $message[] = 'l image a ete modifier avec succes!';
         };
      };
   };


   //Recuper les Password du submit + du Hidden input,  les convertir to md5
   $old_pass = $_POST['old_pass'];
   $update_pass = md5($_POST['update_pass']);
   $new_pass = md5($_POST['new_pass']);
   $confirm_pass = md5($_POST['confirm_pass']);


   //Validations + Update-----------
   if(!empty($update_pass) AND !empty($new_pass) AND !empty($confirm_pass)){
      if($update_pass != $old_pass){
         $message[] = 'ancien mots de pass incorrect!';
      }elseif($new_pass != $confirm_pass){
         $message[] = 'mots de pass de confirmation incorrect!';
      }else{
         $update_pass_query = $conn->prepare("UPDATE `users` SET password = ? WHERE id = ?");
         $update_pass_query->execute([$confirm_pass, $user_id]);
         $message[] = 'Le mots de pass a ete Modifier avec succes!';
      }
   }

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Modification de profile</title>


   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="css/components.css">
</head>
<body>
   
<!-- les Messages des Erreurs sont gerer ici  -->
<?php include 'header.php'; ?>
<!-- ------------------- -->

<section class="update-profile">

   <h1 class="title">Modifier mon profile</h1>

   <form action="" method="POST" enctype="multipart/form-data">
      
      <img src="uploaded_img/<?= $fetch_profile['image']; ?>" alt="">

      <div class="flex">

         <div class="inputBox">
            <span>username :</span>
            <input type="text" name="name" value="<?= $fetch_profile['name']; ?>" placeholder="modifier le username" required class="box">
            <span>email :</span>
            <input type="email" name="email" value="<?= $fetch_profile['email']; ?>" placeholder="modifier l'email" required class="box">
            <span>modifier ma photo :</span>
            <input type="file" name="image" accept="image/jpg, image/jpeg, image/png" class="box">
            <input type="hidden" name="old_image" value="<?= $fetch_profile['image']; ?>">
         </div>

         <div class="inputBox">
            <!-- GARDER L'ANCIER PASS DANS UN HIDDEN INPUT POUR COMPARER -->
            <input type="hidden" name="old_pass" value="<?= $fetch_profile['password']; ?>">
            <span>ancien mots de pass :</span>
            <input type="password" name="update_pass" placeholder="entrer votre ancien mots de pass" class="box">
            <span>nouveau mots de pass :</span>
            <input type="password" name="new_pass" placeholder="entrer un nouveau mots de pass" class="box">
            <span>confirmation du mots de pass :</span>
            <input type="password" name="confirm_pass" placeholder="confirmer le nouveau mots de pass" class="box">
         </div>
      </div>

      <!-- BUTTONS d'UPDATE ou Rediriger vers home.php -->
      <div class="flex-btn">
         <input type="submit" class="btn" value="update profile" name="update_profile">

         <a href="home.php" class="option-btn">Retourner vers l'Acceuil</a>
      </div>
   </form>

</section>










<?php include 'footer.php'; ?>


<script src="js/script.js"></script>

</body>
</html>