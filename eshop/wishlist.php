<?php

@include 'config.php';

session_start();
//en recupere l'id du user connecter
$user_id = $_SESSION['user_id'];
//=================================
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

//Si l'utlisateur Veux ajouter un produit a la Cart==================================
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
      $message[] = 'Ce prodiuit est deja dans votre cart!';
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
      $message[] = 'Le Produit a ete ajouter a la cart avec succes!';
   }
}
//======================================================================================


//SI IL CLICK SUR DELETE SUR CETTTE PAGE
if(isset($_GET['delete'])){

   $delete_id = $_GET['delete'];
   $delete_wishlist_item = $conn->prepare("DELETE FROM `wishlist` WHERE id = ?");
   $delete_wishlist_item->execute([$delete_id]);
   header('location:wishlist.php');
}
//==================



//SI IL CLICK SUR DELETE ALL SUR CETTE PAGE
if(isset($_GET['delete_all'])){

   $delete_wishlist_item = $conn->prepare("DELETE FROM `wishlist` WHERE user_id = ?");
   $delete_wishlist_item->execute([$user_id]);
   header('location:wishlist.php');
}
//=====================

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>wishlist</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   

<!-- IMPORTANT : les message[] sont gerer ici -->
<?php include 'header.php'; ?>
<!-- ---------------------------------------- -->


<section class="wishlist">
   <h1 class="title">products added</h1>
   <div class="box-container">
   <!-- REQUETE POUR AFFICHER TOUT LES WISHLIST -->
   <!-- INITIALISER UNE VARIABLE A ZERO POUR FAIRE LA SOMME DES WISHLIST-->
   <?php
      $grand_total = 0;
      $select_wishlist = $conn->prepare("SELECT * FROM `wishlist` WHERE user_id = ?");
      $select_wishlist->execute([$user_id]);
      if($select_wishlist->rowCount() > 0){
         while($fetch_wishlist = $select_wishlist->fetch(PDO::FETCH_ASSOC)){ 
   ?>
      <form action="" method="POST" class="box">
            <!-- LE DELETE EST GERER DANS CE FICHIER -->
         <a href="wishlist.php?delete=<?= $fetch_wishlist['id']; ?>" class="fas fa-times" onclick="return confirm('delete this from wishlist?');"></a>

         <!-- DETAIL DU PRODUIT  -->
         <a href="view_page.php?pid=<?= $fetch_wishlist['pid']; ?>" class="fas fa-eye"></a>

         <!-- SHOW LES INFORMATION des produit en wishlist -->
         <img src="uploaded_img/<?= $fetch_wishlist['image']; ?>" alt="">
         <div class="name"><?= $fetch_wishlist['name']; ?></div>
         <div class="price">$<?= $fetch_wishlist['price']; ?>/-</div>
         <input type="number" min="1" value="1" class="qty" name="p_qty">

         <!-- PASSER LES INFOS UTILS DANS DES HIDDEN INPUTS -->
         <input type="hidden" name="pid" value="<?= $fetch_wishlist['pid']; ?>">
         <input type="hidden" name="p_name" value="<?= $fetch_wishlist['name']; ?>">
         <input type="hidden" name="p_price" value="<?= $fetch_wishlist['price']; ?>">
         <input type="hidden" name="p_image" value="<?= $fetch_wishlist['image']; ?>">

         <!-- BUTTONS -->
         <input type="submit" value="add to cart" name="add_to_cart" class="btn">
      </form>
   <?php
   // SOMME DES PRIX DES PRODUIT EN WISHLIST
      $grand_total += $fetch_wishlist['price'];
      }
   }else{
      echo '<p class="empty">Votre Wishlist est vide!</p>';
   }
   ?>
   </div>

   <div class="wishlist-total">
      <!-- AFFICHER LE GRAND TOTALE -->
      <p>grand total : <span>$<?= $grand_total; ?>/-</span></p>
      <a href="shop.php" class="option-btn">continuer mon shopping</a>

      <!-- LE DELETE ALL EST GERER DANS CE FICHIER -->
      <a href="wishlist.php?delete_all" class="delete-btn <?= ($grand_total > 1)?'':'disabled'; ?>">supprimmer tout</a>
   </div>
</section>








<?php include 'footer.php'; ?>

<script src="js/script.js"></script>

</body>
</html>