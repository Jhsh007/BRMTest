<script>
	function submitForm(button){
		document.getElementById("sendForm").value = button;
		var myForm = document.getElementById("myForm");
		myForm.submit();
	}
</script>
<?php
session_start();
include_once('databaseCon.php');

echo "<html><style>table, th, td {border: 1px solid black; border-collapse: collapse; text-align: center;}</style>";


if(isset($_POST['sendForm']) && $_POST['sendForm'] != "0"){
	if($_POST['sendForm'] == "showInventory"){
		$sqlSelIn = "select * from inventory inner join products on inventory.id_product = products.id_product order by name_product";
		$resultSelIn = mysqli_query($conn, $sqlSelIn);

		if(mysqli_num_rows($resultSelIn) > 0){
			echo "<table><tr><th>Product</th><th>Quantity</th><th>Price</th><th>Expiration Date</th><th>Lot</th>";
			while($row = mysqli_fetch_assoc($resultSelIn)){
				echo "<tr><td>".$row["name_product"]."</td><td>".$row["quantity_inventory"]."</td><td>".$row["price_inventory"]."</td><td>".$row["expiration_inventory"]."</td><td>".$row["lot_inventory"]."</td></tr>";
			}
			echo "</table><br><br>";
			echo "<input type = 'button' onclick = 'window.location.href =  \"utilities.php\";' value = 'Back' />";
		}
	}else if($_POST['sendForm'] == "invoice"){
		if(isset($_SESSION['idInventory'])){
			$id = "2021";
			$idProduct = $_SESSION["idProduct"];
			$product = $_SESSION["product"];
	  		$quantity = $_SESSION["quantity"];
			$price = $_SESSION["price"];

			$sqlSelId = "select number_order from orders";
			$resultSelId = mysqli_query($conn, $sqlSelId);

			if(mysqli_num_rows($resultSelId) > 0){
				$id.=mysqli_num_rows($resultSelId)+1;
			}else{
				$id.="1";
			}

			echo "<table><tr><th>Order</th><td>".$id."</td></tr><tr><th>Product</th><td>".$product."</td></tr><tr><th>Quantity</th><td>".$quantity."</td></tr><tr><th>Price</th><td>".$price."</td></tr></table><br>";

			$sqlInsOrder = "Insert into orders(number_order, id_product, quantity_order, price_order) values(".$id.", ".$idProduct.", ".$quantity.", ".$price.");";
			$resultInsOrder = mysqli_query($conn, $sqlInsOrder);

			if($resultInsOrder === TRUE){
			  echo "Invoice created successfully<br><br>";
			  echo "<input type = 'button' onclick = 'window.location.href =  \"utilities.php\";' value = 'Back' />"; 
			  $_SESSION['order'] = $id;
			}else{
			  echo "Error: ".$sqlInsOrder."<br>";
			}
			
		}else{
			echo "Without an order to invoice.<br><br>";
			echo "<input type = 'button' onclick = 'window.location.href =  \"utilities.php\";' value = 'Back' />";
		}
	}else if($_POST['sendForm'] == "cancelOrder"){
		if(isset($_SESSION['idInventory'])){

			$selQuan = "select quantity_inventory from inventory where id_inventory = ".$_SESSION['idInventory'].";";
			$resultQuan = mysqli_query($conn, $selQuan);

			if(mysqli_num_rows($resultQuan) > 0){
				$row = mysqli_fetch_assoc($resultQuan);

				$newQuantity = $row['quantity_inventory']+$_SESSION['quantity'];

				$sqlCan = "update inventory set quantity_inventory = ".$newQuantity." where id_inventory = ".$_SESSION['idInventory'].";";
				$resultCan = mysqli_query($conn, $sqlCan);

				if($resultCan === TRUE){
					echo "*********<br>Inventory successfully restored<br>*********<br><br>";
						if(isset($_SESSION['order'])){
							$sqlOrderUp = "update orders set state_order = 'canceled' where number_order = ".$_SESSION['order'].";";
							$resultOrderUp = mysqli_query($conn, $sqlOrderUp);
							if($resultOrderUp === TRUE){
								echo "*********<br>Order successfully canceled<br>*********<br><br>";
							}else{
								echo "error on update order<br><br>";
							}
							echo "<input type = 'button' onclick = 'window.location.href =  \"utilities.php\";' value = 'Back' />";
						}
					session_destroy();
				}else{
					echo "error on update inventory<br><br>";
					echo "<input type = 'button' onclick = 'window.location.href =  \"utilities.php\";' value = 'Back' />";
				}
			}
		}else{
			echo "Without an order to cancel.<br><br>";
			echo "<input type = 'button' onclick = 'window.location.href =  \"utilities.php\";' value = 'Back' />";
		}
	}
}else{
	echo "<form id = 'myForm' action = 'utilities.php' method = 'POST'>
		<input type = 'hidden' id = 'sendForm' name = 'sendForm' value = '0'/>
		<input type = 'button' name = 'showInventory' value = 'Show Inventory' onclick = 'submitForm(\"showInventory\");' /input>
		<input type = 'button' name = 'invoice' value = 'Generate Invoice' onclick = 'submitForm(\"invoice\");' /input>
		<input type = 'button' name = 'cancelOrder' value = 'Cancel Order' onclick = 'submitForm(\"cancelOrder\");' /input>
	</form>";
}

echo "<br><br><input type = 'button' onclick = 'window.location.href =  \"purchaseProducts.php\";' value = 'Go To Purchase' />";
echo "<input type = 'button' onclick = 'window.location.href =  \"index.php\";' value = 'Go To Delivers' />";
echo "</html>";
?>