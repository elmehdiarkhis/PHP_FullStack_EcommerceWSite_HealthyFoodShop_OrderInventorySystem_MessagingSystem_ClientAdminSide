<?php

@include 'config.php';

session_start();

//recuperation de l' ID du User Admin apres LOGIN
$admin_id = $_SESSION['admin_id'];
//==========================
//Verification si User Admin est LogedIn
if(!isset($admin_id)){
   header('location:login.php');
};
//==========================

//=======================================================================
//UPDATE des infos du User Admin dans la form dans cette page ===========
if(isset($_POST['update_profile'])){

   //RECUPERATION du nouveau nom/email du User =========================
   $name = $_POST['name'];
   $email = $_POST['email'];
   //====================================================================



   //UPDATE User Admin dans la DB =======================================
   $update_profile = $conn->prepare("UPDATE `users` SET name = ?, email = ? WHERE id = ?");
   $update_profile->execute([$name, $email, $admin_id]);
   //====================================================================

   //RECUPERATION de la nouvelle IMAGE du User Admin======================
   $image = $_FILES['image']['name'];
   $image_size = $_FILES['image']['size'];
   $image_tmp_name = $_FILES['image']['tmp_name'];
   $image_folder = 'uploaded_img/'.$image;
   $old_image = $_POST['old_image'];
   //====================================================================

   //VERIFICATION si l'image est convenable(size/empty)====================
   if(!empty($image)){
      //set MESSAGE si image trop large
      if($image_size > 2000000){
         $message[] = 'Image est trop large!';
      }else{
         //UPDATE image du User Admin dans la DB ==========================
         $update_image = $conn->prepare("UPDATE `users` SET image = ? WHERE id = ?");
         $update_image->execute([$image, $admin_id]);
         //===============================================================

         //SET MESSAGE si UPDATE succeful=================================
         if($update_image){
            move_uploaded_file($image_tmp_name, $image_folder);
            unlink('uploaded_img/'.$old_image);
            $message[] = 'image modifié avec succés!';
         };
         //================================================================
      };
   };

   //RECUPERATION du MDP ancien et du nouveau MDP / confirmation===========
   $old_pass = $_POST['old_pass'];
   //old MDP hidden in input coming from admin_header.php ================
   $update_pass = md5($_POST['update_pass']); 
   $new_pass = md5($_POST['new_pass']);
   $confirm_pass = md5($_POST['confirm_pass']);
   //======================================================================


   //VERIFICATION si les mdp ne sont pas vide =============================
   if(!empty($update_pass) AND !empty($new_pass) AND !empty($confirm_pass)){
      //VERIF si ancien MDP (input not hidden) == ancien MDP(input hidden)
      if($update_pass != $old_pass){
         $message[] = 'ancien mot de passe ne match pas!';
      }
      //VERIF si new MDP = confirm MDP ====================================
      elseif($new_pass != $confirm_pass){
         $message[] = 'confirmation du mot de passe ne match pas!';
      }
      //SI tout est correct =========================================
      else{

         //UPDATE MDP User Admin dans la DB ===============================
         $update_pass_query = $conn->prepare("UPDATE `users` SET password = ? WHERE id = ?");
         $update_pass_query->execute([$confirm_pass, $admin_id]);
         $message[] = 'mots de pass modifier avec succes!';
         //================================================================
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
   <title>modifier admin profile</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/components.css">

</head>
<body>
   
//NAV=====================================
<?php include 'admin_header.php'; ?>

<section class="update-profile">

   <h1 class="title">modifier profile</h1>

   <form action="" method="POST" enctype="multipart/form-data">
      <img src="uploaded_img/<?= $fetch_profile['image']; ?>" alt="">
      <div class="flex">
         <div class="inputBox">
            <span>pseudo :</span>
            <!-- $fetch_profile comming from admin_header.php -->
            <input type="text" name="name" value="<?= $fetch_profile['name']; ?>" placeholder="modifier pseudo" required class="box">
            <span>email :</span>
            <input type="email" name="email" value="<?= $fetch_profile['email']; ?>" placeholder="modifier email" required class="box">
            <span>modifier photo :</span>
            <input type="file" name="image" accept="image/jpg, image/jpeg, image/png" class="box">
            <input type="hidden" name="old_image" value="<?= $fetch_profile['image']; ?>">
         </div>
         <div class="inputBox">
            <input type="hidden" name="old_pass" value="<?= $fetch_profile['password']; ?>">
            <span>ancien mot de passe :</span>
            <input type="password" name="update_pass" placeholder="entrer ancien mot de passe" class="box">
            <span>nouveau mot de passe :</span>
            <input type="password" name="new_pass" placeholder="entrer nouveau mot de passe" class="box">
            <span>confirmer mot de passe :</span>
            <input type="password" name="confirm_pass" placeholder="confirmer nouveau mot de passe" class="box">
         </div>
      </div>
      <div class="flex-btn">
         <input type="submit" class="btn" value="Modifier profile" name="update_profile">
         <a href="admin_page.php" class="option-btn">Retourner</a>
      </div>
   </form>

</section>













<script src="js/script.js"></script>

</body>
</html>