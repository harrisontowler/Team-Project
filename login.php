<?php
include "db.php";

session_start();

#login session begins first
#If user info is matched correctly to databse it will echo string login_success
#login_success string will then go back to Anonymous funtion $("#login").click() if no user is entered
if(isset($_POST["email"]) && isset($_POST["password"])){
	$email = mysqli_real_escape_string($con,$_POST["email"]);
	$password = md5($_POST["password"]);
	$sql = "SELECT * FROM user_info WHERE email = '$email' AND password = '$password'";
	$run_query = mysqli_query($con,$sql);
	$count = mysqli_num_rows($run_query);
	//if user record is in the database then $count will be equal to 1
	if($count == 1){
		$row = mysqli_fetch_array($run_query);
		$_SESSION["uid"] = $row["user_id"];
		$_SESSION["name"] = $row["first_name"];
		$ip_add = getenv("REMOTE_ADDR");
		//we have created cookies in login_form.php so if this shows that means the user is not loged in
			if (isset($_COOKIE["product_list"])) {
				$p_list = stripcslashes($_COOKIE["product_list"]);
				//this decodes json to array
				$product_list = json_decode($p_list,true);
				for ($i=0; $i < count($product_list); $i++) { 
					//here we check if there is anything already within the cart after getting user ID
					$verify_cart = "SELECT id FROM cart WHERE user_id = $_SESSION[uid] AND p_id = ".$product_list[$i];
					$result  = mysqli_query($con,$verify_cart);
					if(mysqli_num_rows($result) < 1){
						//if the user adds an item to the cart user_id updates database table with valid id
						$update_cart = "UPDATE cart SET user_id = '$_SESSION[uid]' WHERE ip_add = '$ip_add' AND user_id = -1";
						mysqli_query($con,$update_cart);
					}else{
						//if the product alrady exists within the cart it will be deleted within the database
						$delete_existing_product = "DELETE FROM cart WHERE user_id = -1 AND ip_add = '$ip_add' AND p_id = ".$product_list[$i];
						mysqli_query($con,$delete_existing_product);
					}
				}
				//in this section we delete the cookies
				setcookie("product_list","",strtotime("-1 day"),"/");
				//if the user logs in after the cart it will loop back to cart_login
				echo "cart_login";
				exit();
				
			}
			//if the user is login in from the same page login_success will be presented
			echo "login_success";
			exit();
		}else{
			echo "<span style='color:red;'>Please register before login..!</span>";
			exit();
		}
	
}

?>