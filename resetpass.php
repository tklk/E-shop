<?php

	require_once("session.php");
	
	require_once("class.user.php");

	$auth_user = new USER();
				
		$user_id = $_SESSION['user_session'];
		
		$stmt = $auth_user->runQuery("SELECT * 
									  FROM users
									  WHERE user_id=:user_id");
		$stmt->execute(array(":user_id"=>$user_id));
		
		$userRow=$stmt->fetch(PDO::FETCH_ASSOC);
		
	if($auth_user)
	{
		if(isset($_POST['submit']))
		{	
			$old_pass = ($_POST["old_pass"]);
			
			$auth_user = new USER();
					
			$user_id = $_SESSION['user_session'];
			
			$stmt = $auth_user->runQuery("SELECT * 
										  FROM users
										  WHERE user_id=:user_id");
			$stmt->execute(array(":user_id"=>$user_id));
			
			$userRow=$stmt->fetch(PDO::FETCH_ASSOC);
			
			$new_pass = ($_POST["new_pass"]);
				
			$confirm_new_pass = ($_POST["confirm_new_pass"]);
			
			if(strlen($new_pass) < 6)
			{
				$error[] = "Password must be at least 6 characters";	
			}
			else if(password_verify($old_pass, $userRow['user_pass']))
			{		
				if($new_pass == $confirm_new_pass)
				{
					$auth_user->resetpass($user_id,$new_pass,$confirm_new_pass);		
				}
				else
				{
					$error[] = "New password doesn't match";
				}
			}
			else
			{
				$error[] = "Old password doesn't match";
			}
		}	
		else
		{
			$error[] = "Please enter the password.";
		}
	}
?>

<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title> Password reset </title>
		<link rel="stylesheet" href="index.css" type="text/css"  />
	</head>
	<body class="normal">
		<h1 class="top_reset">Hello ! <?php echo $userRow['user_name'] ?> you are here to reset your password.</h1>
        <form  method="post" class="form_reset">
			<h2>Change password</h2>
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
					else
					{
						?>
						&nbsp; Successfully changed &nbsp; Go back to <a href='homepage.php' class="a">home page</a> here
						<?php
					}
				?>				
				</div>
			<br>
			<div class="required" >
				Current password :<br>	
				<input type="txt" name="old_pass" class="user_input" placeholder="Old Password" required />
			</div>
			<div class="required" >
				New password :<br>
				<input type="password" name="new_pass" class="user_input" placeholder="New Password" required />
			</div>
			<div class="required" >
				Reenter new password :<br>
				<input type="password" name="confirm_new_pass" class="user_input" placeholder="Confirm New Password" required />
			</div>
			<button type="submit" name="submit" class="submit" value='Change password'> Save changes </button>
			&nbsp; or &nbsp; 
			<button class="submit">
				<a href="homepage.php" class="b"> Keep my old password </a>
			</button>
		</form>
	</body>
</html>