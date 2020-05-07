<?php 
header("Content-Type: text/html; charset=utf-8");
require_once("session.php");	
require_once("class.user.php");
require_once("mycart.php");
	
	$auth_user = new USER();
	
	$user_id = $_SESSION['user_session'];
	
	$stmt = $auth_user->runQuery("SELECT * 
								  FROM users
								  WHERE user_id=:user_id");
								  
	$stmt->execute(array(":user_id"=>$user_id));
	
	$userRow=$stmt->fetch(PDO::FETCH_ASSOC);
	
if(isset($_POST["customername"]) && ($_POST["customername"]!="")){
	// Shopping cart
	$cart = unserialize($_SESSION['cart']);
	if(!is_object($cart))
	{
		$cart = new myCart();
	}
	
	// Order summary
	$total = $cart->total;
	$deliverfee = $cart->deliverfee;
	$grandtotal = $cart->grandtotal;
	$cname = $_POST["customername"];
	$cmail = $_POST["customeremail"];
	$caddress = $_POST["customeraddress"];
	$ctel = $_POST["customerphone"];
	$cpaytype = $_POST["paytype"];
	try
	{
		$auth_user = new USER();
		$user_id = $_SESSION['user_session'];
		$auth_user->neworder($user_id,$total,$deliverfee,$grandtotal,$cname,$cmail,$caddress,$ctel,$cpaytype);
	}
	catch(PDOException $e)
	{
		echo $e->getMessage();
	}
	$corder_id = unserialize($_SESSION['orderid']);
	
	// Order detail
	if($cart->itemcount > 0) {
		foreach($cart->get_contents() as $item) {
		$detail_id = $item['id'];
		$detail_info = $item['info'];
		$detail_price = $item['price'];
		$detail_qty = $item['qty'];
		$auth_user->orderdetail($corder_id,$detail_id,$detail_info,$detail_price,$detail_qty);
		}
	}
	
	// Email
	$mailcontent=<<<msg
	Dear $cname,
	Thanks for visiting NCTU E-Store!
	
	Order Detail：
	--------------------------------------------------
	Order #： $corder_id 
	Customer Name：$cname 
	Email： $cmail 
	Phone： $ctel 
	Shipping address： $caddress 
	Payment type： $cpaytype 
	Order total：	$grandtotal 
	--------------------------------------------------
	We hope to see you again soon.
	
	NCTU E-Store
msg;
	$mailFrom="=?UTF-8?B?" . base64_encode("NCTU E-Store") . "?= <tingkailiu.nu@gmail.com>";
	$mailto = $_POST["customeremail"];
	$mailSubject="=?UTF-8?B?" . base64_encode("Your NCTU E-Store Order"). "?=";
	$mailHeader="From:".$mailFrom."\r\n";
	$mailHeader.="Content-type:text/html;charset=UTF-8";
	if(!@mail($mailto,$mailSubject,nl2br($mailcontent),$mailHeader)) die("Email Failure！");
	
	// Clear cart
	unset($_SESSION['cart']);
	unset($_SESSION['orderid']);
	$cart = new myCart();
	$_SESSION['cart'] = serialize($cart);
}	
?>
<script language="javascript">
alert("Thank you for shopping with us. We will send a confirmation when your item ships");
window.location.href="homepage.php";
</script>