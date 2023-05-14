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
      $message[] = 'Ce prodiuit est deja dans votre cart!';
   }else{
      $insert_wishlist = $conn->prepare("INSERT INTO `wishlist`(user_id, pid, name, price, image) VALUES(?,?,?,?,?)");
      $insert_wishlist->execute([$user_id, $pid, $p_name, $p_price, $p_image]);
      $message[] = 'Le Produit a ete ajouter a la wishlist avec succes!';
   }
}
//======================================================================================


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
//===================================================================================

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Acceuil</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="css/style.css">
</head>
<body>
 
<!-- IMPORTANT : les message[] sont gerer ici -->
<?php include 'header.php'; ?>
<!-- ---------------------------------------- -->


<div class="home-bg">

   <section class="home">

      <div class="content">
         <span>pas de PANIC, achetez ORGANIC</span>
         <h3>Atteignez une meilleure santé avec des aliments biologiques</h3>
         <p>Notre mise en marché de proximité est basée sur les principes de l'agriculture soutenue par la communauté, c'est-à-dire qu'il n'y a aucun intermédiaire entre vous et votre producteur·trice local·e.</p>
         <a href="about.php" class="btn">à propos</a>
      </div>

   </section>

</div>

<!-- ICI On POUVAIT CREER UNE TABLE CATEGORY  -->
<!-- et DONNER LA POSSIBILITE A L'ADMIN DE LA CRUD  -->
<!-- ET L'UTILISER EN PHP ICI -->
<section class="home-category">

   <h1 class="title">shop par category</h1>

   <div class="box-container">

      <div class="box">
         <img src="images/cat-1.png" alt="">
         <h3>fruits</h3>
         <p>Mangez des fruits biologiques frais et savoureux toute l'année et redécouvrez le plaisir de manger au rythme des saisons.Été comme hiver, découvrez ce qui vous attend !</p>
         <a href="category.php?category=fruits" class="btn">fruits</a>
      </div>

      <div class="box">
         <img src="images/cat-2.png" alt="">
         <h3>viandes</h3>
         <p>Manger de la viande est un geste politique. En achetant directement chez le producteur, je vote pour que les petites fermes familiales continuent d’exister</p>
         <a href="category.php?category=meat" class="btn">viandes</a>
      </div>

      <div class="box">
         <img src="images/cat-3.png" alt="">
         <h3>légumes</h3>
         <p>Choisir les aliments certifiés biologiques du Québec, c'est s'assurer d'une alimentation remplie de fraîcheur, de saveurs et de nutriments, découvrez ce qui vous attend !</p>
         <a href="category.php?category=vegitables" class="btn">légumes</a>
      </div>

      <div class="box">
         <img src="images/cat-4.png" alt="">
         <h3>Poissons</h3>
         <p>Notre marché réfrigéré contient plus de 200 variétés de poissons et de fruits de mer frais. Choisissez vous-même votre poisson</p>
         <a href="category.php?category=fish" class="btn">Poissons</a>
      </div>

   </div>
</section>
<!-- -------------------------------------------- -->




<section class="products">

   <h1 class="title">Derniers produit Ajouté</h1>

   <div class="box-container">

   <!-- Selectionner les 6 dernier produit qui ont ete ajouter derrnierement + les Afficher en Boucle -->
   <?php
      $select_products = $conn->prepare("SELECT * FROM `products` LIMIT 6");
      $select_products->execute();
      if($select_products->rowCount() > 0){
         while($fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)){ 
   ?>
      <form action="" class="box" method="POST">
        
      <!-- Afficher les information -->
         <div class="price">$<span><?= $fetch_products['price']; ?></span>/-</div>
         <a href="view_page.php?pid=<?= $fetch_products['id']; ?>" class="fas fa-eye"></a>
         <img src="uploaded_img/<?= $fetch_products['image']; ?>" alt="">
         <div class="name"><?= $fetch_products['name']; ?></div>
         <!-- ------------------------ -->

         <!-- Hidden Inputs qui contienne les infos du produit-->
         <input type="hidden" name="pid" value="<?= $fetch_products['id']; ?>">
         <input type="hidden" name="p_name" value="<?= $fetch_products['name']; ?>">
         <input type="hidden" name="p_price" value="<?= $fetch_products['price']; ?>">
         <input type="hidden" name="p_image" value="<?= $fetch_products['image']; ?>">
         <input type="number" min="1" value="1" name="p_qty" class="qty">
         <!-- ------------------------ -->

         <!-- Buttons-->
         <input type="submit" value="ajouter a la wishlist" class="option-btn" name="add_to_wishlist">
         <input type="submit" value="ajouter a la cart" class="btn" name="add_to_cart">
      </form>
   <?php
      }
   }else{
      echo '<p class="empty">Pas de Produit pour l instant!</p>';
   }
   ?>
   <!-- ----------------------------------------------------------------------------- -->

   </div>

</section>







<?php include 'footer.php'; ?>

<script src="js/script.js"></script>

</body>
</html>