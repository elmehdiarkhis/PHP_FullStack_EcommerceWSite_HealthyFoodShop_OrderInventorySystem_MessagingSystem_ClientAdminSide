<!-- Si On a set le message[] dans une autre fichier .php  -->
<!-- puisque on includ header dans tout les fichier -->
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
<!-- ------------------------------------------------------- -->
<header class="header">
   <div class="flex">
      <a href="admin_page.php" class="logo">SantéFood<span>.</span></a>

      <nav class="navbar">
         <a href="home.php">Acceuil</a>
         <a href="shop.php">shop</a>
         <a href="orders.php">commandes</a>
         <a href="about.php">à propos</a>
         <a href="contact.php">contact</a>
      </nav>

      <div class="icons">
         <div id="menu-btn" class="fas fa-bars"></div>
         <div id="user-btn" class="fas fa-user"></div>
         <a href="search_page.php" class="fas fa-search"></a>

         <!-- Requete sql : Pour savoir le nombre de AddToCard et le nombre de WichList et les AFFICHER-->
         <?php
            // le UserID est recuperer dans les pgae (ex:Home), non pas dans le header.php
            $count_cart_items = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
            $count_cart_items->execute([$user_id]);
            $count_wishlist_items = $conn->prepare("SELECT * FROM `wishlist` WHERE user_id = ?");
            $count_wishlist_items->execute([$user_id]);
         ?>
         <a href="wishlist.php"><i class="fas fa-heart"></i><span>(<?= $count_wishlist_items->rowCount(); ?>)</span></a>
         <a href="cart.php"><i class="fas fa-shopping-cart"></i><span>(<?= $count_cart_items->rowCount(); ?>)</span></a>
         <!-- ------------------------------ -->

      </div>
      <div class="profile">
         <!-- Requete sql : select pour  Afficher les informations du Profil de la personne connecter  -->
         <?php
            $select_profile = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
            $select_profile->execute([$user_id]);
            $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
         ?>
         <img src="uploaded_img/<?= $fetch_profile['image']; ?>" alt="">
         <p><?= $fetch_profile['name']; ?></p>

         <!-- BUTTONS -->
         <a href="user_profile_update.php" class="btn">Modifier mon profile</a>
         <a href="logout.php" class="delete-btn">logout</a>

         <!-- ---------------------------------------------------------------------------------------- -->
      </div>
   </div>
</header>


