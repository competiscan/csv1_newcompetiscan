<?php 
require_once("../auth_auth.php");
require_once '../includes/functions.php';

@ob_clean();

if(isset($_REQUEST['id']) && trim($_REQUEST['id'])!=''){
    $pid=trim($_REQUEST['id']);
    $q = "SELECT entryID FROM cscan_product_detail WHERE productID='".$pid."'";
        $resultC = $DRW->query($q,$DRW_read);
        $dataC = $DRW->fetch_row($resultC);
        if($dataC[0]){
                $entryID = $dataC[0];
        }else{
            $entryID='EntryID Not exist';
        }
    echo $entryID; die;
    
}

?>