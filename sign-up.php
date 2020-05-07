<?php
	if(isset($_POST['signup']))
	{
		$uname = strip_tags($_POST['txt_uname']);
		$umail = strip_tags($_POST['txt_umail']);
		$upass = strip_tags($_POST['txt_upass']);	
		
		if($uname=="")
		{
			$error[] = "Provide username !";	
		}
		else if($umail=="")
		{
			$error[] = "Provide email id !";	
		}
		else if(!filter_var($umail, FILTER_VALIDATE_EMAIL))
		{
			$error[] = 'Please enter a valid email address !';
		}
		else if($upass=="")
		{
			$error[] = "Provide password !";
		}
		else if(strlen($upass) < 6)
		{
			$error[] = "Password must be at least 6 characters";	
		}
		else
		{
			try
			{
				session_start();

				require_once('class.user.php');

				$user = new USER();

				if($user->is_loggedin()!="")
				{
					$user->redirect('homepage.php');
				}
				$stmt = $user->runQuery("SELECT user_name, user_email 
										 FROM users 
										 WHERE user_name=:uname OR user_email=:umail");
										 
				$stmt->execute(array(':uname'=>$uname, ':umail'=>$umail));
				$row=$stmt->fetch(PDO::FETCH_ASSOC);
					
				if($row['user_name']==$uname)
				{
					$error[] = "Sorry username already taken !";
				}
				else if($row['user_email']==$umail)
				{
					$error[] = "Sorry email id already taken !";
				}
				else
				{
					if($user->register($uname,$umail,$upass))
					{	
						$user->redirect('sign-up.php?joined');
					}
				}
			}
			catch(PDOException $e)
			{
				echo $e->getMessage();
			}
		}	
	}
?>

<!DOCTYPE html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title> Sign up </title>
		<link rel="stylesheet" href="index.css" type="text/css"  />
		<style type="text/css"> 
			 
		</style>
	</head>
	<body class="normal">
		<h1 class="top"></h1>	
		<form method="post" class="form_signin">
			<div class="error">
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
				else if(isset($_GET['joined']))
				{
					?>
					&nbsp; Successfully registered <a href='index.php' class="a">login</a> here
					<?php
				}
				?>
			</div>
			<br>
			<div class="required" >
				Username :<br>
				<input type="text" name="txt_uname" class="user_input" placeholder="Enter Username" 
					   value="<?php if(isset($error)){echo $uname;}?>" />
			</div>
			<div class="required" >
				E-mail :<br>
				<input type="text" name="txt_umail" class="user_input" placeholder="Enter E-Mail ID" 
					   value="<?php if(isset($error)){echo $umail;}?>" />
			</div>
			<div class="required" >
				Password :<br>
				<input type="password" name="txt_upass" class="user_input" placeholder="Enter Password" /><br>
			<h2 class="hint"> (Password should be at least 6 characters) </h2>
			</div>
			<button type="submit" class="submit" name="signup">&nbsp; SIGN UP </button>
			<br><br>
			Already have an account? <br><br>
			<a href="index.php" class="a"> Sign In Here! </a>
		</form>
	</body>
</html>