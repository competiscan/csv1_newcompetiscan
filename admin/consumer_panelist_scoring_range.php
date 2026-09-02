<?php
$ALLOW_GROUPS = array(96);
require_once("../auth_auth.php");
require_once('../includes/ehLog.php');
$success = '';
$sts_msg='';
$monthArray = getmonthArray();
$end_year = date('Y');
$end_month = date('m');
$start_month = 1;
$start_year = 2015;
$order_by = '';
$order_bytxt = ' ';
$_SESSION['month_report']='';
$_SESSION['order']='';
$_SESSION['sort']='';
$order = 0;
$sort = '';
$competi_id = '';
//$conditions = ' Where active=1 And cp.parent_panelist_id=0 ';
$conditions = ' Where active=1 ';
$month_report = date('Y-m');
$panelist_type ='';
if (!empty($_REQUEST['competi_id'])) {
    $competi_id = trim($_REQUEST['competi_id']);
    $seasrcharray = array("'", '"', ' ');
    $replacearray = array("", "", "");
    $competiarray = explode(",", str_replace($seasrcharray, $replacearray, $competi_id));
    $newarraystr = trim(implode("','", $competiarray));
    $conditions .= "  AND cp.competi_id in( '" . $newarraystr . "') ";
}
if (!empty($_REQUEST['month_report'])) {
    $month_report = $_SESSION['month_report']= $_REQUEST['month_report'];
    $conditions .= "  AND LEFT(score_date,7)>='" . $month_report . "' AND LEFT(score_date,7)<='" . $month_report . "'";
}else{
  $conditions .= "  AND LEFT(score_date,7)>='" . $month_report . "' AND LEFT(score_date,7)<='" . $month_report . "'";  
}

if (!empty($_REQUEST['clear']) && trim($_REQUEST['clear']) == 'Clear') {
    header("location:consumer_panelist_scoring_range.php");
}
$filter = array();
if (!empty($_REQUEST)) {
    if (!empty($_REQUEST['sort']))
        $sort =$_SESSION['sort']= trim($_REQUEST['sort']);
    if (!empty($_REQUEST['order']))
        $order =$_SESSION['order']= trim($_REQUEST['order']);
    if (!empty($sort)) {
        switch ($sort) {
            case '1':
                $order_by = 'cpads.panelist_id';
                break;
            case '2':
                $order_by = 'cpads.fico_score';
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
    $order_by = ' ORDER BY cpads.panelist_id ASC ';
}   
include 'top.php';
$limit = 20;
if (isset($_GET['p']))
    $p = trim($_GET['p']);
else
    $p = 0;

function doSort($sort, $order, $dosort, $p, $competi_id,$month_report, $spacer = ' : ') {
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
        print "$spacer<a href=\"" . $_SERVER['PHP_SELF'] . "?competi_id={$competi_id}&month_report={$month_report}&sort=$dosort&order=$order&p=$p$addparam\" class=\"blue\">sort</a>";
    } else {
        print "$spacer<a href=\"" . $_SERVER['PHP_SELF'] . "?competi_id={$competi_id}&month_report={$month_report}&sort=$dosort&order=$order&p=$p$addparam\" class=\"blue\">sort</a>";
    }
}


$sql = "SELECT SQL_CALC_FOUND_ROWS id,cp.competi_id,cpads.fico_score,vantage_score,credit_vision,score_date,created_date FROM cscan_panelists_additional_score cpads LEFT JOIN cscan_panelists cp ON (cpads.panelist_id=cp.panelist_id)  $conditions $order_bytxt $order_by LIMIT $p, $limit";
//echo $sql; 
$rs = $DRW->query($sql, $DRW_read2);
$numquery = $DRW->query("SELECT FOUND_ROWS()", $DRW_read2); 
$nrow = $DRW->fetch_row($numquery);
$numrows = $nrow[0];
?>
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<table width='100%' border='0' cellspacing='0' cellpadding='8' class='text' rules='none' bordercolor='#B9B9B9' align='center' style='border-collapse:collapse'>
    <tr><td class="adminhead" align='center'>Panelist Score</td></tr>
    <tr><td colspan='8' align='center' style="<?php if(!empty($sts_msg) && $sts_msg==1){echo "color:green;";}else{ echo "color:red;";} ?>" class=''><?php if(!empty($sts_msg) &&$sts_msg==1){echo "Your file has been uploaded successfully!";}else if(!empty($sts_msg) &&$sts_msg==2){echo "Your file has not been uploaded successfully!";}else if(!empty($sts_msg) &&$sts_msg==3){echo "Invalid file, please upload a valid file";} ?></td></tr>
    <tr>
        <td class="bodyText">
            <!--<form method="POST" name="importForm" action="<?php echo $_SERVER['PHP_SELF']; ?>" onsubmit="return validate();"  enctype="multipart/form-data">    
                <table width='100%' border="0" cellspacing="0" cellpadding="10" class="text">
                    <tr>
                        <td align="left" width='10%' style="padding-left: 66px;" colspan="2" ><strong>Upload File:</strong>
                          <input type="file" name="file" onchange="check_file_ext(this);"/> <br/>
                          <span style="padding-left: 66px;" class="error">Hint: Only allowed extension(.csv)</span>
                        </td>
                        
                    </tr>
                     <tr>
                        <td align="left" width='20%' style="padding-left: 150px;" colspan="2" >
                          <input class="button" type="submit" name="import_csv" value="Import Csv"/>
                        </td>
                    </tr>
                </table>
            </form>
            <hr>-->
            <form method="request" name="rangeForm" action="<?php echo $_SERVER['PHP_SELF']; ?>" style="display:inline;">    
                <table width='100%' border="0" cellspacing="0" cellpadding="10" class="text">
                    <tr>
                        <td align="left" colspan="" style="padding-left: 40px;" ><strong>Search By Month:</strong>
                            &nbsp;&nbsp;
                            <select name="month_report" class="" id="month1" style="width:130px;" onchange="validateMonth();">
                                <!--<option value="">Select Month</option>-->
                                <?php generate_month($monthArray, $end_month, $end_year, $start_month, $start_year, $month_report); ?>
                            </select>

                        </td>
                       
                         <td align="right" >
                           
                           <input style="width:60px;" type="submit" name="import" value="Import" class="button" onclick="document.location='<?php print 'ficorangeimport.php'; ?>'; return false;" /> 

                        </td>
                    </tr>

                    <tr>
                        <td align="left" style="padding-left: 75px;" colspan="3"><strong>Search By:</strong>
                            &nbsp;&nbsp;
                            <!--<strong>Panelist ID:</strong>-->
                            <input type="text" id="competi_id" placeholder="Panelist ID" style="width:80%;" name="competi_id"   class="input_box2345" value="<?php echo $competi_id; ?>" />
                            <span style="text-align:center;width:100%;float:left;">( Please enter multiple panelist id with comma separated. )</span>
                        </td>
                    </tr>
                    <tr>
                        <td align="center" width='30%' style="padding-left: 66px;" colspan="2" >
                            <input class="button" style="width:60px;" type="submit" name="search" value="Search" />
                            <?php //if (!empty($_REQUEST['panelist_type']) || !empty($_REQUEST['competi_id'])): ?>
                            <input class="button" style="width:60px;" type="submit" name="clear" value="Clear" />
                            <?php // endif; ?>
                        </td>
                        
                        <?php if(isset($_SESSION['sort']) && !empty($_SESSION['sort'])){ ?>
                        <input type="hidden" name="sort" value="<?php echo $sort; ?>">
                        <input type="hidden" name="order" value="<?php echo $order; ?>">
                        <?php } ?>
                        <!--<td align="right"  width='10%'><input class="button" style="width:60px;margin-right: 10px;" type="submit" name="import_csv" value="Import Csv" />
                        </td>-->
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
                            <td align="left" style="width:12%;" class="adminhead" height='15px' ><b>Panelist ID</b><?php doSort($sort, $order, 1, $p, $competi_id,$month_report); ?></td>
                            <td align="left" style="width:12%;" class="adminhead" height='15px' ><b>FICO Score</b><?php doSort($sort, $order, 2, $p, $competi_id,$month_report); ?></td>
                            <td align="left" style="width:12%;" class="adminhead" height='15px' ><b>VantageScore</b><?php doSort($sort, $order, 3, $p, $competi_id,$month_report); ?></td>
                            <td align="left" style="width:10%;" class="adminhead" height='15px' ><b>CreditVision</b><?php doSort($sort, $order,4 , $p, $competi_id, $panelist_type);  ?></td>
                            <td align="left" style="width:8%;" class="adminhead" height='15px' ><b>Score Date</b><?php doSort($sort, $order, 5, $p, $competi_id,$month_report); ?></td>
                            <td align="left" style="width:8%;" class="adminhead" height='15px' ><b>Date</b><?php doSort($sort, $order, 6, $p, $competi_id,$month_report); ?></td>
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
                                   <!--<a href="javascript:void(0)" onclick="openpopup_point('<?php echo trim($row['competi_id']); ?>','<?php echo $insert_date1; ?>');" >
                                        <?php echo $row['competi_id']; ?>
                                   </a>-->
                                   <?php echo $row['competi_id']; ?> 
                                </td>
                                
                                </td>
                                <td align="left"><?php echo $row['fico_score']; ?></td>
                                <td align="left"><?php echo $row['vantage_score']; ?></td>
                                <td align="left"><?php echo $row['credit_vision']; ?></td>
                                <td align="left"><?php echo $row['score_date']; ?></td>
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
                        $firstlink = "[<a href=\"{$_SERVER['PHP_SELF']}?p=0&competi_id={$competi_id}&month_report={$month_report}&sort={$sort}&order={$order}$addparam$addparam2\">First</a>]";
                        $prevlink = "<a href=\"{$_SERVER['PHP_SELF']}?p=$prev&competi_id={$competi_id}&month_report={$month_report}&sort={$sort}&order={$order}$addparam$addparam2\">&laquo; Prev $limiter</a>";
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
                            $middlelinks .= "<a href=\"{$_SERVER['PHP_SELF']}?p=$startnum&competi_id={$competi_id}&month_report={$month_report}&sort={$sort}&order={$order}$addparam$addparam2\">" . ($i + 1) . "</a> ";
                        } else
                            $middlelinks .= ($i + 1) . ' ';
                    }
                    //next and last if not on last
                    if ($limstart < $rowcnt && (($limstart + ($limiter * 2)) < $rowcnt || ($rowcnt - ($limstart + $limiter)) > 0)) {
                        $next = $limstart + $limiter;
                        $nextlink = "<a href=\"{$_SERVER['PHP_SELF']}?p=$next&competi_id={$competi_id}&month_report={$month_report}&sort={$sort}&order={$order}$addparam$addparam2\">Next $limiter &raquo;</a>";
                        $lastlink = "[<a href=\"{$_SERVER['PHP_SELF']}?p=" . (($numbers - 1) * $limiter) . "&competi_id={$competi_id}&month_report={$month_report}&sort={$sort}&order={$order}$addparam$addparam2\">Last</a>]";
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
    <?php } else {
        ?>
        <tr><td colspan='11' align='center' class="error" style="background-color:#ccc;" height='15px' >No record(s) found.</td></tr>
    <?php } ?>
</table>
<?php

function getmonthArray() {
    return array("1" => 'January', "2" => 'February', "3" => 'March', "4" => 'April', "5" => 'May', "6" => 'June', "7" => 'July', "8" => 'August', "9" => 'September', "10" => 'October', "11" => 'November', "12" => 'December');
}

function generate_month($monthArray, $end_month, $end_year, $start_month, $start_year, $month_select) {

    $end_month = intval($end_month);
    $end_year = intval($end_year);
    $start_month = intval($start_month);
    $start_year = intval($start_year);

    for ($year = $end_year; $year >= $start_year; $year--) {
        $tmp_month = 12;
        $tmp_month2 = 1;
        if ($year == $end_year) {
            $tmp_month = $end_month;
        } elseif ($year == $start_year) {
            $tmp_month2 = $start_month;
        }
        for ($month = $tmp_month; $month >= $tmp_month2; $month--) {
            if ($month < 10) {
                $m = '0' . $month;
            } else {
                $m = $month;
            }
            $value = $year . "-" . $m;
            $selected_value = '';
            if ($month_select == $value) {
                $selected_value = "selected=selected";
            }
            $options = "<option " . $selected_value . " value=\"$value\">" . $monthArray[$month] . "  " . $year . "</option>";
            echo $options;
        }
    }
}

include 'bottom.php';
?>
<script type="text/javascript">
var _validFileExtensions = [".xlsx", ".csv"];    
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
    
    var file_document=document.forms["importForm"]["file"].value;
    //var trend_document_hidden=document.forms["importForm"]["trend_document_hidden"].value;
   if(file_document== '')
    {
            alert('Please upload file.');
            document.importForm.trend_document.focus();
            return false;
    }  
}
</script>

