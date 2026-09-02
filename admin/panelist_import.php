<?php
$ALLOW_GROUPS = array(43);
require_once("../auth_auth.php");
require_once("../includes/functions.php");
$stateIDArray = array();
$countries = array('US');
$sqlc = "SELECT DISTINCT countryCode FROM cscan_state JOIN ISO31661_alpha2code ON (code=countryCode) WHERE countryCode<>'US' ORDER BY country";
$rsc = $DRW->query($sqlc,$DRW_read);
while($rowc = $DRW->fetch_row($rsc)) {
	$countries[] = $rowc[0];
}
foreach($countries as $country){
	$sql = "select stateCode,panelist_stateID from cscan_state WHERE countryCode='".$country."' ORDER BY stateName";
	$result = $DRW->query($sql,$DRW_read);
	while($row = $DRW->fetch_row($result)){
		$stateIDArray[$row[0]] = $row[1];
	}
}

if (isset($_POST['submit']) && sizeof($_POST['panelists'])>0) {
	if ($_POST['actions'] == "Move to CRM") {
		/*
		id
		date_modified
		welcome_letter_sent
		imported_to_sugar
		ip_address
		*/
		$sqlbase = "SELECT id,first_name,last_name,birthdate,phone,
			email,primary_address_street,primary_address_city,
			primary_address_state,primary_address_postalcode,description,
			gender,ethnicity,income,contact_method,ownbiz,FICOscore,HealthInsurance,
			LifeInsurance,DentalInsurance,VisionInsurance,SupplementalInsurance,AutoInsurance,
			HomeOwnersRentersInsurance,401k,OtherInvestments,CheckingSavingsAccount,
			CreditCard,Mortgage,LoanEducational,WirelessCellPhone,HomePhone,InternetAccess,
			TVProvider,HealthInsurance_p,LifeInsurance_p,
			DentalInsurance_p,VisionInsurance_p,SupplementalInsurance_p,
			AutoInsurance_p,HomeOwnersRentersInsurance_p,401k_p,OtherInvestments_p,
			CheckingSavingsAccount_p,CreditCard_p,Mortgage_p,LoanEducational_p,
			WirelessCellPhone_p,HomePhone_p,InternetAccess_p,TVProvider_p,HealthInsurance_v,
			LifeInsurance_v,DentalInsurance_v,VisionInsurance_v,SupplementalInsurance_v,OtherInvestments_m,
			id,SupplementalInsurance_m,
			hearSBKC, hearSBKCInsert,familyContactID,rentorown,bizname
			FROM cscan_contacts_pre WHERE imported_to_sugar='0'";
		$sql = $sqlbase . " AND familyContactID = '0' AND (";
		foreach($_POST['panelists'] as $panelist) {
			$sql .= " id = '".$panelist."' OR";
		}
		$sql = substr($sql, 0, -3);
		$sql .= ")";
		$sql .= " ORDER BY date_modified";
		$result_q = $DRW->query($sql,$DRW_read);
		
		while($data = $DRW->fetch_array($result_q)) {
			$pid = $data[0];
			$childrens = 12;
			list($parents,$sid) = insertPan($data,$childrens);
			if(!empty($parents) && !empty($sid)){
				$sql_sub =  $sqlbase . " AND familyContactID = '".$pid."' ORDER BY date_modified";
				$result_q2 = $DRW->query($sql_sub,$DRW_read);
				while($data2 = $DRW->fetch_array($result_q2)) {
					$childrens++;
					insertPan($data2,$childrens,$parents,$sid);
				}
			}
		}
	}
	elseif ($_POST['actions'] == "Delete") {
		$sql = "DELETE FROM cscan_contacts_pre WHERE ";
		foreach ($_POST['panelists'] as $panelist) {
			$sql .= " id = '".$panelist."' OR familyContactID = '".$panelist."' OR";
		}
		$sql = substr($sql, 0, -3);
		if ($DRW->query($sql, $DRW_main)) {
                    foreach ($_POST['panelists'] as $panelist) {
                         $data = [
                             'auth_id' => $GLOBALS['AUTH_DATA']['userID'],
                             'auth_name'=>authUserName($GLOBALS['AUTH_DATA']['userID']),
                             'deleted_id' => (int) $panelist,
                             'sql_query' => $sql,
                             'ip_address' => ipAddress(),
                             'user_agent' => $_SERVER['HTTP_USER_AGENT'],
                             'delete_type' => 'Panelist Management',
                             'is_mobile' => isMobile(),
                             'insert_date' => date("Y-m-d H:i:s")
                         ];
                         trackDelete($data);
                         $emailData[] = $data;
                     }
                if (count($emailData) > 0) {
                    $html = '<table width="100%" border="1">';
                    $html .= '<tr><td>Auth ID</td><td>User Name</td><td>Deleted ID</td><td>SQL Query</td><td>IP Address</td><td>User Agent</td><td>Module</td><td>Is Mobile</td><td>Deleted Date (CST)</td></tr>';
                    foreach ($emailData as $tr) {
                        if (is_array($tr) && count($tr) > 0) {
                            $html .= '<tr>';
                            foreach ($tr as $td) {
                                $html .= '<td>' . $td . '</td>';
                            }
                            $html .= '</tr>';
                        }
                    }
                    $html .= '</table>';

                    sendDevAlert('Caution! Data Deleted From Manage Panelists', $html);
                }
            }
                
                
                
	}
}
else{
	ob_end_clean();
	header('Location: managePanelists.php');
	exit;
}
ob_end_clean();
header('Location: '.$_SERVER['HTTP_REFERER']);

function insertPan($data,$childrens=12,$parents='',$reports_to_id=''){
	global $DRW,$DRW_read,$DRW_main,$DRW_crm;
	global $stateIDArray;
	$i=0;
	$pid = $data[$i++];
	$first_name = $data[$i++];
	$last_name = $data[$i++];
	$birthdate = $data[$i++];
	$phone = $data[$i++];
	$email = $data[$i++];
	$primary_address_street = $data[$i++];
	$primary_address_city = $data[$i++];
	$primary_address_state = $data[$i++];
	$primary_address_postalcode = $data[$i++];
	$description = $data[$i++];
	$gender = $data[$i++];
	$ethnicity = $data[$i++];
	$income = $data[$i++];
	$contact_method = $data[$i++];
	$ownbiz = $data[$i++];
	$FICOscore = $data[$i++];
	$HealthInsurance = $data[$i++];
	$LifeInsurance = $data[$i++];
	$DentalInsurance = $data[$i++];
	$VisionInsurance = $data[$i++];
	$SupplementalInsurance = $data[$i++];
	$AutoInsurance = $data[$i++];
	$HomeOwnersRentersInsurance = $data[$i++];
	$f401k = $data[$i++];
	$OtherInvestments = $data[$i++];
	$CheckingSavingsAccount = $data[$i++];
	$CreditCard = $data[$i++];
	$Mortgage = $data[$i++];
	$LoanEducational = $data[$i++];
	$WirelessCellPhone = $data[$i++];
	$HomePhone = $data[$i++];
	$InternetAccess = $data[$i++];
	$TVProvider = $data[$i++];
	$HealthInsurance_p = $data[$i++];
	$LifeInsurance_p = $data[$i++];
	$DentalInsurance_p = $data[$i++];
	$VisionInsurance_p = $data[$i++];
	$SupplementalInsurance_p = $data[$i++];
	$AutoInsurance_p = $data[$i++];
	$HomeOwnersRentersInsurance_p = $data[$i++];
	$f401k_p = $data[$i++];
	$OtherInvestments_p = $data[$i++];
	$CheckingSavingsAccount_p = $data[$i++];
	$CreditCard_p = $data[$i++];
	$Mortgage_p = $data[$i++];
	$LoanEducational_p = $data[$i++];
	$WirelessCellPhone_p = $data[$i++];
	$HomePhone_p = $data[$i++];
	$InternetAccess_p = $data[$i++];
	$TVProvider_p = $data[$i++];
	$HealthInsurance_v = $data[$i++];
	$LifeInsurance_v = $data[$i++];
	$DentalInsurance_v = $data[$i++];
	$VisionInsurance_v = $data[$i++];
	$SupplementalInsurance_v = $data[$i++];
	$OtherInvestments_m = $data[$i++];
	$cscan_contacts_pre_id = $data[$i++];
	$SupplementalInsurance_m = $data[$i++];
	$hearSBKC = (int)$data[$i++];
	$hearSBKCInsert = $data[$i++];
	$familyContactID = $data[$i++];
	$rentorown = $data[$i++];
	$bizname = $data[$i++];
	$id = '';
	if($first_name!='' || $last_name!='' || $email!=''){
		$result_C = $DRW->query("SELECT COUNT(*) FROM contacts WHERE first_name='".$DRW->real_escape_string($first_name)."' AND last_name='".$DRW->real_escape_string($last_name)."' AND email1='".$DRW->real_escape_string($email)."' AND deleted<>1",$DRW_crm);
		$dataC = $DRW->fetch_array($result_C);
		if(empty($dataC[0])){
			$id = create_sugar_id();

			$link = 'http://crm.competiscan.com/index.php?action=DetailView&module=Contacts&record='.$id;
			/*
			(Income) phone_other
			(questions and answers) description

			(Auto Insurance [homeowners/renters] Carrier) assistant
			(Investment Carrier) assistant_phone
			*/
			$assistant = $AutoInsurance_p;
			if($assistant!='' && $HomeOwnersRentersInsurance_p!='') $assistant .= " / ";
			$assistant .= $HomeOwnersRentersInsurance_p;

			$assistant_phone = $f401k_p;
			if($assistant_phone!='' && $OtherInvestments_p!='') $assistant_phone .= " / ";
			$assistant_phone .= $OtherInvestments_p;
			if($assistant_phone!='') $assistant_phone .= " / ";
			$assistant_phone .= $OtherInvestments_m;
			
			if (empty($parents)) {
				$sql = "INSERT INTO cscan_consumer_inc (sugarcrm_id) VALUES ('".$DRW->real_escape_string($id)."')";
				$DRW->query($sql,$DRW_main);
				$parents = $DRW->insert_id($DRW_main);
			}
			
			$department = $parents.'-'.$childrens.'-';
			$ustate = strtoupper($primary_address_state);
			$department .= $stateIDArray[$ustate];
			
			if($ownbiz!=''){
				$description .= "Own business? $ownbiz\n";
			}
			if($FICOscore!=''){
				$description .= "FICO score? $FICOscore\n";
			}
			
			switch ($hearSBKC) {
				case 1:
					$hearSBKC_CRM = "Surfing the Web";
					break;
				case 2:
					$hearSBKC_CRM = "Referral: ".$hearSBKCInsert;
					break;
				case 3:
					$hearSBKC_CRM = "Other: ".$hearSBKCInsert;
					break;
				default:
					$hearSBKC_CRM = "";
			}
			
			$modified_user_id = $assigned_user_id = $created_by = '8e5b0cc5-9f3b-62fd-c451-45d12031fc81';
			
			$insurance_typeA = array();
			if($HealthInsurance) $insurance_typeA[] = 'Health';
			if($DentalInsurance) $insurance_typeA[] = 'Dental';
			if($VisionInsurance) $insurance_typeA[] = 'Vision';
			if($SupplementalInsurance) $insurance_typeA[] = 'Supplemental';
			if($LifeInsurance) $insurance_typeA[] = 'Life';
			if($AutoInsurance) $insurance_typeA[] = 'Auto';
			//$insurance11low = strtolower($insurance11);
			//if($insurance10=='Yes' && strpos($insurance11low,'disability')!==false) $insurance_typeA[] = 'Disability';
			//if($insurance10=='Yes' && strpos($insurance11low,'long')!==false) $insurance_typeA[] = 'Long Term Card';
			$insurance_type = implode('^,^',$insurance_typeA);

			$insurance_carrierA= array();
			if($HealthInsurance_p!='') $insurance_carrierA[] = "$HealthInsurance_p - health - $HealthInsurance_v";
			if($LifeInsurance_p!='') $insurance_carrierA[] = "$LifeInsurance_p - life - $LifeInsurance_v";
			if($DentalInsurance_p!='') $insurance_carrierA[] = "$DentalInsurance_p - dental - $DentalInsurance_v";
			if($VisionInsurance_p!='') $insurance_carrierA[] = "$VisionInsurance_p - vision - $VisionInsurance_v";
			if($SupplementalInsurance_p!='' || $SupplementalInsurance_m!=''){
				$tmp = "$SupplementalInsurance_p  - supplemental - $SupplementalInsurance_v";
				if($SupplementalInsurance_m!='') $tmp .= " [$SupplementalInsurance_m]";
				$insurance_carrierA[] = $tmp;
			}
			$insurance_carrier = implode("\n",$insurance_carrierA);

			if($FICOscore=='Yes'){
				$fico_allow_c = '1';
			}
			else {
				$fico_allow_c = '0';
			}
			
			if ($ownbiz == 'Yes') {
				$ownbiz = '1';
			} else {
				$ownbiz = '0';
			}
			
			if ($rentorown == 'Own') {
				$rentorown = '1';
			} else if ($rentorown == 'Rent') {
				$rentorown = '2';
			}
			
			$query = "INSERT INTO `contacts` (id,first_name,last_name,email1,primary_address_street,primary_address_city,primary_address_state,
				primary_address_postalcode,phone_work,birthdate,phone_other,description,assistant,assistant_phone,date_entered,date_modified,
				modified_user_id,assigned_user_id,created_by,department, reports_to_id) VALUES ('".$DRW->real_escape_string($id)."','".$DRW->real_escape_string($first_name)."',
				'".$DRW->real_escape_string($last_name)."','".$DRW->real_escape_string($email)."','".$DRW->real_escape_string($primary_address_street)."',
				'".$DRW->real_escape_string($primary_address_city)."','".$DRW->real_escape_string($primary_address_state)."','".$DRW->real_escape_string($primary_address_postalcode)."',
				'".$DRW->real_escape_string($phone)."','".$DRW->real_escape_string($birthdate)."','".$DRW->real_escape_string($income)."',
				'".$DRW->real_escape_string($description)."','".$DRW->real_escape_string($assistant)."','".$DRW->real_escape_string($assistant_phone)."',
				NOW(),NOW(),'".$DRW->real_escape_string($modified_user_id)."','".$DRW->real_escape_string($assigned_user_id)."',
				'".$DRW->real_escape_string($created_by)."','$department', '".$reports_to_id."')";
			$result = $DRW->query($query,$DRW_crm);
			$query = "INSERT INTO `contacts_cstm` (id_c,contact_gender_c,ethnicity_r_c,insurance_type_c,insurance_carrier_c,contact_type_m_c,sent_date_c,fico_allow_c, hear_c, homeownership_c, ownbiz_c, ownbiz_name_c) VALUES ('".$DRW->real_escape_string($id)."','".$DRW->real_escape_string($gender)."','".$DRW->real_escape_string($ethnicity)."','".$DRW->real_escape_string($insurance_type)."','".$DRW->real_escape_string($insurance_carrier)."','cons_panelist','0000-00-00',$fico_allow_c, '".$DRW->real_escape_string($hearSBKC_CRM)."', '".$rentorown."', '".$ownbiz."', '".$DRW->real_escape_string($bizname)."')";
			$result = $DRW->query($query,$DRW_crm);
			$sugar_id_e = create_sugar_id();
			$query = "insert into email_addresses (id,email_address,email_address_caps,date_created,date_modified) values ('".$sugar_id_e."','".$DRW->real_escape_string($email)."','".$DRW->real_escape_string(strtoupper($email))."',NOW(),NOW())";
			$result = $DRW->query($query,$DRW_crm);
			$query = "insert into email_addr_bean_rel (id,email_address_id,bean_id,bean_module,primary_address,date_created,date_modified) values ('".create_sugar_id()."','".$sugar_id_e."','".$id."','Contacts',1,NOW(),NOW())";
			$result = $DRW->query($query,$DRW_crm);
		}
		$query = "UPDATE cscan_contacts_pre SET imported_to_sugar=1 WHERE id=$cscan_contacts_pre_id";
		$result = $DRW->query($query,$DRW_main);
	}
	return array($parents,$id);
}
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
?>