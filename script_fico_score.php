<?php
require_once('includes/globalSession.php');

$sql= "SELECT userID,emailAddress,companyName,active FROM cscan_users";
$query = $DRW->query($sql,$DRW_read);
    while($rowData = $DRW->fetch_assoc($query)) { 
        $userID=$rowData['userID'];
        $emailAddress=$rowData['emailAddress'];
        $companyName=$rowData['companyName'];
        $active=$rowData['active'];
        $sql_check= "SELECT field_name FROM cscan_users_additional_fields_allow where userID='".$userID."' And (field_name='fico' OR field_name='vantage_score' OR field_name='credit_vision')";
        $query_check = $DRW->query($sql_check,$DRW_read);
        $resultCount = $DRW->num_rows($query_check);
        $fico="No";
        $vantage_score="No";
        $credit_vision="No";
        if($resultCount >0){
            while($rowDataChek = $DRW->fetch_assoc($query_check)) {
               $field_name=$rowDataChek['field_name'];
               
               if($field_name=='fico'){
                   $fico ='Yes';               
                   
               }
                
               if($field_name=='vantage_score'){
                   $vantage_score ='Yes';               
                   
               }
               
               if($field_name=='credit_vision'){
                   $credit_vision ='Yes';               
                   
               }
            }
            $sql_check_user= "SELECT userID FROM cscan_client_profile_temp_report where userID='".$userID."'";
            $query_check_user = $DRW->query($sql_check_user,$DRW_read);
            if($DRW->num_rows($query_check_user) <1) {
                $sql_ins = "INSERT INTO cscan_client_profile_temp_report Set userID='".$userID."',emailAddress='".$DRW->real_escape_string($emailAddress)."', companyName='".$DRW->real_escape_string($companyName)."',fico='".$fico."',vantage_score='".$vantage_score."',credit_vision='".$credit_vision."',active='".$active."'";
                $DRW->query($sql_ins, $DRW_main);
            }
        }
        
       
    }
echo "SUCCESS"; die;
?>