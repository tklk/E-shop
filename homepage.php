<?php 
header("Content-Type: text/html; charset=UTF-8");
require_once("session.php");	
require_once("class.user.php");
	
	$auth_user = new USER();
	
	$user_id = $_SESSION['user_session'];
	
	$stmt = $auth_user->runQuery("SELECT * 
								  FROM users
								  WHERE user_id=:user_id");
								  
	$stmt->execute(array(":user_id"=>$user_id));
	
	$userRow=$stmt->fetch(PDO::FETCH_ASSOC);

// Default number of products showed per page
$pageRow_records = 8;

// Current page
$num_pages = 1;

// Update page
if (isset($_GET['page'])) {
  $num_pages = $_GET['page'];
}



if(isset($_GET["cid"])&&($_GET["cid"]!="")){
  // Query with category restriction
	$query_RecProduct = "SELECT * FROM `product` WHERE `categoryid`=".$_GET["cid"]." ORDER BY `productid` DESC";
}elseif(isset($_GET["keyword"])&&($_GET["keyword"]!="")){
  // Query with key word restriction
	$query_RecProduct = "SELECT * FROM `product` WHERE `productname` LIKE '%".$_GET["keyword"]."%' OR `description` LIKE '%".$_GET["keyword"]."%' ORDER BY `productid` DESC";
}elseif(isset($_GET["price1"]) && isset($_GET["price2"]) && ($_GET["price1"]<=$_GET["price2"])){
  // Query with price range restriction
	$query_RecProduct = "SELECT * FROM `product` WHERE `productprice` BETWEEN ".$_GET["price1"]." AND ".$_GET["price2"]." ORDER BY `productid` DESC";
}else{
  // Default query
	$query_RecProduct = "SELECT * FROM `product` ORDER BY `productid` DESC";
}

// Calculate starting row number for current page
$startRow_records = ($num_pages -1) * $pageRow_records;
$query_limit_RecProduct = $query_RecProduct." LIMIT ".$startRow_records.", ".$pageRow_records;

// Query with any key word restriction
$RecProduct = $auth_user->runQuery($query_limit_RecProduct);
$RecProduct->execute();

// Query without key word restriction
$all_RecProduct = $auth_user->runQuery($query_RecProduct);
$all_RecProduct->execute();

// Footnote page number
$total_records = $all_RecProduct->fetchColumn();
$total_pages = ceil($total_records/$pageRow_records);


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

// keep URL
function keepURL(){
	$keepURL = "";
	if(isset($_GET["keyword"])) $keepURL.="&keyword=".urlencode($_GET["keyword"]);
	if(isset($_GET["price1"])) $keepURL.="&price1=".$_GET["price1"];
	if(isset($_GET["price2"])) $keepURL.="&price2=".$_GET["price2"];	
	if(isset($_GET["cid"])) $keepURL.="&cid=".$_GET["cid"];
	return $keepURL;
}
?>

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title> NCTU E-SHOP </title>
<link href="style.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="index.css" type="text/css"  />
</head>
<body>
	<div class="forbeut" >
		<img src="images/logobanner.png" width="1300" align="absmiddle"> 
    <br><br>
	</div>
<table width="80%" border="0" align="center" cellpadding="4" cellspacing="0" bgcolor="#FFFFFF">
  <tr>
    <td class="tdbline"><table width="100%" border="0" cellspacing="0" cellpadding="10">
        <tr valign="top">
          <td width="200" class="tdrline"><div class="boxtl"></div>
            <div class="boxtr"></div>
            <div class="categorybox">
              <p class="heading"><img src="images/search_icon.png" width="16" height="16" align="absmiddle"> Product <span class="smalltext"> Search </span></p>
              <form name="form1" method="get" action="homepage.php">
                <p>
                  <input name="keyword" type="text" id="keyword" value="Search for anything" size="12" onClick="this.value='';">
                  <input type="submit" id="button" value="Search">
                </p>
              </form>
              <p class="heading">
                <img src="images/price-icon.png" width="16" height="16" align="absmiddle"> Price 
                <span class="smalltext"> range </span></p>
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
                <li><a href="homepage.php"> All <span class="categorycount">(<?php echo $row_RecTotal["totalNum"];?>)</span></a></li>
                <?php	while($row_RecCategory=$RecCategory->fetch(PDO::FETCH_ASSOC)){ ?>
                <li><a href="homepage.php?cid=<?php echo $row_RecCategory["categoryid"];?>"><?php echo $row_RecCategory["categoryname"];?> <span class="categorycount">(<?php echo $row_RecCategory["productNum"];?>)</span></a></li>
                <?php }?>
              </ul>
            </div>
            <div class="boxbl"></div>
            <div class="boxbr"></div></td>
          <td><div class="subjectDiv"> <span class="heading"><img src="images/store_icon.png" width="16" height="16" align="absmiddle"></span> Menu </div>
            <div class="actionDiv"><a href="cart.php"><img src="./images/shoppingcar.jpg" title="購物車" width="90px" height="40px"></a></div>
			<div class="list">&nbsp; Dear &nbsp; <?php echo $userRow['user_email']; ?>
				<ul class="pslist">
          <li><a href="resetpass.php" > Reset password </a></li>  

				  <li><a href="logout.function.php?logout=true" > Logout </a></li>
        </ul>
			</div>
            <?php	while($row_RecProduct=$RecProduct->fetch(PDO::FETCH_ASSOC)){ ?>
            <div class="albumDiv">
              <div class="picDiv"><a href="product.php?id=<?php echo $row_RecProduct["productid"];?>">
                <?php if($row_RecProduct["productimages"]==""){?>
                <img src="***" alt="No image" width="120" height="120" border="0" />
                <?php }else{?>
                <img src="proimg/<?php echo $row_RecProduct["productimages"];?>" alt="<?php echo $row_RecProduct["productname"];?>" width="110" height="110" border="0" />
                <?php }?>
                </a></div>
              <div class="albuminfo">
                <a href="product.php?id=<?php echo $row_RecProduct["productid"];?>"><?php echo $row_RecProduct["productname"];?></a><br />
                <span class="smalltext">$ </span>
                <span class="redword"><?php echo $row_RecProduct["productprice"];?></span>
              </div>
            </div>
            <?php }?>
            <div class="navDiv">
              <?php if ($num_pages > 1) { // Page > 1 ?>
              <a href="?page=1<?php echo keepURL();?>">|&lt;</a> <a href="?page=<?php echo $num_pages-1;?><?php echo keepURL();?>">&lt;&lt;</a>
              <?php }else{?>
              |&lt; &lt;&lt;
              <?php }?>
              <?php
  	  for($i=1;$i<=$total_pages;$i++){
  	  	  if($i==$num_pages){
  	  	  	  echo $i." ";
  	  	  }else{
  	  	      $urlstr = keepURL();
  	  	      echo "<a href=\"?page=$i$urlstr\">$i</a> ";
  	  	  }
  	  }
  	  ?>
              <?php if ($num_pages < $total_pages) { // Page < End ?>
              <a href="?page=<?php echo $num_pages+1;?><?php echo keepURL();?>">&gt;&gt;</a> <a href="?page=<?php echo $total_pages;?><?php echo keepURL();?>">&gt;|</a>
              <?php }else{?>
              &gt;&gt; &gt;|
              <?php }?>
            </div></td>
        </tr>
      </table></td>
  </tr>
  <tr>
    <td height="30" align="center" class="trademark"> NCTU E-Store </td>
  </tr>
</table>
</body>
</html>