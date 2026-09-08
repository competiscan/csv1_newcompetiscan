<?php
require_once("includes/competi_def.php");
require_once('includes/dbcon.php');
$sql_query = "SELECT trend_id,category_id,trend_name,trend_link,trend_date FROM cscan_trend_report where category_id!=0 and audience_id=''";	
$rs_query = $DRW->query($sql_query,$DRW_read);
$sectorNamePrint='';
$sectorName='';
$subsubcatName='';
$catName ='';
$subcatName='';
$subsubcatName='';
while($rowData = $DRW->fetch_array($rs_query)) {
       $trend_id=$rowData['trend_id'];
       $category_id=$rowData['category_id'];
       $trend_name = $DRW->real_escape_string($rowData['trend_name']);
       $trend_link=$rowData['trend_link'];
       $trend_date=$rowData['trend_date'];
       $sql ="SELECT level,c.* FROM ( SELECT @r AS _id, (SELECT @r := parentID FROM cscan_sector WHERE sectorID = _id) AS parent_id, @l := @l + 1 AS level FROM (SELECT @r := '$category_id', @l := 0) vars, cscan_sector m WHERE @r <> 0) d JOIN cscan_sector c ON d._id = c.sectorID  order By level DESC";
       //$sql = "SELECT sectorID,sectorName FROM cscan_sector WHERE parentID='$category_id' ORDER BY sectorName ASC";	
       $rs = $DRW->query($sql,$DRW_read);
       $resultCount = $DRW->num_rows($rs);
        $num=1;
        if($resultCount > 0) {
                while($row = $DRW->fetch_assoc($rs)) {
                    $sectorNamePrint = $row['sectorName'];
                    $level=$row['level'];
			$sectorNamePrint = $sectorNamePrint;
                        if($num==1){
                            $sectorName =$sectorNamePrint;
                            //echo "Sector=====".$sectorNamePrint."<br/>";
                        }elseif ($num==2) {
                          $catName =$sectorNamePrint;
                          //echo "Category=====".$sectorNamePrint."<br/>";
                         }
                         elseif ($num==3) {
                          $subcatName =$sectorNamePrint;
                           //echo "SUBCategory=====".$sectorNamePrint."<br/>";
                         }elseif ($num==4) {
                             $subsubcatName =$sectorNamePrint;
                            //echo "SUBSUBCategory=====".$sectorNamePrint."<br/>";
                            }
                      
                
            $num++;}
                       
        }
       $sqlchk = "SELECT trend_id FROM cscan_temp_category where trend_id='$trend_id'";
        $rs_chk = $DRW->query($sqlchk,$DRW_read);
        $rowCount = $DRW->num_rows($rs_chk);
        if($rowCount==0){
        $sqlInsert="Insert into cscan_temp_category set sector ='$sectorName',category='$catName',subcategory='$subcatName',subtosubcategory='$subsubcatName',trend_id='$trend_id',trend_name='$trend_name',trend_link='$trend_link',trend_date='$trend_date'";
        $resp=$DRW->query($sqlInsert,$DRW_main);
        }
        $sectorName='';
        $catName='';
        $subcatName='';
        $subsubcatName='';
      }
    
?>