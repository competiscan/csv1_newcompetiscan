<?php 
require_once("includes/ehLog_set.php");
$ehL->start(__FILE__);
ini_set("default_charset", "utf-8");
ini_set("memory_limit", "-1");
set_time_limit(0);

include_once('includes/dbcon.php');
$DRW->databaseReadWrite_die = 1;
require_once('includes/clean.php');
require_once('includes/functions.php');

// This is only for onetime update record
$productID='';
$query="select productID from cscan_product_temp";
$result = $DRW->query($query,$DRW_read);
if($DRW->num_rows($result)>0){
    $row = $DRW->fetch_row($result);
    if(!empty($row)){
        $productID=$row[0];            
    }    
}
if($productID!=''){
    $query2 = "SELECT productID,state from cscan_product_detail where productID>'".$productID."' order by productID ASC limit 10000";
}else{
    $query2 = "SELECT productID,state from cscan_product_detail order by productID ASC limit 10000";
}

$result2 = $DRW->query($query2,$DRW_read);
if($DRW->num_rows($result2)>0){
    while($row2 = $DRW->fetch_row($result2)){
        $productID = $row2[0];
        $state     = $row2[1];        
        $update_state=$state;
        $arr_state=array();
        if(!empty($state)){
            $arr_state= explode(",",$state);           
        }
        
        $query3 = "SELECT panelist_id,ppstateID from cscan_panelists_product where productID='".$productID."'";
        $result3 = $DRW->query($query3,$DRW_read);
        if($DRW->num_rows($result3)>0){
            $num=$DRW->num_rows($result3);
            $updt=false;
            $arr_state2=array();
            while($row3 = $DRW->fetch_row($result3)){
                $panelist_id=$row3[0];
                $ppstateID=$row3[1];
                if($ppstateID>0){
                    if(!in_array($ppstateID,$arr_state)){
                        $arr_state[]=$ppstateID;                       
                        $updt=true;
                    }
                    if(!in_array($ppstateID,$arr_state2)){                        
                        $arr_state2[]=$ppstateID;
                        //$updt=true;
                    }
                    if(count($arr_state)>$num){
                        $arr_state=$arr_state2;
                        $updt=true;
                    }
                    
                }
            }
            if($updt){
                if(count($arr_state)>0){
                    $update_state=implode(',',$arr_state); 
                   echo $sqlP = "Update cscan_product_detail set state='".$update_state."' where productID='".$productID."'";
                    $DRW->query($sqlP, $DRW_main);
                    for($i=0;$i<count($arr_state);$i++){
                        $st_id=$arr_state[0];
                        $query4 = "SELECT productID from cscan_product_detail_state where productID='".$productID."' AND stateID='".$st_id."'";
                        $result4 = $DRW->query($query4,$DRW_read);
                        if($DRW->num_rows($result4)<=0){
                            $query5 = "SELECT countryCode from cscan_state where stateID='".$st_id."'";
                            $result5 = $DRW->query($query5,$DRW_read);
                            $row5 = $DRW->fetch_row($result);
                            $countryCode='';
                            if(!empty($row5)){
                                $countryCode=$row5[0];            
                            }                            
                            $sql6 = "INSERT INTO cscan_product_detail_state SET productID='".$productID."', stateID='".$st_id."', countryCode_copy ='".$countryCode."'";
                            $DRW->query($sql6,$DRW_main);                            
                            
                        }                                                
                    }
                }                
            }
            
            
        }        
        
        $sqlP = "Update cscan_product_temp set productID='".$productID."' where id=1";
        $DRW->query($sqlP, $DRW_main);
    }    
}

?>