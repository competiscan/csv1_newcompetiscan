<?php
$ALLOW_GROUPS = array(107);
require_once("../auth_auth.php");
require_once('../includes/ehLog.php');
$msg = '';
$success = '';
if (!empty($_REQUEST['msg'])) {
    $msg = $_REQUEST['msg'];
}

function array2csv(array $array) {
    if (count($array) == 0) {
        return null;
    }
    ob_start();
    $df = fopen("php://output", 'w');
    foreach ($array['data'] as $row) {
        fputcsv($df, $row);
    }
    fclose($df);
    return ob_get_clean();
}

function download_send_headers($filename) {
    // disable caching
    $now = gmdate("D, d M Y H:i:s");
    header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
    header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
    header("Last-Modified: {$now} GMT");
    // force download  
    header("Content-Type: application/force-download");
    header("Content-Type: application/octet-stream");
    header("Content-Type: application/download");
    // disposition / encoding on response body
    header("Content-Disposition: attachment;filename={$filename}");
    header("Content-Transfer-Encoding: binary");
}

$monthArray = getmonthArray();
$end_year = date('Y');
$end_month = date('m');
$start_month = 1;
$start_year = 2015;
$order_by1 = '';
$order_by = '';
$order_bytxt1=' ';
$order_bytxt = ' ';
$_SESSION['month_report']='';
$_SESSION['order']='';
$_SESSION['sort']='';
$order = 0;
$sort = '';
$competi_id = '';
$conditions = '';
$conditions1='';
$month_report = date('Y-m');
$panelist_type ='';
if (!empty($_REQUEST['competi_id'])) {
    $competi_id = trim($_REQUEST['competi_id']);
    $seasrcharray = array("'", '"', ' ');
    $replacearray = array("", "", "");
    $competiarray = explode(",", str_replace($seasrcharray, $replacearray, $competi_id));
    $newarraystr = trim(implode("','", $competiarray));
    $conditions1 = "  AND competi_id in( '" . $newarraystr . "') ";
}
if (!empty($_REQUEST['month_report'])) {
    $month_report = $_SESSION['month_report']= $_REQUEST['month_report'];
    $conditions .= "  AND LEFT(insert_date,7)>='" . $month_report . "' AND LEFT(insert_date,7)<='" . $month_report . "'";
}else{
  $conditions .= "  AND LEFT(insert_date,7)>='" . $month_report . "' AND LEFT(insert_date,7)<='" . $month_report . "'";  
}

if (!empty($_REQUEST['clear']) && trim($_REQUEST['clear']) == 'Clear') {
    header("location:provider_scoring_monthly_reports.php");
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
                $order_by = 'competi_id';
                break;
            case '2':
                $order_by = 'email_piece';
                break;
            case '3':
                $order_by = 'email_piece_point';
                break;
            case '4':
                $order_by = 'first_name';
                break;
            case '5':
                $order_by = 'last_name';
                break;
            case '6':
                $order_by = 'address';
                break;
            case '7':
                $order_by = 'city';
                break;
            case '8':
                $order_by = 'state';
                break;
            case '9':
                $order_by = 'postalcode';
                break;
            case '10':
                $order_by = 'email';
                break;
            case '11':
                $order_by = 'insert_date';
                break;
            default:
                $order_by = 'panelist_id';
                break;
        }
        if (empty($order)) {
           $order_by .= ' ASC';  
        } else {
            $order_by .= ' DESC';
        }
    }
}
if (!empty($order_by)){
   $order_bytxt = " ORDER BY "; 
}
if (!empty($order_by1)){
   $order_bytxt1 = " ORDER BY "; 
}
$group_bytxt = "";  
 if (empty(trim($order_bytxt)) && empty(trim($order_by))) {
    $order_bytxt = ' ORDER BY ';
    $order_by = ' panelist_id ASC ';
}   
if (!empty($_REQUEST['export']) && trim($_REQUEST['export']) == 'Export') {//pr($_POST);die;
    $arrExport = array();
    $exp_sql="SELECT SQL_CALC_FOUND_ROWS panelist_id, competi_id,first_name,last_name,address,city,state,postalcode,email FROM cscan_panelists Where active=1 And parent_panelist_id=0 AND competi_id REGEXP '-44-' AND contact_type='prod_panelist' $conditions1 $order_bytxt $order_by";
    $exp_rs = $DRW->query($exp_sql, $DRW_read2);
    if (!empty($exp_rs)) {
        $arrExport['data'][] = array("Panelist ID","Email Pieces","Email Point","Incentive Value($)","First Name", "Last Name",
            "Address", "City", "State", "Zip", "Email", "Date");
        
        while ($exp_row = $DRW->fetch_assoc($exp_rs)) {
            $competi_id = $exp_row['competi_id'];
            $chkSql_month = "SELECT email_piece,email_piece_point,entry_date,insert_date FROM  cscan_provider_scoring_monthly_reports WHERE  competi_id='".$competi_id."' $conditions";
            $chkQuery_month = $DRW->query($chkSql_month, $DRW_read);
            $email_piece_point='0';
            $email_piece='0';
            if($DRW->num_rows($chkQuery_month) > 0){
                $rowDataCheckMonth = $DRW->fetch_assoc($chkQuery_month);
                $email_piece=$rowDataCheckMonth['email_piece'];
                $email_piece_point=$rowDataCheckMonth['email_piece_point'];
            }
            $incentive_value='0';
            if($email_piece_point>0){
             $incentive_value =20;   
            }
            $insert_date = date('M-Y', strtotime($month_report));
           
            $arrExport['data'][] = array($exp_row['competi_id'],$email_piece,$email_piece_point,$incentive_value,$exp_row['first_name'], $exp_row['last_name'],
                $exp_row['address'], $exp_row['city'], $exp_row['state'],
                $exp_row['postalcode'], $exp_row['email'], $insert_date,
            );
            
        }
    }
    download_send_headers("provider_scoring_monthly_reports_" . date("Y-m-d") . ".csv");
    echo array2csv($arrExport);
    die();
}
include 'top.php';
$limit = 50;
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
    $addparam3 = '';
    if (!empty($_REQUEST['d'])) {
        $addparam3 = "&d=" . $_REQUEST['d'];
    }
    if ($sort == ($dosort * -1) || $sort != $dosort) {
        print "$spacer<a href=\"" . $_SERVER['PHP_SELF'] . "?competi_id={$competi_id}&month_report={$month_report}&sort=$dosort&order=$order&p=$p$addparam\" class=\"blue\">sort</a>";
    } else {
        print "$spacer<a href=\"" . $_SERVER['PHP_SELF'] . "?competi_id={$competi_id}&month_report={$month_report}&sort=$dosort&order=$order&p=$p$addparam\" class=\"blue\">sort</a>";
    }
}
$sql = "SELECT SQL_CALC_FOUND_ROWS panelist_id,competi_id,first_name,last_name,address,city,state,postalcode,email FROM cscan_panelists Where active=1 AND parent_panelist_id=0 AND competi_id REGEXP '-44-' AND contact_type='prod_panelist' $conditions1 $order_bytxt $order_by LIMIT $p, $limit";
//echo $sql;
$rs = $DRW->query($sql, $DRW_read2);
$numquery = $DRW->query("SELECT FOUND_ROWS()", $DRW_read2); 
$nrow = $DRW->fetch_row($numquery);
$numrows = $nrow[0];
?>
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<table width='100%' border='0' cellspacing='0' cellpadding='8' class='text' rules='none' bordercolor='#B9B9B9' align='center' style='border-collapse:collapse'>
    <tr><td class="adminhead" align='center'>Provider Scoring Monthly Report</td></tr>
     <?php if ($msg == '1') {
        ?>
        <tr>  <td align='center' style="color:#14734F;font-weight:bold;font-size:14px;" >Bags Remaining updated successfully.</td></tr>
        <?php
    }
    if ($msg == '2') {
            ?>
            <tr>  <td align='center' style="color:#14734F;font-weight:bold;font-size:14px;" > Bags Remaining reset successfully.</td></tr>
        <?php
    }
    if ($msg == '3') {
        ?>
            <tr>  <td align='center' style="color:#14734F;font-weight:bold;font-size:14px;" > Added bonus point successfully.</td></tr>
        <?php
    }if ($msg == '5') {
        ?>
            <tr>  <td align='center' style="color:#14734F;font-weight:bold;font-size:14px;" >Direct mail point updated successfully.</td></tr>
        <?php
    }
    ?>
    <tr>
        <td class="bodyText">
            <form method="request" name="rangeForm" action="<?php echo $_SERVER['PHP_SELF']; ?>" style="display:inline;">    
                <table width='100%' border="0" cellspacing="0" cellpadding="10" class="text">
                    <tr>
                        <td align="left" colspan="2" style="padding-left: 40px;" ><strong>Search By Month:</strong>
                            &nbsp;&nbsp;
                            <select name="month_report" class="" id="month1" style="width:130px;" onchange="validateMonth();">
                                <!--<option value="">Select Month</option>-->
                                <?php generate_month($monthArray, $end_month, $end_year, $start_month, $start_year, $month_report); ?>
                            </select>

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
                        <td align="center" width='75%' style="padding-left:80px;" colspan="2" >
                            <input class="button" style="width:60px;" type="submit" name="search" value="Search" />
                            <?php //if (!empty($_REQUEST['panelist_type']) || !empty($_REQUEST['competi_id'])): ?>
                            <input class="button" style="width:60px;" type="submit" name="clear" value="Clear" />
                            <?php // endif; ?>
                        </td>
                        <?php if(isset($_SESSION['sort']) && !empty($_SESSION['sort'])){ ?>
                        <input type="hidden" name="sort" value="<?php echo $sort; ?>">
                        <input type="hidden" name="order" value="<?php echo $order; ?>">
                        <?php } ?>
                    </tr>
                    <tr>
                         <td align="right" width='10%'><input class="button" style="width:60px;margin-right:0px;" type="submit" name="export" value="Export" />
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
                            <!--<td  align="left" style="width:3%;"  class="adminhead" height='15px'><input name="setUnset" onclick="setAll()" value="on" type="checkbox"></td> -->
                            <td align="left" style="width:10%;" class="adminhead" height='15px' ><b>Panelist ID</b><?php //doSort($sort, $order, 1, $p, $competi_id,$month_report); ?></td>
                            <td align="left" style="width:10%;" class="adminhead" height='15px' ><b>Email Pieces</b><?php //doSort($sort, $order, 2, $p, $competi_id, $month_report); ?></td>
                            <td align="left" style="width:10%;" class="adminhead" height='15px' ><b>Email Point</b><?php //doSort($sort, $order, 3, $p, $competi_id, $month_report); ?></td>
                            <td align="left" style="width:10%;" class="adminhead" height='15px' ><b>Incentive Value($)</b><?php //doSort($sort, $order, 8, $p, $competi_id, $month_report); ?></td>
                            <td align="left" style="width:10%;" class="adminhead" height='15px' ><b>First Name</b><?php //doSort($sort, $order, 9, $p, $competi_id,$month_report); ?></td>
                            <td align="left" style="width:10%;" class="adminhead" height='15px' ><b>Last Name</b><?php //doSort($sort, $order, 10, $p, $competi_id,$month_report); ?></td>
                            <td align="left" style="width:5%;" class="adminhead" height='15px' ><b>Address</b><?php //doSort($sort, $order,11 , $p, $competi_id, $month_report);  ?></td>
                            <td align="left" style="width:5%;" class="adminhead" height='15px' ><b>City</b><?php //doSort($sort, $order, 12, $p, $competi_id,$month_report); ?></td>
                            <td align="left" style="width:5%;" class="adminhead" height='15px' ><b>State</b><?php //doSort($sort, $order, 13, $p, $competi_id,$month_report); ?></td>
                            <td align="left" style="width:5%;" class="adminhead" height='15px' ><b>Zip</b><?php //doSort($sort, $order, 14, $p, $competi_id,$month_report); ?></td>
                            <td align="left" style="width:5%;" class="adminhead" height='15px' ><b>Email</b><?php //doSort($sort, $order, 15, $p, $competi_id,$month_report); ?></td>
                            <td align="left" style="width:10%;" class="adminhead" height='15px' ><b>Date</b><?php //doSort($sort, $order, 16, $p, $competi_id,$month_report); ?></td>

                        </tr>
                        <?php
                        $className = '';
                        while ($row = $DRW->fetch_assoc($rs)) {
                            $panelist_id = $row['panelist_id'];
                            $competi_id1 = $row['competi_id'];
                            
                            $chkSql_month = "SELECT email_piece,email_piece_point,entry_date,insert_date FROM  cscan_provider_scoring_monthly_reports WHERE competi_id='".$competi_id1."' $conditions";
                            $chkQuery_month = $DRW->query($chkSql_month, $DRW_read);
                            $email_piece_point='0';
                            $email_piece='0';
                            if($DRW->num_rows($chkQuery_month) > 0){
                                $rowDataCheckMonth = $DRW->fetch_assoc($chkQuery_month);
                                $email_piece=$rowDataCheckMonth['email_piece'];
                                $email_piece_point=$rowDataCheckMonth['email_piece_point'];
                            }
                            $incentive_value='0';
                            if($email_piece_point>0){
                             $incentive_value =20;   
                            }
                            
                            if ($className == 'selected-bg')
                                $className = 'white-bg';
                            else
                                $className = 'selected-bg';
                            ?>
                            <tr valign=top class="<?php echo $className; ?>">
                                <!--<td align="left">
                                    <input class="chkresetbag" name="competiids[]" value="<?php echo $row['panelist_id'] . '###' . $row['competi_id']; ?>" type="checkbox">
                                </td>-->
                                <td align="left">
                                    <?php echo $row['competi_id']; ?>
                                   <!--<a href="javascript:void(0)" onclick="openpopup_point('<?php echo trim($row['competi_id']); ?>','<?php echo $insert_date1; ?>');" >
                                        <?php echo $row['competi_id']; ?>
                                   </a>-->
                                </td>
                                
                                <td align="left"><?php echo $email_piece; ?></td>
                                <td align="left"><?php echo $email_piece_point; ?></td>
                                <td align="left"><?php echo $incentive_value; ?></td>
                                <td align="left"><?php echo $row['first_name']; ?></td>
                                <td align="left"><?php echo $row['last_name']; ?></td>
                                <td align="left"><?php echo $row['address']; ?></td>
                                <td align="left"><?php echo $row['city']; ?></td>
                                <td align="left"><?php echo $row['state']; ?></td>
                                <td align="left"><?php echo $row['postalcode']; ?></td>
                                <td align="left"><?php echo $row['email']; ?>
                                </td>
                                <td align="left"><?php echo date('M-Y', strtotime($month_report)); ?>
                                </td>
                                
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
                    $addparam3 = '';
                    if (!empty($_REQUEST['b'])) {
                        $addparam = "&b=" . $_REQUEST['b'];
                    }
                    if (!empty($_REQUEST['r'])) {
                        $addparam2 = "&r=" . $_REQUEST['r'];
                    }
                    if (!empty($_REQUEST['d'])) {
                        $addparam3 = "&d=" . $_REQUEST['d'];
                    }
                    //first and previous only if not on first
                    if ($limstart > 0) {
                        if ($limstart >= $limiter)
                            $prev = $limstart - $limiter;
                        else
                            $prev = 0;
                        $firstlink = "[<a href=\"{$_SERVER['PHP_SELF']}?p=0&competi_id={$competi_id}&month_report={$month_report}&sort={$sort}&order={$order}$addparam$addparam2$addparam3\">First</a>]";
                        $prevlink = "<a href=\"{$_SERVER['PHP_SELF']}?p=$prev&competi_id={$competi_id}&month_report={$month_report}&sort={$sort}&order={$order}$addparam$addparam2$addparam3\">&laquo; Prev $limiter</a>";
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
                            $middlelinks .= "<a href=\"{$_SERVER['PHP_SELF']}?p=$startnum&competi_id={$competi_id}&month_report={$month_report}&sort={$sort}&order={$order}$addparam$addparam2$addparam3\">" . ($i + 1) . "</a> ";
                        } else
                            $middlelinks .= ($i + 1) . ' ';
                    }
                    //next and last if not on last
                    if ($limstart < $rowcnt && (($limstart + ($limiter * 2)) < $rowcnt || ($rowcnt - ($limstart + $limiter)) > 0)) {
                        $next = $limstart + $limiter;
                        $nextlink = "<a href=\"{$_SERVER['PHP_SELF']}?p=$next&competi_id={$competi_id}&month_report={$month_report}&sort={$sort}&order={$order}$addparam$addparam2$addparam3\">Next $limiter &raquo;</a>";
                        $lastlink = "[<a href=\"{$_SERVER['PHP_SELF']}?p=" . (($numbers - 1) * $limiter) . "&competi_id={$competi_id}&month_report={$month_report}&sort={$sort}&order={$order}$addparam$addparam2$addparam3\">Last</a>]";
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


