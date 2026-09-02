<?php
$ALLOW_GROUPS = array(105);
require_once("../auth_auth.php");
require_once('../includes/ehLog.php');
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
$start_year = 2021;
//$tdt = date('Y-m-d');
$order_by = '';
$order_bytxt = ' ';
$_SESSION['month_report']='';
$_SESSION['order']='';
$_SESSION['sort']='';
$order = 0;
$sort = '';
$conditions = ' Where ';
$month_report = date('Y-m');
if (!empty($_REQUEST['month_report'])) {
    $month_report = $_SESSION['month_report']= $_REQUEST['month_report'];
    $conditions .= "  LEFT(time_period,7)>='" . $month_report . "' AND LEFT(time_period,7)<='" . $month_report . "'";
}else{
  $conditions .= "  LEFT(time_period,7)>='" . $month_report . "' AND LEFT(time_period,7)<='" . $month_report . "'";  
}

if (!empty($_REQUEST['clear']) && trim($_REQUEST['clear']) == 'Clear') {
    header("location:digital_records.php");
}

if (!empty($_REQUEST)) {
    if (!empty($_REQUEST['sort']))
        $sort =$_SESSION['sort']= trim($_REQUEST['sort']);
    if (!empty($_REQUEST['order']))
        $order =$_SESSION['order']= trim($_REQUEST['order']);
    if (!empty($sort)) {
        switch ($sort) {
            case '1':
                $order_by = 'company_id';
                break;
            case '2':
                $order_by = 'advertiser_name';
                break;
            case '3':
                $order_by = 'location';
                break;
            case '4':
                $order_by = 'spend';
                break;
            case '5':
                $order_by = 'impressions';
                break;
            case '6':
                $order_by = 'time_period';
                break;
            case '7':
                $order_by = 'mchannel_id';
                break;
            case '8':
                $order_by = 'digital_source_id';
                break;
            default:
                $order_by = 'id';
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
$group_bytxt = " "; 
 if (empty(trim($order_bytxt)) && empty(trim($order_by))) {
    $order_bytxt = ' ORDER BY ';
    $order_by = ' id ASC ';
}   
if (!empty($_REQUEST['export']) && trim($_REQUEST['export']) == 'Export') {//pr($_POST);die;
    $arrExport = array();
    $exp_sql = "SELECT SQL_CALC_FOUND_ROWS id,channel,mchannel_id,digital_source_id,spend,impressions,company_id,advertiser_name,advertiser_domain,publisher,monitored_page,campaign_landing_page,location,time_period,insert_date FROM cscan_biscience_digital_fields $conditions $group_bytxt $order_bytxt $order_by ";
    $exp_rs = $DRW->query($exp_sql, $DRW_read2);
    if (!empty($exp_rs)) {
        $arrExport['data'][] = array("Company","Channel","Digital Source","Advertiser Name","Advertiser Domain","Publisher","Monitored Page","Campaign Landing Page","Location","Spend","Impressions","Time Period","Process Date",);
        while ($exp_row = $DRW->fetch_assoc($exp_rs)) {
            $spend = $exp_row['spend'];
            $impressions=$exp_row['impressions'];
            $sql = "SELECT companyID, companyName FROM cscan_company WHERE companyID='".$exp_row['company_id']."'";
            $result = $DRW->query($sql,$DRW_read);
            $company='';
            if ($DRW->num_rows($result) > 0) {
                $rowComp = $DRW->fetch_array($result);
                $company = $rowComp['companyName'];
            }
            $advertiser_name=$exp_row['advertiser_name'];
            $advertiser_domain=$exp_row['advertiser_domain'];
            $publisher=$exp_row['publisher'];
            $monitored_page=$exp_row['monitored_page'];
            $campaign_landing_page=$exp_row['campaign_landing_page'];
            $location=$exp_row['location'];
            $mchannel_id=$exp_row['mchannel_id'];
            if($mchannel_id==5){
                $mchannel="Online Display";
            }elseif($mchannel_id==10){
                $mchannel="Online Video";
            }else{
               $mchannel=''; 
            }
            $digital_source_id=$exp_row['digital_source_id'];
            if($digital_source_id==1){
                $digital_source="Desktop";
            }elseif($digital_source_id==2){
                $digital_source="Mobile";
            }elseif($digital_source_id==3){
                $digital_source="In App Android";
            }elseif($digital_source_id==4){
                $digital_source="In App IOS";
            }elseif($digital_source_id==5){
                $digital_source="Social";
            }else{
               $digital_source=''; 
            }
            $time_period=$exp_row['time_period'];
            //$insert_date = date('M-Y', strtotime($exp_row['insert_date']));
            $insert_date = $exp_row['insert_date'];
            $arrExport['data'][] = array($company,$mchannel,$digital_source,$advertiser_name,$advertiser_domain,$publisher,$monitored_page,$campaign_landing_page,$location,$spend,$impressions,$time_period,$insert_date,
            );
            
        }
    }
    download_send_headers("digital_records_" . date("Y-m-d") . ".csv");
    echo array2csv($arrExport);
    die();
}
include 'top.php';
$limit = 50;
if (isset($_GET['p']))
    $p = trim($_GET['p']);
else
    $p = 0;

function doSort($sort, $order, $dosort, $p,$month_report, $spacer = ' : ') {
    if (empty($order)) {
        $order = 1;
    } else {
        $order = 0;
    }
    
    if ($sort == ($dosort * -1) || $sort != $dosort) {
        print "$spacer<a href=\"" . $_SERVER['PHP_SELF'] . "?month_report={$month_report}&sort=$dosort&order=$order&p=$p\" class=\"blue\">sort</a>";
    } else {
        print "$spacer<a href=\"" . $_SERVER['PHP_SELF'] . "?month_report={$month_report}&sort=$dosort&order=$order&p=$p\" class=\"blue\">sort</a>";
    }
}


$sql = "SELECT SQL_CALC_FOUND_ROWS id,channel,mchannel_id,digital_source_id,spend,impressions,company_id,advertiser_name,location,time_period,insert_date FROM cscan_biscience_digital_fields  $conditions $group_bytxt $order_bytxt $order_by LIMIT $p, $limit";
//echo $sql;
$rs = $DRW->query($sql, $DRW_read2);
$numquery = $DRW->query("SELECT FOUND_ROWS()", $DRW_read2); 
$nrow = $DRW->fetch_row($numquery);
$numrows = $nrow[0];
?>
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<table width='100%' border='0' cellspacing='0' cellpadding='8' class='text' rules='none' bordercolor='#B9B9B9' align='center' style='border-collapse:collapse'>
    <tr><td class="adminhead" align='center'>Digital Records</td></tr>
    <tr>
        <td class="bodyText">
            <form method="request" name="rangeForm" action="<?php echo $_SERVER['PHP_SELF']; ?>" style="display:inline;">    
                <table width='100%' border="0" cellspacing="0" cellpadding="10" class="text">
                    <tr>
                        <td align="right" colspan="2" style="padding-left: 40px;" ><strong>Search By Month:</strong>
                            &nbsp;&nbsp;
                            <select name="month_report" class="" id="month1" style="width:130px;" onchange="validateMonth();">
                                <!--<option value="">Select Month</option>-->
                                <?php generate_month($monthArray, $end_month, $end_year, $start_month, $start_year, $month_report); ?>
                            </select>

                        </td>
                    </tr>

                   <!-- <tr>
                        <td align="left" style="padding-left: 75px;" colspan="3"><strong>Search By:</strong>
                            &nbsp;&nbsp;
                           
                            <input type="text" id="competi_id" placeholder="Panelist ID" style="width:80%;" name="competi_id"   class="input_box2345" value="<?php echo $competi_id; ?>" />
                            <span style="text-align:center;width:100%;float:left;">( Please enter multiple panelist id with comma separated. )</span>
                        </td>
                    </tr>-->
                    <tr>
                        <td align="right" width='50%' style="padding-left: 66px;" colspan="2" >
                            <input class="button" style="width:60px;" type="submit" name="search" value="Search" />
                            <?php //if (!empty($_REQUEST['panelist_type']) || !empty($_REQUEST['competi_id'])): ?>
                            <input class="button" style="width:60px;" type="submit" name="clear" value="Clear" />
                            <?php // endif; ?>
                        </td>
                        <?php if(isset($_SESSION['sort']) && !empty($_SESSION['sort'])){ ?>
                        <input type="hidden" name="sort" value="<?php echo $sort; ?>">
                        <input type="hidden" name="order" value="<?php echo $order; ?>">
                        <?php } ?>
                       
                        <td align="right"  width='40%'><input class="button" style="width:60px;margin-right: 10px;" type="submit" name="export" value="Export" />
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
                            <td align="left" style="width:6%;" class="adminhead" height='15px' ><b>Sr. No.</b><?php //doSort($sort, $order, 2, $p, $month_report); ?></td>
                            <td align="left" style="width:10%;" class="adminhead" height='15px' ><b>Company</b><?php doSort($sort, $order, 1, $p, $month_report); ?></td>
                            <td align="left" style="width:10%;" class="adminhead" height='15px' ><b>Channel</b><?php doSort($sort, $order, 7, $p,$month_report); ?></td>
                            <td align="left" style="width:10%;" class="adminhead" height='15px' ><b>Digital Source</b><?php doSort($sort, $order, 8, $p,$month_report); ?></td>
                             <td align="left" style="width:10%;" class="adminhead" height='15px' ><b>Advertiser Name</b><?php doSort($sort, $order, 2, $p, $month_report); ?></td>
                            <td align="left" style="width:10%;" class="adminhead" height='15px' ><b>Location</b><?php doSort($sort, $order, 3, $p, $month_report); ?></td>
                            <td align="left" style="width:10%;" class="adminhead" height='15px' ><b>Spend</b><?php doSort($sort, $order, 4, $p,$month_report); ?></td>
                            <td align="left" style="width:10%;" class="adminhead" height='15px' ><b>Impressions</b><?php doSort($sort, $order, 5, $p,$month_report); ?></td>
                            <td align="left" style="width:10%;" class="adminhead" height='15px' ><b>Time Period</b><?php doSort($sort, $order, 6, $p,$month_report); ?></td>
                            <td align="left" style="width:10%;" class="adminhead" height='15px' ><b>Process Date</b><?php //doSort($sort, $order, 9, $p,$month_report); ?></td>
                        </tr>

                        <?php
                        $className = '';
                        //$serial = ($p * $limit) + 1;
                        $serial = $p + 1;
                        while ($row = $DRW->fetch_assoc($rs)) {
                            $ids = $row['id'];
                            $sql = "SELECT companyID, companyName FROM cscan_company WHERE companyID='".$row['company_id']."'";
                            $result = $DRW->query($sql,$DRW_read);
                            $company='';
                            if ($DRW->num_rows($result) > 0) {
                            $rowComp = $DRW->fetch_array($result);
                            $company = $rowComp['companyName'];
                            }
                            $spend = $row['spend'];
                            $impressions=$row['impressions'];
                            $advertiser_name=$row['advertiser_name'];
                            $location=$row['location'];
                            $mchannel_id=$row['mchannel_id'];
                            if($mchannel_id==5){
                                $mchannel="Online Display";
                            }elseif($mchannel_id==10){
                                $mchannel="Online Video";
                            }else{
                               $mchannel=''; 
                            }
                            $digital_source_id=$row['digital_source_id'];
                            if($digital_source_id==1){
                                $digital_source="Desktop";
                            }elseif($digital_source_id==2){
                                $digital_source="Mobile";
                            }elseif($digital_source_id==3){
                                $digital_source="In App Android";
                            }elseif($digital_source_id==4){
                                $digital_source="In App IOS";
                            }elseif($digital_source_id==5){
                                $digital_source="Social";
                            }else{
                               $digital_source=''; 
                            }
                            $time_period=$row['time_period'];
                            //$insert_date = date('M-Y', strtotime($row['insert_date']));
                            $insert_date1 = date('Y-m-d', strtotime($row['insert_date']));
                            $sr_no = $serial++;
                            
                            if ($className == 'selected-bg')
                                $className = 'white-bg';
                            else
                                $className = 'selected-bg';
                            ?>
                            <tr valign=top class="<?php echo $className; ?>">
                                <td align="left"><?php echo $sr_no; ?></td>
                                <td align="left"><?php echo $company; ?></td>
                                <td align="left"><?php echo $mchannel; ?></td>
                                <td align="left"><?php echo $digital_source; ?></td>
                                <td align="left"><?php echo $advertiser_name; ?></td>
                                 <td align="left"><?php echo $location; ?></td>
                                <td align="left"><?php echo $spend; ?></td>
                                <td align="left"><?php echo $impressions; ?></td>
                                <td align="left"><?php echo $time_period; ?></td>
                                <td align="left"><?php echo $row['insert_date']; ?></td>
                                
                            </tr>
                        <?php  } ?>
                               
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
                    
                    //first and previous only if not on first
                    if ($limstart > 0) {
                        if ($limstart >= $limiter)
                            $prev = $limstart - $limiter;
                        else
                            $prev = 0;
                        $firstlink = "[<a href=\"{$_SERVER['PHP_SELF']}?p=0&month_report={$month_report}&sort={$sort}&order={$order}\">First</a>]";
                        $prevlink = "<a href=\"{$_SERVER['PHP_SELF']}?p=$prev&month_report={$month_report}&sort={$sort}&order={$order}\">&laquo; Prev $limiter</a>";
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
                            $middlelinks .= "<a href=\"{$_SERVER['PHP_SELF']}?p=$startnum&month_report={$month_report}&sort={$sort}&order={$order}\">" . ($i + 1) . "</a> ";
                        } else
                            $middlelinks .= ($i + 1) . ' ';
                    }
                    //next and last if not on last
                    if ($limstart < $rowcnt && (($limstart + ($limiter * 2)) < $rowcnt || ($rowcnt - ($limstart + $limiter)) > 0)) {
                        $next = $limstart + $limiter;
                        $nextlink = "<a href=\"{$_SERVER['PHP_SELF']}?p=$next&month_report={$month_report}&sort={$sort}&order={$order}\">Next $limiter &raquo;</a>";
                        $lastlink = "[<a href=\"{$_SERVER['PHP_SELF']}?p=" . (($numbers - 1) * $limiter) . "&month_report={$month_report}&sort={$sort}&order={$order}\">Last</a>]";
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


