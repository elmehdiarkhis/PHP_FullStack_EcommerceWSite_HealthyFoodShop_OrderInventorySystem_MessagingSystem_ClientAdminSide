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
//UPDATE les infos du produit dans la balise form dans cette page 
if(isset($_POST['update_product'])){

    //RECUPERATION des infos dans La form avec method POST===============
   $pid = $_POST['pid'];
   $name = $_POST['name'];
   $price = $_POST['price'];
   $category = $_POST['category'];
   $details = $_POST['details'];
   //=====================================================================

    //RECUPERATION des infos de l'image uploaded==================
   $image = $_FILES['image']['name'];
   $image_size = $_FILES['image']['size'];
   $image_tmp_name = $_FILES['image']['tmp_name'];
   $image_folder = 'uploaded_img/'.$image;
   $old_image = $_POST['old_image'];
   //==============================================================


   

   if(!empty($image)){
      if($image_size > 2000000){
         $message[] = 'Image est trop large!';
      }else{

         //UPDATE imgae du produit dans DB==============================
         $update_image = $conn->prepare("UPDATE `products` SET image = ? WHERE id = ?");
         $update_image->execute([$image, $pid]);
         //=============================================================


         //SI Update succes :
         if($update_image){

            //UPDATE des infos du produit dans la DB (tout sauf l'image)==================
            $update_product = $conn->prepare("UPDATE `products` SET name = ?, category = ?, details = ?, price = ? WHERE id = ?");
            $update_product->execute([$name, $category, $details, $price, $pid]);
            //Set message pour utilisateur =========================
            $message[] = 'Produit modifié avec succés!';
            move_uploaded_file($image_tmp_name, $image_folder);
            unlink('uploaded_img/'.$old_image);
            //=============================================================
         }
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
   <title>Modification produits</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/admin_style.css">

</head>
<body>
   
//navbar===================
<?php include 'admin_header.php'; ?>

<section class="update-product">

   <h1 class="title">Modifier produit</h1>   

   <?php
      //GET ID from BUTTON Update in admin_products.php====================
      $update_id = $_GET['update'];


      //SELECTION des info du produit Avec ID ============================
      $select_products = $conn->prepare("SELECT * FROM `products` WHERE id = ?");
      $select_products->execute([$update_id]);
      //===================================================================

      //AFFICHAGE des infos du PRODUIT(form) avec valeur=info=============
      if($select_products->rowCount() > 0){
         while($fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)){ 
   ?>
   <form action="" method="post" enctype="multipart/form-data">
      <input type="hidden" name="old_image" value="<?= $fetch_products['image']; ?>">
      <input type="hidden" name="pid" value="<?= $fetch_products['id']; ?>">
      <img src="uploaded_img/<?= $fetch_products['image']; ?>" alt="">
      <input type="text" name="name" placeholder="enter product name" required class="box" value="<?= $fetch_products['name']; ?>">
      <input type="number" name="price" min="0" placeholder="enter product price" required class="box" value="<?= $fetch_products['price']; ?>">
      <select name="category" class="box" required>
         <option selected><?= $fetch_products['category']; ?></option>
         <option value="vegitables">légumes</option>
         <option value="fruits">fruits</option>
         <option value="meat">viande</option>
         <option value="fish">poisson</option>
      </select>
      <textarea name="details" required placeholder="enter product details" class="box" cols="30" rows="10"><?= $fetch_products['details']; ?></textarea>
      <input type="file" name="image" class="box" accept="image/jpg, image/jpeg, image/png">
      <div class="flex-btn">
         <input type="submit" class="btn" value="Modifier produit" name="update_product">
         <a href="admin_products.php" class="option-btn">Retourner</a>
      </div>
   </form>
   <?php
         }
      }
      //=================================================================

      //Message s'il n'y a pas de produit
      else{
         echo '<p class="empty">Pas de produit trouvés!</p>';
      }
   ?>

</section>













<script src="js/script.js"></script>

</body>
</html>