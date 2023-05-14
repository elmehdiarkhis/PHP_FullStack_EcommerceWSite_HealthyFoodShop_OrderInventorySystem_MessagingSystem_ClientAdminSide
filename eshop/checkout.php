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




//SI IL CLICK SUR ORDER EN BAS DE CETTE PAGE
if(isset($_POST['order'])){

   //RECUPERER LES INFORMATION DU FORM
   $name = $_POST['name'];
   $number = $_POST['number'];
   $email = $_POST['email'];
   $method = $_POST['method'];
   $address = 'flat no. '. $_POST['flat'] .' '. $_POST['street'] .' '. $_POST['city'] .' '. $_POST['state'] .' '. $_POST['country'] .' - '. $_POST['pin_code'];
   $placed_on = date('d-M-Y');

   $cart_total = 0;
   $cart_products[] = ''; //TABLEAU DE STIRNG

   $cart_query = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
   $cart_query->execute([$user_id]);


   //REMPLIR LE TABLEAU DE STRING AVEC -> banane(10)
   //CALCULER LE TOTALE DE LA CARTE
   if($cart_query->rowCount() > 0){
      while($cart_item = $cart_query->fetch(PDO::FETCH_ASSOC)){
         $cart_products[] = $cart_item['name'].' ( '.$cart_item['quantity'].' )';
         $sub_total = ($cart_item['price'] * $cart_item['quantity']);
         $cart_total += $sub_total;
      };
   };

   $total_products = implode(', ', $cart_products);
   //separer avec virgule les element du tableau


   //check si l'ordre existe deja
   $order_query = $conn->prepare("SELECT * FROM `orders` WHERE name = ? AND number = ? AND email = ? AND method = ? AND address = ? AND total_products = ? AND total_price = ?");
   $order_query->execute([$name, $number, $email, $method, $address, $total_products, $cart_total]);

   if($cart_total == 0){
      $message[] = 'Votre Cart est Vide';
   }elseif($order_query->rowCount() > 0){
      $message[] = 'Votre commande est deja placer!';
   }else{
      //INSERER DANS ORDERS PUIS DELETE DE CART
      $insert_order = $conn->prepare("INSERT INTO `orders`(user_id, name, number, email, method, address, total_products, total_price, placed_on) VALUES(?,?,?,?,?,?,?,?,?)");
      $insert_order->execute([$user_id, $name, $number, $email, $method, $address, $total_products, $cart_total, $placed_on]);
      $delete_cart = $conn->prepare("DELETE FROM `cart` WHERE user_id = ?");
      $delete_cart->execute([$user_id]);
      $message[] = 'Votre Ordre est a ete placer avec succes!';
   }

}

?>



<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>checkout</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<!-- IMPORTANT : les message[] sont gerer ici -->
<?php include 'header.php'; ?>
<!-- ---------------------------------------- -->

<section class="display-orders">

   <!-- REQUQTE RECUPER TOUT LES CART DU USER SPECIFIQUE -->
    <!-- GT += PRIX * QTY -->
   <?php
      $cart_grand_total = 0;
      $select_cart_items = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
      $select_cart_items->execute([$user_id]);
      if($select_cart_items->rowCount() > 0){
         while($fetch_cart_items = $select_cart_items->fetch(PDO::FETCH_ASSOC)){
            $cart_total_price = ($fetch_cart_items['price'] * $fetch_cart_items['quantity']);
            $cart_grand_total += $cart_total_price;
   ?>
   <!-- AFFICHER LE PRIX EST LA QUANTITE -->
   <p> <?= $fetch_cart_items['name']; ?> <span>(<?= '$'.$fetch_cart_items['price'].'/- x '. $fetch_cart_items['quantity']; ?>)</span> </p>
   <?php
    }
   }else{
      echo '<p class="empty">Votre Carte est Vide!</p>';
   }
   ?>
   <!-- AFFICHER LE GT -->
   <div class="grand-total">grand totale : <span>$<?= $cart_grand_total; ?>/-</span></div>
</section>

<section class="checkout-orders">

   <form action="" method="POST">

      <h3>placer votre ordre</h3>

      <div class="flex">
         <div class="inputBox">
            <span>votre nom :</span>
            <input type="text" name="name" placeholder="entrez votre nom" class="box" required>
         </div>
         <div class="inputBox">
            <span>votre numero :</span>
            <input type="number" name="number" placeholder="entrez votre numero" class="box" required>
         </div>
         <div class="inputBox">
            <span>votre email :</span>
            <input type="email" name="email" placeholder="entrez votre email" class="box" required>
         </div>
         <div class="inputBox">
            <span> methode de payement :</span>
            <select name="method" class="box" required>
               <option value="cash on delivery">cash on delivery</option>
               <option value="credit card">credit card</option>
               <option value="paytm">paytm</option>
               <option value="paypal">paypal</option>
            </select>
         </div>
         <div class="inputBox">
            <span>address line 01 :</span>
            <input type="text" name="flat" placeholder="e.g. flat number" class="box" required>
         </div>
         <div class="inputBox">
            <span>address line 02 :</span>
            <input type="text" name="street" placeholder="e.g. street name" class="box" required>
         </div>
         <div class="inputBox">
            <span>ville :</span>
            <input type="text" name="city" placeholder="e.g. mumbai" class="box" required>
         </div>
         <div class="inputBox">
            <span>province/etat :</span>
            <input type="text" name="state" placeholder="e.g. maharashtra" class="box" required>
         </div>
         <div class="inputBox">
            <span>pays :</span>
            <input type="text" name="country" placeholder="e.g. India" class="box" required>
         </div>
         <div class="inputBox">
            <span>pin code :</span>
            <input type="number" min="0" name="pin_code" placeholder="e.g. 123456" class="box" required>
         </div>
      </div>

      <!-- SI LE GRAND TOTAL EST INFERRIEUR a 1 > DISABLED -->
      <input type="submit" name="order" class="btn <?= ($cart_grand_total > 1)?'':'disabled'; ?>" value="place order">

   </form>

</section>








<?php include 'footer.php'; ?>

<script src="js/script.js"></script>

</body>
</html>