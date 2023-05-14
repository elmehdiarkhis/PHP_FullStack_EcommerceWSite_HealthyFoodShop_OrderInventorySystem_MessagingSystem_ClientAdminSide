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
//DELETE MESSAGE dans le BUTTON supprimer dans cette page
if(isset($_GET['delete'])){

   $delete_id = $_GET['delete'];
   $delete_message = $conn->prepare("DELETE FROM `message` WHERE id = ?");
   $delete_message->execute([$delete_id]);

   //REDIRECT vers admin_contacts.php
   header('location:admin_contacts.php');

}
//=========================================================================

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>messages</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/admin_style.css">

</head>
<body>
   
<?php include 'admin_header.php'; ?>

<section class="messages">

   <h1 class="title">messages</h1>

   <div class="box-container">

   <?php

   //SELECTION de tous les infos des MESSAGE de la DB =====================
      $select_message = $conn->prepare("SELECT * FROM `message`");
      $select_message->execute();
   //=====================================================================

   //AFFICHAGE des infos de chaque MESSAGES ===============================
      if($select_message->rowCount() > 0){
         while($fetch_message = $select_message->fetch(PDO::FETCH_ASSOC)){
   ?>
   <div class="box">
      <p> id du user : <span><?= $fetch_message['user_id']; ?></span> </p>
      <p> nom : <span><?= $fetch_message['name']; ?></span> </p>
      <p> num de tel : <span><?= $fetch_message['number']; ?></span> </p>
      <p> email : <span><?= $fetch_message['email']; ?></span> </p>
      <p> message : <span><?= $fetch_message['message']; ?></span> </p>
      <a href="admin_contacts.php?delete=<?= $fetch_message['id']; ?>" onclick="return confirm('delete this message?');" class="delete-btn">supprimer</a>
   </div>
   <?php
         }
      }
      //===================================================================

      //SI il n'y a pas de MESSAGE dans la DB =====================
      else{
         echo '<p class="empty">vous n\'avez pas messages!</p>';
      }
      //===========================================================
   ?>

   </div>

</section>













<script src="js/script.js"></script>

</body>
</html>