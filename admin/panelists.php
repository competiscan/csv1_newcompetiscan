<?php 
require_once("../auth_auth.php");
require_once '../includes/functions.php';

require_once('../vendor/autoload.php');

@ob_clean();

$findval = isset($_REQUEST['findval']) ? trim(filter_var($_REQUEST['findval'],FILTER_SANITIZE_STRING)) : '';
if($findval == ''){
    exit;
}
$competi_ids_only = array();
if(isset($_REQUEST['competi_ids'])){
    $competi_ids = explode(',',$_REQUEST['competi_ids']);
    foreach($competi_ids as $cid_date){
        if($cid_date!=''){
            list($cid) = explode('|',$cid_date);
            if(!in_array($cid,$competi_ids_only)) {
                $competi_ids_only[] = $cid;
            }
        }
    }
}
else{
    $competi_ids = array();
}
$i = 0;
$mysqlFindVal = mysqlLike($findval);
$filterWhere = "(last_name LIKE '".$mysqlFindVal."%' OR first_name LIKE '".$mysqlFindVal."%')";
$orderByField = 'last_name, first_name';
if(preg_match('/([0-9]+)/',$findval)) {
    //findval contains at least one number, so search by id rather than name.
    $filterWhere = "(competi_id LIKE '".$mysqlFindVal."%' OR competi_id LIKE '0".$mysqlFindVal."%')";
    $orderByField = 'competi_id';
}
$resultC = $DRW->query("SELECT competi_id,panelist_id,first_name,last_name,gender,DATEDIFF(CURDATE(),birthdate) as age,incomeID,stateID,contactTypeID,birthdate,ownbiz FROM cscan_panelists 
    WHERE active=1 and $filterWhere ORDER BY $orderByField",$DRW_read);
while($dataC = $DRW->fetch_row($resultC)){
    $competi_id = $dataC[0];
    $panelist_id = $dataC[1];
    $first_name = $dataC[2];
    $last_name = $dataC[3];
    $gender = strtoupper(substr($dataC[4],0,1)); // radio M, F
    $age = floor($dataC[5]/365);
    $incomeID = $dataC[6];
    $stateID = $dataC[7];
    $contactTypeID = $dataC[8];
    $birthdate = $dataC[9];
    $ownbiz = $dataC[10];
    
    $mChannelID = 1;
    if($contactTypeID==1){
        $mPanelID = 4;
    }
    elseif($contactTypeID==2){
        $mPanelID = 1;
    }
    if($incomeID==0) {
        $incomeID = -1;
    }
    $ageID = -1;
    if($birthdate!='0000-00-00'){
        $ageObj = new HS\Age($DRW);
        $ageObj->setAge($age);
        $ageID = $ageObj->getGroupsAsCommaDelimitedString();
    }
    if($gender!='M' && $gender!='F'){
        $gender = 'N';
    }
    else{
        if(!in_array($panelist_id.'|0000-00-00 00:00:00',$competi_ids) && !in_array($panelist_id,$competi_ids)){
            echo "<a href=\"#\" style=\"color: #000000;\" onclick=\"addPan($panelist_id,'$competi_id',$mChannelID,$mPanelID,'$gender',$ageID,$incomeID,$stateID,$ownbiz); return false;\">";
            if(in_array($panelist_id,$competi_ids_only)) {
                echo '* ';
            }
            echo "$competi_id (".htmlspecialchars("$first_name $last_name").")</a><br />";
        }
        $i++;
    }
}
?>
