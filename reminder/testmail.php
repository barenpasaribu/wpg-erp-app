<?php
 require_once "Mail.php";
 
 $from = "Cewe Genit <noreplay@minanga.co.id>";
 $to = "r.ginting@minanga.co.id";
 $subject = "Hi!";
 $body = "<html><b>Hi,\n\nHow are you? <br>Test... Success</html>";
 
 $host = "192.168.1.205";///"116.90.167.32";
 $username = "r.ginting@minanga.co.id";
 $password = "lateralang";
 
 $headers = array ('From' => $from,
   'To' => $to,
   'Subject' => $subject,
   'Content-Type'=> 'text/html');
 $mail = Mail::factory('smtp',
   array ('host' => $host,
     'auth' => true,
	 'port' => 25,
     'username' => $username,
     'password' => $password));
 
 $kirim = $mail->send($to, $headers, $body);
 
 if (PEAR::isError($kirim)) {
   echo("<p>" . $kirim->getMessage() . "</p>");
  } else {
   echo("<p>Message successfully sent!</p>");
  }
  
 ?>