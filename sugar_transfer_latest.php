#!/usr/bin/php
<?php 
require_once("includes/ehLog_set.php");
$ehL->start(__FILE__);

require_once('includes/dbcon.php');
$DRW->databaseReadWrite_die = 1;
require_once('includes/functions.php');

$contactTypeArray = array('prod_panelist'=>'1','cons_panelist'=>'2');
$stateArray = array();
$stateNameArray = array();
$q = "SELECT stateID,stateCode,stateName FROM cscan_state WHERE stateID<>99";
$rs = $DRW->query($q,$DRW_read);
while($data2 = $DRW->fetch_row($rs)){
	$stateArray[$data2[1]] = $data2[0];
	$stateNameArray[$data2[0]] = $data2[2];
}
$incomeArray = array();
$q = "SELECT incomeID,incomeName FROM cscan_incometype";
$rs = $DRW->query($q,$DRW_read);
while($data2 = $DRW->fetch_row($rs)){
	$incomeArray[$data2[1]] = $data2[0];
}

$q = "SELECT SQL_NO_CACHE id,first_name,last_name,phone_work,ethnicity_r_c,contact_type_m_c,insurance_type_c,insurance_carrier_c,phone_mobile,contact_gender_c,phone_other,department,email1,birthdate,email2,assistant,assistant_phone,points_c,envelope_sent_c,primary_address_street,primary_address_city,primary_address_state,primary_address_postalcode,primary_address_country,alt_address_street,alt_address_city,alt_address_state,alt_address_postalcode,alt_address_country,description,company_c,prominent_carriers_c,state_licenses_c,health_type_c,member_signup_c,life_c,sent_date_c,annuities_c,recruitment_c,series_7_c,homeownership_c,more_email_c,deleted,ownbiz_c,fico_score_c,fico_allow_c,ownbiz_name_c,reports_to_id,dma_code_c
	FROM contacts LEFT JOIN contacts_cstm ON (id=id_c)";
$result = $DRW->query($q,$DRW_crm);
while($data = $DRW->fetch_row($result)){
	$i=0;
	$id = trim($data[$i++]);
        $id = 'b1f94db2-4642-8b55-3377-4f6ca3a3c75a';
	$first_name = trim($data[$i++]);
	$last_name = trim($data[$i++]);
	$phone_work = trim($data[$i++]);
	$ethnicity_r_c = trim($data[$i++]);
	$contact_type_m_c = trim($data[$i++]);
	$insurance_type_c = trim($data[$i++]);
	$insurance_carrier_c = trim($data[$i++]);
	$phone_mobile = trim($data[$i++]);
	$contact_gender_c = trim($data[$i++]);
	$phone_other = trim($data[$i++]);
	$department = trim($data[$i++]);
	$email1 = trim($data[$i++]);
	$birthdate = trim($data[$i++]);
	$email2 = trim($data[$i++]);
	$assistant = trim($data[$i++]);
	$assistant_phone = trim($data[$i++]);
	$points_c = trim($data[$i++]);
	$envelope_sent_c = trim($data[$i++]);
	$primary_address_street = trim($data[$i++]);
	$primary_address_city = trim($data[$i++]);
	$primary_address_state = strtoupper(trim($data[$i++]));
	$primary_address_postalcode = trim($data[$i++]);
	$primary_address_country = trim($data[$i++]);
	$alt_address_street = trim($data[$i++]);
	$alt_address_city = trim($data[$i++]);
	$alt_address_state = strtoupper(trim($data[$i++]));
	$alt_address_postalcode = trim($data[$i++]);
	$alt_address_country = trim($data[$i++]);
	$description = trim($data[$i++]);
	$company_c = trim($data[$i++]);
	$prominent_carriers_c = trim($data[$i++]);
	$state_licenses_c = trim($data[$i++]);
	$health_type_c = trim($data[$i++]);
	$member_signup_c = trim($data[$i++]);
	$life_c = trim($data[$i++]);
	$sent_date_c = trim($data[$i++]);
	$annuities_c = trim($data[$i++]);
	$recruitment_c = trim($data[$i++]);
	$series_7_c = trim($data[$i++]);
	$homeownership_c = trim($data[$i++]);
	$more_email_c = trim($data[$i++]);
	$deleted = trim($data[$i++]);
	$ownbiz_c = trim($data[$i++]);
	$fico_score_c = trim($data[$i++]);
	$fico_allow_c = trim($data[$i++]);
	$ownbiz_name_c = trim($data[$i++]);
	$reports_to_id = trim($data[$i++]);
	$dma_code_c = trim($data[$i++]);
	
	$split = explode('-',$primary_address_postalcode);
	if(count($split)>=2){
		$primary_address_postalcode = trim($split[0]);
	}
	
	$email1 = '';
	$email2 = '';
	$more_email_c = '';
	echo $q2 = "SELECT SQL_NO_CACHE email_address,primary_address FROM email_addresses ea, email_addr_bean_rel eb WHERE bean_id='".$DRW->real_escape_string($id)."' AND ea.id=eb.email_address_id AND bean_module='Contacts' order by primary_address desc,eb.date_created asc";
	$result2 = $DRW->query($q2,$DRW_crm);
	while($data2 = $DRW->fetch_row($result2)){
		$email_address = trim($data2[0]);
		$primary_address = $data2[1];
		if($email_address!=''){
			if($primary_address==1 || $email1==''){
				$email1 = $email_address;
			}
			else{
				if($more_email_c!=''){
					$more_email_c .= '; ';
				}
				$more_email_c .= $email_address;
			}
		}
	}
	echo '</br>'.$email1.'</br>';
	if($deleted==1){
		$query = "UPDATE cscan_panelists SET active=0 WHERE sugar_id='".$DRW->real_escape_string($id)."'";
		//$DRW->query($query,$DRW_main);
		continue;
	}
	
	if(isset($contactTypeArray[$contact_type_m_c])) $contactTypeID = $contactTypeArray[$contact_type_m_c];
	else $contactTypeID = 0;
	
	if(isset($stateArray[$primary_address_state])) $stateID = $stateArray[$primary_address_state];
	else {
		$stateID = 0;
		foreach($stateNameArray as $c=>$s){
			if(strtoupper($s)==$primary_address_state){
				$stateID = $c;
				break;	
			}
		}
	}
	
	if(isset($stateArray[$alt_address_state])) $stateID2 = $stateArray[$alt_address_state];
	else {
		$stateID2 = 0;
		foreach($stateNameArray as $c=>$s){
			if(strtoupper($s)==$primary_address_state){
				$stateID = $c;
				break;	
			}
		}
	}
	
	if(isset($incomeArray[$phone_other])) $incomeID = $incomeArray[$phone_other];
	else $incomeID = 0;
	
	$parent_panelist_id = 0;
	if(!empty($reports_to_id)){
		$check = "SELECT panelist_id FROM cscan_panelists WHERE sugar_id='".$DRW->real_escape_string($reports_to_id)."'";
		echo '</br>'.$check.'</br>';
                $check = $DRW->query($check,$DRW_read);
		$checkCount = $DRW->fetch_row($check);
		if(!empty($checkCount[0])){	
			$parent_panelist_id = $checkCount[0];
		}
	}
	
	$check = "SELECT SQL_NO_CACHE COUNT(*) FROM cscan_panelists WHERE sugar_id='".$DRW->real_escape_string($id)."'";
	$check = $DRW->query($check,$DRW_read);
	$checkCount = $DRW->fetch_row($check);	
        echo '</br>'.$check.'</br>';
	if($checkCount[0]==0){
		$query = "INSERT INTO cscan_panelists (sugar_id,first_name,last_name,phone,ethnicity,contact_type,insurance_type,insurance_carrier,age,gender,income,competi_id,email,birthdate,alt_email,auto_insurance,investment_carrier,address,city,state,postalcode,country,address2,city2,state2,postalcode2,country2,description,company,prominent_carriers,state_licenses,health_type,membership_signup,life,sent_date,annuities,recruitment,series_seven,homeownershipID,contactTypeID,stateID,incomeID,stateID2,more_email,ownbiz,fico_score,fico_allow,ownbiz_name,parent_panelist_id,active) 
			VALUES ('".$DRW->real_escape_string($id)."','".$DRW->real_escape_string($first_name)."','".$DRW->real_escape_string($last_name)."','".$DRW->real_escape_string($phone_work)."','".$DRW->real_escape_string($ethnicity_r_c)."','".$DRW->real_escape_string($contact_type_m_c)."','".$DRW->real_escape_string($insurance_type_c)."','".$DRW->real_escape_string($insurance_carrier_c)."','".$DRW->real_escape_string($phone_mobile)."','".$DRW->real_escape_string($contact_gender_c)."','".$DRW->real_escape_string($phone_other)."','".$DRW->real_escape_string($department)."','".$DRW->real_escape_string($email1)."','".$DRW->real_escape_string($birthdate)."','".$DRW->real_escape_string($email2)."','".$DRW->real_escape_string($assistant)."','".$DRW->real_escape_string($assistant_phone)."','".$DRW->real_escape_string($primary_address_street)."','".$DRW->real_escape_string($primary_address_city)."','".$DRW->real_escape_string($primary_address_state)."','".$DRW->real_escape_string($primary_address_postalcode)."','".$DRW->real_escape_string($primary_address_country)."','".$DRW->real_escape_string($alt_address_street)."','".$DRW->real_escape_string($alt_address_city)."','".$DRW->real_escape_string($alt_address_state)."','".$DRW->real_escape_string($alt_address_postalcode)."','".$DRW->real_escape_string($alt_address_country)."','".$DRW->real_escape_string($description)."','".$DRW->real_escape_string($company_c)."','".$DRW->real_escape_string($prominent_carriers_c)."','".$DRW->real_escape_string($state_licenses_c)."','".$DRW->real_escape_string($health_type_c)."','".$DRW->real_escape_string($member_signup_c)."','".$DRW->real_escape_string($life_c)."','".$DRW->real_escape_string($sent_date_c)."','".$DRW->real_escape_string($annuities_c)."','".$DRW->real_escape_string($recruitment_c)."','".$DRW->real_escape_string($series_7_c)."','".$DRW->real_escape_string($homeownership_c)."','".$DRW->real_escape_string($contactTypeID)."','".$DRW->real_escape_string($stateID)."','".$DRW->real_escape_string($incomeID)."','".$DRW->real_escape_string($stateID2)."','".$DRW->real_escape_string($more_email_c)."','".$DRW->real_escape_string($ownbiz_c)."','".$DRW->real_escape_string($fico_score_c)."','".$DRW->real_escape_string($fico_allow_c)."','".$DRW->real_escape_string($ownbiz_name_c)."','".$DRW->real_escape_string($parent_panelist_id)."',1)";
		//$DRW->query($query,$DRW_main);
                echo '</br>'.$query.'</br>';
	}
	else{
		$query = "UPDATE cscan_panelists SET 
			first_name='".$DRW->real_escape_string($first_name)."',
			last_name='".$DRW->real_escape_string($last_name)."',
			phone='".$DRW->real_escape_string($phone_work)."',
			ethnicity='".$DRW->real_escape_string($ethnicity_r_c)."',
			contact_type='".$DRW->real_escape_string($contact_type_m_c)."',
			insurance_type='".$DRW->real_escape_string($insurance_type_c)."',
			insurance_carrier='".$DRW->real_escape_string($insurance_carrier_c)."',
			age='".$DRW->real_escape_string($phone_mobile)."',
			gender='".$DRW->real_escape_string($contact_gender_c)."',
			income='".$DRW->real_escape_string($phone_other)."',
			competi_id='".$DRW->real_escape_string($department)."',
			email='".$DRW->real_escape_string($email1)."',
			birthdate='".$DRW->real_escape_string($birthdate)."',
			alt_email='".$DRW->real_escape_string($email2)."',
			auto_insurance='".$DRW->real_escape_string($assistant)."',
			investment_carrier='".$DRW->real_escape_string($assistant_phone)."',
			address='".$DRW->real_escape_string($primary_address_street)."',
			city='".$DRW->real_escape_string($primary_address_city)."',
			state='".$DRW->real_escape_string($primary_address_state)."',
			postalcode='".$DRW->real_escape_string($primary_address_postalcode)."',
			country='".$DRW->real_escape_string($primary_address_country)."',
			address2='".$DRW->real_escape_string($alt_address_street)."',
			city2='".$DRW->real_escape_string($alt_address_city)."',
			state2='".$DRW->real_escape_string($alt_address_state)."',
			postalcode2='".$DRW->real_escape_string($alt_address_postalcode)."',
			country2='".$DRW->real_escape_string($alt_address_country)."',
			description='".$DRW->real_escape_string($description)."',
			company='".$DRW->real_escape_string($company_c)."',
			prominent_carriers='".$DRW->real_escape_string($prominent_carriers_c)."',
			state_licenses='".$DRW->real_escape_string($state_licenses_c)."',
			health_type='".$DRW->real_escape_string($health_type_c)."',
			membership_signup='".$DRW->real_escape_string($member_signup_c)."',
			life='".$DRW->real_escape_string($life_c)."',
			sent_date='".$DRW->real_escape_string($sent_date_c)."',
			annuities='".$DRW->real_escape_string($annuities_c)."',
			recruitment='".$DRW->real_escape_string($recruitment_c)."',
			series_seven='".$DRW->real_escape_string($series_7_c)."',
			homeownershipID='".$DRW->real_escape_string($homeownership_c)."',
			contactTypeID='".$DRW->real_escape_string($contactTypeID)."',
			stateID='".$DRW->real_escape_string($stateID)."',
			incomeID='".$DRW->real_escape_string($incomeID)."',
			stateID2='".$DRW->real_escape_string($stateID2)."',
			more_email='".$DRW->real_escape_string($more_email_c)."',
			ownbiz='".$DRW->real_escape_string($ownbiz_c)."',
			fico_score='".$DRW->real_escape_string($fico_score_c)."',
			fico_allow='".$DRW->real_escape_string($fico_allow_c)."',
			ownbiz_name='".$DRW->real_escape_string($ownbiz_name_c)."',
			parent_panelist_id='".$DRW->real_escape_string($parent_panelist_id)."',
			active=1
			WHERE sugar_id='".$DRW->real_escape_string($id)."'";
                echo '</br>'.$query.'</br>';
		//$DRW->query($query,$DRW_main);
	}
	
	$qp = "SELECT SQL_NO_CACHE code FROM cscan_dma_code_postalcode where pppostalcode='".$primary_address_postalcode."'";
	$resultp = $DRW->query($qp,$DRW_read);
	$datap = $DRW->fetch_row($resultp);
	if(!empty($datap[0]) && $datap[0]!=$dma_code_c){
		$query = "UPDATE contacts_cstm SET dma_code_c='".$DRW->real_escape_string($datap[0])."' WHERE id_c='".$DRW->real_escape_string($id)."'";
		//$DRW->query($query,$DRW_crm);
	}die;
}
die;
$q = "SELECT SQL_NO_CACHE DISTINCT email_from_one FROM cscan_email WHERE panelist_id=0";
$result = $DRW->query($q,$DRW_read);
while($data = $DRW->fetch_row($result)){
	
	$result_c_p = $DRW->query("SELECT SQL_NO_CACHE panelist_id,stateID FROM cscan_panelists WHERE active=1 AND (email='".$DRW->real_escape_string($data[0])."' OR alt_email='".$DRW->real_escape_string($data[0])."' OR more_email LIKE '%".mysqlLike($data[0])."%') LIMIT 1",$DRW_read);
	$data_c_p = $DRW->fetch_row($result_c_p);
	$panelist_id = (int) $data_c_p[0];
	$email_stateID = (int) $data_c_p[1];
       
	if($panelist_id!=0){
		$query = "UPDATE cscan_email SET panelist_id=$panelist_id,email_stateID=$email_stateID WHERE email_from_one='".$DRW->real_escape_string($data[0])."' AND panelist_id=0";
		$DRW->query($query,$DRW_main);
	}
}

$parr = array();
$sql = "SELECT age_pID,age_pmin FROM cscan_age_product ORDER BY age_psort";
$result = $DRW->query( $sql,$DRW_read );
while( $row = $DRW->fetch_row( $result ) ){
	$parr[$row[0]] = $row[1];
}
// OR ppfico_score=0 OR isBiz=0
$q = "SELECT SQL_NO_CACHE DISTINCT panelist_id FROM cscan_panelists_product WHERE ppage=0 OR ppstateID=0 OR pgender='' OR homeownershipID=0 OR pincomeID=0 OR ppageID=0 OR pppostalcode=''";
$result = $DRW->query($q,$DRW_read);
while($data = $DRW->fetch_row($result)){
	$panelist_id = $data[0];
	
	$defs = "SELECT SQL_NO_CACHE DATEDIFF(CURDATE(),birthdate) as agedays,stateID,gender,homeownershipID,incomeID,fico_score,ownbiz,postalcode
		FROM cscan_panelists WHERE panelist_id=".$panelist_id;
	$resultD = $DRW->query($defs,$DRW_read);
	$dataD = $DRW->fetch_row($resultD);
	$ppage = floor($dataD[0]/365);
	$ppstateID = (int)$dataD[1];
	$pgender = strtoupper(substr(trim($dataD[2]),0,1));
	$homeownershipID = (int)$dataD[3];
	$pincomeID = (int)$dataD[4];
	$ppfico_score = (int)$dataD[5];
	$ownbiz = (int)$dataD[6];
	$pppostalcode = trim($dataD[7]);
	$ppageID = 0;
	foreach($parr as $pid=>$min){
		if($ppage>=$min){
			$ppageID = $pid;
		}
		else{
			break;
		}
	}
	
	if($ppage!=0){
		$sqlU = "UPDATE cscan_panelists_product SET ppage=$ppage WHERE panelist_id=$panelist_id AND ppage=0";
		$DRW->query($sqlU,$DRW_main);
	}
	if($ppstateID!=0){
		$sqlps = "SELECT DISTINCT productID FROM cscan_panelists_product WHERE panelist_id=$panelist_id AND ppstateID=0";
		$resultps = $DRW->query( $sqlps,$DRW_read );
		while( $rowps = $DRW->fetch_row( $resultps ) ){
			updateStateLookup($rowps[0]);
		}
		$sqlU = "UPDATE cscan_panelists_product SET ppstateID=$ppstateID WHERE panelist_id=$panelist_id AND ppstateID=0";
		$DRW->query($sqlU,$DRW_main);
	}
	if($pgender!=''){
		$sqlU = "UPDATE cscan_panelists_product SET pgender='$pgender' WHERE panelist_id=$panelist_id AND pgender=''";
		$DRW->query($sqlU,$DRW_main);
	}
	if($homeownershipID!=0){
		$sqlU = "UPDATE cscan_panelists_product SET homeownershipID=$homeownershipID WHERE panelist_id=$panelist_id AND homeownershipID=0";
		$DRW->query($sqlU,$DRW_main);
	}
	if($pincomeID!=0){
		$sqlU = "UPDATE cscan_panelists_product SET pincomeID=$pincomeID WHERE panelist_id=$panelist_id AND pincomeID=0";
		$DRW->query($sqlU,$DRW_main);
	}
	if($ppageID!=0){
		$sqlU = "UPDATE cscan_panelists_product SET ppageID=$ppageID WHERE panelist_id=$panelist_id AND ppageID=0";
		$DRW->query($sqlU,$DRW_main);
	}
	if($ppfico_score!=0){
		$sqlU = "UPDATE cscan_panelists_product SET ppfico_score=$ppfico_score WHERE panelist_id=$panelist_id AND ppfico_score=0";
		$DRW->query($sqlU,$DRW_main);
	}
	/*if($isBiz!=0){
		$sqlU = "UPDATE cscan_panelists_product SET isBiz=$isBiz WHERE panelist_id=$panelist_id AND isBiz=0";
		$DRW->query($sqlU,$DRW_main);
	}*/
	if($pppostalcode!=''){
		$sqlU = "UPDATE cscan_panelists_product SET pppostalcode='".$DRW->real_escape_string($pppostalcode)."' WHERE panelist_id=$panelist_id AND pppostalcode=''";
		$DRW->query($sqlU,$DRW_main);
	}
}

$sql = "select panelist_id from cscan_panelists where active=1";
$result = $DRW->query($sql,$DRW_read);
while($row = $DRW->fetch_row($result)) {
	$ppstateID = 0;
	$pppostalcode = '';
	$last_move = '';
	$defs = "select ppstateID,pppostalcode,ppaddeddate from cscan_panelists_product where panelist_id=$row[0] order by ppaddeddate asc";
	$resultD = $DRW->query($defs,$DRW_read);
	while($dataD = $DRW->fetch_row($resultD)){
		if((!empty($ppstateID) && $dataD[0]!=$ppstateID) || (!empty($pppostalcode) && $dataD[1]!=$pppostalcode)){
			$insert2 = '';
			if(!empty($ppstateID) && $dataD[0]!=$ppstateID){
				$insert2 .= ','.$ppstateID.','.$dataD[0];
			}
			else{
				$insert2 .= ','.$dataD[0].','.$dataD[0];
			}
			if(!empty($pppostalcode) && $dataD[1]!=$pppostalcode){
				$insert2 .= ",'".$DRW->real_escape_string($pppostalcode)."','".$DRW->real_escape_string($dataD[1])."'";
			}
			else{
				$insert2 .= ",'".$DRW->real_escape_string($dataD[1])."','".$DRW->real_escape_string($dataD[1])."'";
			}
			$last_move = $dataD[2];
			$sqlU = "REPLACE INTO cscan_panelists_mover (panelist_id,pm_date,stateID1,stateID2,postalcode1,postalcode2) VALUES ($row[0],'$dataD[2]'$insert2)";
			$DRW->query($sqlU,$DRW_main);
		}
		$ppstateID = (int)$dataD[0];
		$pppostalcode = $dataD[1];
	}
	if($last_move!=''){
		$sqlU = "UPDATE cscan_panelists SET last_move='".$DRW->real_escape_string($last_move)."' WHERE panelist_id=$row[0]";
		$DRW->query($sqlU,$DRW_read);
	}
}

$ehL->stop();
?>