<script>
	function calcPrice(){
		var quantity = document.getElementById("quantity").value;
		var uPrice = document.getElementById("uPrice").value;
		var total = quantity * uPrice;
		document.getElementById("totalPrice").value = total;
		document.getElementById("totalPriceH").value = total;
	  	//return total;
	}
	function validateForm(){
		var field = document.getElementById('quantity').value;
		if(/^\d+$/.test(field)){
			return true;
		}else{
			alert("Not a number");
			return false;
		}
	}
</script>
<?php
session_start();
include_once('databaseCon.php');

if(isset($_POST['sendQ'])){
	$idInventory = $_POST['idInventory'];
	$oldQuantity = $_POST['oldQuantity'];
	$quantity = $_POST['quantity'];
	$newQuantity = $oldQuantity-$quantity;
	$price = $_POST['totalPriceH'];
	$product = $_POST['nameProduct'];
	$idProduct = $_POST['idProduct'];

	$sqlUpIn = "update inventory set quantity_inventory = ".$newQuantity." where id_inventory = ".$idInventory.";";
	$resultUpIn = mysqli_query($conn, $sqlUpIn);

	if($resultUpIn === TRUE){
	  echo "*********<br>Order ready to generate<br>*********<br>";
	  $_SESSION["idInventory"] = $idInventory;
	  $_SESSION["idProduct"] = $idProduct;
	  $_SESSION["product"] = $product;
	  $_SESSION["quantity"] = $quantity;
	  $_SESSION["price"] = $price;
	}else{
	  echo "Error: ".$sqlUpIn."<br>";
	}
}

if(isset($_POST['sendP'])){
	$id = $_POST['product'];
	$sqlSelJoin = "select products.id_product, name_product, id_inventory, quantity_inventory, price_inventory from inventory inner join products on inventory.id_product = products.id_product where products.id_product = '".$id."'";
	$resultSelJoin = mysqli_query($conn, $sqlSelJoin);

	if($id > 0 && mysqli_num_rows($resultSelJoin) > 0){
		$row = mysqli_fetch_assoc($resultSelJoin);
		if($row['quantity_inventory'] > 0){
	    	echo "<form action = 'purchaseProducts.php' onsubmit='return validateForm();' method = 'POST'>
	    		<input type = 'hidden' name = 'idProduct' value = '".$row['id_product']."' />
	    		<input type = 'hidden' name = 'nameProduct' value = '".$row['name_product']."' />
	    		<label name = '".$row["id_product"]."'>".$row["name_product"]."</label><br>";
	    	echo "<label> Stock: </label>
	    		<input type = 'hidden' name = 'idInventory' value = '".$row['id_inventory']."' />
	    		<input type = 'hidden' name = 'oldQuantity' value = '".$row['quantity_inventory']."' />
	    		<label> ".$row['quantity_inventory'].": </label><br>
	    		<label> Unitary price: </label>
	    		<label> ".$row['price_inventory']." </label>
	    		<input type = 'hidden' id = 'uPrice' value = '".$row['price_inventory']."' />
	    		<br><br>
	    		********************************************<br>
	    		<label> Quantity for buy: </label>
				<input type = 'text' id = 'quantity' name = 'quantity' onchange = 'calcPrice();' /><br>
				<label> Total price: </label>
				<input type = 'hidden' id = 'totalPriceH' name = 'totalPriceH' value = '0' />
				<input type = 'text' id = 'totalPrice' name = 'total' value = '0' disabled /><br>
				<button type = 'submit' name = 'sendQ' text = 'Ingresar'>Buy</button>
			</form>
			*********************************************<br>";
		}
	}else{
		echo "Whitout stock<br>";
	}

	echo "<input type = 'button' onclick = 'window.location.href =  \"purchaseProducts.php\";' value = 'Back' />";
}else{
	if(isset($_SESSION['idInventory'])){
		echo "An order is already active, cancel it before create another";
	}else{
		$sqlSelPr = "select * from products";
		$resultSelPr = mysqli_query($conn, $sqlSelPr);

		if(mysqli_num_rows($resultSelPr) > 0){
			echo "
			<form action = 'purchaseProducts.php' method = 'POST'>
				<label> Product: </label>";

			echo "<select name = 'product'><option value = '0'>-------------</>";	
			while($row = mysqli_fetch_assoc($resultSelPr)){
				echo "<option value = ".$row["id_product"].">". $row["name_product"]."</option><br>";
			}
			echo "</select><br>
			<input type = 'submit' name = 'sendP' value = 'Accept' /></form>";
		}else{
			echo "Whitout products";
		}
	}
}

echo "<br><br><input type = 'button' onclick = 'window.location.href =  \"utilities.php\";' value = 'Go To Utilities' />";
echo "<input type = 'button' onclick = 'window.location.href =  \"index.php\";' value = 'Go To Delivers' />";
echo "</html>";

echo "</html>";
?>