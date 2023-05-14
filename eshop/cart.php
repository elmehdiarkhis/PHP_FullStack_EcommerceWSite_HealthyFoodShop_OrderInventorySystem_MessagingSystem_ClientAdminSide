<!--=============================== LE USER VEUX VOIR LES ELEMENT DE CA CART============================================= -->
<?php

@include 'config.php';

session_start();
//Recupere l'id du USER connecter=====
$user_id = $_SESSION['user_id'];
//======================================

//Si le USER n'est pas encore connecter
if(!isset($user_id)){
   header('location:login.php');
};
//======================================


// isSET viene touts de cart.php-----------
if(isset($_GET['delete'])){

   //recuperer l'ID de la cart , du LINK
   $delete_id = $_GET['delete'];

   //requette pour delete where id de la cart
   $delete_cart_item = $conn->prepare("DELETE FROM `cart` WHERE id = ?");
   $delete_cart_item->execute([$delete_id]);

   //reload la page
   header('location:cart.php');
}

if(isset($_GET['delete_all'])){

   //requette pour delete ALL >> where l'ID du USER , non pas du produit ,
   $delete_cart_item = $conn->prepare("DELETE FROM `cart` WHERE user_id = ?");
   $delete_cart_item->execute([$user_id]);

   //reload la page
   header('location:cart.php');
}

if(isset($_POST['update_qty'])){

   // Recuperer l'id de le cart du Hidden input
   $cart_id = $_POST['cart_id'];
   // Recuperer la qunatite choisir par le user 
   $p_qty = $_POST['p_qty'];

   //requete sql pour update la quantite du produit dans la cart where id 
   $update_qty = $conn->prepare("UPDATE `cart` SET quantity = ? WHERE id = ?");
   $update_qty->execute([$p_qty, $cart_id]);
   $message[] = 'la quantite a ete modifier avec succes!';
}
// ---------------------------------------
?>


<!--------=========================================== HTML======================================-------------------- -->
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Shopping cart</title>


   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<!-- IMPORTANT : les message[] sont gerer ici -->
<?php include 'header.php'; ?>
<!-- ---------------------------------------- -->

<section class="shopping-cart">

   <h1 class="title">Produits Ajouter</h1>

   <div class="box-container">
         <!-- --------------------------------SELECT TOUT LES ADDED  TO CART ET LES AFFICHER 1 PAR 1 ------------------------------------>
      <?php
         $grand_total = 0;
         $select_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
         $select_cart->execute([$user_id]);
         if($select_cart->rowCount() > 0){
            while($fetch_cart = $select_cart->fetch(PDO::FETCH_ASSOC)){ 
      ?>
            <form action="" method="POST" class="box">
               <!-- gerer le DELETE byID DANS CETTE PAGE -->
               <a href="cart.php?delete=<?= $fetch_cart['id']; ?>" class="fas fa-times" onclick="return confirm('Etes vous sur de vouloir le suprimmer de votre Carte ?');"></a>
               <!-- l'envoyer a view_page.php si il veux voir les detail du produit  -->
               <a href="view_page.php?pid=<?= $fetch_cart['pid']; ?>" class="fas fa-eye"></a>

               <!-- Afficher les Informatiion de la CART -->
               <img src="uploaded_img/<?= $fetch_cart['image']; ?>" alt="">
               <div class="name"><?= $fetch_cart['name']; ?></div>
               <div class="price">$<?= $fetch_cart['price']; ?>/-</div>
               <!-- envoyer l'id en submit dans un Hidden Input -->
               <input type="hidden" name="cart_id" value="<?= $fetch_cart['id']; ?>">
               <!-- choix de la quantite + BUTTON Update_quantite -->
               <div class="flex-btn">
                  <input type="number" min="1" value="<?= $fetch_cart['quantity']; ?>" class="qty" name="p_qty">
                  <input type="submit" value="update" name="update_qty" class="option-btn">
               </div>
               <!-- CALCUL POUR AFFICHER le TOTAL DE JUST CE PRODUIT -->
               <div class="sub-total"> sub total : <span>$<?= $sub_total = ($fetch_cart['price'] * $fetch_cart['quantity']); ?>/-</span> </div>
            </form>
      
      <?php
         // AVANT LE ELSE , AJOUTER Le SOUS TOTAL , AU TOTAL DE TOUTS LE PRODUIT
         $grand_total += $sub_total;
         }
      }else{
         echo '<p class="empty">your cart is empty</p>';
      }
      ?>
   </div>

   <div class="cart-total">
      <!-- AFFICHER LE PRIX TOTAL DE TOUT LES PRODUITS -->
      <p>grand total : <span>$<?= $grand_total; ?>/-</span></p>

      <!-- Redirect to shop -->
      <a href="shop.php" class="option-btn">Continuer mon shopping</a>


      <!-- BUTTONS DELETE ALL OU CHECKOUT -->
         <!-- LES BUTTON SERONT DSIABLED SI  Le GRAND TOTAL n'est pas SUPERRIEUR A 1 -->
      <a href="cart.php?delete_all" class="delete-btn <?= ($grand_total > 1)?'':'disabled'; ?>">Suprimmer tout</a>
      <a href="checkout.php" class="btn <?= ($grand_total > 1)?'':'disabled'; ?>">Acheter(Checkout)</a>
   </div>

</section>


<!-- INCLUDE LE FOOTER -->
<?php include 'footer.php'; ?>
<!-- ----------- -->


<script src="js/script.js"></script>

</body>
</html>