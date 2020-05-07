<!--index-->

<!DOCTYPE html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title> NCTU E-Store </title>
		<link rel="stylesheet" href="index.css" type="text/css" />
	</head>
	<body class="normal">
		<h1 class="top"></h1>
		<form method="post" class="form_login" action="login.function.php" > 
			<div class="error">

			</div>
			<br>
			<div class="required" >
				Username or e-mail :<br>
				<input type="text" class="user_input" name="txt_uname_email" placeholder="" required />
			</div>
			<div class="required" >
				Password :<br>
				<input type="password" class="user_input" name="txt_password" placeholder="" />
			</div >
			<button type="submit" class="submit" name="login" > SIGN IN </button>
			<br>
			<br>
			Don't have a account?<br><br>
			<a href="sign-up.php" class="a">Sign Up Here!</a>
		</form>
	</body>
</html>