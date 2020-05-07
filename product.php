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
// Product menu- ---------------------- //
// ------------------------------------ //
$query_RecProduct = "SELECT * FROM `product` WHERE `productid`=".$_GET["id"];
$RecProduct = $auth_user->runQuery($query_RecProduct);
$RecProduct->execute();
$row_RecProduct=$RecProduct->fetch(PDO::FETCH_ASSOC);

// ------------------------------------ //
// Category menu- ---------------------- //
// ------------------------------------ //
$query_RecCategory = "SELECT `category`.`categoryid`, `category`.`categoryname`, `category`.`categorysort`, count(`product`.`productid`) as productNum FROM `category` LEFT JOIN `product` ON `category`.`categoryid` = `product`.`categoryid` GROUP BY `category`.`categoryid`, `category`.`categoryname`, `category`.`categorysort` ORDER BY `category`.`categorysort` ASC";
$RecCategory = $auth_user->runQuery($query_RecCategory);
$RecCategory->execute();

// Total record
$query_RecTotal = "SELECT count(`productid`)as totalNum FROM `product`";
$RecTotal = $auth_user->runQuery($query_RecTotal);
$RecTotal->execute();
$row_RecTotal=$RecTotal->fetch(PDO::FETCH_ASSOC);

// ------------------------------------ //
// Shopping cart ---------------------- //
// ------------------------------------ //

$cart = unserialize($_SESSION['cart']);
if(!is_object($cart))
{
	$cart = new myCart();
}
$_SESSION['cart'] = serialize($cart);
?>

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title> NCTU E-SHOP </title>
<link href="style.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="index.css" type="text/css"  />
<style><!--Discussion form css-->
.top{
 margin:auto;
 width:56vw;
 text-align:right;
 padding:15vh 0 0 0;
 font-family:微軟正黑體;
}
/*.nav{
 background-color:#339;
 padding: 10px 0px;
 }*/
.nav a {
  color: #5a5a5a;
  font-size: 11px;
  font-weight: bold;
  text-transform: uppercase;
}

.nav li {
  display: inline;
}
 .CSSTableGenerator {
 margin:auto;
 padding:0px;
 width:35vw;
 }
 .CSSTableGenerator table{
    border-collapse: collapse;
    border-spacing: 0;
 width:100%;
 height:100%;
 margin:0px;padding:0px;
}.CSSTableGenerator tr:last-child td:last-child {
 -moz-border-radius-bottomright:9px;
 -webkit-border-bottom-right-radius:9px;
 border-bottom-right-radius:9px;
}
.CSSTableGenerator table tr:first-child td:first-child {
 -moz-border-radius-topleft:9px;
 -webkit-border-top-left-radius:9px;
 border-top-left-radius:9px;
}
.CSSTableGenerator table tr:first-child td:last-child {
 -moz-border-radius-topright:9px;
 -webkit-border-top-right-radius:9px;
 border-top-right-radius:9px;
 
}.CSSTableGenerator tr:last-child td:first-child{
 -moz-border-radius-bottomleft:9px;
 -webkit-border-bottom-left-radius:9px;
 border-bottom-left-radius:9px;
 
}.CSSTableGenerator tr:hover td{
 background-color:#005fbf;
 color:white;
}
.CSSTableGenerator td{
 vertical-align:middle;
 background-color:#e5e5e5;
 border:1px solid #999999;
 border-width:0px 1px 1px 0px;
 text-align:left;
 padding:8px;
 font-size:16px;
 font-family:Arial,微軟正黑體;
 font-weight:normal;
 color:#000000;
}.CSSTableGenerator tr:last-child td{
 border-width:0px 1px 0px 0px;
}.CSSTableGenerator tr td:last-child{
 border-width:0px 0px 1px 0px;
}.CSSTableGenerator tr:last-child td:last-child{
 border-width:0px 0px 0px 0px;
}
.CSSTableGenerator tr:first-child td{
  background:-o-linear-gradient(bottom, #005fbf 5%, #005fbf 100%); 
  background:-webkit-gradient( linear, left top, left bottom, color-stop(0.05, #005fbf), color-stop(1, #005fbf) );
  background:-moz-linear-gradient( center top, #005fbf 5%, #005fbf 100% );
  filter:progid:DXImageTransform.Microsoft.gradient(startColorstr="#005fbf", endColorstr="#005fbf"); 
  background: -o-linear-gradient(top,#005fbf,005fbf);
  background-color:#005fbf;
  text-align:center;
  font-size:20px;
  font-family:Arial, 微軟正黑體;
  font-weight:bold;
  color:#ffffff;
}
.CSSTableGenerator tr:first-child:hover td{
  background:-o-linear-gradient(bottom, #005fbf 5%, #005fbf 100%); 
  background:-webkit-gradient( linear, left top, left bottom, color-stop(0.05, #005fbf), color-stop(1, #005fbf) );
  background:-moz-linear-gradient( center top, #005fbf 5%, #005fbf 100% );
  filter:progid:DXImageTransform.Microsoft.gradient(startColorstr="#005fbf", endColorstr="#005fbf"); 
  background: -o-linear-gradient(top,#005fbf,005fbf);
  background-color:#005fbf;
}
<!--增加留言css-->
.container{
  margin:auto;
  background-color:#f5f5f5;
  width:800px;
  padding-bottom: 20px;
 }
 .button{
  text-align:center;
  padding:20px 0;
 }
 .top h3{
  font-family:微軟正黑體;
  text-align:center;
  padding:10px 0;
 }
 .form-group{
  font-family:微軟正黑體;
  font-size:16px;
 }
</style>
</head>
<body>
<div class="forbeut1" >
    <a href="homepage.php?"><img src="images/logo.png" width="150" align="absmiddle"></a><br><br>
  </div>
<table width="80%" border="0" align="center" cellpadding="4" cellspacing="0" bgcolor="#FFFFFF">
  <tr>
    <td class="tdbline"><table width="100%" border="0" cellspacing="0" cellpadding="10">
      <tr valign="top">
        <td width="200" class="tdrline">
            <div class="boxtl"></div>
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
			  <?php	while($row_RecCategory=$RecCategory->fetch(PDO::FETCH_ASSOC)){ ?>
              <li><a href="homepage.php?cid=<?php echo $row_RecCategory["categoryid"];?>"><?php echo $row_RecCategory["categoryname"];?> <span class="categorycount">(<?php echo $row_RecCategory["productNum"];?>)</span></a></li>
              <?php }?>
            </ul>
          </div>
          <div class="boxbl"></div>
          <div class="boxbr"></div></td>
        <td><div class="subjectDiv"> <span class="heading"><img src="images/store_icon.png" width="16" height="16" align="absmiddle"></span> Product Detail </div>
                      <div class="actionDiv"><a href="cart.php"><img src="./images/shoppingcar.jpg" title="購物車" width="90px" height="40px"></a></div>
		    <div class="list">&nbsp; Dear &nbsp; <?php echo $userRow['user_email']; ?>
        <ul class="pslist">
				<li><a href="resetpass.php" class="c"> Reset password </a></li>
				<li><a href="logout.function.php?logout=true" class="c"> Logout </a></li>
        </ul>
			  </div>
			<div class="whole">
          <div class="albumDiv">
            <div class="picDiv">
              <?php if($row_RecProduct["productimages"]==""){?>
              <img src="***" alt="No image" width="120" height="120" border="0" />
              <?php }else{?>
              <img src="proimg/<?php echo $row_RecProduct["productimages"];?>" alt="<?php echo $row_RecProduct["productname"];?>" width="135" height="135" border="0" />
              <?php }?>
            </div>
            <div class="albuminfo">
              <span class="smalltext"> $ </span>
              <span class="redword">   <?php echo $row_RecProduct["productprice"];?></span>
            </div>
            </div>
          <div class="contentDiv">
            <div class="titleDiv">
              <?php echo $row_RecProduct["productname"];?></div>
            <div class="dataDiv">
              <p><?php echo nl2br($row_RecProduct["description"]);?></p>
              <hr width="100%" size="1" />
              <form name="form3" method="post" action="cart.php">
				        <input name="id" type="hidden" id="id" value="<?php echo $row_RecProduct["productid"];?>">
				        <input name="name" type="hidden" id="name" value="<?php echo $row_RecProduct["productname"];?>">
				        <input name="price" type="hidden" id="price" value="<?php echo $row_RecProduct["productprice"];?>">
				        <input name="qty" type="hidden" id="qty" value="1">
				        <input type="submit" name="button3" id="button3" value="Add to Cart">
				        <input type="button" name="button4" id="button4" value="Back" onClick="window.history.back();">
              </form>
          </div>
			</br>
			
<!-- Display message board -->	
<?php
	$auth_user = new USER();
	$product_id = $row_RecProduct["productid"];				
	$user_id = $_SESSION['user_session'];
			
	$stmt = $auth_user->runQuery("SELECT * 
								  FROM usermessage
								  WHERE msg_product_id=$product_id");
	$stmt->execute();
	
while($userRow=$stmt->fetch(PDO::FETCH_ASSOC))	
{	
?>
<div class="container" width="56%" >
  <div class="CSSTableGenerator" width="56%">
      <table align="center" width="56%">
            <tr>
              <td><?php echo $userRow['message_subject']?></td>
            </tr>
            <tr>
              <td width="15%"> User #</td>
              <td width="41%"><?php echo $userRow['user_id']?></td>
            </tr>
            <tr>
              <td> Review </td>
              <td><?php echo $userRow['message_content']?></td>
            </tr>
        </table>
 </div>
</div>
<br />
<?php } ?>
<!-- Add message -->
<?php	
if(isset($_POST['leavemessage']))
{	
	$message_subject = strip_tags($_POST['txt_message_subject']);
	$message_content = strip_tags($_POST['txt_message_content']);
	$product_id = $row_RecProduct["productid"];
	if($message_subject=="")
	{
		$error[] = "Create a subject!";	
	}
	else if($message_content=="")
	{
		$error[] = "Leave your question!";	
	}
	else
	{
		try
		{
			$auth_user = new USER();
			$user_id = $_SESSION['user_session'];
			$stmt = $auth_user->runQuery("SELECT * 
										  FROM users
										  WHERE user_id=:user_id");
			$stmt->execute(array(":user_id"=>$user_id));					
			$auth_user->LeaveMessage($user_id,$message_subject,$message_content, $product_id);
			echo "<meta http-equiv='refresh' content='0'>";
		}
		catch(PDOException $e)
		{
			echo $e->getMessage();
		}
	}
}
?>
<form id="form1" name="form1" method="post" action="" class="form-horizontal">
        <div class="form-group">
				<?php
				if(isset($error))
				{
					foreach($error as $error)
					{
						?>
						&nbsp; <?php echo $error; ?>
						<?php
					}
				}
				?>
		</div>
        <div class="form-group">
            <label for="guestSubject" class="col-sm-4 control-label"> Subject </label>
            <div class="col-sm-6">
              <input type="text" class="form-control" name="txt_message_subject" id="guestSubject" />
            </div>
        </div>
        <div class="form-group">
          <label for="guestContent" class="col-sm-4 control-label"> Content </label>
          <div class="col-sm-6">
              <textarea type="text" name="txt_message_content" class="form-control" id="guestContent" rows="5"></textarea>
          </div>
        </div>
        <div class="button">
            <input type="submit" name="leavemessage" id="button" value="Submit" class="btn"/>
        </div>
</form>
</div>

          </div>
		  </td>
        </tr>
    </table></td>
  </tr>
  <tr>
    <td height="30" align="center" class="trademark"> NCTU E-Store </td>
  </tr>
</table>
</body>
</html>