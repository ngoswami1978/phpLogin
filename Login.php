<?php

define('INCLUDE_CHECK',true);

require 'connect.php';
require 'functions.php';
// Those two files can be included only if INCLUDE_CHECK is defined


session_name('tzLogin');
// Starting the session

session_set_cookie_params(2*7*24*60*60);
// Making the cookie live for 2 weeks

session_start();

if($_SESSION['id'] && !isset($_COOKIE['tzRemember']) && !$_SESSION['rememberMe'])
{
	// If you are logged in, but you don't have the tzRemember cookie (browser restart)
	// and you have not checked the rememberMe checkbox:

	$_SESSION = array();
	session_destroy();
	
	// Destroy the session
}


if(isset($_GET['logoff']))
{
	$_SESSION = array();
	session_destroy();
	
	header("Location: Login.php");
	exit;
}

if($_POST['submit']=='Login')
{
	// Checking whether the Login form has been submitted
	
	$err = array();
	// Will hold our errors
	
	
	if(!$_POST['username'] || !$_POST['password'])
		$err[] = 'All the fields must be filled in!';
	
	if(!count($err))
	{
		$_POST['username'] = mysql_real_escape_string($_POST['username']);
		$_POST['password'] = mysql_real_escape_string($_POST['password']);
		$_POST['rememberMe'] = (int)$_POST['rememberMe'];
		
		// Escaping all input data

		$row = mysql_fetch_assoc(mysql_query("SELECT id,usr FROM members WHERE usr='{$_POST['username']}' AND pass='".md5($_POST['password'])."'"));

		if($row['usr'])
		{
			// If everything is OK login
			
			$_SESSION['usr']=$row['usr'];
			$_SESSION['id'] = $row['id'];
			$_SESSION['rememberMe'] = $_POST['rememberMe'];
			
			// Store some data in the session
			
			setcookie('tzRemember',$_POST['rememberMe']);
		}
		else $err[]='Wrong username and/or password!';
	}
	
	if($err)
	$_SESSION['msg']['login-err'] = implode('<br />',$err);
	// Save the error messages in the session

	header("Location: Login.php");
	exit;
}
else if($_POST['submit']=='Register')
{
	// If the Register form has been submitted
	
	$err = array();
	
	if(strlen($_POST['username'])<4 || strlen($_POST['username'])>32)
	{
		$err[]='Your username must be between 3 and 32 characters!';
	}
	
	if(preg_match('/[^a-z0-9\-\_\.]+/i',$_POST['username']))
	{
		$err[]='Your username contains invalid characters!';
	}
	
	if(!checkEmail($_POST['email']))
	{
		$err[]='Your email is not valid!';
	}
	
	if(!count($err))
	{
		// If there are no errors
		
		$pass = substr(md5($_SERVER['REMOTE_ADDR'].microtime().rand(1,100000)),0,6);
		// Generate a random password
		
		$_POST['email'] = mysql_real_escape_string($_POST['email']);
		$_POST['username'] = mysql_real_escape_string($_POST['username']);
		// Escape the input data
		
		
		mysql_query("	INSERT INTO members(usr,pass,email,regIP,dt)
						VALUES(
						
							'".$_POST['username']."',
							'".md5($pass)."',
							'".$_POST['email']."',
							'".$_SERVER['REMOTE_ADDR']."',
							NOW()
							
						)");
		
		if(mysql_affected_rows($link)==1)
		{
			send_mail(	'neeraj@myandroidng.com',
						$_POST['email'],
						'Lakshaya Apartment Registration - Your New Password',
						'Your password is: '.$pass);

			$_SESSION['msg']['reg-success']='We sent you an email with your new password!';
		}
		else $err[]='This username is already taken!';
	}

	if(count($err))
	{
		$_SESSION['msg']['reg-err'] = implode('<br />',$err);
	}	
	
	header("Location: Login.php");
	exit;
}

$script = '';

if($_SESSION['msg'])
{
	// The script below shows the sliding panel on page load
	
	$script = '
	<script type="text/javascript">
	
		$(function(){
		
			$("div#panel").show();
			$("#toggle a").toggle();
		});
	
	</script>';
	
}
?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Lakshaya Apartment</title>
    
    <link rel="stylesheet" type="text/css" href="Login.css" media="screen" />
    <link rel="stylesheet" type="text/css" href="login_panel/css/slide.css" media="screen" />
    
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js"></script>
    
    <!-- PNG FIX for IE6 -->
    <!-- http://24ways.org/2007/supersleight-transparent-png-in-ie6 -->
    <!--[if lte IE 6]>
        <script type="text/javascript" src="login_panel/js/pngfix/supersleight-min.js"></script>
    <![endif]-->
    
    <script src="login_panel/js/slide.js" type="text/javascript"></script>
    
    <?php echo $script; ?>
</head>

<body>

<!-- Panel -->
<div id="toppanel">
	<div id="panel">
		<div class="content clearfix">
			<div class="left">
				<h1>The Serious android developer Panel</h1>				
				<h2>apartment solution Android</h2>		
				<p class="grey">Please register yourself here / Login to enter into Appartment system !</p>				
				<h2>A Big Thanks</h2>
				<p class="grey">To all supporters and my friends</p>
				<p class="grey"><u>Contact Us</u></p>
				<p class="grey">Neeraj Goswami</p>
				<p class="grey">+91- 9899846293</p>
			</div>            
            
            <?php
			
			if(!$_SESSION['id']):
			
			?>
            
			<div class="left">
				<!-- Login Form -->
				<form class="clearfix" action="" method="post">
					<h1>Member Login</h1>
                    
                    <?php
						
						if($_SESSION['msg']['login-err'])
						{
							echo '<div class="err">'.$_SESSION['msg']['login-err'].'</div>';
							unset($_SESSION['msg']['login-err']);
						}
					?>
					
					<label class="grey" for="username">Username:</label>
					<input class="field" type="text" name="username" id="username" value="" size="23" />
					<label class="grey" for="password">Password:</label>
					<input class="field" type="password" name="password" id="password" size="23" />
	            	<label><input name="rememberMe" id="rememberMe" type="checkbox" checked="checked" value="1" /> &nbsp;Remember me</label>
        			<div class="clear"></div>
					<input type="submit" name="submit" value="Login" class="bt_login" />
				</form>
			</div>
			<div class="left right">			
				<!-- Register Form -->
				<form action="" method="post">
					<h1>Not a member yet? Sign Up!</h1>		
                    
                    <?php						
						if($_SESSION['msg']['reg-err'])
						{
							echo '<div class="err">'.$_SESSION['msg']['reg-err'].'</div>';
							unset($_SESSION['msg']['reg-err']);
						}
						
						if($_SESSION['msg']['reg-success'])
						{
							echo '<div class="success">'.$_SESSION['msg']['reg-success'].'</div>';
							unset($_SESSION['msg']['reg-success']);
						}
					?>
                    		
					<label class="grey" for="username">Username:</label>
					<input class="field" type="text" name="username" id="username" value="" size="23" />
					<label class="grey" for="email">Email:</label>
					<input class="field" type="text" name="email" id="email" size="23" />
					<label>A password will be e-mailed to you.</label>
					<input type="submit" name="submit" value="Register" class="bt_register" />
				</form>
			</div>
            
            <?php
			
			else:
			
			?>
            
            <div class="left">
            
            <h1>Members panel</h1>
            
            <p>You can put member-only data here</p>
            <a href="registered.php">View a special member page</a>
            <p>- or -</p>
            <a href="?logoff">Log off</a>
            
            </div>
            
            <div class="left right">
            </div>
            
            <?php
			endif;
			?>
		</div>
	</div> <!-- /login -->	

    <!-- The tab on top -->	
	<div class="tab">
		<ul class="login">
	    	<li class="left">&nbsp;</li>
	        <li>Hello <?php echo $_SESSION['usr'] ? $_SESSION['usr'] : 'Guest';?>!</li>
			<li class="sep">|</li>
			<li id="toggle">
				<a id="open" class="open" href="#"><?php echo $_SESSION['id']?'Open Panel':'Log In | Register';?></a>
				<a id="close" style="display: none;" class="close" href="#">Close Panel</a>			
			</li>
	    	<li class="right">&nbsp;</li>
		</ul> 
	</div> <!-- / top -->
	
</div> <!--panel -->


<div class="pageContent">
<div id="main">
	<?php
		if(!$_SESSION['id']):			
	?>			
   
   <div class="container">
				<h1>GCM - Google Cloud Messaging </h1>
				<h2>Google Cloud Messaging in Android</h2>
			</div>            		
			
			<div class="container">
			<p>In this post, I will be discussing about Introduction to Google Cloud Messaging service. You should have heard the word ‘GCM’ somewhere and you are here to understand what it is and how it is used in Android applications.Following this post, I will be creating couple of Android applications incorporating GCM service to demonstrate in what are the different ways we can use GCM, so I request you to keep track of GCM tutorial series gonna be published here.</p>
			<p> GCM – Google Cloud Messaging is a free service from Google to send and receive messages to and from Android devices.</p>
			
			<br>
			<br>			
			<h1><p>Primary characteristics of Google Cloud Messaging (GCM)</p></h1>
			<p>It allows third party application servers (Java, Dot Net or even Php server) to send messages to their Android applications.</p>
			<p>Using the GCM Cloud Connection Server, you can receive upstream messages from the user’s device.</p>
			<p>An Android application on an Android device doesn’t need to be running to receive messages. The system will wake up the Android application via Intent broadcast when the message arrives, as long as the application is set up with the proper broadcast receiver and permissions.</p>
			<p>It does not provide any built-in user interface or other handling for message data. GCM simply passes raw message data received straight to the Android application. Handling data is upto the developer.</p>
			<p>For example, the application might post a notification, display a custom user interface, or silently sync data.</p>
			<p>It requires devices running Android 2.2 or higher that also have the Google Play Store application installed, or or an emulator running Android 2.2 with Google APIs.</p>
			<p>It uses an existing connection for Google services. For pre-3.0 devices, this requires users to set up their Google account on their mobile devices. A Google account is not a requirement on devices running Android 4.0.4 or higher.</p>          		  
			</div>
			
	<?php			
		else:			
	?>		
			<div class="container">
			<h1>A Apartment Login System</h1>
			<h2>Owners &amp; Renters Registration also avaibable in Android</h2>
        </div>            
		
		<div class="container">
			<p>Lakshya Apartments is one of the residential development of Lakshya Infratech, located in Ankur Vihar, Ghaziabad. It offers spacious and skillfully designed 1BHK, 2BHK and 3BHK apartments. The project is well equipped with all the amenities to facilitate the needs of the residents.</p>
			<p>Lakshya Homes is one of the popular residential developments in Kadugodi, neighborhood of Bangalore. It is among the completed projects of its Builder. It has lavish yet thoughtfully designed residences in 1 Blocks.</p>			
			<br><br>			
			<h1><p>Project Delivery</p></h1>
			<p>Project is Ready to Occupy and was delivered around March 2015</p>
		</div>			
		<div class="clear"></div>        
			
		<div class="container">
			<h1><p>To Apartment Owners of Lakshaya Residence<p></h1>
			<p>You are hereby invited to the General Meeting for the establishment of a Homeowners Association of Lakshaya Residence will take place on Wednesday 10th of September at 9.00pm with the agenda outlined below.This meeting will take place in the meeting room on the ground floor in Lakshaya Residence.</p>
			<br>
			<p>If this meeting does not meet the required majority, then a second meeting will take place on Wednesday 17th of April at 9.00pm at the same location stated above.</p>
			<br>
			<p>We kindly request that all homeowners or their nominated proxies attend this important meeting</p>
			
			<p>The agenda of the meeting will be as follows;</p>
			<p>1.      Opening, registration of attendees and nomination of a Chairman of the meeting.</p>
			<p>2.      Proposal for the system of management and the board.</p>
			<p>3.      Nomination of the Management Board and a property manager (supervisor)</p>
			<p>4.      Suggestions</p>
			<p>5.      Closing</p>
		</div>				
		
		<div class="clear"></div>        
		
		<div class="container">
			<h1><p>Apartment App Features</p></h1>	
			<h2>
			<p>Home</p>
			<p>Lakshaya Community GuideLines</p>
			<p>General Information</p>
			<p>Maintainance</p>
			<p>Leisure Facilities</p>
			<p>Laundry Facilities</p>
			<p>Safety and Security</p>
			<p>Common Areas </p>
			<p>Parking</p>
			<p>Homeowner Association</p>
			<p>Residents Photo Album</p>
			<p>Utilities</p>
			<p>Lakshaya Events</p>
			<p>Important Phone Numbers</p>
			<p>Maps</p>
			<p>Post and comments</p>
			<p>Renters</p>
			<p>Rules and Regulations</p>
			<p>Reviews & Testimonials</p>
			<p>Contact Us</p>
			<p>About Us</p>
			</h2>
		</div>
		
		<div class="container tutorial-info">
			This is a Link through which you can notify messages to the registered android users only <a href="http://www.myandroidng.com/Apartment/Pushnotificationlakshay.php" >Click me</a>
		</div>
			
	<?php
		endif;
	?>	
	<div class="container">
		<div class="copyright">Copyright &copy; <?php echo date("Y"); ?> Neeraj Goswami All rights reserved.</div>
	</div>
	
</div>
</div>	
</body>

</html>
