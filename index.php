<script>
	function validateForm(){
		var field0 = document.getElementById('product').value;
		var field1 = document.getElementById('quantity').value;
		var field2 = document.getElementById('lot').value;
		var field3 = document.getElementById('price').value;
		var field4 = document.getElementById('date').value;

		if(field0 != 0){
			if(/^\d+$/.test(field1)){
				if(/^\d+$/.test(field2)){
					if(/^\d+$/.test(field3)){
						field4Split = field4.split('-');
						if(field4Split.length == 3){
							if(/^\d+$/.test(field4Split[0])){
								if(/^\d+$/.test(field4Split[1])){
									if(/^\d+$/.test(field4Split[2])){
										return true;
									}else{
										alert("Date must have format yyyy-mm-dd");
										return false;
									}
								}else{
									alert("Date must have format yyyy-mm-dd");
									return false;
								}
							}else{
								alert("Date must have format yyyy-mm-dd");
								return false;
							}
						}else{
							alert("Date must have format yyyy-mm-dd");
							return false;
						}
					}else{
						alert("Not a number in price field");
						return false;
					}
				}else{
					alert("Not a number in lot field");
					return false;
				}
			}else{
				alert("Not a number in quantity field");
				return false;
			}
		}else{
			alert("You must select a product");
			return false;
		}
	}
	</script>
	<?php
	session_start();
	include_once('databaseCon.php');
	$sql = "select * from products";
$result = mysqli_query($conn, $sql);

echo "
<html>
	<form action = 'index.php' onsubmit = 'return validateForm();' method = 'POST'>
		<label> Product: </label>";

echo "<select id = 'product' name = 'product'><option value = '0'>-------------</>";
if (mysqli_num_rows($result) > 0) {
  while($row = mysqli_fetch_assoc($result)) {
    echo "<option value = ".$row["id_product"].">". $row["name_product"]."</option><br>";
  }
}
echo "</select><br>";

echo "
<label> Quantity: </label>
		<input type = 'text' id = 'quantity' name = 'quantity' /><br>
		<label> Lot Number: </label>
		<input type = 'text' id = 'lot' name = 'lot' /><br>
		<label> Expiration Date: </label>
		<input type = 'text' id = 'date' name = 'date' /><br>
		<label> Price: </label>
		<input type = 'text' id = 'price' name = 'price' /><br>
		<input type = 'submit' name = 'sendIn' value = 'Accept'/>
	</form>
</html>
";
if(isset($_POST['sendIn'])){
	$name = $_POST['product'];
	$quantity = $_POST['quantity'];
	$lot = $_POST['lot'];
	$date = $_POST['date'];
	$price = $_POST['price'];

	$sqlSelIn = "select id_inventory from inventory";
	$resultSelIn = mysqli_query($conn, $sqlSelIn);

	$id = mysqli_num_rows($resultSelIn)+1;

	$sqlIn = "Insert into inventory(id_inventory, id_product, quantity_inventory, lot_inventory, expiration_inventory, price_inventory) values(".$id.", ".$name.", ".$quantity.", ".$lot.", '".$date."', ".$price.");";
	$resultIn = mysqli_query($conn, $sqlIn);
	if($resultIn === TRUE){
	  echo "New record created successfully";
	}else{
	  echo "Error: ".$sqlIn."<br>";
	}
}

echo "<br><br><input type = 'button' onclick = 'window.location.href =  \"purchaseProducts.php\";' value = 'Go To Purchase' />";
echo "<input type = 'button' onclick = 'window.location.href =  \"utilities.php\";' value = 'Go To Utilities' />";
echo "</html>";
?>