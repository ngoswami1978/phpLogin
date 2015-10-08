<?php
session_name('tzLogin');
session_set_cookie_params(2*7*24*60*60);
session_start();
?>

<?php
	//generic php function to send GCM push notification
        function sendPushNotificationToGCM($registatoin_ids, $message) {

	//Google cloud messaging GCM-API url
        $url = 'https://android.googleapis.com/gcm/send';

        $fields = array(
            'registration_ids' => $registatoin_ids,
            'data' => $message,
        );
		// Google Cloud Messaging GCM API Key
		define("GOOGLE_API_KEY", "AIzaSyBUXg3AZR0wGgGTs9GIaYQ0OFEPFibLNzg"); 		
        $headers = array(
            'Authorization: key=' . GOOGLE_API_KEY,
            'Content-Type: application/json'
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);	
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);				
        if ($result === FALSE) {
            die('Curl failed: ' . curl_error($ch));
        }
        curl_close($ch);
        return $result;
    }
?>
<?php

	
	//this block is to post message to GCM on-click
	$pushStatus = "";	
	if(!empty($_GET["push"])) {	
		$gcmRegID  = file_get_contents("GCMRegId.txt");
		$pushMessage = $_POST["message"];	
		if (isset($gcmRegID) && isset($pushMessage)) {		
			$gcmRegIds = array($gcmRegID);
			$message = array("m" => $pushMessage);	
			$pushStatus = sendPushNotificationToGCM($gcmRegIds, $message);
		}		
	}
	
	//this block is to receive the GCM regId from external (mobile apps)
	if(!empty($_GET["shareRegId"])) {
		$gcmRegID  = $_POST["regId"]; 
		file_put_contents("GCMRegId.txt",$gcmRegID);
		echo "Ok!";
		exit;
	}	
?>


<html>
    <head>
        <title>Apartment Notification System</title>
		 <link rel="stylesheet" type="text/css" href="Login.css" media="screen" /> 
    </head>
	<body>
	<div id="main">
		<div class="container">	
			<h1>Apartment Notification System</h1>	
		</div>    
		<div class="container">    
		<?php
			if($_SESSION['id'])
			echo '<h1>Hello, '.$_SESSION['usr'].'! You are ready to send notification!</h1>';
			else echo '<h1>Please, <a href="Login.php">login</a> and come back later!</h1>';
		?>
		</div>
		
		<form method="post" action="PushNotificationPHP.php?push=1">					                             
			<div>                                
				<textarea rows="2" name="message" cols="23" placeholder="Please write Message to transmit to cell phone"></textarea>
			</div>
			<div><input type="submit"  value="Send Push Notification" /></div>
		</form>
		<p><h3><?php echo $pushStatus; ?></h3></p>        
    </body>
	</div>
</html>