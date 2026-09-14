<?php
if(isset($_POST['email'])) {
	
	// EDIT THE LINES BELOW AS REQUIRED
	$email_from = "producers@sbkcenter.com";
	//$email_to = "producers@sbkcenter.com; consumers@sbkcenter.com";
	$email_to = "arvind.chaurasia@newmediaguru.org; pradeep.chaurasia@newmediaguru.org";
	$email_subject = "Producer Panel - New panelist submission";
	
	$fields = array(
		'first_name' => 'First Name:',
		'last_name' => 'Last Name:',
		'email' => 'Email Address:',
		'company' => 'Company:',
		'address' => 'Street Address:',
		'city' => 'City:',
		'state' => 'State/Province:',
		'zip' => 'Zip/Postal Code:',
		'telephone' => 'Telephone Number:',
		'referral' => 'Referred by:',
		'carriers' => 'Please name 3 Major Carriers you write with:',
		'sell_pc' => 'Do you sell Property and Casualty insurance?',
		'sell_health' => 'Do you sell Health insurance and if so what size:',
		'sell_life' => 'Do you sell Life Insurance?',
		'sell_annuities' => 'Do you sell Annuities?',
		'business_state' => 'Are there any other states you write business with other than the state you reside in?',
		'series7' => 'Do you have your Series 7 license?',
		'sell_medicare' => 'Do you sell Medicare products?',
		'sell_401k' => 'Do you sell 401Ks?',
		'sell_worksite' => 'Do you sell Worksite Products?',
		'worksite_carriers' => "\tIf YES which carriers?",
	);
	
	$input = array();
	foreach($fields as $f=>$n){
		if(isset($_REQUEST[$f])){
			if(is_array($_REQUEST[$f])){
				$_REQUEST[$f] = implode(', ',$_REQUEST[$f]);
			}
			$input[$f] = clean_string($_REQUEST[$f]);
		}
		else{
			$input[$f] = '';
		}
	}
	
	$error_message = "";
	$email_exp = '/^[A-Za-z0-9._%-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,4}$/';
	if(!preg_match($email_exp,$input['email'])) {
		$error_message .= 'The Email Address you entered does not appear to be valid.<br />';
	}
	$string_exp = "/^[0-9().-]+$/";
	if(!preg_match($string_exp,$input['telephone'])) {
		$error_message .= 'The Telephone number you entered does not appear to be valid.<br />';
	}
	if(strlen($error_message) > 0) {
		echo '0';
		exit;
	}
	
	$email_message = "Form details below.\n\n";
	foreach($fields as $f=>$n){
		$email_message .= $n." ".$input[$f]."\n";
	}
	// create email headers
	$headers = 'From: '.$email_from."\r\n".
	'Reply-To: '.$email_from."\r\n" .
	'X-Mailer: PHP/' . phpversion();
	@mail($email_to, $email_subject, $email_message, $headers);  
	
	echo '1';
}
else{
	echo '0';
}
function clean_string($string) {
	$bad = array("content-type","bcc:","to:","cc:","href");
	return str_replace($bad,"",$string);
}
?>
