<?php
ini_set("memory_limit","-1");
set_time_limit(0);
ini_set('mysql.connect_timeout', 5000);
ini_set('default_socket_timeout', 5000);

require_once("../includes/dbcon.php");
$DRW->databaseReadWrite_die = 1;
require_once '../includes/functions.php';
require_once __DIR__ . '/../includes/Document.php';

/* For update incomeID and ppageID in panelists_product table */
/*
    $sql = "select id,productID,panelist_id from cscan_product_detail_duplicate WHERE upd_status=0";
    $rs = $DRW->query($sql,$DRW_read);
    $productID='';
    $panelist_id='';
    $pincomeID='';
    $ppageID ='';
    $i=1;
    while($row = $DRW->fetch_row($rs)){
        $id=$row[0];
        $productID=$row[1];
        $panelist_id=$row[2]; 
        
        $defs = "SELECT DATEDIFF(CURDATE(),birthdate) as agedays,income,incomeID FROM cscan_panelists WHERE panelist_id=" . (float) $panelist_id;
        $resultD = $DRW->query($defs, $DRW_read);
        $dataD = $DRW->fetch_row($resultD);
        $ppage = floor($dataD[0] / 365);
        $income=$dataD[1];
        $pincomeID = (int) $dataD[2];
        $ageObj = new \HS\Age($DRW);
        $ageObj->setAge($ppage);
        $ppageID = $ageObj->getGroupsAsCommaDelimitedString($ppage);
        
        $sqlU = "UPDATE cscan_panelists_product set pincomeID='".$pincomeID."',ppageID=$ppageID where productID='".$productID."' AND panelist_id='".$panelist_id."'";
       
        $DRW->query($sqlU, $DRW_main);
        
        $sqlU_dup = "UPDATE cscan_product_detail_duplicate set upd_status='1' where id='".$id."'";
        $DRW->query($sqlU_dup, $DRW_main);
        
        
        $productID='';
        $panelist_id='';
        $pincomeID='';
        $ppageID ='';
        $i++;
        
    }
    echo 'completed: '.$i;
*/

/* END For update incomeID and ppageID in panelists_product table */
    
 /* For update incomeID and ppageID in product_detail table */

    $sql = "select id,productID,panelist_id from cscan_product_detail_duplicate WHERE upd_status=1";
    $rs = $DRW->query($sql,$DRW_read);
    $productID='';
    $panelist_id='';
    $pincomeID='';
    $ppageID ='';
    $i=1;
    $incomearr=array();
    $ppagearr=array();
    while($row = $DRW->fetch_row($rs)){
        $id=$row[0];
        $productID=$row[1];
        $panelist_id=$row[2]; 
        
        $defs = "SELECT pincomeID,ppageID FROM cscan_panelists_product WHERE productID=" . (float) $productID;
        $resultD = $DRW->query($defs, $DRW_read);
        $incomearr=array();
        $ppagearr=array();
        $income_save='';
        $ppage_save='';
        while($dataD = $DRW->fetch_row($resultD)){
            if(!empty($dataD[0]) AND $dataD[0]!=0 AND $dataD[0]!=''){
                $incomearr[]=$dataD[0];
            }
            if(!empty($dataD[1]) AND $dataD[1]!=0 AND $dataD[1]!=''){
                $ppagearr[]=$dataD[1]; 
            }
        }
        if(!empty($incomearr)){
            $income_arr=array_unique($incomearr);
            $income_save=implode(',',$income_arr);
        }
        if(!empty($ppagearr)){
            $ppage_arr=array_unique($ppagearr);
            $ppage_save=implode(',',$ppage_arr);
        }
        if($income_save!='' || $ppage_save!=''){
            
          $sqlU_dup = "UPDATE cscan_product_detail set age='".$ppage_save."',incomeID='".$income_save."' where productID='".$productID."'";
            
          $DRW->query($sqlU_dup, $DRW_main);
            
            $sqlU_dup = "UPDATE cscan_product_detail_duplicate set upd_status='2' where id='".$id."'";
            $DRW->query($sqlU_dup, $DRW_main);
            $i++;
        }       
        
        
        
        
    }
    echo 'completed: '.$i;


/* END For update incomeID and ppageID in product_detail table */   



?>
