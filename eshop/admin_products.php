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



//==================================================================
//INSERTION des infos du produit dans la balise form dans cette page 
if(isset($_POST['add_product'])){


    //RECUPERATION des infos dans La form avec method POST==============
   $name = $_POST['name'];
   $price = $_POST['price'];
   $category = $_POST['category'];
   $details = $_POST['details'];
    //====================================================================


    //RECUPERATION des infos de l'image uploaded==================
   $image = $_FILES['image']['name'];

   $image_size = $_FILES['image']['size'];
   $image_tmp_name = $_FILES['image']['tmp_name'];
   $image_folder = 'uploaded_img/'.$image;
    //============================================================


    //VERIFICATION si le produit existe deja ============================
   $select_products = $conn->prepare("SELECT * FROM `products` WHERE name = ?");
   $select_products->execute([$name]);
    //=====================================================================

    //Si le produi existe deja: ajout message =============================================
   if($select_products->rowCount() > 0){
      $message[] = 'Ce produit existe deja!';
   }
   else{
   //Si le produit n'existe pas ===========================================
     
      //Si l'image est de size convenable==================
      if($image_size < 2000000){
        
        //INSERTION du new Produit dans DB===============================
        $insert_products = $conn->prepare("INSERT INTO `products`(name, category, details, price, image) VALUES(?,?,?,?,?)");
        $insert_products->execute([$name, $category, $details, $price, $image]);

        //SI INSERTION succes: ajout message / ajout photo dans le dossier==========
        if($insert_products){            
            move_uploaded_file($image_tmp_name, $image_folder);
            $message[] = 'Nouveau produit qjouté!';
         }   
      }
      else{
         //Si l'image est de grande size===================
         $message[] = 'Image est trop large!';
      }  
     //====================================================

   }

};


//DELETE produit si click sur BUTTON Delete (avec id )===================
if(isset($_GET['delete'])){

    //RECUPERATION de l'ID du produit===================
   $delete_id = $_GET['delete'];

   //DELETE de l'image du produit du Dossier uploaded_img=================
   $select_delete_image = $conn->prepare("SELECT image FROM `products` WHERE id = ?");
   $select_delete_image->execute([$delete_id]);
   $fetch_delete_image = $select_delete_image->fetch(PDO::FETCH_ASSOC);
   unlink('uploaded_img/'.$fetch_delete_image['image']);
   //================================================================

   //DELETE produit de la DB==========================================
   $delete_products = $conn->prepare("DELETE FROM `products` WHERE id = ?");
   $delete_products->execute([$delete_id]);
    //===============================================================

    //DELETE produit de la WISHLIST ================================
   $delete_wishlist = $conn->prepare("DELETE FROM `wishlist` WHERE pid = ?");
   $delete_wishlist->execute([$delete_id]);
    //==============================================================

    //DELETE produit du CART =========================================
   $delete_cart = $conn->prepare("DELETE FROM `cart` WHERE pid = ?");
   $delete_cart->execute([$delete_id]);
   //===============================================================

   //redirect vers admin_products.php ===================================
   header('location:admin_products.php');


}
//========================================================================

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Produits</title>


   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="css/admin_style.css">

</head>
<body>
   
<!-- LES MESSAGES SONT GERER ICI -->
<?php include 'admin_header.php'; ?>
<!-- -- -->

<section class="add-products">

   <h1 class="title">Ajouter un nouveau produit</h1>

   <form action="" method="POST" enctype="multipart/form-data">
      <div class="flex">
         <div class="inputBox">
         <input type="text" name="name" class="box" required placeholder="enter product name">
         <select name="category" class="box" required>
            <option value="" selected disabled>selectionner categorie</option>
               <option value="vegitables">légumes</option>
               <option value="fruits">fruits</option>
               <option value="meat">viande</option>
               <option value="fish">poisson</option>
         </select>
         </div>
         <div class="inputBox">
         <input type="number" min="0" name="price" class="box" required placeholder="entrer prix du produit">
         <input type="file" name="image" required class="box" accept="image/jpg, image/jpeg, image/png">
         </div>
      </div>
      <textarea name="details" class="box" required placeholder="entrer détail du produit" cols="30" rows="10"></textarea>
      <input type="submit" class="btn" value="Ajouter produit" name="add_product">
   </form>

</section>

<section class="show-products">

   <h1 class="title">Produit ajouté</h1>

   <div class="box-container">

   <!-- AFFICHAGE de tout les produits dans DB ====================== -->
   <?php
    
    //SELECTION des infos des produis ===================================
      $show_products = $conn->prepare("SELECT * FROM `products`");
      $show_products->execute();
      //================================================================

      //Si produit existe =========================================
      if($show_products->rowCount() > 0){



        //While fetch : AFFICHAGE des produits===========================
         while($fetch_products = $show_products->fetch(PDO::FETCH_ASSOC)){  
   ?>
   <div class="box">
      <div class="price">$<?= $fetch_products['price']; ?>/-</div>
      <img src="uploaded_img/<?= $fetch_products['image']; ?>" alt="">
      <div class="name"><?= $fetch_products['name']; ?></div>
      <div class="cat"><?= $fetch_products['category']; ?></div>
      <div class="details"><?= $fetch_products['details']; ?></div>
      <div class="flex-btn">


        <!-- Button pour UPDDATE -->
        <!-- update= ID -->
        <!-- update in admin_update_product.php -->
         <a href="admin_update_product.php?update=<?= $fetch_products['id']; ?>" class="option-btn">Modifier</a>


         <!-- Button pour DELETE  -->
         <!-- delete= ID -->
         <a href="admin_products.php?delete=<?= $fetch_products['id']; ?>" class="delete-btn" onclick="return confirm('voulez vous suprimmer ce Produit?');">Supprimer</a>
      </div>
   </div>
   <?php
      }
      //=================================================================
   }
   //Si aucun PRODUIT n'existe dans DB: affiche message
   else{
      echo '<p class="empty">Pas de produit ajouté!</p>';
   }
   ?>
   <!-- ============================================================= -->

   </div>

</section>











<script src="js/script.js"></script>

</body>
</html>