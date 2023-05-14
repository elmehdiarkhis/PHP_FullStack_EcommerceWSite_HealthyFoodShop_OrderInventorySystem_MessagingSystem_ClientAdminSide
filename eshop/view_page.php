<?php

@include 'config.php';

session_start();

//en recupere l'id
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


//Si l'utlisateur Veux ajouter un produit au WishList==================================
if(isset($_POST['add_to_wishlist'])){

   //recuperer les valeur des Hidden input dans le form en bas======
   $pid = $_POST['pid'];
   $p_name = $_POST['p_name'];
   $p_price = $_POST['p_price'];
   $p_image = $_POST['p_image'];
   //=============================


   //Requete Sql : Check si le produit deja Added to WichList ou deja Added to CART Par ce USER======
   $check_wishlist_numbers = $conn->prepare("SELECT * FROM `wishlist` WHERE name = ? AND user_id = ?");
   $check_wishlist_numbers->execute([$p_name, $user_id]);
   //------
   $check_cart_numbers = $conn->prepare("SELECT * FROM `cart` WHERE name = ? AND user_id = ?");
   $check_cart_numbers->execute([$p_name, $user_id]);

   //Validations
   if($check_wishlist_numbers->rowCount() > 0){
      $message[] = 'Ce prodiuit est deja dans votre wishlist!';
   }elseif($check_cart_numbers->rowCount() > 0){
      $message[] = 'Ce prodiuit est deja dans votre carte!';
   }else{
      $insert_wishlist = $conn->prepare("INSERT INTO `wishlist`(user_id, pid, name, price, image) VALUES(?,?,?,?,?)");
      $insert_wishlist->execute([$user_id, $pid, $p_name, $p_price, $p_image]);
      $message[] = 'Le Produit a ete ajouter a la wishlist!';
   }

}

//Si l'utlisateur Veux ajouter un produit au Add_to_Cart==================================
if(isset($_POST['add_to_cart'])){

   //recuperer les valeur des Hidden input dans le form en bas======
   $pid = $_POST['pid'];
   $p_name = $_POST['p_name'];
   $p_price = $_POST['p_price'];
   $p_image = $_POST['p_image'];
   $p_qty = $_POST['p_qty'];
   //=============================

   //Requete Sql : Check si le produit est deja Added to Add To Cart======
   $check_cart_numbers = $conn->prepare("SELECT * FROM `cart` WHERE name = ? AND user_id = ?");
   $check_cart_numbers->execute([$p_name, $user_id]);

   if($check_cart_numbers->rowCount() > 0){
      $message[] = 'Ce prodiuit est deja dans votre carte!';
   }else{

      //Requete sql : check si le produit et Dans la Wich List
      // > Si Oui , le Delete de la WichList et l'ajouter dans l'add_to_cart
      //Si Non , l'ajouter
      $check_wishlist_numbers = $conn->prepare("SELECT * FROM `wishlist` WHERE name = ? AND user_id = ?");
      $check_wishlist_numbers->execute([$p_name, $user_id]);

      if($check_wishlist_numbers->rowCount() > 0){
         $delete_wishlist = $conn->prepare("DELETE FROM `wishlist` WHERE name = ? AND user_id = ?");
         $delete_wishlist->execute([$p_name, $user_id]);
      }

      $insert_cart = $conn->prepare("INSERT INTO `cart`(user_id, pid, name, price, quantity, image) VALUES(?,?,?,?,?,?)");
      $insert_cart->execute([$user_id, $pid, $p_name, $p_price, $p_qty, $p_image]);
      $message[] = 'Le Produit a ete ajouter a la carte avec succes';
   }

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Vue Rapide</title>


   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<!-- IMPORTANT : les message[] sont gerer ici -->
<?php include 'header.php'; ?>
<!-- ---------------------------------------- -->

<section class="quick-view">

   <h1 class="title">Vue Rapide</h1>

   <!-- SELECT LE PRODUIT QUAND VEUX VOIR RAPIDEMENT -->
   <?php
      // on recupere le Products ID du Link , (cart.php/Home.php)
      $pid = $_GET['pid'];
      $select_products = $conn->prepare("SELECT * FROM `products` WHERE id = ?");
      $select_products->execute([$pid]);
      if($select_products->rowCount() > 0){
         while($fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)){ 
   ?>
         <form action="" class="box" method="POST">
            <!-- AFFICHER LES INFORMATIONS DU PRODUIT -->
            <div class="price">$<span><?= $fetch_products['price']; ?></span>/-</div>
            <img src="uploaded_img/<?= $fetch_products['image']; ?>" alt="">
            <div class="name"><?= $fetch_products['name']; ?></div>
            <div class="details"><?= $fetch_products['details']; ?></div>

            <!-- BUTTONS (ADD TO ...)  -->
            <!-- Hidden inputs pour recupere les infos apres submit -->
            <input type="hidden" name="pid" value="<?= $fetch_products['id']; ?>">
            <input type="hidden" name="p_name" value="<?= $fetch_products['name']; ?>">
            <input type="hidden" name="p_price" value="<?= $fetch_products['price']; ?>">
            <input type="hidden" name="p_image" value="<?= $fetch_products['image']; ?>">
            <input type="number" min="1" value="1" name="p_qty" class="qty">
            <!-- --- -->
            <input type="submit" value="ajouter a la wishlist" class="option-btn" name="add_to_wishlist">
            <input type="submit" value="ajouter a la cart" class="btn" name="add_to_cart">
            <!-- ------------------------------------------------- -->
         </form>
   <?php
         }
      }else{
         echo '<p class="empty">Pas de Produit disponnible pour l instant!</p>';
      }
   ?>

</section>








<?php include 'footer.php'; ?>

<script src="js/script.js"></script>

</body>
</html>

