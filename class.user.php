<?php
require_once('config.php');
require_once("mycart.php");
class USER
{	
	private $conn;
	
	public function __construct()
	{
		$database = new Database();
		$db = $database->db_connection();	
		$this->conn = $db;
    }
	
	public function runQuery($sql)
	{
		$stmt = $this->conn->prepare($sql);
		return $stmt;
	}
	
	public function show()
	{
		try
		{
			$stmt = $this->conn->prepare("SELECT * 
										  FROM usermessage
										  ORDER BY message_sq DESC");										  
			$stmt->execute();
			return $stmt;	
		}
		catch(PDOException $e)
		{
			echo $e->getMessage();
		}
	}
	
	public function register($uname,$umail,$upass)
	{
		try
		{
			$new_password = password_hash($upass, PASSWORD_DEFAULT);			
			$stmt = $this->conn->prepare("INSERT INTO users(user_name,user_email,user_pass) 
		                                               VALUES(:uname, :umail, :upass)");												  
			$stmt->bindparam(":uname", $uname);
			$stmt->bindparam(":umail", $umail);
			$stmt->bindparam(":upass", $new_password);										  				
			$stmt->execute();				
			return $stmt;	
		}
		catch(PDOException $e)
		{
			echo $e->getMessage();
		}				
	}
	
	public function LeaveMessage($user_id,$message_subject,$message_content,$product_id)
	{
		try
		{	
			$stmt = $this->conn->prepare("INSERT INTO usermessage(user_id,message_subject, message_content, msg_product_id) 
		                                               VALUES('$user_id', '$message_subject', '$message_content', '$product_id')");			
			$stmt->execute();	
			
			return $stmt;		
		}
		catch(PDOException $e)
		{
			echo $e->getMessage();
		}				
	}
	
	public function neworder($user_id,$total,$deliverfee,$grandtotal,$cname,$cmail,$caddress,$ctel,$cpaytype)
	{
		try
		{	
			$stmt = $this->conn->prepare("INSERT INTO `order`(`user_id`, `total`, `deliverfee`, `grandtotal`, `cname`, `cemail`, `caddress`, `cphone`, `paytype`) 
		                         VALUES('$user_id','$total','$deliverfee','$grandtotal','$cname','$cmail','$caddress','$ctel','$cpaytype')");		
			$stmt->execute();
			$LAST_ID = $this->conn->lastInsertId();
			$_SESSION['orderid'] = serialize($LAST_ID);
			return $stmt;		
		}
		catch(PDOException $e)
		{
			echo $e->getMessage();
		}				
	}
	
	public function orderdetail($coder_id,$detail_id,$detail_info,$detail_price,$detail_qty)
	{
		try
		{	
			$corder_id = unserialize($_SESSION['orderid']);
			$stmt = $this->conn->prepare("INSERT INTO `orderdetail`
								(`orderid`,`productid`,`productname`,`unitprice`,`quantity`) 
		                         VALUES('$corder_id','$detail_id','$detail_info','$detail_price','$detail_qty')");		
			$stmt->execute();	
			
			return $stmt;		
		}
		catch(PDOException $e)
		{
			echo $e->getMessage();
		}				
	}
		
	public function resetpass($user_id,$new_pass,$confirm_new_pass)
	{
		try
		{
			$reset_password = password_hash($new_pass, PASSWORD_DEFAULT);
			
			$stmt = $this->conn->prepare("UPDATE users
										  SET user_pass='$reset_password'
		                                  WHERE user_id=:uid"); 			
			$stmt->bindparam(":uid", $user_id);			
			$stmt->execute();				
			return $stmt;	
		}
		catch(PDOException $e)
		{
			echo $e->getMessage();
		}				
	}
	
	public function doLogin($uname,$umail,$upass)
	{
		try
		{
			$stmt = $this->conn->prepare("SELECT user_id, user_name, user_email, user_pass 
										  FROM users 
										  WHERE user_name=:uname OR user_email=:umail ");										  
			$stmt->execute(array(':uname'=>$uname, ':umail'=>$umail));
			$userRow=$stmt->fetch(PDO::FETCH_ASSOC);
			
			if($stmt->rowCount() == 1)
			{
				if(password_verify($upass, $userRow['user_pass']))
				{
					$_SESSION['user_session'] = $userRow['user_id'];
					$cart = unserialize($_SESSION['cart']);
					$_SESSION['cart'] = serialize($cart);
					return true;
				}
				else
				{
					return false;
				}
			}
		}
		catch(PDOException $e)
		{
			echo $e->getMessage();
		}
	}
	
	public function is_loggedin()
	{
		if(isset($_SESSION['user_session']))
		{
			return true;
		}
	}
	
	public function redirect($url)
	{
		header("Location: $url");
	}
	
	public function doLogout()
	{
		session_destroy();
		unset($_SESSION['user_session']);
		return true;
	}
}
?>