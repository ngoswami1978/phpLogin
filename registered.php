<?php
session_name('tzLogin');
session_set_cookie_params(2*7*24*60*60);
session_start();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Registered users only! | Lakshaya Apartment</title>    
    <link rel="stylesheet" type="text/css" href="Login.css" media="screen" />    
</head>

<body>

<div id="main">
	<div class="container">
		<h1>Registered Users Only!</h1>
		<h2>Login to view this resource!</h2>
    </div>    
	
    <div class="container">    
    <?php
	if($_SESSION['id'])
	echo '<h1>Hello, '.$_SESSION['usr'].'! You are registered and logged in!</h1>';
	else echo '<h1>Please, <a href="Login.php">login</a> and come back later!</h1>';
    ?>
    </div>
	
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
</div>


</body>
</html>
