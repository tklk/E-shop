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

// ------------------------------------ //
// Shopping cart ---------------------- //
// ------------------------------------ //
$cart = unserialize($_SESSION['cart']);
if(!is_object($cart))
{
	$cart = new myCart();
}
$_SESSION['cart'] = serialize($cart);

// ------------------------------------ //
// Product menu- ---------------------- //
// ------------------------------------ //
$query_RecCategory = "SELECT `category`.`categoryid`, `category`.`categoryname`, `category`.`categorysort`, count(`product`.`productid`) as productNum FROM `category` LEFT JOIN `product` ON `category`.`categoryid` = `product`.`categoryid` GROUP BY `category`.`categoryid`, `category`.`categoryname`, `category`.`categorysort` ORDER BY `category`.`categorysort` ASC";
$RecCategory = $auth_user->runQuery($query_RecCategory);
$RecCategory->execute();

// Total record
$query_RecTotal = "SELECT count(`productid`)as totalNum FROM `product`";
$RecTotal = $auth_user->runQuery($query_RecTotal);
$RecTotal->execute();
$row_RecTotal=$RecTotal->fetch(PDO::FETCH_ASSOC);
?>


<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title>
<link href="style.css" rel="stylesheet" type="text/css">
<script language="javascript">
function checkForm(){	
	if(document.cartform.customername.value==""){
		alert("Please enter your name!");
		document.cartform.customername.focus();
		return false;
	}
	if(document.cartform.customeremail.value==""){
		alert("Please enter your email!");
		document.cartform.customeremail.focus();
		return false;
	}
	if(!checkmail(document.cartform.customeremail)){
		document.cartform.customeremail.focus();
		return false;
	}	
	if(document.cartform.customerphone.value==""){
		alert("Please enter your phone!");
		document.cartform.customerphone.focus();
		return false;
	}
	if(document.cartform.customeraddress.value==""){
		alert("Please enter your address!");
		document.cartform.customeraddress.focus();
		return false;
	}
	return confirm('Confirm your order');
}
function checkmail(myEmail) {
	var filter  = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
	if(filter.test(myEmail.value)){
		return true;
	}
	alert("Please enter a valid email address!");
	return false;
}
</script>
</head>
<body>
	<div class="forbeut1" >
		<a href="homepage.php?"><img src="images/logo.png" width="150" align="absmiddle"></a><br><br>
	</div>
<table width="80%" border="0" align="center" cellpadding="4" cellspacing="0" bgcolor="#FFFFFF">
  <tr>
    <td class="tdbline"><table width="100%" border="0" cellspacing="0" cellpadding="10">
        <tr valign="top">
          <td width="200" class="tdrline"><div class="boxtl"></div>
            <div class="boxtr"></div>
            <div class="categorybox">
              <p class="heading">
                <img src="images/search_icon.png" width="16" height="16" align="absmiddle"> Product 
                <span class="smalltext"> Search </span>
              </p>
              <form name="form1" method="get" action="homepage.php">
                <p>
                  <input name="keyword" type="text" id="keyword" value="Search for anything" size="12" onClick="this.value='';">
                  <input type="submit" id="button" value="Search">
                </p>
              </form>
              <p class="heading">
                <img src="images/price-icon.png" width="16" height="16" align="absmiddle"> Price 
                <span class="smalltext"> range </span>
              </p>
              <form action="homepage.php" method="get" name="form2" id="form2">
                <p>
                  <input name="price1" type="text" id="price1" value="0" size="3">
                  -
                  <input name="price2" type="text" id="price2" value="0" size="3">
                  <input type="submit" id="button2" value="Search">
                </p>
              </form>
            </div>
            <div class="boxbl"></div>
            <div class="boxbr"></div>
            <hr width="100%" size="1" />
            <div class="boxtl"></div>
            <div class="boxtr"></div>
            <div class="categorybox">
              <p class="heading">
                <img src="images/type_icon.png" width="16" height="16" align="absmiddle"> Shop by Category 
              </p>
              <ul>
                <li><a href="homepage.php?"> All <span class="categorycount">(<?php echo $row_RecTotal["totalNum"];?>)</span></a></li>
                <?php	while($row_RecCategory = $RecCategory->fetch(PDO::FETCH_ASSOC)){ ?>
                <li><a href="homepage.php?cid=<?php echo $row_RecCategory["categoryid"];?>"><?php echo $row_RecCategory["categoryname"];?> <span class="categorycount">(<?php echo $row_RecCategory["productNum"];?>)</span></a></li>
                <?php }?>
              </ul>
            </div>
            <div class="boxbl"></div>
            <div class="boxbr"></div></td>
          <td>
          <div class="subjectDiv"><span class="heading"><img src="images/store_icon.png" width="16" height="16" align="absmiddle"></span> Checkout </div>
            <div class="normalDiv">
              <?php if($cart->itemcount > 0) {?>
              <p class="heading"><img src="images/shopping_icon.png" width="16" height="16" align="absmiddle"> Order Detail </p>
              <table width="90%" border="0" align="center" cellpadding="2" cellspacing="1">
                <tr>
                  <th bgcolor="#ECE1E1"><p> # </p></th>
                  <th bgcolor="#ECE1E1"><p> Product Name </p></th>
                  <th bgcolor="#ECE1E1"><p> Quantity </p></th>
                  <th bgcolor="#ECE1E1"><p> Price </p></th>
                  <th bgcolor="#ECE1E1"><p> Sub </p></th>
                </tr>
                <?php		  
		  	$i=0;
			foreach($cart->get_contents() as $item) {
			$i++;
		  ?>
                <tr>
                  <td align="center" bgcolor="#F6F6F6" class="tdbline"><p><?php echo $i;?>.</p></td>
                  <td bgcolor="#F6F6F6" class="tdbline"><p><?php echo $item['info'];?></p></td>
                  <td align="center" bgcolor="#F6F6F6" class="tdbline"><p><?php echo $item['qty'];?></p></td>
                  <td align="center" bgcolor="#F6F6F6" class="tdbline"><p>$ <?php echo number_format($item['price']);?></p></td>
                  <td align="center" bgcolor="#F6F6F6" class="tdbline"><p>$ <?php echo number_format($item['subtotal']);?></p></td>
                </tr>
                <?php }?>
                <tr>
                  <td align="center" valign="baseline" bgcolor="#F6F6F6"><p> Shipping & Handling </p></td>
                  <td valign="baseline" bgcolor="#F6F6F6"><p>&nbsp;</p></td>
                  <td align="center" valign="baseline" bgcolor="#F6F6F6"><p>&nbsp;</p></td>
                  <td align="center" valign="baseline" bgcolor="#F6F6F6"><p>&nbsp;</p></td>
                  <td align="center" valign="baseline" bgcolor="#F6F6F6"><p>$ <?php echo number_format($cart->deliverfee);?></p></td>
                </tr>
                <tr>
                  <td align="center" valign="baseline" bgcolor="#F6F6F6"><p> Total </p></td>
                  <td valign="baseline" bgcolor="#F6F6F6"><p>&nbsp;</p></td>
                  <td align="center" valign="baseline" bgcolor="#F6F6F6"><p>&nbsp;</p></td>
                  <td align="center" valign="baseline" bgcolor="#F6F6F6"><p>&nbsp;</p></td>
                  <td align="center" valign="baseline" bgcolor="#F6F6F6"><p class="redword">$ <?php echo number_format($cart->grandtotal);?></p></td>
                </tr>
              </table>
              <hr width="100%" size="1" />
              <p class="heading"><img src="images/user_icon.png" width="16" height="16" align="absmiddle"> Customer Info </p>
              <form action="cartreport.php" method="post" name="cartform" id="cartform" onSubmit="return checkForm();">
                <table width="90%" border="0" align="center" cellpadding="4" cellspacing="1">
                  <tr>
                    <th width="20%" bgcolor="#ECE1E1"><p> Name </p></th>
                    <td bgcolor="#F6F6F6"><p>
                        <input type="text" name="customername" id="customername">
                        <font color="#FF0000">*</font></p></td>
                  </tr>
                  <tr>
                    <th width="20%" bgcolor="#ECE1E1"><p> Email </p></th>
                    <td bgcolor="#F6F6F6"><p>
                        <input type="text" name="customeremail" id="customeremail">
                        <font color="#FF0000">*</font></p></td>
                  </tr>
                  <tr>
                    <th width="20%" bgcolor="#ECE1E1"><p> Phone </p></th>
                    <td bgcolor="#F6F6F6"><p>
                        <input type="text" name="customerphone" id="customerphone">
                        <font color="#FF0000">*</font></p></td>
                  </tr>
                  <tr>
                    <th width="20%" bgcolor="#ECE1E1"><p> Address </p></th>
                    <td bgcolor="#F6F6F6"><p>
                        <input name="customeraddress" type="text" id="customeraddress" size="40">
                        <font color="#FF0000">*</font></p></td>
                  </tr>
                  <tr>
                    <th width="20%" bgcolor="#ECE1E1"><p> Payment method </p></th>
                    <td bgcolor="#F6F6F6"><p>
                        <select name="paytype" id="paytype">
                          <option value="Wire transfer" selected> Wire transfer </option>
                          <option value="Online payment"> Add a Credit/Debit cards </option>
                          <option value="COD"> Cash on delivery </option>
                        </select>
                      </p></td>
                  </tr>
                  <tr>
                    <td colspan="2" bgcolor="#F6F6F6"><p><font color="#FF0000">*</font> Required fields </p></td>
                  </tr>
                </table>
                <hr width="100%" size="1" />
                <p align="center">
                  <input name="cartaction" type="hidden" id="cartaction" value="update">
                  <input type="submit" name="updatebtn" id="button3" value="Place your order">
                  <input type="button" name="backbtn" id="button4" value="Back" onClick="window.history.back();">
                </p>
              </form>
            </div>
            <?php }else{ ?>
            <div class="infoDiv"> Cart is empty. </div>
            <?php } ?></td>
        </tr>
      </table></td>
  </tr>
  <tr>
    <td height="30" align="center" class="trademark"> NCTU E-Store </td>
  </tr>
</table>
</body>
</html>