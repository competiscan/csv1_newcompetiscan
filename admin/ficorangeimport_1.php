<?php
$ALLOW_GROUPS = array(82);
require_once("../auth_auth.php");
require_once('../includes/ehLog.php');
$action='';
if (!empty($_REQUEST['clear']) && trim($_REQUEST['clear']) == 'Clear') {
    header("location:ficorangeimport.php");
}
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
                    //$colArray = array("PanelistID", "FICO Score", "VantageScore", "CreditVision","Score Date");
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
                                if($lineArray[0]=="PanelistID" && $lineArray[1]=="FICO Score" && $lineArray[2]=="VantageScore" && $lineArray[3]=="CreditVision" && $lineArray[4]=="Score Date"){
                                }else{
                                   $action="5";
                                   if($filename!='') unlink($filename);
                                   ob_end_clean();
                                    header("Location: {$_SERVER['PHP_SELF']}?done=$action");
                                    exit;
                                }
                            }
                            //echo "<pre>"; 
                            //print_r($lineArray);
                            if($num>1 && $lineArray[0]!='' && $lineArray[4]!=''){
                                if(strstr($lineArray[0],'/')){
                                    $exp_pan=explode('/',$lineArray[0]);
                                    $lineArray[0]=$exp_pan[2]."-".$exp_pan[0]."-".$exp_pan[1];                         
                                }
                                $panelistSql = "SELECT panelist_id FROM cscan_panelists WHERE competi_id = '".$lineArray[0]."' AND active=1 AND parent_panelist_id=0";
                                $panelistQuery = $DRW->query($panelistSql, $DRW_read);
                                $rowData = $DRW->fetch_assoc($panelistQuery);
                                $panelist_id=$rowData['panelist_id'];
                                $fico_score   = $lineArray[1];
                                $vantage_score  = $lineArray[2];
                                $credit_vision  = $lineArray[3];
                                $score_date = date("Y-m-d", strtotime($lineArray[4]));
                                if($DRW->num_rows($panelistQuery) > 0)
                                {    
                                    $chkSql = "SELECT id,panelist_id,fico_score,vantage_score,credit_vision FROM cscan_panelists_additional_score WHERE panelist_id = '".$panelist_id."' AND LEFT(score_date,7)='".substr($score_date,0,7)."'";
                                    $chkQuery = $DRW->query($chkSql, $DRW_read);
                                    $chkRowData = $DRW->fetch_assoc($chkQuery);
                                    $ID=$chkRowData['id'];
                                    $dbfico_score=$chkRowData['fico_score'];
                                    $dbvantage_score=$chkRowData['vantage_score'];
                                    $dbcredit_vision=$chkRowData['credit_vision'];
                                    if($DRW->num_rows($chkQuery) < 1){
                                        $sql_insert="INSERT INTO cscan_panelists_additional_score (panelist_id,fico_score, vantage_score, credit_vision, score_date,created_date) VALUES ('".$panelist_id."','".$fico_score."', '".$vantage_score."', '".$credit_vision."', '".$score_date."', NOW())"; 
                                        $DRW->query($sql_insert,$DRW_main);   
                                    }else{
                                        
                                       if($fico_score!='' && $vantage_score!='' && $credit_vision!=''){
                                       $sql_update="UPDATE cscan_panelists_additional_score set fico_score='".$fico_score."',vantage_score='".$vantage_score."',credit_vision='".$credit_vision."' Where id='".$ID."' AND panelist_id='".$panelist_id."'"; 
                                       $DRW->query($sql_update,$DRW_main); 
                                       }elseif($fico_score=='' && $vantage_score=='' && $credit_vision!=''){
                                           $sql_update1="UPDATE cscan_panelists_additional_score set credit_vision='".$credit_vision."' Where id='".$ID."' AND panelist_id='".$panelist_id."'"; 
                                           $DRW->query($sql_update1,$DRW_main);  
                                       }elseif($fico_score!='' && $vantage_score!='' && $credit_vision==''){
                                            $sql_update2="UPDATE cscan_panelists_additional_score set fico_score='".$fico_score."',vantage_score='".$vantage_score."' Where id='".$ID."' AND panelist_id='".$panelist_id."'";
                                            $DRW->query($sql_update2,$DRW_main); 
                                       }elseif($fico_score!='' && $dbfico_score==''){
                                            $sql_update2="UPDATE cscan_panelists_additional_score set fico_score='".$fico_score."' Where id='".$ID."' AND panelist_id='".$panelist_id."'";
                                            $DRW->query($sql_update2,$DRW_main); 
                                       }elseif($vantage_score!='' && $dbvantage_score==''){
                                            $sql_update2="UPDATE cscan_panelists_additional_score set vantage_score='".$vantage_score."' Where id='".$ID."' AND panelist_id='".$panelist_id."'";
                                            $DRW->query($sql_update2,$DRW_main); 
                                       }elseif($credit_vision!='' && $dbcredit_vision==''){
                                            $sql_update2="UPDATE cscan_panelists_additional_score set credit_vision='".$credit_vision."' Where id='".$ID."' AND panelist_id='".$panelist_id."'";
                                            $DRW->query($sql_update2,$DRW_main); 
                                       }
                                    }    
                                }else{
                                    $sql_error="INSERT INTO cscan_panelists_additional_score_error (panelist_id,fico_score, vantage_score, credit_vision, score_date,row_num,created_date) VALUES ('".$lineArray[0]."','".$fico_score."', '".$vantage_score."', '".$credit_vision."', '".$score_date."','".$num."', NOW())"; 
                                    $DRW->query($sql_error,$DRW_main);
                                }
                                $action = "1";
                            }                                                                                              
                        }
             $num++;}
             fclose($file);
             if($filename!='') unlink($filename);
            }
        } 
    }else{
        $action = "3";
        ob_end_clean();
        header("Location: {$_SERVER['PHP_SELF']}?done=$action");
        exit;
    }
}
if($action==1){
ob_end_clean();
header("Location:consumer_panelist_scoring_range.php");exit;
}

$order_by = '';
$order_bytxt = ' ';
$_SESSION['order']='';
$_SESSION['sort']='';
$order = 0;
$sort = '';
$competi_id = '';
$conditions = '';
$month_report = date('Y-m');
$panelist_type ='';

$filter = array();
if (!empty($_REQUEST)) {
    if (!empty($_REQUEST['sort']))
        $sort =$_SESSION['sort']= trim($_REQUEST['sort']);
    if (!empty($_REQUEST['order']))
        $order =$_SESSION['order']= trim($_REQUEST['order']);
    if (!empty($sort)) {
        switch ($sort) {
            case '1':
                $order_by = 'panelist_id';
                break;
            case '2':
                $order_by = 'fico_score';
                break;
            case '3':
                $order_by = 'vantage_score';
                break;
            case '4':
                $order_by = 'credit_vision';
                break;
            case '5':
                $order_by = 'score_date';
                break;
            case '6':
                $order_by = 'row_num';
                break;
            case '7':
                $order_by = 'created_date';
                break;
        }
        if (empty($order)) {
            $order_by .= ' ASC';
            //$order = 1;
        } else {
            $order_by .= ' DESC';
            //$order = 0;
        }
    }
}
if (!empty($order_by)){
   $order_bytxt = " ORDER BY "; 
}
 if (empty(trim($order_by))) {
    $order_by = ' ORDER BY id ASC ';
}   
include 'top.php';
$limit = 20;
if (isset($_GET['p']))
    $p = trim($_GET['p']);
else
    $p = 0;

function doSort($sort, $order, $dosort, $p, $competi_id, $spacer = ' : ') {
    if (empty($order)) {
        $order = 1;
    } else {
        $order = 0;
    }
    $addparam = '';
    if (!empty($_REQUEST['b'])) {
        $addparam = "&b=" . $_REQUEST['b'];
    }
    $addparam2 = '';
    if (!empty($_REQUEST['r'])) {
        $addparam2 = "&r=" . $_REQUEST['r'];
    }
    if ($sort == ($dosort * -1) || $sort != $dosort) {
        print "$spacer<a href=\"" . $_SERVER['PHP_SELF'] . "?competi_id={$competi_id}&sort=$dosort&order=$order&p=$p$addparam\" class=\"blue\">sort</a>";
    } else {
        print "$spacer<a href=\"" . $_SERVER['PHP_SELF'] . "?competi_id={$competi_id}&sort=$dosort&order=$order&p=$p$addparam\" class=\"blue\">sort</a>";
    }
}


$sql = "SELECT SQL_CALC_FOUND_ROWS id,panelist_id,fico_score,vantage_score,credit_vision,score_date,row_num,created_date FROM cscan_panelists_additional_score_error   $conditions $order_bytxt $order_by LIMIT $p, $limit";
//echo $sql; 
$rs = $DRW->query($sql, $DRW_read2);
$numquery = $DRW->query("SELECT FOUND_ROWS()", $DRW_read2); 
$nrow = $DRW->fetch_row($numquery);
$numrows = $nrow[0];
?>
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<table width='100%' border='0' cellspacing='0' cellpadding='8' class='text' rules='none' bordercolor='#B9B9B9' align='center' style='border-collapse:collapse'>
    <tr><td class="adminhead" align='center'>Import Consumer Scoring Reports</td></tr>
    
    <tr>
      <td colspan="8" align='center' class="error">
          <?php 
            if(isset($_GET['done'])){
                if($_GET['done']==5){
                        print '[Invalid csv column name, please match uploaded csv column name.]';
                }elseif($_GET['done']==3){
                    print '[Invalid file, please upload a valid file.]';
                }
            } 
    ?>&nbsp;</td>
  </tr>
    <tr>
        <td class="bodyText">
            
            <form method="post" name="importForm" enctype="multipart/form-data" onsubmit="return validate();" action="<?php print $_SERVER['PHP_SELF']; ?>">
                <table width='100%' border="0" cellspacing="0" cellpadding="10" class="text">
                    <tr>
                        <td align="center" colspan="" style="padding-left: 50px;" >
                        <strong style="padding-left:0px;">Upload CSV with one entry per row and fields in order:</strong><br />
                        <p style="padding-left: 82px;">PanelistID, FICO Score, VantageScore, CreditVision, Score Date <a href='http://files1.competiscan.com/fileuploads/83712scoreUploads.csv'>Download</a></p>
                        </td>
                    </tr>
                    <tr>
                        <td align="center" colspan="" style="padding-left: 30px;" ><strong>File:</strong>
                            <input type="file" name="importfile" size="40" class="input_box" onchange="check_file_ext(this);"/>
                            <br/>
                            <span style="padding-left: 0px;" class="error">Hint: Only allowed extension(.csv)</span>

                        </td>
                       
                        <td align="right" >
                           
                           <input type="submit" name="Back" value="Back" class="button" onclick="document.location='<?php print 'consumer_panelist_scoring_range.php'; ?>'; return false;" /> 

                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" align="left" width='30%' style="padding-left: 220px;">
                        <input class="button" type="submit" name="upload" value="Upload" />
                        <!--<input class="button" style="width:60px;" type="submit" name="clear" value="Clear" />-->
                        </td>
                    </tr>
              </table>
            </form>
        </td>
    </tr>
    <?php if (!empty($numrows)) { ?>
        <tr>
            <td>
                <form name="frmreport" id="frmreport" method="post" action="">
                    <table width='100%' border='0' cellspacing='0' cellpadding='8' class='text' rules='none' bordercolor='#B9B9B9' align='center' style='border-collapse:collapse'>
                        <tr>
                            <td align="left" style="width:12%;" class="adminhead" height='15px' ><b>Panelist ID</b><?php //doSort($sort, $order, 1, $p, $competi_id); ?></td>
                            <td align="left" style="width:12%;" class="adminhead" height='15px' ><b>FICO Score</b><?php //doSort($sort, $order, 2, $p, $competi_id); ?></td>
                            <td align="left" style="width:12%;" class="adminhead" height='15px' ><b>VantageScore</b><?php //doSort($sort, $order, 3, $p, $competi_id); ?></td>
                            <td align="left" style="width:10%;" class="adminhead" height='15px' ><b>CreditVision</b><?php //doSort($sort, $order,4 , $p, $competi_id);  ?></td>
                            <td align="left" style="width:8%;" class="adminhead" height='15px' ><b>Score Date</b><?php //doSort($sort, $order, 5, $p, $competi_id); ?></td>
                            <td align="left" style="width:8%;" class="adminhead" height='15px' ><b>Rows Num</b><?php //doSort($sort, $order, 6, $p, $competi_id); ?></td>
                            <td align="left" style="width:8%;" class="adminhead" height='15px' ><b>Date</b><?php //doSort($sort, $order, 7, $p, $competi_id); ?></td>
                        </tr>

                        <?php
                        $className = '';
                        while ($row = $DRW->fetch_assoc($rs)) {
                            $ids = $row['id'];
                            $insert_date = date('M-Y', strtotime($row['score_date']));
                            $insert_date1 = date('Y-m', strtotime($row['score_date']));
                            if ($className == 'selected-bg')
                                $className = 'white-bg';
                            else
                                $className = 'selected-bg';
                            ?>
                            <tr valign=top class="<?php echo $className; ?>">
                                <td align="left">
                                  <?php echo $row['panelist_id']; ?> 
                                </td>
                                
                                </td>
                                <td align="left"><?php echo $row['fico_score']; ?></td>
                                <td align="left"><?php echo $row['vantage_score']; ?></td>
                                <td align="left"><?php echo $row['credit_vision']; ?></td>
                                <td align="left"><?php echo $row['score_date']; ?></td>
                                <td align="left"><?php echo $row['row_num']; ?></td>
                                <td align="left"><?php echo $row['created_date']; ?></td>
                                
                            </tr>
                        <?php } ?>

                    </table>
                </form>
            </td>
        </tr>    
        <tr>
            <td colspan="11">
                <table border="0" width="100%" cellspacing = "0"  cellpadding ="7">
                    <tr><td>&nbsp;</td></tr>
                    <?php
                    $firstlink = '[First]';
                    $prevlink = '[Prev]';
                    $nextlink = '[Next]';
                    $lastlink = '[Last]';
                    $middlelinks = '';
                    $limstart = $p;
                    $limiter = $limit;
                    $rowcnt = $numrows;
                    $show = 10;
                    $addparam = '';
                    $addparam2 = '';
                    if (!empty($_REQUEST['b'])) {
                        $addparam = "&b=" . $_REQUEST['b'];
                    }
                    if (!empty($_REQUEST['r'])) {
                        $addparam2 = "&r=" . $_REQUEST['r'];
                    }
                    //first and previous only if not on first
                    if ($limstart > 0) {
                        if ($limstart >= $limiter)
                            $prev = $limstart - $limiter;
                        else
                            $prev = 0;
                        $firstlink = "[<a href=\"{$_SERVER['PHP_SELF']}?p=0&competi_id={$competi_id}&sort={$sort}&order={$order}$addparam$addparam2\">First</a>]";
                        $prevlink = "<a href=\"{$_SERVER['PHP_SELF']}?p=$prev&competi_id={$competi_id}&sort={$sort}&order={$order}$addparam$addparam2\">&laquo; Prev $limiter</a>";
                    }
                    // middle loop through total results
                    $numbers = ceil($rowcnt / $limiter);
                    $loopstart = ceil($limstart / $limiter);
                    if ($loopstart < ($show - 1))
                        $loopstart = 0; // begin, do not move until 4
                    if ($numbers < $show)
                        $loopend = $numbers; // loopend is less than $show
                    else
                        $loopend = $loopstart + $show;
                    if ($loopend > $numbers && $loopstart != 0) { // end, show last $show
                        $loopstart = $numbers - $show;
                        $loopend = $numbers;
                    }
                    for ($i = $loopstart; $i < $loopend; $i++) {
                        $startnum = $limiter * $i;
                        if ($startnum != $limstart) {
                            $middlelinks .= "<a href=\"{$_SERVER['PHP_SELF']}?p=$startnum&competi_id={$competi_id}&sort={$sort}&order={$order}$addparam$addparam2\">" . ($i + 1) . "</a> ";
                        } else
                            $middlelinks .= ($i + 1) . ' ';
                    }
                    //next and last if not on last
                    if ($limstart < $rowcnt && (($limstart + ($limiter * 2)) < $rowcnt || ($rowcnt - ($limstart + $limiter)) > 0)) {
                        $next = $limstart + $limiter;
                        $nextlink = "<a href=\"{$_SERVER['PHP_SELF']}?p=$next&competi_id={$competi_id}&sort={$sort}&order={$order}$addparam$addparam2\">Next $limiter &raquo;</a>";
                        $lastlink = "[<a href=\"{$_SERVER['PHP_SELF']}?p=" . (($numbers - 1) * $limiter) . "&competi_id={$competi_id}&sort={$sort}&order={$order}$addparam$addparam2\">Last</a>]";
                    }
                    if ($middlelinks != '')
                        $middlelinks = "[ $middlelinks ] &nbsp;";
                    print "<tr><td align=\"center\" class=\"bodytext\">$firstlink &nbsp; $prevlink &nbsp; $middlelinks $nextlink &nbsp; $lastlink</td></tr>";
                    print "<tr><td align=\"center\" class=\"bodytext\">Showing results " . ($limstart + 1) . " to ";
                    if ($limstart + $limiter < $rowcnt)
                        print ($limstart + $limiter);
                    else
                        print $rowcnt;
                    print " of $rowcnt</td></tr>";
                    ?>
                </table>
            </td>
        </tr>
        <tr>
            <td align="right" >
                           
            <input type="submit" name="Back" value="Back" class="button" onclick="document.location='<?php print 'consumer_panelist_scoring_range.php'; ?>'; return false;" /> 

            </td>
        </tr>
    <?php } else {
        ?>
        <tr><td colspan='11' align='center' class="error" style="background-color:#ccc;" height='15px' >No record(s) found.</td></tr>
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
                alert("Sorry, " + sFileName + " is invalid, allowed extensions are: " + _validFileExtensions.join(", "));
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

