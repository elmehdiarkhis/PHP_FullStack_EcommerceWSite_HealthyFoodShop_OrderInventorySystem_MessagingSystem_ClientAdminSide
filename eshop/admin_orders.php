<?php

@include 'config.php';

session_start();


//Recupere l'id de l'admin connecter=====
$admin_id = $_SESSION['admin_id'];
//======================================


//si la connexion n'est pas faite par un ADMIN
if(!isset($admin_id)){
   header('location:login.php');
};
//===========================



//SI IL CLICK SUR UPDATE_ORDER DANS CETTE PAGE------
if(isset($_POST['update_order'])){

   //on recupere l'id du Hidden Input
   $order_id = $_POST['order_id'];

   //on recupere le value du <select>
   $update_payment = $_POST['update_payment'];

   //Requete Update du statut de payment 
   $update_orders = $conn->prepare("UPDATE `orders` SET payment_status = ? WHERE id = ?");
   $update_orders->execute([$update_payment, $order_id]);
   $message[] = 'payment has been updated!';

};
//================================================



//SI IL CLICK SUR DELETE  DANS CETTE PAGE------
if(isset($_GET['delete'])){

   //on recupere l'id du qu'on a placer sur le LIENS
   $delete_id = $_GET['delete'];

   //REQUETE de SUPREESION de l'ORDRE
   $delete_orders = $conn->prepare("DELETE FROM `orders` WHERE id = ?");
   $delete_orders->execute([$delete_id]);
   header('location:admin_orders.php');
}
//================================================

?>


<!--------=========================================== HTML======================================-------------------- -->
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>orders</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="css/admin_style.css">

</head>
<body>
   

<!-- ON INCLU LA BAR DU HEADER (NAVBAR EN HAUT DE LA PAGE) -->
<?php include 'admin_header.php'; ?>
<!-- ----------------------------------- -->


<section class="placed-orders">

   <h1 class="title">placed orders</h1>

   <div class="box-container">

      <!-- --------------------------------SELECT TOUT LES ORDRES ET LES AFFICHER 1 PAR 1 ------------------------------------>
      <?php
         $select_orders = $conn->prepare("SELECT * FROM `orders`");
         $select_orders->execute();
         if($select_orders->rowCount() > 0){
            while($fetch_orders = $select_orders->fetch(PDO::FETCH_ASSOC)){
      ?>
            <div class="box"> 
            <!-- ------------AFFICHAGE DES INFORMATION DE L'ORDRE-------------------- -->
               <p> user id : <span><?= $fetch_orders['user_id']; ?></span> </p>
               <p> placed on : <span><?= $fetch_orders['placed_on']; ?></span> </p>
               <p> name : <span><?= $fetch_orders['name']; ?></span> </p>
               <p> email : <span><?= $fetch_orders['email']; ?></span> </p>
               <p> number : <span><?= $fetch_orders['number']; ?></span> </p>
               <p> address : <span><?= $fetch_orders['address']; ?></span> </p>
               <p> total products : <span><?= $fetch_orders['total_products']; ?></span> </p>
               <p> total price : <span>$<?= $fetch_orders['total_price']; ?>/-</span> </p>
               <p> payment method : <span><?= $fetch_orders['method']; ?></span> </p>
            <!-- ---------FORM POUR UPDATE le PAYMENTS STATUE OU DELETE L'ORDRE , GERER SUR LA meme page PHP---------------------- -->
               <form action="" method="POST">
                  <input type="hidden" name="order_id" value="<?= $fetch_orders['id']; ?>">
                  <select name="update_payment" class="drop-down">
                     <option value="" selected disabled><?= $fetch_orders['payment_status']; ?></option>
                     <option value="pending">pending</option>
                     <option value="completed">completed</option>
                  </select>
                  <!-- BUTTONS -->
                  <div class="flex-btn">
                     <input type="submit" name="update_order" class="option-btn" value="update">
                     <a href="admin_orders.php?delete=<?= $fetch_orders['id']; ?>" class="delete-btn" onclick="return confirm('delete this order?');">delete</a>
                  </div>
               </form>
            </div>
      <?php
         }
      }else{
         echo '<p class="empty">no orders placed yet!</p>';
      }
      ?>

   </div>

</section>












<script src="js/script.js"></script>

</body>
</html>