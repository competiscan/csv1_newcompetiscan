<?php $ALLOW_GROUPS = array(41);
require_once("../auth_auth.php");
//include 'top.php';
if (isset($_REQUEST['hid']) && $_REQUEST['hid']!=''){
    $hid=$_REQUEST['hid'];
}
if (isset($_REQUEST['tbl']) && $_REQUEST['tbl']!=''){
    $tbl=$_REQUEST['tbl'];
}
   if($hid!='' and $tbl!=''){       
       $sql = "SELECT creative_id,creative_path FROM ".$tbl." WHERE creative_id='".$hid."'";
       $rs = $DRW->query($sql,$DRW_digital);
       $nrow = $DRW->fetch_row($rs);
       $creative_path = $nrow[1];
       $htmlimg = file_get_contents($creative_path);
      echo  $htmlimg;       
   }
    
    