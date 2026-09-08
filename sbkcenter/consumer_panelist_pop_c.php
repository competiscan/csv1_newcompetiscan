<?php session_start();
//error_reporting(0);
//ob_start();
//include(__DIR__.'/sbkc_def.php');
$cleint_ip=get_client_ip();
$uri=$_SERVER['REQUEST_URI'];
$referer_url=$_SERVER['HTTP_REFERER'];
if(!strstr($referer_url,'sbkcenter') && (!strstr($referer_url,'google')) && (!strstr($referer_url,'youtube')) && (!strstr($referer_url,'yahoo.com')) && (!strstr($referer_url,'outlook.live'))  && !isset($_SESSION['referer_url'])){    
    $_SESSION['referer_url']=$referer_url;
}

if(strstr($uri,'sbkcenter/consumer_panelist_pop_c.php')){
    $floating_writer_ip ='172.18.4.231';//floating writer (currently on dh08042012) rw
    $conn_hostname=$floating_writer_ip;
    $conn_username="root";
    $conn_password="Xohv3iewotezu8ah";
    $conn_database="competi_competidb";    
}else{
    $floating_writer_ip = '34.226.25.177';//floating writer (currently on dh08042012) rw
    $conn_hostname=$floating_writer_ip;
    $conn_username="app_writeuser";
    $conn_password="Ano@11SDFLH@13NMldrf";
    $conn_database="competi_competidb";
}   

if(!defined('ENV')){
    define('ENV',getenv('SERVER_NAME'));
}
if(ENV == 'localhost'){
    $floating_writer_ip = '10.0.0.19';//floating writer (currently on dh08042012) rw
    $conn_hostname=$floating_writer_ip;
    $conn_username="root";
    $conn_password="root@20165";
    $conn_database="competi_competidb";
}
else if(ENV == 'demo.competiscan.com'){
    $floating_writer_ip ='172.18.4.231';//floating writer (currently on dh08042012) rw
    $conn_hostname=$floating_writer_ip;
    $conn_username="root";
    $conn_password="Xohv3iewotezu8ah";
    $conn_database="competi_competidb";    
}else{
    $floating_writer_ip = '34.226.25.177';//floating writer (currently on dh08042012) rw
    $conn_hostname=$floating_writer_ip;
    $conn_username="app_writeuser";
    $conn_password="Ano@11SDFLH@13NMldrf";
    $conn_database="competi_competidb";
}
    
$dbh = mysql_connect($conn_hostname, $conn_username, $conn_password) or die ("Unable to connect to MySQL");
$selected = mysql_select_db($conn_database,$dbh) or die ("Could not select Competiscan database");
//print_r($_SESSION);
if(!isset($_SESSION['save_id'])){
        
    $query = "INSERT INTO `cscan_contacts_pre` (date_modified,ip_address) VALUES(NOW(),'".mysql_real_escape_string($cleint_ip)."')";
    $result = mysql_query($query);// or die("Unable to execute query :'".$query."' due to following error : ".mysql_error());
    $incquery = "SELECT LAST_INSERT_ID()";
    $incquery = mysql_query($incquery) or die(mysql_error());
    $_SESSION['save_id'] = mysql_result($incquery,0);
    $_SESSION['save'] = array();
}

if(isset($_POST['comeFrom'])) $comeFrom = (int)$_POST['comeFrom'];
else $comeFrom = -1;

$stateIDArray = array();
$countries = array('US');
$sqlc = "SELECT DISTINCT countryCode FROM cscan_state JOIN ISO31661_alpha2code ON (code=countryCode) WHERE countryCode<>'US' ORDER BY country";
$rsc = mysql_query($sqlc);
while($rowc = mysql_fetch_row($rsc)) {
    $countries[] = $rowc[0];
}
foreach($countries as $country){
    $sql = "select stateCode,panelist_stateID from cscan_state WHERE countryCode='".$country."' ORDER BY stateName";
    $result = mysql_query($sql);
    while($row = mysql_fetch_row($result)){
        $stateIDArray[$row[0]] = $row[1];
    }
}

$titles2 = array(
	'Health Insurance',
	'Life Insurance',
	'Dental Insurance',
	'Vision Insurance',
	'Supplemental Insurance (Critical Illness, Long Term Care, Disability)',//4
	'Auto Insurance',
	'Home Owners/Renters Insurance',
	'401(k)',
	'Other Investments (Charles Schwab, Fidelity, Edward Jones, etc)', //8
	'Checking/Savings Account',//9
	'Credit Card',//10
	'Mortgage',//11
	'HELOC/Home Equity',//12
	'Other Loans (Unsecured, Educational, etc)',//13
	'Wireless/Cell Phone',
	'Home Phone',
	'Internet Access',
	'TV Provider' //17
);
$tcount = count($titles2);
$db1 = array(
	'HealthInsurance',
	'LifeInsurance',
	'DentalInsurance',
	'VisionInsurance',
	'SupplementalInsurance',
	'AutoInsurance',
	'HomeOwnersRentersInsurance',
	'401k',
	'OtherInvestments',
	'CheckingSavingsAccount',
	'CreditCard',
	'Mortgage',
	'HomeEquity',
	'LoanEducational',
	'WirelessCellPhone',
	'HomePhone',
	'InternetAccess',
	'TVProvider'
);
$providerArray = array('My Employer'=>'My Employer', 'Self Insured'=>'Self Insured', 'Spouse/Family\'s Employer'=>'Spouse/Family\'s Employer');
$providercount = count($providerArray);

//removed the submit for this last step
if($comeFrom==3){
    if(count($_SESSION['save'])>18){
        $description = '';
        $setext = '';
        foreach($titles2 as $key=>$title){
            if(isset($_SESSION['save']['insurance'.$key]) && $_SESSION['save']['insurance'.$key]=='1'){
                $description .= "$title\tYes\n";
                if(isset($_REQUEST['insurance_p'.$key])) {
                    if($key==8){
                            $qtext = 'Which companies are your investments with (Charles Schwab, Fidelity, Edward Jones, etc)?';
                    }
                    elseif($key==17){
                            $qtext = 'Who is your TV provider?';
                    }elseif($key == 12){
                            $qtext = 'Who is your Home Equity Loan lender?';
                    }elseif($key == 9){
                            $qtext = 'Who is your Checking/Savings Account with?';
                    }elseif($key == 10){
                            $qtext = 'Which credit cards do you have?';
                    }
                    elseif($key == 11){
                            $qtext = 'Who is your Mortgage lender?';
                    }elseif($key == 13) {
                            $qtext = 'What other loans do you have (Unsecured, Educational, etc)?';
                    }
                    else{
                            $qtext = 'Who is your '.$title.' provider?';
                    }
                    $description .= "$qtext\t{$_REQUEST['insurance_p'.$key]}\n";
                    $setext .= ",{$db1[$key]}_p='".mysql_real_escape_string($_REQUEST['insurance_p'.$key])."'";
                }
                if($key<5){
                    if(isset($_REQUEST['insurance_v'.$key])) {
                            $description .= "Who provides your $title?\t".implode(', ',$_REQUEST['insurance_v'.$key])."\n";
                            $setext .= ",{$db1[$key]}_v='".mysql_real_escape_string(implode(', ',$_REQUEST['insurance_v'.$key]))."'";
                    }
                    if($key==4){
                            if(isset($_REQUEST['supplemental'.$key])) {
                                    $description .= "What types of Supplemental Insurance do you have (Cancer, Critical Illness, Long Term Care, Disability, etc)?\t{$_REQUEST['supplemental'.$key]}\n";
                                    $setext .= ",{$db1[$key]}_m='".mysql_real_escape_string($_REQUEST['supplemental'.$key])."'";
                            }
                    }

                }
                elseif($key==8){
                    if(isset($_REQUEST['investments'.$key])) {
                        $description .= "What types of investments do you own (Annuities, IRA, Mutual Funds, etc)?\t{$_REQUEST['investments'.$key]}\n";
                        $setext .= ",{$db1[$key]}_m='".mysql_real_escape_string($_REQUEST['investments'.$key])."'";
                    }
                }elseif($key == 13){
                    if(isset($_REQUEST['otherloan'.$key])) {
                        $description .= "Which lenders are your other loans with (Bank of America, Citibank, etc)?\t{$_REQUEST['otherloan'.$key]}\n";
                        $setext .= ",{$db1[$key]}_v='".mysql_real_escape_string($_REQUEST['otherloan'.$key])."'";
                    }
                }
        }
        else{
            $description .= "$title\tNo\n";
        }
        }
        if($setext!=''){
            $query = "UPDATE `cscan_contacts_pre` SET description='".mysql_real_escape_string($description)."'$setext WHERE id={$_SESSION['save_id']}";
            $result = mysql_query($query);// or die("Unable to execute query :'".$query."' due to following error : ".mysql_error());
        }
        $_SESSION['save_id'] = -1;
        $_SESSION['save'] = array();
    }
    $doclose = true;
}
else $doclose = false;

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Welcome To Small Business Knowledge Center</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet" type="text/css" />
<link href="consumerform.css" rel="stylesheet" type="text/css">
<script src="ajax.js" type="text/javascript"></script>
<script src="jquery-1.6.4.min.js" type="text/javascript"></script>
<script type="text/javascript">
<!--
$(document).ready(function() {
	$('#familyMemberForm').hide();
	$('#familyInsertNotification').hide();
});

function submitFamilyMemberForm() {
	$("#frm1").submit();
}

function showFamilyMemberForm() {
	$('#familyMemberForm').show();
	$('#familyMemberQuestion').hide();
	$('#familyInsertNotification').html("");
}

function insertFamilyMemberData() {
	var name = $('#name').val();
	var name2 = $('#name2').val();
	var email = $('#email').val();
	var address = $('#address').val();
	var apt = $('#apt').val();
	var city = $('#city').val();
	var state = $('#state').val();
	var zip = $('#zip').val();
	var phone = $('#phone').val();
	var extension = $('#extension').val();
	var contact_method = $('#contact_method').val();
	var month = $('#month').val();
	var day = $('#day').val();
	var year = $('#year').val();
	var birthday = year+'-'+month+'-'+day;
	var gender = $('#gender').val();
	var ownbiz = $('#ownbiz').val();
	var bizname = $('#bizname').val();
	
	var link = 'name='+encodeURIComponent(name)+'&name2='+encodeURIComponent(name2)+'&email='+encodeURIComponent(email)+'&address='+encodeURIComponent(address)+'&apt='+encodeURIComponent(apt)+'&city='+encodeURIComponent(city)+'&state='+encodeURIComponent(state)+'&zip='+encodeURIComponent(zip)+'&phone='+encodeURIComponent(phone)+'&extension='+encodeURIComponent(extension)+'&contact_method='+encodeURIComponent(contact_method)+'&birthday='+encodeURIComponent(birthday)+'&gender='+encodeURIComponent(gender)+'&ownbiz='+encodeURIComponent(ownbiz)+'&bizname='+encodeURIComponent(bizname);
	
	$.ajax({
	  url: 'familyMemberSubmit.php?'+link,
	  success: function(data) {
		$('#familyInsertNotification').show();
		$('#familyInsertNotification').html(data);
		$('#familyInsertNotification').fadeOut(3000);
	  }
	
	});
}

function cancelFamilyEntry() {
	$('#familyMemberForm').hide();
	$('#familyMemberQuestion').show();
	
	refreshFamilyEntry();
}

function refreshFamilyEntry() {
	$('#name').val("");
	$('#name2').val("");
	$('#email').val("");
	/*$('#address').val("");
	$('#apt').val("");
	$('#city').val("");
	$('#state').val("");
	$('#zip').val("");
	$('#phone').val("");
	$('#extension').val("")*/
	$('#contact_method').val("");
	$('#month').val("");
	$('#day').val("");
	$('#year').val("");
	$('#gender').val("");
	$('#ownbiz').val("");
	$('#bizname').val("");
}

function FMValidate()
{
	if($('#name').val() == "" || $('#name').val() == 0)
	{
		alert("Please enter your First Name");
		$('#name').focus();
		return false;
	}
	if($('#name2').val() == "" || $('#name2').val() == 0)
	{
		alert("Please enter your Last Name");
		$('#name2').focus();
		return false;
	}
	if($('#email').val() == "" || $('#email').val() == 0)
	{
		alert("Please enter your Email");
		$('#email').focus();		
		return false;
	}
	if($('#address').val() == "" || $('#address').val() == 0)
	{
		alert("Please enter your Street Address");
		$('#address').focus();
		return false;
	}
	if( $('#city').val() == "" ||  $('#city').val() == 0)
	{
		alert("Please enter your City");
		$('#city').focus();
		return false;
	}
	if($('#state').val() == "" || $('#state').val() == 0)
	{
		alert("Please enter your State/Province");
		$('#state').focus();
		return false;
	}
	if($('#zip').val() == "" || $('#zip').val() == 0)
	{
		alert("Please enter your Postal Code");
		$('#zip').focus();
		return false;
	}
	if($('#phone').val() == "" || $('#phone').val() == 0)
	{
		alert("Please enter your Phone Number");
		$('#phone').focus();
		return false;
	}
	if ($('#contact_method').val() == "" || $('#contact_method').val() == 0) {
		alert("Please enter a Preferred Contact Method");
		$('#contact_method').focus();
		return false;
	}
	if($('#month').val() == "" || $('#day').val() == "" || $('#year').val() == "")
	{
		alert("Please enter your Birthday");
		$('#month').focus();
		return false;
	}
	if($('#gender').val() == "")
	{
		alert("Please enter your Gender");
		$('#gender').focus();
		return false;
	}
	if($('#ownbiz').val() == "")
	{
		alert("Does this person own his/her own business?");
		$('#ownbiz').focus();
		return false;
	}

	insertFamilyMemberData();
	cancelFamilyEntry()
}

  function validate()
  {  
    <?php if($comeFrom==0){ ?>
		/*
         if(document.frm1.referby && (document.frm1.referby.value == "" || document.frm1.referby.value == 0))
		 {
		     alert("Who were you referred by?");
			 document.frm1.referby.focus();
			 return false;
		 }
		*/
           
         if(document.frm1.name && (document.frm1.name.value == "" || document.frm1.name.value == 0))
		 {
		     alert("Please enter your First Name");
			 document.frm1.name.focus();
			 return false;
		 }
         if(document.frm1.name2 && (document.frm1.name2.value == "" || document.frm1.name2.value == 0))
		 {
		     alert("Please enter your Last Name");
			 document.frm1.name2.focus();
			 return false;
		 }
		 if(document.frm1.email && (document.frm1.email.value == "" || document.frm1.email.value == 0))
		 {
		     alert("Please enter your Email");
			 document.frm1.email.focus();
			 return false;
		 }
		 if(document.frm1.address && (document.frm1.address.value == "" || document.frm1.address.value == 0))
		 {
		     alert("Please enter your Street Address");
			 document.frm1.address.focus();
			 return false;
		 }
		 if(document.frm1.city && (document.frm1.city.value == "" || document.frm1.city.value == 0))
		 {
		     alert("Please enter your City");
			 document.frm1.city.focus();
			 return false;
		 }
		 if(document.frm1.state && document.frm1.state.selectedIndex<1) //(document.frm1.state.value == "" || document.frm1.state.value == 0))
		 {
		     alert("Please enter your State/Province");
			 document.frm1.state.focus();
			 return false;
		 }
		 if(document.frm1.zip && (document.frm1.zip.value == "" || document.frm1.zip.value == 0))
		 {
		     alert("Please enter your Postal Code");
			 document.frm1.zip.focus();
			 return false;
		 }
		 if(document.frm1.phone && (document.frm1.phone.value == "" || document.frm1.phone.value == 0))
		 {
		     alert("Please enter your Phone Number");
			 document.frm1.phone.focus();
			 return false;
		 }
		 if((document.frm1.month && document.frm1.month.selectedIndex<1) || (document.frm1.day && document.frm1.day.selectedIndex<1) || (document.frm1.year && document.frm1.year.selectedIndex<1))
		 {
		     alert("Please enter your Birthday");
			 document.frm1.month.focus();
			 return false;
		 }
		 if(document.frm1.gender && document.frm1.gender.selectedIndex<1)
		 {
		 	 alert("Please enter your Gender");
			 document.frm1.gender.focus();
			 return false;
		 }
		/* if(document.frm1.ethnicity && document.frm1.ethnicity.selectedIndex<1)
		 {
		     alert("Please enter your Ethnicity");
			 document.frm1.ethnicity.focus();
			 return false;
		 }*/
		
		 if(document.frm1.income && document.frm1.income.selectedIndex<1)
		 {
		     alert("Please enter your Household Income Level");
			 document.frm1.income.focus();
			 return false;
		 }
		
		 if(document.frm1.rentorown && document.frm1.rentorown.selectedIndex<1)
		 {
		     alert("Do you rent or own your home?");
			 document.frm1.rentorown.focus();
			 return false;
		 }
                 
                 if(document.frm1.credit_score && document.frm1.credit_score.selectedIndex<1)
		 {
		     alert("Please select a credit score.");
			 document.frm1.credit_score.focus();
			 return false;
		 }
		 if(document.frm1.ownbiz && document.frm1.ownbiz.selectedIndex<1)
		 {
		     alert("Do you own your own business?");
			 document.frm1.ownbiz.focus();
			 return false;
		 }
                 
		 if(document.frm1.FICOscore && document.frm1.FICOscore.selectedIndex<1)
		 {
		     alert("Would you be willing to provide the SBKC with your FICO score?");
			 document.frm1.FICOscore.focus();
			 return false;
		 }
                 
		
	<?php }
	elseif($comeFrom==1){ 
		$_SESSION['save']['name'] = trim($_REQUEST['name']);
		$_SESSION['save']['referby'] = trim($_REQUEST['referby']);
		$_SESSION['save']['name2'] = trim($_REQUEST['name2']);
		$_SESSION['save']['email'] = trim($_REQUEST['email']);
		$_SESSION['save']['address'] = trim($_REQUEST['address']);
		$_SESSION['save']['apt'] = trim($_REQUEST['apt']);
		$_SESSION['save']['city'] = trim($_REQUEST['city']);
		$_SESSION['save']['state'] = trim($_REQUEST['state']);
		$_SESSION['save']['zip'] = trim($_REQUEST['zip']);
		$_SESSION['save']['phone'] = trim($_REQUEST['phone']);
		$_SESSION['save']['extension'] = trim($_REQUEST['extension']);
		$_SESSION['save']['contact_method'] = trim($_REQUEST['contact_method']);
		$_SESSION['save']['month'] = trim($_REQUEST['month']);
		$_SESSION['save']['day'] = trim($_REQUEST['day']);
		$_SESSION['save']['year'] = trim($_REQUEST['year']);
		$_SESSION['save']['gender'] = trim($_REQUEST['gender']);
		$_SESSION['save']['ethnicity'] = trim($_REQUEST['ethnicity']);
		$_SESSION['save']['income'] = trim($_REQUEST['income']);
		$_SESSION['save']['ownbiz'] = trim($_REQUEST['ownbiz']);
		$_SESSION['save']['FICOscore'] = trim($_REQUEST['FICOscore']);
		$_SESSION['save']['rentorown'] = trim($_REQUEST['rentorown']);
                ################ for add credit_score section #####################
                $_SESSION['save']['credit_score'] = trim($_REQUEST['credit_score']);
		$address = $_SESSION['save']['address'];
		$apt = $_SESSION['save']['apt'];
		if($apt!='') $address .= " Apt # $apt";
		
		$phone = $_SESSION['save']['phone'];
		$phonein = preg_replace('/[^0-9]/','',$_SESSION['save']['phone']);
		
		$_SESSION['save']['hearSBKCInsert'] = trim($_REQUEST['hearSBKCInsert']);
		$_SESSION['save']['hearSBKC'] = $_REQUEST['hearSBKC'];
		
		$_SESSION['save']['bizname'] = trim($_REQUEST['bizname']);
		
		$phoneout = '';
		$offset = 0;
		$len = strlen($phonein);
		if($len>=7){
			if($len>=10) {
				if($len>10 && substr($phonein,$offset,1)=='1'){
					$offset+=1;
				}
				$phoneout .= substr($phonein,$offset,3);
				$offset+=3;
				$phoneout .= '-';
			}
			$phoneout .= substr($phonein,$offset,3);
			$offset+=3;
			$phoneout .= '-';
			$phoneout .= substr($phonein,$offset,4);
			$offset+=4;
			if($len>$offset) {
				$phoneout .= ' ';
				$phoneout .= substr($phonein,$offset);
			}
			$phone = $phoneout;
		}
		$ext = $_SESSION['save']['extension'];
		if($ext!="") $phone .= " x".$ext;
		
		$birthdate = $_SESSION['save']['year'].'-'.$_SESSION['save']['month'].'-'.$_SESSION['save']['day'];
		
		$query = "UPDATE `cscan_contacts_pre` SET first_name='".mysql_real_escape_string($_SESSION['save']['name'])."',last_name='".mysql_real_escape_string($_SESSION['save']['name2'])."',
			birthdate='".mysql_real_escape_string($birthdate)."',phone='".mysql_real_escape_string($phone)."',email='".mysql_real_escape_string($_SESSION['save']['email'])."',
			primary_address_street='".mysql_real_escape_string($address)."',primary_address_city='".mysql_real_escape_string($_SESSION['save']['city'])."',primary_address_state='".mysql_real_escape_string($_SESSION['save']['state'])."',
			primary_address_postalcode='".mysql_real_escape_string($_SESSION['save']['zip'])."',gender='".mysql_real_escape_string($_SESSION['save']['gender'])."',
			ethnicity='".mysql_real_escape_string($_SESSION['save']['ethnicity'])."',income='".mysql_real_escape_string($_SESSION['save']['income'])."',ownbiz='".mysql_real_escape_string($_SESSION['save']['ownbiz'])."',contact_method='".mysql_real_escape_string($_SESSION['save']['contact_method'])."',FICOscore='".mysql_real_escape_string($_SESSION['save']['FICOscore'])."',referby='".mysql_real_escape_string($_SESSION['save']['referby'])."',rentorown='".mysql_real_escape_string($_SESSION['save']['rentorown'])."',
			hearSBKC = '".$_SESSION['save']['hearSBKC']."', hearSBKCInsert = '".mysql_real_escape_string($_SESSION['save']['hearSBKCInsert'])."', credit_score = '".mysql_real_escape_string($_SESSION['save']['credit_score'])."', bizname = '".mysql_real_escape_string($_SESSION['save']['bizname'])."' 
			WHERE id={$_SESSION['save_id']}";
	
		$result = mysql_query($query);// or die(mysql_error());
                if(isset($_SESSION['referer_url']) && $_SESSION['referer_url']!=''){
                    $query = "UPDATE `cscan_contacts_pre` SET referer_url='".mysql_real_escape_string($_SESSION['referer_url'])."' WHERE id={$_SESSION['save_id']}";
		    $result = mysql_query($query);                    
                }
                    
		
		foreach($titles2 as $key=>$title){
			print ' if(document.frm1.insurance'.$key.' && !document.frm1.insurance'.$key.'[0].checked && !document.frm1.insurance'.$key.'[1].checked)
			 {
			     alert("'.$title.'?");
				 document.frm1.insurance'.$key.'[0].focus();
				 return false;
			 }
			';
		} 
	}
	elseif($comeFrom==2){ 
		$setext = '';
		for($i=0;$i<$tcount;$i++){
			if(isset($_REQUEST['insurance'.$i])) {
				if($setext!=''){
					$setext .= ',';
				}
				$_SESSION['save']['insurance'.$i] = $_REQUEST['insurance'.$i];
				
				$setext .= "{$db1[$i]}=".$_REQUEST['insurance'.$i];
			}
			else $_SESSION['save']['insurance'.$i] = '0';
		}
		if($setext!=''){
			$query = "UPDATE `cscan_contacts_pre` SET $setext WHERE id={$_SESSION['save_id']}";
			$result = mysql_query($query);// or die("Unable to execute query :'".$query."' due to following error : ".mysql_error());
		}
											
		foreach($titles2 as $key=>$title){
			if(isset($_SESSION['save']['insurance'.$key]) && $_SESSION['save']['insurance'.$key]=='1'){
				if($key==8){
					$qtext = 'Which companies are your investments with (Charles Schwab, Fidelity, Edward Jones, etc)?';
				}else if($key == 4){
					$qtext = "Who is your Supplemental Insurance provider (AFLAC, Unum, Colonial Life, Guardian, etc)?";
				}
				elseif($key == 12){
					$qtext = 'Who is your Home Equity Loan lender?';
				}elseif($key == 10){
						$qtext = 'Which credit cards do you have?';
				}
				elseif($key==17){
					$qtext = 'Who is your TV provider?';
				}elseif($key == 9){
					$qtext = 'Who is your Checking/Savings Account with?';
				}elseif($key == 11){
					$qtext = 'Who is your Mortgage lender?';
				}elseif($key == 13) {
					$qtext = 'What other loans do you have (Unsecured, Educational, etc)?';
				}	  		
				else{
					$qtext = 'Who is your '.$title.' provider?';
				}
				if($key==4){
					print 'if(document.frm1.supplemental'.$key.' && document.frm1.supplemental'.$key.'.value==\'\')
					 {
					     alert("What types of Supplemental Insurance do you have? (Cancer, Critical Illness, Long Term Care, Disability, etc)");
						 document.frm1.supplemental'.$key.'.focus();
						 return false;
					 }
					';
				}elseif($key==8){
						print 'if(document.frm1.investments'.$key.' && document.frm1.investments'.$key.'.value==\'\')
						 {
						     alert("What types of investments do you own (Annuities, IRA, Mutual Funds, etc)?");
							 document.frm1.investments'.$key.'.focus();
							 return false;
						 }
						';
					}
				print '
				 if(document.frm1.insurance_p'.$key.' && document.frm1.insurance_p'.$key.'.value==\'\')
				 {
				     alert("'.$qtext.'");
					 document.frm1.insurance_p'.$key.'.focus();
					 return false;
				 }
				 
				';
				if($key<5){
					print 'if(document.frm1[\'insurance_v'.$key.'[]\'])
					 {
					 	var isChecked = false;
					 	for(var i=0;i<document.frm1[\'insurance_v'.$key.'[]\'].length;i++){
					 		if(document.frm1[\'insurance_v'.$key.'[]\'][i].checked){
					 			isChecked = true;
					 			break;
					 		}
					 	}
					 	if(!isChecked){
					     alert("Is your '.$title.' through...\n(select all that apply)");
						 document.frm1[\'insurance_v'.$key.'[]\'][0].focus();
						 return false;
					 	}
					 }
					';
					
					
				}elseif($key == 13){
					print '
					 if(document.frm1.otherloan'.$key.' && document.frm1.otherloan'.$key.'.value==\'\')
					 {
					     alert("Which lenders are your other loans with (Bank of America, Citibank, etc)?");
						 document.frm1.otherloan'.$key.'.focus();
						 return false;
					 }

					';
				}
				
			}
		} 
	}
	?>
	return true;
  }
//-->
</script>

<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-33612694-1']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>

</head>
<body <?php if($doclose) print 'onload="self.close();"';  ?>>
 <?php 
if($_SESSION['save_id']!=-1){	
    if($comeFrom==-1){ ?>
    <table width="533" border="0" align="center" cellpadding="0" cellspacing="0" class="main-table">
        <tr> 
            <td class="border1">&nbsp;</td>
        </tr>
        <tr> 
            <td class="border">
                            <table width="498" border="0" align="center" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td height="23" class="sbkbar">
                                           <center>
                                            <p><img src="images/sbkclogo.gif" width="250" height=61><br><br></p>
                                            </center>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td valign="bottom" bgcolor="#FFFFFF"><br> 
                                    <table width="469" border="0" align="center" cellpadding="0" cellspacing="0">
                                    <!--<tr>
                                                                    <td width="469" valign="top" align="center"><img src="sbkc_images/logo.gif" width="397" height="139"></td>
                                                            </tr>-->
                                                            <tr> 
                                                                    <td valign="top" align="center">
                                                                <h2>Get compensated for your junk mail!</h2>								    
                                                                <br></td>
                                                            </tr>
                                                            <tr>
                                                                    <td>
                                                                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                            <tr valign="top"> 
                                            <td>
                                                    <form name="frm1" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" onSubmit="return validate();">
                                                            <table width="100%" border="0" cellpadding="0" cellspacing="10" class="formtable">
                                                                            <tr>
                                                                                    <td>

                                                                                      <p><br>
                                                                                        By filling out your confidential Panelist Profile, you are agreeing to participate in our market research study of direct mail and email marketing. We will compensate you for sending in your materials that you might otherwise throw away!&nbsp;<br>
                                                                                        <br>
                                                                                        As a Consumer Panelist, we will reward you with points for each usable piece of direct mail and email you send to us. Those points earn you prepaid MasterCard/Visa prepaid card, which can be used at any number of retailers.
                                                                                      <p>If you are a business owner or self-employed, you are eligible to earn bonus points for your materials. To earn rewards faster, be sure to specify that you own a business in your Panelist Profile form.</p>
<p>By registering yourself and any additional adult members of your household, you can maximize your point earning potential! </p>
<p>We respect your privacy:</p><ul type="disc">
                                            <li>All personal information is deleted from materials prior to use in our study.</li>
                                            <li>We shred and recycle everything we receive and your privacy is 100% protected.</li>
                                            <li>Participation is free, and there is no commitment - you can opt out at any time.</li>
                                            <li>No one will sell you       anything.</li>

                                            <li>We will not share your email address or any  personal information with anyone.</li>
                                          </ul>

<p><strong>Please Note:</strong> We have limited space available in our US study depending on your location and demographic information. If we are at capacity in your area, you will be placed onto a waiting list. In the meantime, you may still be contacted for special requests such as surveys and secret shopper projects.&nbsp;</p></center>
                                                                                    </td>
                                                                            </tr>
                                                            <!--<tr><td align="center">
                                                                            Do you receive vast amounts of direct "junk" mail and email "spam"?
                                                                            </td></tr>
                                                                            <tr><td align="center">
                                                                            Would you like to be compensated to get rid of it?<br>
                                                                            </td></tr>
                                                            <tr><td>&nbsp;</td></tr>
                                                                            <tr><td>
                                                                            By filling out this form you are agreeing to be a part of The Small Business Knowledge Center consumer panel. This is an ongoing participation panel which simply requires you to send us your junk mail and junk email.<br>
                                                                            </td></tr>
                                                                            <tr><td>As a member of our panel, we will reward you with points, to be used toward gift certificates at a variety of stores that you can choose.<br>
                                                                            </td></tr>
                                                                            <tr><td>We shred and recycle everything we receive and your privacy is 100% protected.  
                                                                            </td></tr>
                                                            <tr><td>&nbsp;</td></tr>
                                                            <tr>
                                                            <td><strong>Please tell us your level of interest in forwarding your junk mail and email on an ongoing basis?</strong></td>
                                                            </tr>
                                                            <tr>
                                                            <td><label><input type="radio" name="comeFrom" value="0" checked>Very Interested</label><br>
                                                                            <label><input type="radio" name="comeFrom" value="3">Somewhat Interested</label><br>
                                                                            <label><input type="radio" name="comeFrom" value="3">Not at all Interested</label></td>
                                                            </tr>-->
                                                            <tr>
                                                            <td>
                                                                                    <input type = "hidden" name = "comeFrom" value = "0">
                                                                                    Please click Next to create your Panelist Profile. <input type="submit" name="Submit" value="Next">
                                                                            </td>
                                                            </tr>
                                                            </table>
                                                 </form>
                                                                                            </td>
                                            </tr>
                                              </table>
                                                                    </td>
                                          </tr>
                                        </table>
                                            <p align="left">&nbsp;</p>
                                    </td>
                            </tr>
                            <tr> 
                              <td height="23" class="sbkbar"><center>
      <p><a target="_blank" id="bbblink" class="sehzbus" href="https://www.bbb.org/chicago/business-reviews/marketing-programs-and-services/small-business-knowledge-center-in-chicago-il-88346842#bbblogo" title="Small Business Knowledge Center, Marketing Programs & Services, Chicago, IL" style="display: block;position: relative;overflow: hidden; width: 100px; height: 38px; margin: 0px; padding: 0px;"><img style="padding: 0px; border: none;" id="bbblinkimg" src="https://seal-chicago.bbb.org/logo/sehzbus/small-business-knowledge-center-88346842.png" width="200" height="38" alt="Small Business Knowledge Center, Marketing Programs & Services, Chicago, IL" /></a><br>

          <a href="SBKC_Terms_and_Conditions.pdf" target="_blank">Terms & Conditions</a>
        <br/> <br/>
        All rights reserved 2006 - <?php echo date("Y"); ?> Small Business Knowledge Center<br />
        <a href="https://www.sbkcenter.com/">www.sbkcenter.com</a></p>
</center>
              </div> </td>
                    </tr>
                    </table>
            </td>
    </tr>
    <tr> 

            <td class="border2">&nbsp;</td>
    </tr>
    </table>

    <?php }
    elseif($comeFrom==0){ 
            ?>
    <table width="533" border="0" align="center" cellpadding="0" cellspacing="0" class="main-table">
      <tr> 
        <td class="border1">&nbsp;</td>
      </tr>
      <tr> 
            <td class="border">
                            <table width="498" border="0" align="center" cellpadding="0" cellspacing="0">
                                    <tr>
                                            <td height="23" class="sbkbar"><p><center>
                                              <p><img src="images/sbkclogo.gif" width="250" height=61></p>
                                              <p><br>
                                          </p>
                    </center></td>
                                    </tr>
                                    <tr>
                                            <td valign="bottom" bgcolor="#FFFFFF"><br> 
                                    <table width="469" border="0" align="center" cellpadding="0" cellspacing="0">
                                    <!--<tr>
                                                                    <td width="469" valign="top" align="center"><img src="sbkc_images/logo.gif" width="397" height="139"></td>
                                                            </tr>-->
                                                            <tr> 
                                                              <td valign="top" align="center">
                                                                <h2>Get compensated for your junk mail!</h2><br>	
                                                            </tr>
                                                            <tr>
                                                                    <td>
                                                                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                            <tr valign="top"> 
                                            <td>
                                                    <form name="frm1" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" onSubmit="return validate();">
                                                            <table width="100%" border="0" cellpadding="0" cellspacing="10" class="formtable">
                                                            <tr>
                                                            <td valign="top">Page 1/3</td>
                                                    <td align="right"><span class="mandatory">* Fields required</span></td>
                                                    </tr>
    <!--
                                                    <tr>
                                                    <td width="20%" valign="top"><strong>Who were you referred by? (Please include full name)</strong><span class="mandatory">*</span></td>
                                                    <td width="80%" valign="bottom"><input name="referby" type="text" class="input" size="50" value="<?php if(isset($_SESSION['save']['referby'])) print htmlspecialchars($_SESSION['save']['referby'],ENT_QUOTES); ?>"></td>
                                                    </tr>
    -->
                                                    <tr>
                                                    <td width="20%" valign="top"><strong>First Name</strong><span class="mandatory">*</span></td>
                                                    <td width="80%"><input name="name" onblur="sendPan(document.frm1.name.value,'first_name');" type="text" class="input" size="50" value="<?php if(isset($_SESSION['save']['name'])) print htmlspecialchars($_SESSION['save']['name'],ENT_QUOTES); ?>"></td>
                                                    </tr>
                                                    <tr>
                                                    <td valign="top"><strong>Last Name</strong><span class="mandatory">*</span></td>
                                                    <td><input name="name2" type="text" onblur="sendPan(document.frm1.name2.value,'last_name');" class="input" size="50" value="<?php if(isset($_SESSION['save']['name2'])) print htmlspecialchars($_SESSION['save']['name2'],ENT_QUOTES); ?>"></td>
                                                    </tr>
                                                    <tr>
                                                    <td valign="top"><strong>Email</strong><span class="mandatory">*</span></td>
                                                    <td><input name="email" type="text" onblur="sendPan(document.frm1.email.value,'email');" class="input" size="50" value="<?php if(isset($_SESSION['save']['email'])) print htmlspecialchars($_SESSION['save']['email'],ENT_QUOTES); ?>"></td>
                                                    </tr>
                                                    <tr>
                                                    <td valign="top"><strong>Street Address</strong><span class="mandatory">*</span></td>
                                                    <td><input name="address" type="text" class="input" size="35" value="<?php if(isset($_SESSION['save']['address'])) print htmlspecialchars($_SESSION['save']['address'],ENT_QUOTES); ?>">
                                                    &nbsp;
                                                    <strong>Apt #</strong>&nbsp; <input name="apt" type="text" class="input" size="2" value="<?php if(isset($_SESSION['save']['apt'])) print htmlspecialchars($_SESSION['save']['apt'],ENT_QUOTES); ?>"></td>
                                                    </tr>
                                                    <tr>
                                                    <td valign="top"><strong>City</strong><span class="mandatory">*</span></td>
                                                    <td><input name="city" type="text" class="input" size="35" value="<?php if(isset($_SESSION['save']['city'])) print htmlspecialchars($_SESSION['save']['city'],ENT_QUOTES); ?>"></td>
                                                    </tr>
                                                    <tr>
                                                    <td valign="top"><strong>Select State/Province</strong><span class="mandatory">*</span></td>
                                                    <td><select name="state"><option value="">&nbsp;</option><?php 
                                                                                                                                    foreach($stateIDArray as $state=>$ID){
                                                                                                                                            echo "<option value=\"{$state}\"";
                                                                                                                                            if(isset($_SESSION['save']['state']) && $_SESSION['save']['state']==$state){
                                                                                                                                                    echo ' selected="selected"';
                                                                                                                                            }
                                                                                                                                            echo ">$state</option>";
                                                                                                                                    }
                                                                                                                                    ?></select>
                                                                                                                                    &nbsp;&nbsp;<strong>Postal Code</strong><span class="mandatory">*</span>
                                                                                                                                    <input name="zip" type="text" class="input" size="10" value="<?php if(isset($_SESSION['save']['zip'])) print htmlspecialchars($_SESSION['save']['zip'],ENT_QUOTES); ?>">
                                                                                                                            </td>
                                                                                                                    </tr>
                                                                                                                    <tr>
                                                                                                                            <td valign="top"><strong>Phone</strong><span class="mandatory">*</span></td>
                                                    <td>
                                                                                                                                    <input name="phone" type="text" class="input" size="16" maxlength="13" value="<?php if(isset($_SESSION['save']['phone'])) print htmlspecialchars($_SESSION['save']['phone'],ENT_QUOTES); ?>">
                                                            (ie. 800-123-4567)&nbsp;<strong>Ext</strong>&nbsp;
                                                            <input name="extension" type="text" class="input" size="4" maxlength="4" value="<?php if(isset($_SESSION['save']['extension'])) print htmlspecialchars($_SESSION['save']['extension'],ENT_QUOTES); ?>">
                                                                                                                            </td>
                                                                                                                    </tr>
                                                                                                                    <tr>
                                                                                                                            <td valign="top" colspan="2">
                                                                                                                                    <strong>Preferred Method of Contact?</strong><span class="mandatory">*</span>
                                                                                                                                    <select name="contact_method">
                                                                                                                                            <?php
                                                                                                                                                    $selArray = array("email"=>'Email',"phone"=>'Phone');
                                                                                                                                                    foreach($selArray as $num=>$show) {
                                                                                                                                                            echo "\n\t\t\t\t\t\t\t\t\t\t\t\t\t<option value=\"{$num}\"";
                                                                                                                                                            if(isset($_SESSION['save']['contact_method']) && $_SESSION['save']['contact_method']==$num) print ' selected';
                                                                                                                                                            echo ">$show</option>";
                                                                                                                                                    }
                                                                                                                                            ?>
                                                                                                                                    </select>
                                                                                                                            </td>
                                                                                                                    </tr>
                                                                                                                    <tr>
                                                                                                                            <td><strong>Date of Birth</strong><span class="mandatory">*</span></td>
                                                                                                                            <td>
                                                                                                                                    <select name="month">
                                                                                                                                            <option value="">Month</option>
                                                                                                                                            <?php
                                                                                                                                                    $selArray = array("01"=>'Jan',"02"=>'Feb',"03"=>'Mar',"04"=>'Apr',"05"=>'May',"06"=>'Jun',"07"=>'Jul',"08"=>'Aug',"09"=>'Sep',"10"=>'Oct',"11"=>'Nov',"12"=>'Dec');
                                                                                                                                                    foreach($selArray as $num=>$show) {
                                                                                                                                                            echo "\n\t\t\t\t\t\t\t\t\t\t\t\t\t<option value=\"{$num}\"";
                                                                                                                                                            if(isset($_SESSION['save']['month']) && $_SESSION['save']['month']==$num) print ' selected';
                                                                                                                                                            echo ">$show</option>";
                                                                                                                                                    }
                                                                                                                                            ?>
                                                                                                                                    </select>
                                                                                                                                    <select name="day">
                                                                                                                                            <option value="">Day</option>
                                                                                                                                            <?php
                                                                                                                                                    for($i=1;$i <= 31; $i++) {
                                                                                                                                                            $num = str_pad($i,2,'0',STR_PAD_LEFT);
                                                                                                                                                            echo "\n\t\t\t\t\t\t\t\t\t\t\t\t\t<option value=\"{$num}\"";
                                                                                                                                                            if(isset($_SESSION['save']['day']) && $_SESSION['save']['day']==$num) print ' selected';
                                                                                                                                                            echo ">$num</option>";
                                                                                                                                                    }
                                                                                                                                            ?>
                                                                                                                                    </select>
                                                                                                                                    <select name="year">
                                                                                                                                            <option value="">Year</option>
                                                                                                                                            <?php
                                                                                                                                                    for($i=1930;$i < 2007; $i++) {
                                                                                                                                                            echo "\n\t\t\t\t\t\t\t\t\t\t\t\t\t<option value=\"{$i}\"";
                                                                                                                                                            if(isset($_SESSION['save']['year']) && $_SESSION['save']['year']==$i) print ' selected';
                                                                                                                                                            echo ">$i</option>";
                                                                                                                                                    }
                                                                                                                                            ?>
                                                                                                                                    </select>
                                                                                                                            </td>
                                                                                                                    </tr>
                                                                                                                    <tr>
                                                                                                                            <td><strong>Gender</strong><span class="mandatory">*</span></td>
                                                                                                                            <td>
                                                                                                                                    <select name="gender">
                                                                                                                                            <option value="">choose</option>
                                                                                                                                            <?php
                                                                                                                                                    $selArray = array("M"=>'Male',"F"=>'Female');
                                                                                                                                                    foreach($selArray as $num=>$show) {
                                                                                                                                                            echo "\n\t\t\t\t\t\t\t\t\t\t\t\t\t<option value=\"{$num}\"";
                                                                                                                                                            if(isset($_SESSION['save']['gender']) && $_SESSION['save']['gender']==$num) print ' selected';
                                                                                                                                                            echo ">$show</option>";
                                                                                                                                                    }
                                                                                                                                            ?>
                                                                                                                                    </select>
                                                                                                                                    &nbsp; &nbsp;
                                                                                                                                    <!--
                                                                                                                                    <strong>Ethnicity</strong>
                                                                                                                                    <select name="ethnicity">
                                                                                                                                            <option value="">choose</option>
                                                                                                                                            <?php
                                                                                                                                                    $selArray = array("African American"=>'African American',"Asian"=>'Asian',"Caucasian"=>'Caucasian',"Hispanic"=>'Hispanic',"Others"=>'Other');
                                                                                                                                                    foreach($selArray as $num=>$show) {
                                                                                                                                                            echo "\n\t\t\t\t\t\t\t\t\t\t\t\t\t<option value=\"{$num}\"";
                                                                                                                                                            if(isset($_SESSION['save']['ethnicity']) && $_SESSION['save']['ethnicity']==$num) print ' selected';
                                                                                                                                                            echo ">$show</option>";
                                                                                                                                                    }
                                                                                                                                            ?>
                                                                                                                                    </select>
                                                                                                                                    -->
                                                                                                                            </td>
                                                                                                                    </tr>
                                                                                                                    <tr>
                                                                                                                            <td colspan="2"><strong>Household Income Level</strong><span class="mandatory">*</span>
                                                                                                                                    <select name="income">
                                                                                                                                            <option value="">choose</option>
                                                                                                                                            <?php
                                                                                                                                                    $selArray = array("Under $25k"=>'Under $25k',"$25k-$49k"=>'$25k-$49k',"$50k-$74k"=>'$50k-$74k',"$75k-$99k"=>'$75k-$99k',"$100k-$149k"=>'$100k-$149k',"$150k+"=>'$150k+');
                                                                                                                                                    /*$selArray = array("Under $20k"=>'Under $20k',"$20k-$24k"=>'$20k-$24k',"$25k-$34k"=>'$25k-$34k',"$35k-$44k"=>'$35k-$44k',"$45k-$54k"=>'$45k-$54k',"$55k-$64k"=>'$55k-$64k',"$65k-$74k"=>'$65k-$74k',"$75k-$100k"=>'$75k-$100k',"$101k-$150k"=>'$101k-$150k',"$150k+"=>'$150k+');*/
                                                                                                                                                    foreach($selArray as $num=>$show) {
                                                                                                                                                            echo "\n\t\t\t\t\t\t\t\t\t\t\t\t\t<option value=\"{$num}\"";
                                                                                                                                                            if(isset($_SESSION['save']['income']) && $_SESSION['save']['income']==$num) print ' selected';
                                                                                                                                                            echo ">$show</option>";
                                                                                                                                                    }
                                                                                                                                            ?>
                                                                                                                                    </select>
                                                                                                                            </td>
                                                                                                                    </tr>
                                                                                                                            <tr>
                                                                                                                                    <td colspan="2"><strong>Do you rent or own your home?</strong><span class="mandatory">*</span>
                                                                                                                                            <select name="rentorown">
                                                                                                                                                    <option value="">choose</option>
                                                                                                                                                    <?php
                                                                                                                                                            $selArray = array("Rent"=>'Rent',"Own"=>'Own');
                                                                                                                                                            foreach($selArray as $num=>$show) {
                                                                                                                                                                    echo "\n\t\t\t\t\t\t\t\t\t\t\t\t\t<option value=\"{$num}\"";
                                                                                                                                                                    if(isset($_SESSION['save']['rentorown']) && $_SESSION['save']['rentorown']==$num) print ' selected';
                                                                                                                                                                    echo ">$show</option>";
                                                                                                                                                            }
                                                                                                                                                    ?>
                                                                                                                                            </select>

                                                                                                                                    </td>
                                                                                                                            </tr>
                                                                                                                          <!--  ################ for add credit_score section ##################### -->
                                                                                                                            <tr>
                                                                                                                                    <td colspan="2"><strong>Credit Score</strong><span class="mandatory">*</span>
                                                                                                                                    <?php

                                                                                                                                    $sql = "select id,credit_score from cscan_credit_score";
                                                                                                                                    $result = mysql_query($sql);
                                                                                                                                    ?>
                                                                                                                                        <select name="credit_score">
                                                                                                                                                    <option value="">choose</option>
                                                                                                                                                    <?php
                                                                                                                                                    while ($row = mysql_fetch_row($result)) {
                                                                                                                                                        $stateIDArray[$row[0]] = $row[1];
                                                                                                                                                        $id             =   $row[0];
                                                                                                                                                        $credit_score   =   $row[1];
                                                                                                                                                        echo "\n\t\t\t\t\t\t\t\t\t\t\t\t\t<option value=\"{$id}\"";
                                                                                                                                                                    if(isset($_SESSION['save']['credit_score']) && $_SESSION['save']['credit_score']==$id) print ' selected';
                                                                                                                                                                    echo ">$credit_score</option>";     
                                                                                                                                                        }


                                                                                                                                                    ?>
                                                                                                                                            </select>

                                                                                                                                    </td>
                                                                                                                            </tr>
                                                                                                                           <!-- ################ for add credit_score section ##################### -->
                                                                                                                    <tr>
                                                                                                                            <td colspan="2"><p>Business owners and those who are  self-employed earn bonus points for their materials and may receive special  survey opportunities.</p>
                                                                                                                              <br>
                                                                                                                                    <strong>Do you own your own business?</strong><span class="mandatory">*</span>
                                                                                                                                    <select name="ownbiz">
                                                                                                                                            <option value="">choose</option>
                                                                                                                                            <?php
                                                                                                                                                    $selArray = array("Yes"=>'Yes',"No"=>'No');
                                                                                                                                                    foreach($selArray as $num=>$show) {
                                                                                                                                                            echo "\n\t\t\t\t\t\t\t\t\t\t\t\t\t<option value=\"{$num}\"";
                                                                                                                                                            if(isset($_SESSION['save']['ownbiz']) && $_SESSION['save']['ownbiz']==$num) print ' selected';
                                                                                                                                                            echo ">$show</option>";
                                                                                                                                                    }
                                                                                                                                            ?>
                                                                                                                                    </select>
                                                                                                                            </td>
                                                                                                                    </tr>
                                                                                                                    <tr>
                                                    <td valign="top" colspan ="2"><strong>Please list your primary area of business?</strong><br>
                                                    <input name="bizname" type="text" onblur="sendPan(document.frm1.bizname.value,'bizname');" class="input" size="50" value="<?php if(isset($_SESSION['save']['bizname'])) print htmlspecialchars($_SESSION['save']['bizname'],ENT_QUOTES); ?>"></td>
                                                    </tr>
                                                                                                                    <tr>
                                                                                                                            <td colspan="2">
                                                                                                                                    <!--
                                                                                                                                    <strong>Would you be willing to provide the SBKC<br />with your FICO score?</strong><span class="mandatory">*</span>
                                                                                                                                    <select name="FICOscore">
                                                                                                                                            <option value="">choose</option>
                                                                                                                                            <?php
                                                                                                                                                    $selArray = array("Yes"=>'Yes',"No"=>'No');
                                                                                                                                                    foreach($selArray as $num=>$show) {
                                                                                                                                                            echo "\n\t\t\t\t\t\t\t\t\t\t\t\t\t<option value=\"{$num}\"";
                                                                                                                                                            if(isset($_SESSION['save']['FICOscore']) && $_SESSION['save']['FICOscore']==$num) print ' selected';
                                                                                                                                                            echo ">$show</option>";
                                                                                                                                                    }
                                                                                                                                            ?>
                                                                                                                                    </select>
                                                                                                                                    -->
                                                                                                                            </td>
                                                                                                                    </tr>
                                                                                                                    <tr>
                                                                                                                            <td colspan = "2">
                                                                                                                    <b>How did you hear about the SBKC Consumer Panel?</b>
                                                                                                                    <p>
                                                                                                                    <?php echo str_repeat('&nbsp;', 5); ?><input type="radio" name="hearSBKC" value="1" <?php if ($_SESSION['save']['hearSBKC'] == 1 || !isset($_SESSION['save']['hearSBKC'])) echo 'checked'; ?> >Surfing the Web<br>
                                                                                                                    <?php echo str_repeat('&nbsp;', 5); ?><input type="radio" name="hearSBKC" value="2" <?php if ($_SESSION['save']['hearSBKC'] == 2) echo 'checked'; ?> >Referral (please insert name so we can credit this person)<br>
                                                                                                                    <?php echo str_repeat('&nbsp;', 5); ?><input type="radio" name="hearSBKC" value="3" <?php if ($_SESSION['save']['hearSBKC'] == 3) echo 'checked'; ?> >Other (insert)<br>
                                                                                                                    <?php echo str_repeat('&nbsp;', 8); ?><input type="text" name = "hearSBKCInsert" size="50" class="input" value="<?php if(isset($_SESSION['save']['hearSBKCInsert'])) print htmlspecialchars($_SESSION['save']['hearSBKCInsert'],ENT_QUOTES); ?>"><br><br/>
                                                                                                                    By clicking next you agree to SBKC's <a href="SBKC_Privacy_Policy.pdf" target="_blank">Privacy Policy</a><br/>

                                                                                                                    </p>
                                                                                                                            </td>
                                                                                                                    </tr>
                                                        <tr>
                                                          <td colspan="2"><input type="submit" name="Submit" value="Next"></td>
                                                        </tr>
                                                    </table>
                                                  <input type="hidden" name="comeFrom" value="1">
                                                 </form>
                                                                                            </td>
                                            </tr>
                                              </table>
                                                                    </td>
                                          </tr>
                                        </table>
                                            <p align="left">&nbsp;</p>
                                    </td>
                            </tr>
                            <tr> 
                                    <td height="23" class="sbkbar"><center>
                                        <p><a target="_blank" id="bbblink" class="sehzbus" href="https://www.bbb.org/chicago/business-reviews/marketing-programs-and-services/small-business-knowledge-center-in-chicago-il-88346842#bbblogo" title="Small Business Knowledge Center, Marketing Programs & Services, Chicago, IL" style="display: block;position: relative;overflow: hidden; width: 100px; height: 38px; margin: 0px; padding: 0px;"><img style="padding: 0px; border: none;" id="bbblinkimg" src="https://seal-chicago.bbb.org/logo/sehzbus/small-business-knowledge-center-88346842.png" width="200" height="38" alt="Small Business Knowledge Center, Marketing Programs & Services, Chicago, IL" /></a><br>
                                             <a href="SBKC_Terms_and_Conditions.pdf" target="_blank">Terms & Conditions</a>
                                        <br/> <br/>
                                          All rights reserved 2006 - <?php echo date("Y"); ?> Small Business Knowledge Center<br />
                        <a href="https://www.sbkcenter.com/">www.sbkcenter.com</a></p>
              </center></td>
                    </tr>
                    </table>
            </td>
    </tr>
    <tr> 
            <td class="border2">&nbsp;</td>
    </tr>
    </table>

    <?php }
    elseif($comeFrom==1){ 
    ?>

    <table width="533" border="0" align="center" cellpadding="0" cellspacing="0" class="main-table">
      <tr> 
        <td class="border1">&nbsp;</td>
      </tr>
      <tr> 
            <td class="border">
                            <table width="498" border="0" align="center" cellpadding="0" cellspacing="0">
                                    <tr>
                                            <td height="23" class="sbkbar"><center>
                                              <p><img src="images/sbkclogo.gif" width="250" height=61><br>
                                                <br>
                                          </p>
</center></td>
                                    </tr>
                                    <tr> 
                                            <td valign="bottom" bgcolor="#FFFFFF"> 
                                                    <table width="469" border="0" align="center" cellpadding="0" cellspacing="0">
                                                            <!--<tr>
                                                                    <td width="469" valign="top" align="center"><img src="sbkc_images/logo.gif" width="397" height="139"></td>
                                                            </tr>-->
                                                            <tr> 
                                                                    <td valign="top" align="center">
                                                                <br><h2>Get compensated for your junk mail!</h2>	<br>
                                                            </tr>
                                                            <tr>
                                                                    <td>
                                                                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                            <tr valign="top"> 
                                            <td>
                                                    <form id="frm1" name="frm1" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" onSubmit="return validate();">
                                                                    <table width="100%" border="0" cellpadding="0" cellspacing="10" class="formtable">

                                                                                    <tr>
                                                                                            <td colspan="2">
                                                                                                    <table width="100%" border="0" cellpadding="0" cellspacing="0"><tr>
                                                                                                    <td valign="top">Page 2/3</td>
                                                                                            <td align="right">
                                                                                                            <!--<span class="mandatory">* Fields required</span>
                      --></td>
                                                                                            </tr></table>
                                                                                    </td>
                                                                                    </tr>
                                                                                    <!--
                                                                                    <tr>
                                                                                            <td colspan="2">Consumer Relationships</td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                            <td colspan="2">Please indicate if you have any of the following:</td>
                                                                                    </tr>
                                                                                    <?php 
                                                                                    /*foreach($titles2 as $key=>$title){
                                                                                            print '<tr>
                                                                                                    <td valign="top" style="border-bottom-width: 1px;border-bottom-style: dashed;border-bottom-color: #0099CC;"><strong>'.$title.'</strong><span class="mandatory">*</span></td>
                                                                                                    <td valign="bottom" nowrap>
                                                                                                            <label><input type="radio" name="insurance'.$key.'" value="1"';
                                                                                                            if(isset($_SESSION['save']['insurance'.$key]) && $_SESSION['save']['insurance'.$key]=='1') echo ' checked';
                                                                                                            print '>Yes</label>&nbsp;<label><input type="radio" name="insurance'.$key.'" value="0"';
                                                                                                            if(isset($_SESSION['save']['insurance'.$key]) && $_SESSION['save']['insurance'.$key]=='0') echo ' checked';
                                                                                                            print '>No</label>
                                                                                                    </td>
                                                                                            </tr>
                                                                                            ';
                                                                                    }*/
                                                                                    ?>
                        <tr>
                                            -->
                                            <tr>
                                                    <td colspan = "2">
                                                    <p>If you would like to earn points for the mail of other adult  members of your household who are over 18 years of age, please provide their  information below.</p>
<div id="familyMemberQuestion"><p><b>Ready to add members of your household?</b> <input type = "button" id = "family1" name = "family1" value = "YES" onclick = "showFamilyMemberForm()"> <!--<input type = "button" id = "family2" name = "family2" value = "NO" onclick = "submitFamilyMemberForm()">-->
            <br>
            <b>No additional members to add.</b> <input type = "button" id = "family2" name = "family2" value = "Submit" onclick = "submitFamilyMemberForm()">

    </p>
                                                    </div>
                                                    <!--
############################################################							
                                                    FAMILY MEMBER FORM
############################################################
                                                    -->
                                                    <div id="familyInsertNotification"></div>
                                                    <div id="familyMemberForm">
                                                            <table width="100%"><tr><td align ="right"><span class="mandatory">* Fields required</span></td></tr></table>

                                                    <table width="100%" border="0" cellpadding="0" cellspacing="10" class="formtable">

                                            <tr>
                                            <td width="20%" valign="top"><strong>First Name</strong><span class="mandatory">*</span></td>
                                            <td width="80%"><input id="name" type="text" class="input" size="50" value=""></td>
                                            </tr>
                                            <tr>
                                            <td valign="top"><strong>Last Name</strong><span class="mandatory">*</span></td>
                                            <td><input id="name2" type="text" class="input" size="50" value=""></td>
                                            </tr>
                                            <tr>
                                            <td valign="top"><strong>Email</strong><span class="mandatory">*</span></td>
                                            <td><input id="email" type="text"  class="input" size="50" value=""></td>
                                            </tr>
                                            <tr>
                                            <td valign="top"><strong>Street Address</strong><span class="mandatory">*</span></td>

                                            <td><input id="address" type="text" class="input" size="35" value="<?php if(isset($_SESSION['save']['address'])) print htmlspecialchars($_SESSION['save']['address'],ENT_QUOTES); ?>">
                                            &nbsp;
                                            <strong>Apt #</strong>&nbsp; <input id="apt" type="text" class="input" size="2" value="<?php if(isset($_SESSION['save']['apt'])) print htmlspecialchars($_SESSION['save']['apt'],ENT_QUOTES); ?>"></td>
                                            </tr>
                                            <tr>
                                            <td valign="top"><strong>City</strong><span class="mandatory">*</span></td>
                                            <td>
                                                                                                                            <input id="city" type="text" class="input" size="10" value="<?php if(isset($_SESSION['save']['city'])) print htmlspecialchars($_SESSION['save']['city'],ENT_QUOTES); ?>">
                                                                                                                            &nbsp;&nbsp;<strong>Select State/Province</strong><span class="mandatory">*</span>
                                                                                                                            <select id="state"><option value="">&nbsp;</option><?php 
                                                                                                                            foreach($stateIDArray as $state=>$ID){
                                                                                                                                    echo "<option value=\"{$state}\"";
                                                                                                                                    if(isset($_SESSION['save']['state']) && $_SESSION['save']['state']==$state){
                                                                                                                                            echo ' selected="selected"';
                                                                                                                                    }
                                                                                                                                    echo ">$state</option>";
                                                                                                                            }
                                                                                                                            ?></select>
                                                                                                                            &nbsp;&nbsp;<strong>Postal Code</strong><span class="mandatory">*</span>
                                                                                                                            <input id="zip" type="text" class="input" size="10" value="<?php if(isset($_SESSION['save']['zip'])) print htmlspecialchars($_SESSION['save']['zip'],ENT_QUOTES); ?>">
                                                                                                                    </td>
                                                                                                            </tr>
                                                                                                            <tr>
                                                                                                                    <td valign="top"><strong>Phone</strong><span class="mandatory">*</span></td>
                                            <td>
                                                                                                                            <input id="phone" type="text" class="input" size="16" maxlength="13" value="<?php if(isset($_SESSION['save']['phone'])) print htmlspecialchars($_SESSION['save']['phone'],ENT_QUOTES); ?>">
                                                    (ie. 800-123-4567)&nbsp;<strong>Ext</strong>&nbsp;
                                                    <input id="extension" type="text" class="input" size="4" maxlength="4" value="<?php if(isset($_SESSION['save']['extension'])) print htmlspecialchars($_SESSION['save']['extension'],ENT_QUOTES); ?>">
                                                                                                                    </td>
                                                                                                            </tr>
                                                                                                            <tr>
                                                                                                                    <td valign="top" colspan="2">
                                                                                                                            <strong>Preferred Method of Contact?</strong><span class="mandatory">*</span>
                                                                                                                            <select id="contact_method">
                                                                                                                                    <?php
                                                                                                                                            $selArray = array("email"=>'Email',"phone"=>'Phone');
                                                                                                                                            foreach($selArray as $num=>$show) {
                                                                                                                                                    echo "\n\t\t\t\t\t\t\t\t\t\t\t\t\t<option value=\"{$num}\"";

                                                                                                                                                    echo ">$show</option>";
                                                                                                                                            }
                                                                                                                                    ?>
                                                                                                                            </select>
                                                                                                                    </td>
                                                                                                            </tr>
                                                                                                            <tr>
                                                                                                                    <td><strong>Date of Birth</strong><span class="mandatory">*</span></td>
                                                                                                                    <td>
                                                                                                                            <select id="month">
                                                                                                                                    <option value="">Month</option>
                                                                                                                                    <?php
                                                                                                                                            $selArray = array("01"=>'Jan',"02"=>'Feb',"03"=>'Mar',"04"=>'Apr',"05"=>'May',"06"=>'Jun',"07"=>'Jul',"08"=>'Aug',"09"=>'Sep',"10"=>'Oct',"11"=>'Nov',"12"=>'Dec');
                                                                                                                                            foreach($selArray as $num=>$show) {
                                                                                                                                                    echo "\n\t\t\t\t\t\t\t\t\t\t\t\t\t<option value=\"{$num}\"";
                                                                                                                                                    echo ">$show</option>";
                                                                                                                                            }
                                                                                                                                    ?>
                                                                                                                            </select>
                                                                                                                            <select id="day">
                                                                                                                                    <option value="">Day</option>
                                                                                                                                    <?php
                                                                                                                                            for($i=1;$i <= 31; $i++) {
                                                                                                                                                    $num = str_pad($i,2,'0',STR_PAD_LEFT);
                                                                                                                                                    echo "\n\t\t\t\t\t\t\t\t\t\t\t\t\t<option value=\"{$num}\"";
                                                                                                                                                    echo ">$num</option>";
                                                                                                                                            }
                                                                                                                                    ?>
                                                                                                                            </select>
                                                                                                                            <select id="year">
                                                                                                                                    <option value="">Year</option>
                                                                                                                                    <?php
                                                                                                                                            for($i=1930;$i < 2007; $i++) {
                                                                                                                                                    echo "\n\t\t\t\t\t\t\t\t\t\t\t\t\t<option value=\"{$i}\"";
                                                                                                                                                    echo ">$i</option>";
                                                                                                                                            }
                                                                                                                                    ?>
                                                                                                                            </select>
                                                                                                                    </td>
                                                                                                            </tr>
                                                                                                            <tr>
                                                                                                                    <td><strong>Gender</strong><span class="mandatory">*</span></td>
                                                                                                                    <td>
                                                                                                                            <select id="gender">
                                                                                                                                    <option value="">choose</option>
                                                                                                                                    <?php
                                                                                                                                            $selArray = array("M"=>'Male',"F"=>'Female');
                                                                                                                                            foreach($selArray as $num=>$show) {
                                                                                                                                                    echo "\n\t\t\t\t\t\t\t\t\t\t\t\t\t<option value=\"{$num}\"";
                                                                                                                                                    echo ">$show</option>";
                                                                                                                                            }
                                                                                                                                    ?>
                                                                                                                            </select>
                                                                                                                    </td>
                                                                                                            </tr>
                                                                                                            <tr>
                                                                                                                    <td colspan="2">
                                                                                                                            We occasionally offer special survey opportunities for business owners.<br>
                                                                                                                            <strong>Does this person own his/her own business?</strong><span class="mandatory">*</span>
                                                                                                                            <select id="ownbiz">
                                                                                                                                    <option value="">choose</option>
                                                                                                                                    <?php
                                                                                                                                            $selArray = array("Yes"=>'Yes',"No"=>'No');
                                                                                                                                            foreach($selArray as $num=>$show) {
                                                                                                                                                    echo "\n\t\t\t\t\t\t\t\t\t\t\t\t\t<option value=\"{$num}\"";
                                                                                                                                                    echo ">$show</option>";
                                                                                                                                            }
                                                                                                                                    ?>
                                                                                                                            </select>
                                                                                                                    </td>
                                                                                                            </tr>
                                                                                                            <tr>
                                                    <td valign="top" colspan="2"><strong>What type of his/her business?</strong>
                                                      <input id ="bizname" name="bizname" type="text" class="input" size="50" value=""></td>
                                                    </tr>

                                                <tr>
                                                  <td colspan="2"><input type="button" name="Submit" value="Add" onclick = "FMValidate();"> <input type = "button" value="Cancel" onclick = "cancelFamilyEntry();"></td>
                                                </tr>
                                            </table>
                                                    </div>
                                                    </td>
                                            <tr>
                          <td colspan="2"><!-- <input type="submit" name="Submit" value="Next">--></td>
                        </tr>

                          </table>			
                        <input type="hidden" name="comeFrom" value="2">
                      </form>
                                                            </td>
                                                    </tr>
                                            </table>
                                    </td>
          </tr>
          <tr> 
            <td>&nbsp;</td>
                            </tr>
                    </table>
            <p align="left">&nbsp;</p></td>
            </tr>
            <tr> 
            <td height="23" class="sbkbar"><center>
              <p><a target="_blank" id="bbblink" class="sehzbus" href="https://www.bbb.org/chicago/business-reviews/marketing-programs-and-services/small-business-knowledge-center-in-chicago-il-88346842#bbblogo" title="Small Business Knowledge Center, Marketing Programs & Services, Chicago, IL" style="display: block;position: relative;overflow: hidden; width: 100px; height: 38px; margin: 0px; padding: 0px;"><img style="padding: 0px; border: none;" id="bbblinkimg" src="https://seal-chicago.bbb.org/logo/sehzbus/small-business-knowledge-center-88346842.png" width="200" height="38" alt="Small Business Knowledge Center, Marketing Programs & Services, Chicago, IL" /></a><br>

                    <a href="SBKC_Terms_and_Conditions.pdf" target="_blank">Terms & Conditions</a>
                  <br/><br/>
                All rights reserved 2006 - <?php echo date("Y"); ?> Small Business Knowledge Center<br />
                <a href="https://www.sbkcenter.com/">www.sbkcenter.com</a></p>
</center></td>
            </tr>
                    </table>
                    </td>
            </tr>
      <tr> 
        <td class="border2">&nbsp;</td>
      </tr>
    </table>

    <?php }
    elseif($comeFrom==2){ 
    ?>

    <table width="533" border="0" align="center" cellpadding="0" cellspacing="0" class="main-table">
      <tr> 
        <td class="border1">&nbsp;</td>
      </tr>
      <tr> 
            <td class="border">

                            <table width="498" border="0" align="center" cellpadding="0" cellspacing="0">
                                    <tr>
                                            <td height="23" class="sbkbar"><center>
                                              <p><img src="images/sbkclogo.gif" width="250" height=61><br>
                                                <br>
                                          </p>
</center></td>
                                    </tr>
                                    <tr> 
                                            <td valign="bottom" bgcolor="#FFFFFF" border-color="#b7b7b7"> 
                                                    <table width="469" border="0" align="center" cellpadding="0" cellspacing="0">
                                                            <!--<tr>
                                                                    <td width="469" valign="top" align="center"><img src="sbkc_images/logo.gif" width="397" height="139"></td>
                                                            </tr>-->
                                                            <tr> 
                                                                    <td valign="top" align="center"></p>
                                                                <br><h2>Get compensated for your junk mail!</h2>	<br>
                                                            </tr>
                                                            <tr>
                                                                    <td>

                                                                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                            <tr valign="top"> 
                                            <td>

                                                    <form name="frm1" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" onSubmit="return validate();">
                                                    <table width="100%" border="0" cellpadding="0" cellspacing="10" class="formtable">
                                                                                                    <tr>
                                                                                                                            <td colspan="2">
                                                                                                                                    <table width="100%" border="0" cellpadding="0" cellspacing="0"><tr>
                                                                                                                                    <td valign="top">Page 3/3</td>
                                                                                                            <td align="right">
                                                                                                                                            <!--<span class="mandatory">*Fields required</span>-->
                                                                                                                                    </td>
                                                                                                            </tr></table>
                                                                                                    </td>
                                                                                                                    </tr>
                                                                                    <?php 
                                                                                    $subj = 'Thank you! Your Request to Join SBKC has been Received';
                                                                                    $bodytext = "Thank you for your interest. We received your request to join our exciting SBKC Consumer market research panel!\nWe are processing your membership and you should hear from us by Friday of this week via email with all the details you will need to participate.\nIn the interim, if you have any questions, please feel free to email us at consumers@sbkcenter.com.\nThanks once again for your interest. We look forward to working with you!\n\nBest,\n\nAshley, Consumer Panel Manager";
                                                                                    $bodyhtml = <<< MAILBODY
<html>

<iframe src="https://forwardrocketlaunch.com/p.ashx?o=1702&t=TRANSACTION_ID" height="1" width="1" frameborder="0"></iframe>

<body> <p>Thank you for your interest. We received your application to join our exciting SBKC Consumer market research panel!</p>
<p>Please remember to add <a href="mailto:Consumers@sbkcenter.com " target="_top">Consumers@sbkcenter.com</a> to your email contact list so that when we email you about your application, it does not accidentally wind up in your Spam folder.</p>
<p>Due to a large number of individuals interested in our Panel, your application will be added to our waiting list. We appreciate your patience while we review all applications and try to find spots for everyone! We will notify you once a space becomes available or if you do not qualify for the Panel at this time.</p>
<p>In the interim, if you have any questions or concerns, please feel free to email us at <a href="mailto:Consumers@sbkcenter.com " target="_top">Consumers@sbkcenter.com</a>.</p>

<p>Thanks once again for your interest. We look forward to working with you!</p>
<p>&nbsp;</p>
<p>Best,</p>
<p>&nbsp;</p>
<p>Ashley, Consumer Panel Manager</p>
</body>
</html>>
MAILBODY;
                                                                                    if(!empty($_SESSION['save']['email'])){
                                                                                            require_once('Mail.php');
                                                                                            require_once('Mail/mime.php');
                                                                                            $crlf = "\n";
                                                                                            //$mail =& Mail::factory('mail','-f'.$EMAIL_error);
                                                                                            $params = array(
                                                                                                    'username'=>'',
                                                                                                    'password'=>'',
                                                                                                    'persist'=>true,
                                                                                            );
                                                                                            $mail =& Mail::factory('smtp',$params);

                                                                                            $hdrs = array('To'=>$_SESSION['save']['email'],'From'=>'consumers@sbkcenter.com','Subject'=>$subj);
                                                                                            //$hdrs = array('To'=>$_SESSION['save']['email'],'From'=>'share@competiscan.com','Subject'=>$subj);
                                                                                            
                                                                                            $mime = new Mail_mime($crlf);
                                                                                            $mime->setHTMLBody($bodyhtml);
                                                                                            //$mime->setTXTBody($bodytext);
                                                                                            $body = $mime->get();
                                                                                            $headers = $mime->headers($hdrs);
                                                                                            $send = $mail->send($_SESSION['save']['email'], $headers, $body);
                                                                                            if (PEAR::isError($send)) { 
                                                                                                    //echo $send->getdebuginfo();
                                                                                            }
                                                                                    }
                                                                                    $_SESSION['save_id'] = -1;
                                                                                    $_SESSION['save'] = array();
                                                                                    /*foreach($titles2 as $key=>$title){
                                                                                            if(isset($_SESSION['save']['insurance'.$key]) && $_SESSION['save']['insurance'.$key]=='1'){
                                                                                                    if($key==4){
                                                                                                                    print '<tr>
                                                                                                                            <td valign="top"><strong>What types of Supplemental Insurance do you have? (Cancer, Critical Illness, Long Term Care, Disability, etc)</strong><span class="mandatory">*</span></td>
                                                                                                                            <td valign="top" nowrap><input type="text" name="supplemental'.$key.'" value="';
                                                                                                                            if(isset($_SESSION['save']['supplemental'.$key])) print htmlspecialchars($_SESSION['save']['supplemental'.$key],ENT_QUOTES);
                                                                                                                            print '">
                                                                                                                            </td>
                                                                                                                    </tr>
                                                                                                                    ';
                                                                                                    }	elseif($key==8){
                                                                                                                    print '<tr>
                                                                                                                            <td valign="top"><strong>What types of investments do you own (Annuities, IRA, Mutual Funds, etc)?</strong><span class="mandatory">*</span></td>
                                                                                                                            <td valign="top" nowrap><input type="text" name="investments'.$key.'" value="';
                                                                                                                            if(isset($_SESSION['save']['investments'.$key])) print htmlspecialchars($_SESSION['save']['investments'.$key],ENT_QUOTES);
                                                                                                                            print '">
                                                                                                                            </td>
                                                                                                                    </tr>
                                                                                                                    ';
                                                                                                            }
                                                                                                    print '<tr>
                                                                                                            <td valign="top"><strong>';
                                                                                                            if($key==8){
                                                                                                                    print 'Which companies are your investments with (Charles Schwab, Fidelity, Edward Jones, etc)?';
                                                                                                            }elseif($key == 9){
                                                                                                                    print 'Who is your Checking/Savings Account with?';
                                                                                                            }elseif($key == 11){
                                                                                                                    print 'Who is your Mortgage lender?';
                                                                                                            }elseif($key == 10){
                                                                                                                print 'Which credit cards do you have?';
                                                                                                            }
                                                                                                            elseif($key==17){
                                                                                                                    print 'Who is your TV provider?';
                                                                                                            }else if($key == 4){
                                                                                                                    print "Who is your Supplemental Insurance provider (AFLAC, Unum, Colonial Life, Guardian, etc)?";
                                                                                                            }elseif($key == 12 ){
                                                                                                                    print "Who is your Home Equity Loan lender?";
                                                                                                            }elseif($key == 13) {
                                                                                                                            print 'What other loans do you have (Unsecured, Educational, etc)?';
                                                                                                            }
                                                                                                            else{
                                                                                                                    print 'Who is your '.$title.' provider?';
                                                                                                            }
                                                                                                            print '</strong><span class="mandatory">*</span></td>
                                                                                                            <td valign="top" nowrap><input type="text" name="insurance_p'.$key.'" value="';
                                                                                                            if(isset($_SESSION['save']['insurance_p'.$key])) print htmlspecialchars($_SESSION['save']['insurance_p'.$key],ENT_QUOTES);
                                                                                                            print '">
                                                                                                            </td>
                                                                                                    </tr>
                                                                                                    ';
                                                                                                    if($key<5){
                                                                                                            print '<tr>
                                                                                                                    <td valign="top"><strong>Is your '.$title.' through...<br />(select all that apply)</strong><span class="mandatory">*</span></td>
                                                                                                                    <td valign="top" nowrap>';
                                                                                                                            $currp = 0;
                                                                                                                            foreach($providerArray as $num=>$show) {
                                                                                                                                    echo "<label><input type=\"checkbox\" name=\"insurance_v{$key}[]\" value=\"{$num}\"";
                                                                                                                                    if(isset($_SESSION['save']['insurance_v'.$key]) && in_array($num,$_SESSION['save']['insurance_v'.$key])) print ' checked';
                                                                                                                                    echo ">$show</label>";
                                                                                                                                    $currp++;
                                                                                                                                    if($providercount!=$currp) print '<br />';
                                                                                                                            }
                                                                                                                    print '</td>
                                                                                                            </tr>
                                                                                                            ';

                                                                                                    }elseif($key == 13){
                                                                                                                    print '<tr>
                                                                                                                            <td valign="top"><strong>Which lenders are your other loans with (Bank of America, Citibank, etc)?</strong><span class="mandatory">*</span></td>
                                                                                                                            <td valign="top" nowrap><input type="text" name="otherloan'.$key.'" value="';
                                                                                                                            if(isset($_SESSION['save']['otherloan'.$key])) print htmlspecialchars($_SESSION['save']['otherloan'.$key],ENT_QUOTES);
                                                                                                                            print '">
                                                                                                                            </td>
                                                                                                                    </tr>
                                                                                                                    ';

                                                                                                    }

                                                                                            }
                                                                                    }*/
                                                                                    ?>
                                                                                                                    <tr>

                                                                                                                    </tr>
                                                                                                                    <tr>
                                                                                                                            <td colspan="2"><h1>Thank You!</p></h1>
                                                                                                                              <p><br>
                                                                                                                                Thank you for submitting your Panelist Profile! We will contact you if you are eligible for the study via email and phone call. Once accepted, you will receive a Welcome Kit with important information about your membership.<br>
                                                                                                                                <br>
                                                                                                                                If you are located within Canada, please allow 2-3 weeks for your welcome Kit to arrive.<br>																  
                                                                                                              </p><p>&nbsp;<img src="https://forwardrocketlaunch.com/p.ashx?o=3013&f=img&t=TRANSACTION_ID" width="1" height="1" border="0" />
                                                                                                              <?php
                                                                                                                if(isset($_SESSION['referer_url']) && $_SESSION['referer_url']!=''){  ?>
                                                                                                                  
                                                                                                                    <iframe src="https://forwardrocketlaunch.com/p.ashx?a=579&e=336&t=TRANSACTION_ID" height="1" width="1" frameborder="0"></iframe>
                                                                                                                    <iframe src="https://roi-rocket.org/p.ashx?a=579&e=336&t=TRANSACTION_ID" height="1" width="1" frameborder="0"></iframe>
                                                                                                                    
                                                                                                                <?php 
                                                                                                                    $_SESSION['referer_url']='';
                                                                                                                    unset($_SESSION['referer_url']);
                                                                                                                } 
                                                                                                                ?>
                                                                                                              </p></td>
                                                                                                                    </tr>
                                                        <!--<tr>
                                                      <td colspan="2"><input type="submit" name="Submit" value="Submit"></td>
                                                        </tr>-->
                                                      </table>
                                                    <input type="hidden" name="comeFrom" value="3">
                                                     </form>

                                                                                            </td>
                                                                                    </tr>
                                                                            </table>

                                                                    </td>
                                        </tr>
                                      <tr>
                                        <td>&nbsp;</td>
                                                            </tr>
                                                    </table>

                                                    <p align="left">&nbsp;</p>
                                            </td>
                                    </tr>
                                    <tr> 
                                    <td height="23" class="sbkbar"><center>
                                      <p><a target="_blank" id="bbblink" class="sehzbus" href="https://www.bbb.org/chicago/business-reviews/marketing-programs-and-services/small-business-knowledge-center-in-chicago-il-88346842#bbblogo" title="Small Business Knowledge Center, Marketing Programs & Services, Chicago, IL" style="display: block;position: relative;overflow: hidden; width: 100px; height: 38px; margin: 0px; padding: 0px;"><img style="padding: 0px; border: none;" id="bbblinkimg" src="https://seal-chicago.bbb.org/logo/sehzbus/small-business-knowledge-center-88346842.png" width="200" height="38" alt="Small Business Knowledge Center, Marketing Programs & Services, Chicago, IL" /></a><br

          <a href="SBKC_Terms_and_Conditions.pdf" target="_blank">Terms & Conditions</a>
                                          <br/><br/>
                                        All rights reserved 2006 - <?php echo date("Y"); ?> Small Business Knowledge Center<br />
                  <a href="https://www.sbkcenter.com/">www.sbkcenter.com</a></p>
</center></td>
                                    </tr>
                            </table>

                    </td>
            </tr>
      <tr> 
            <td class="border2">&nbsp;</td>
      </tr>
    </table>

    <?php }
    else print 'Thank You'; 
}
?>
</body>
</html>
<?php
//@return String containing a sugar id in the format: aaaaaaaa-bbbb-cccc-dddd-eeeeeeeeeeee
function create_sugar_id()
{
    $microTime = microtime();
	list($a_dec, $a_sec) = explode(" ", $microTime);

	$dec_hex = sprintf("%x", $a_dec* 1000000);
	$sec_hex = sprintf("%x", $a_sec);

	ensure_length($dec_hex, 5);
	ensure_length($sec_hex, 6);

	$sugar_id = "";
	$sugar_id .= $dec_hex;
	$sugar_id .= create_sugar_id_section(3);
	$sugar_id .= '-';
	$sugar_id .= create_sugar_id_section(4);
	$sugar_id .= '-';
	$sugar_id .= create_sugar_id_section(4);
	$sugar_id .= '-';
	$sugar_id .= create_sugar_id_section(4);
	$sugar_id .= '-';
	$sugar_id .= $sec_hex;
	$sugar_id .= create_sugar_id_section(6);

	return $sugar_id;
}
function create_sugar_id_section($characters)
{
	$return = "";
	for($i=0; $i<$characters; $i++)
	{
		$return .= sprintf("%x", mt_rand(0,15));
	}
	return $return;
}
function ensure_length(&$string, $length)
{
	$strlen = strlen($string);
	if($strlen < $length)
	{
		$string = str_pad($string,$length,"0");
	}
	else if($strlen > $length)
	{
		$string = substr($string, 0, $length);
	}
} 
function get_client_ip() {
    $ipaddress = '';
    if (getenv('HTTP_CLIENT_IP'))
        $ipaddress = getenv('HTTP_CLIENT_IP');
    else if(getenv('HTTP_X_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
    else if(getenv('HTTP_X_FORWARDED'))
        $ipaddress = getenv('HTTP_X_FORWARDED');
    else if(getenv('HTTP_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_FORWARDED_FOR');
    else if(getenv('HTTP_FORWARDED'))
       $ipaddress = getenv('HTTP_FORWARDED');
    else if(getenv('REMOTE_ADDR'))
        $ipaddress = getenv('REMOTE_ADDR');
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;
}
?>
