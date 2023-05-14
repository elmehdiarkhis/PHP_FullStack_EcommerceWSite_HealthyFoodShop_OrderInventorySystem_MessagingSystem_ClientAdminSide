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

//=========================================================================
//DELETE User Admin dans le BUTTON supprimer dans cette page
if(isset($_GET['delete'])){

   //RECUPERATION de l'ID from Button supp========================
   $delete_id = $_GET['delete'];

   //DELETE User Admin from DB ========================================
   $delete_users = $conn->prepare("DELETE FROM `users` WHERE id = ?");
   $delete_users->execute([$delete_id]);
   //REDIRECT vers cette page.
   header('location:admin_users.php');
   //==================================================================

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>users</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/admin_style.css">

</head>
<body>
   
<?php include 'admin_header.php'; ?>

<section class="user-accounts">

   <h1 class="title">comptes des user</h1>

   <div class="box-container">

      <?php

      //SELECTION des Users Admin dans la DB============================
         $select_users = $conn->prepare("SELECT * FROM `users`");
         $select_users->execute();
      //=================================================================


      //AFFICHAGE des infos de tous les Users Admin ======================
         while($fetch_users = $select_users->fetch(PDO::FETCH_ASSOC)){
      ?>
      <div class="box" style="<?php if($fetch_users['id'] == $admin_id){ echo 'display:none'; }; ?>">
         <img src="uploaded_img/<?= $fetch_users['image']; ?>" alt="">
         <p> id du user : <span><?= $fetch_users['id']; ?></span></p>
         <p> pseudo : <span><?= $fetch_users['name']; ?></span></p>
         <p> email : <span><?= $fetch_users['email']; ?></span></p>
         <p> type de user : <span style=" color:<?php if($fetch_users['user_type'] == 'admin'){ echo 'orange'; }; ?>"><?= $fetch_users['user_type']; ?></span></p>

         <!-- BUTTON pour DELETE -->
         <!-- GET delete= ID -->
         <a href="admin_users.php?delete=<?= $fetch_users['id']; ?>" onclick="return confirm('supprimer ce user?');" class="delete-btn">supprimer</a>
      </div>
      <?php
      }

      //===================================================================
      ?>
   </div>

</section>













<script src="js/script.js"></script>

</body>
</html>