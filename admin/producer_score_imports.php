<?php
$ALLOW_GROUPS = array(104);
require_once("../auth_auth.php");
require_once('../includes/ehLog.php');
$action='';
$lineRowArray = array();
$flag='';
if(isset($_POST['upload']) && $_POST['upload']=='Upload' && !empty($_FILES['importfile']['name'])){
    $csvMimes = array('text/x-comma-separated-values', 'text/comma-separated-values', 'application/octet-stream', 'application/vnd.ms-excel', 'application/x-csv', 'text/x-csv', 'text/csv', 'application/csv', 'application/excel', 'application/vnd.msexcel');
    if(!empty($_FILES['importfile']['name']) && in_array($_FILES['importfile']['type'], $csvMimes)){
        $uploaddir = substr(dirname(__FILE__),0,-5);
        $filename = $uploaddir . 'tmp_upload/' . basename($_FILES['importfile']['name']);
        if (move_uploaded_file($_FILES['importfile']['tmp_name'], $filename)) {
            $coltotal = 5;
            $file = fopen($filename,'r');
            if($file){
                    $num=1;
                    while (!feof($file)) {
                        $line = trim(fgets($file, 4096));
                        if($line!=''){
                            $lineArray = array();
                            $lineArray = preg_split('/,(?=(?:[^"]*"[^"]*")*(?![^"]*"))/',$line);
                            $colcount = count($lineArray);
                            array_walk($lineArray, 'trim_value');
                            if($colcount>$coltotal){
                                    $lineArray = array_slice($lineArray, 0, $coltotal);
                            }
                            elseif($colcount<$coltotal){
                                    $lineArray = array_pad($lineArray, $coltotal, '');
                            }
                            foreach($lineArray as $key=>$value){
                                    $lineArray[$key] = preg_replace('/^"(.+)"$/s','$1',$lineArray[$key]);
                                    $lineArray[$key] = preg_replace('/""/','"',$lineArray[$key]);
                            }
                            if($num==1){
                               // echo '<pre>';
                              // print_r($lineArray); die;
                                if(strtolower($lineArray[0])==strtolower("Panelist ID") && strtolower($lineArray[1])==strtolower("Direct Mail Point") && strtolower($lineArray[2])==strtolower("Bags Sent") && strtolower($lineArray[3])==strtolower("Month") && strtolower($lineArray[4])==strtolower("Year")){
                                }else{
                                   $action="5";
                                   if($filename!='') unlink($filename);
                                    ob_end_clean();
                                    header("Location: {$_SERVER['PHP_SELF']}?done=$action");
                                    exit;
                                }
                            }
                            //echo "<pre>"; 
                           //print_r($lineArray); die;
                            if($num>1 && $lineArray[0]!=''){
                                if(strstr($lineArray[0],'/')){
                                    $exp_pan=explode('/',$lineArray[0]);
                                    $pan_dt   = (int)$exp_pan[1];                                
                                    $panelist_date_number = str_pad($pan_dt, 2, '0', STR_PAD_LEFT);
                                    $lineArray[0]=$exp_pan[2]."-".$exp_pan[0]."-".$panelist_date_number;                         
                                }
                                $panelistSql = "SELECT panelist_id,competi_id FROM cscan_panelists WHERE competi_id = '".$lineArray[0]."' AND contact_type='prod_panelist' order by active DESC limit 0,1";
                                $panelistQuery = $DRW->query($panelistSql, $DRW_read);                                
                                $direct_mail_point=$lineArray[1];
                                $bags_remaining=$lineArray[2];
                                $add_month    = $lineArray[3];
                                $add_year     = $lineArray[4];
                                $month_number   = (int)$add_month;                                
                                $score_month_updt = str_pad($month_number, 2, '0', STR_PAD_LEFT);
                                $add_date=$add_year.'-'.$score_month_updt.'-01';
                                
                                
                                if($DRW->num_rows($panelistQuery) > 0)
                                {  
                                    $rowData = $DRW->fetch_assoc($panelistQuery);
                                    $panelist_id=$rowData['panelist_id'];
                                    $sql_import="INSERT INTO cscan_producer_panelist_score_import (panelist_id,direct_mail_point, bags_remaining,import_date) VALUES ('".$lineArray[0]."','".$lineArray[1]."', '".$lineArray[2]."','".$add_date."')";
                                    $DRW->query($sql_import,$DRW_main);
                                    $chkSql = "SELECT id,panelist_id,competi_id,direct_mail_point FROM cscan_producer_scoring_monthly_reports WHERE panelist_id = '".$panelist_id."' AND competi_id='".$lineArray[0]."' AND LEFT(insert_date,7)='".substr($add_date,0,7)."'";
                                    $chkQuery = $DRW->query($chkSql, $DRW_read);
                                    if($DRW->num_rows($chkQuery) > 0){
                                        $rowDataCheck = $DRW->fetch_assoc($chkQuery);
                                        $ID=$rowDataCheck['id'];
                                        $panelist_id=$rowDataCheck['panelist_id'];
                                        $direct_mail_point=($rowDataCheck['direct_mail_point']+$direct_mail_point);
                                        $sql_update="UPDATE cscan_producer_scoring_monthly_reports set direct_mail_point='".$direct_mail_point."' Where id='".$ID."' AND panelist_id='".$panelist_id."'"; 
                                        $DRW->query($sql_update,$DRW_main); 
                                          
                                    }else{
                                        
                                        $sql_insert="INSERT INTO cscan_producer_scoring_monthly_reports (panelist_id,competi_id,direct_mail_point,entry_date,insert_date) VALUES ('".$panelist_id."','".$lineArray[0]."', '".$direct_mail_point."','".$add_date."','".$add_date."')";
                                        $DRW->query($sql_insert,$DRW_main); 
                                    } 
                                    $chkSqltotal = "SELECT id,panelist_id,competi_id,bag_remaining FROM cscan_producer_scoring_total_reports WHERE panelist_id = '".$panelist_id."' AND competi_id='".$lineArray[0]."'";
                                    $chkQuerytotal = $DRW->query($chkSqltotal, $DRW_read);
                                    if($DRW->num_rows($chkQuerytotal) > 0){
                                        $rowDataCheckTotal = $DRW->fetch_assoc($chkQuerytotal);
                                        $idtotal=$rowDataCheckTotal['id'];
                                        $panelist_id=$rowDataCheckTotal['panelist_id'];
                                        $bags_remaining=($rowDataCheckTotal['bag_remaining']-$bags_remaining);
                                        if($bags_remaining<0){
                                          $bags_remaining=0;  
                                        }
                                        
                                        $sql_updatetotal="UPDATE cscan_producer_scoring_total_reports set bag_remaining='".$bags_remaining."' Where id='".$idtotal."' AND panelist_id='".$panelist_id."' AND competi_id='".$lineArray[0]."'"; 
                                        $DRW->query($sql_updatetotal,$DRW_main);
                                    }else{
                                        $sql_inserttotal="INSERT INTO cscan_producer_scoring_total_reports (panelist_id,competi_id,bag_remaining,entry_date,insert_date) VALUES ('".$panelist_id."','".$lineArray[0]."', '".$bags_remaining."','".$add_date."','".$add_date."')";
                                        $DRW->query($sql_inserttotal,$DRW_main); 
                                    }
                                }else{
                                    $flag = "4";
                                    array_push($lineArray, $num);
                                    $lineRowArray[]=$lineArray;
                                    $sql_error="INSERT INTO cscan_producer_panelist_import_error (panelist_id,direct_mail_point, bags_remaining,row_num,import_date) VALUES ('".$lineArray[0]."','".$direct_mail_point."', '".$bags_remaining."','".$num."','".$add_date."')";
                                    $DRW->query($sql_error,$DRW_main);
                                }
                               
                            }                                                                                              
                        }
             $num++;}
             $action = "1";
             fclose($file);
             if($filename!='') unlink($filename);
            }
        } 
    }else{
        $action = "3";
        
    }
}
include 'top.php';
?>
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<table width='100%' border='0' cellspacing='0' cellpadding='8' class='text' rules='none' bordercolor='#B9B9B9' align='center' style='border-collapse:collapse'>
    <tr><td class="adminhead" align='center'>Import Producer Panelist Score</td></tr>
    
    <tr>
      <td colspan="8" style="padding-left: 205px;<?php if($action==1 && $flag==""){echo "color:#14734F;font-weight:bold;font-size:14px;";} ?>" align='left' class="<?php if($action==1 && $flag==""){echo "";}else{ echo "error";}?>">
          <?php 
                if(isset($_GET['done']) and $_GET['done']!=''){
                        print 'Invalid csv column name, please upload csv with correct column name.';
                }elseif($action==3){
                    print '[Invalid file, please upload a valid file.]';
                }elseif($action==1 && $flag==4){
                    print 'There are some panelistID not found in our database, <br/>so that records have not imported. please find below that records.';
                }elseif($action==1){
                    print 'Record has been uploded successfully!';
                }
                
    ?>&nbsp;</td>
  </tr>
    <tr>
        <td class="bodyText">
            
            <form method="post" name="importForm" enctype="multipart/form-data" onsubmit="return validate();" action="<?php print $_SERVER['PHP_SELF']; ?>">
                <table width='100%' border="0" cellspacing="0" cellpadding="10" class="text">
                    <tr>
                        <td align="center" colspan="" style="padding-left: 70px;" >
                        <strong style="padding-left:0px;">Upload CSV with one entry per row and fields in order:</strong><br />
                        <p style="padding-left: 32px;">Panelist ID, Direct Mail Point, Bags Sent, Month, Year</p>
                        </td>
                  
                        <!--<td colspan="" align="right" width='10%' style="padding-right: 100px;">
                        <a href='https://files.competiscan.com/fileuploads/17238scoreUploads_sample.csv'>Please click here to view the csv sample file </a>
                        </td>-->
                       
                    
                    </tr>
                    <tr>
                        <td align="center" colspan="" style="padding-left: 30px;" ><strong>File:</strong>
                            <input type="file" name="importfile" size="40" class="input_box" onchange="check_file_ext(this);"/>
                            <br/>
                            <span style="padding-left: 0px;" class="error">Hint: Only allowed extension(.csv) file.</span>

                        </td>
                       
                        <td align="right" >
                           
                           <input style="width:50px;" type="submit" name="Back" value="Back" class="button" onclick="document.location='<?php print 'producer_scoring_monthly_reports.php'; ?>'; return false;" /> 

                        </td>
                    </tr>
                    
                    <tr>
                        <td colspan="2" align="left" width='30%' style="padding-left: 220px;">
                        <input style="width:60px;" class="button" type="submit" name="upload" value="Upload" />
                        <!--<input class="button" style="width:60px;" type="submit" name="clear" value="Clear" />-->
                        </td>
                       
                    </tr>
                    
              </table>
            </form>
        </td>
    </tr>
    <?php if (!empty($lineRowArray)) { ?>
        <tr>
            <td>
                <form name="frmreport" id="frmreport" method="post" action="">
                    <table width='100%' border='0' cellspacing='0' cellpadding='8' class='text' rules='none' bordercolor='#B9B9B9' align='center' style='border-collapse:collapse'>
                        <tr>
                            <td align="left" style="width:8%;" class="adminhead" height='15px' ><b>Rows Number</b></td>
                            <td align="left" style="width:12%;" class="adminhead" height='15px' ><b>Panelist ID</b></td>
                            <td align="left" style="width:12%;" class="adminhead" height='15px' ><b>Direct Mail Point</b></td>
                            <td align="left" style="width:12%;" class="adminhead" height='15px' ><b>Bag remaining</b></td>
                            <td align="left" style="width:10%;" class="adminhead" height='15px' ><b>Month</b></td>
                            <td align="left" style="width:8%;" class="adminhead" height='15px' ><b>Year</b></td>
                        </tr>

                        <?php
                        //echo '<pre>';
                        //print_r($lineRowArray); 
                        $className = '';
                        for($i=0; $i <count($lineRowArray); $i++){
                            if ($className == 'selected-bg')
                                $className = 'white-bg';
                            else
                                $className = 'selected-bg';
                            ?>
                            <tr valign=top class="<?php echo $className; ?>">
                                <td align="left">
                                  <?php echo $lineRowArray[$i][5]; ?> 
                                </td>
                                
                                </td>
                                <td align="left"><?php echo $lineRowArray[$i][0]; ?></td>
                                <td align="left"><?php echo $lineRowArray[$i][1]; ?></td>
                                <td align="left"><?php echo $lineRowArray[$i][2]; ?></td>
                                <td align="left"><?php echo $lineRowArray[$i][3]; ?></td>
                                <td align="left"><?php echo $lineRowArray[$i][4]; ?></td>
                                
                            </tr>
                        <?php } ?>

                    </table>
                </form>
            </td>
        </tr>    
        
        <tr>
            
            <td align="right" >
            <input type="submit" style="width:50px;" name="Back" value="Back" class="button" onclick="document.location='<?php print 'producer_scoring_monthly_report.php'; ?>'; return false;" /> 

            </td>
        </tr>
    <?php } else {
        ?>
        <!--<tr><td colspan='11' align='center' class="error" style="background-color:#ccc;" height='15px' >No record(s) found.</td></tr>-->
    <?php } ?>
</table>

<?php
include 'bottom.php';
function trim_value(&$value){
   $value = trim($value);
}
?>
<script type="text/javascript">
var _validFileExtensions = [".csv"];    
function check_file_ext(oInput) {
    if (oInput.type == "file") {
        var sFileName = oInput.value;
         if (sFileName.length > 0) {
            var blnValid = false;
            for (var j = 0; j < _validFileExtensions.length; j++) {
                var sCurExtension = _validFileExtensions[j];
                if (sFileName.substr(sFileName.length - sCurExtension.length, sCurExtension.length).toLowerCase() == sCurExtension.toLowerCase()) {
                    blnValid = true;
                    break;
                }
            }
             
            if (!blnValid) {
                alert("Sorry, Only allowed extension(.csv) file.");
                oInput.value = "";
                return false;
            }
        }
    }
    return true;
}
function validate()
{
    
    var file_document=document.forms["importForm"]["importfile"].value;
    //var trend_document_hidden=document.forms["importForm"]["trend_document_hidden"].value;
   if(file_document== '')
    {
            alert('Please upload csv file.');
            document.importForm.importfile.focus();
            return false;
    }  
}

</script>

