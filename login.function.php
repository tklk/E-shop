<!--login function-->
<?php

	session_start();

	require_once("class.user.php");

	$login = new USER();

	if(isset($_POST['login']))
	{
		$uname = strip_tags($_POST['txt_uname_email']);
		
		$umail = strip_tags($_POST['txt_uname_email']);
		
		$upass = strip_tags($_POST['txt_password']);
			
		if($login->doLogin($uname,$umail,$upass))
		{
			$login->redirect('homepage.php');
		}
		else
		{
			$login->redirect('index.php');
		}	
	}
?>