<?php
$ALLOW_GROUPS = array(82);
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
//$tdt = date('Y-m-d');
$order_by = '';
$order_bytxt = ' ';
$_SESSION['month_report2']='';
$_SESSION['order']='';
$_SESSION['sort']='';
$order = 0;
$sort = '';
$competi_id = '';
$conditions = ' Where active=1 And parent_panelist_id=0 ';
$month_report = date('Y-m');
$panelist_type ='';
if (!empty($_REQUEST['competi_id'])) {
    $competi_id = trim($_REQUEST['competi_id']);
    $seasrcharray = array("'", '"', ' ');
    $replacearray = array("", "", "");
    $competiarray = explode(",", str_replace($seasrcharray, $replacearray, $competi_id));
    $newarraystr = trim(implode("','", $competiarray));
    $conditions .= "  AND competi_id in( '" . $newarraystr . "') ";
}
if (!empty($_REQUEST['month_report'])) {
    $month_report = $_SESSION['month_report2']= $_REQUEST['month_report'];
    $conditions2 = "  Where LEFT(insert_date,7)>='" . $month_report . "' AND LEFT(insert_date,7)<='" . $month_report . "'";
}else{
  $conditions2 = "  Where LEFT(insert_date,7)>='" . $month_report . "' AND LEFT(insert_date,7)<='" . $month_report . "'";  
}

if (!empty($_REQUEST['clear']) && trim($_REQUEST['clear']) == 'Clear') {
    header("location:consumer_monthly_nonparticipation_report.php");
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
                $order_by = 'first_name';
                break;
            case '3':
                $order_by = 'last_name';
                break;
            case '4':
                $order_by = 'address';
                break;
            case '5':
                $order_by = 'city';
                break;
            case '6':
                $order_by = 'state';
                break;
            case '7':
                $order_by = 'postalcode';
                break;
            case '8':
                $order_by = 'email';
                break;
            case '9':
                $order_by = 'insert_date';
                break;
            default:
                $order_by = 'panelist_id';
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
$group_bytxt = " GROUP BY csdr.panelist_id "; 
 if (empty(trim($order_bytxt)) && empty(trim($order_by))) {
    $order_bytxt = ' ORDER BY ';
    $order_by = ' panelist_id ASC ';
}   
if (!empty($_REQUEST['export']) && trim($_REQUEST['export']) == 'Export') {//pr($_POST);die;
    $arrExport = array();    
    $exp_sql = "SELECT SQL_CALC_FOUND_ROWS cp.panelist_id, cp.competi_id,cp.first_name,cp.last_name,cp.address,cp.city,cp.state,cp.postalcode,cp.email FROM cscan_panelists cp $conditions AND competi_id NOT IN (SELECT competi_id from cscan_consumer_scoring_daily_report $conditions2 ) AND panelist_id NOT IN(select panelist_id from cscan_removing_panelists where product_count<=0) $order_bytxt $order_by ";
    $exp_rs = $DRW->query($exp_sql, $DRW_read2);
    if (!empty($exp_rs)) {               
        $arrExport['data'][] = array("Panelist ID", "Date", "First Name", "Last Name",
            "Address", "City", "State", "Zip", "Email", "Bucket");
        while ($exp_row = $DRW->fetch_assoc($exp_rs)) {            
            $insert_date = date('M-Y', strtotime($month_report));
            $arrExport['data'][] = array($exp_row['competi_id'], $insert_date, $exp_row['first_name'], $exp_row['last_name'],
                $exp_row['address'], $exp_row['city'], $exp_row['state'],
                $exp_row['postalcode'], $exp_row['email'], 0,
            );
        }
    }
    download_send_headers("consumer_monthly_nonparticipation_report" . date("Y-m-d") . ".csv");
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
    if ($sort == ($dosort * -1) || $sort != $dosort) {
        print "$spacer<a href=\"" . $_SERVER['PHP_SELF'] . "?competi_id={$competi_id}&month_report={$month_report}&sort=$dosort&order=$order&p=$p$addparam\" class=\"blue\">sort</a>";
    } else {
        print "$spacer<a href=\"" . $_SERVER['PHP_SELF'] . "?competi_id={$competi_id}&month_report={$month_report}&sort=$dosort&order=$order&p=$p$addparam\" class=\"blue\">sort</a>";
    }
}

$sql = "SELECT SQL_CALC_FOUND_ROWS cp.panelist_id, cp.competi_id,cp.first_name,cp.last_name,cp.address,cp.city,cp.state,cp.postalcode,cp.email FROM cscan_panelists cp $conditions AND competi_id NOT IN (SELECT competi_id from cscan_consumer_scoring_daily_report $conditions2 ) AND panelist_id NOT IN(select panelist_id from cscan_removing_panelists where product_count<=0) $order_bytxt $order_by LIMIT $p, $limit";
//echo $sql;
$rs = $DRW->query($sql, $DRW_read);
$numquery = $DRW->query("SELECT FOUND_ROWS()", $DRW_read); 
$nrow = $DRW->fetch_row($numquery);
$numrows = $nrow[0];
$sql_check = "SELECT count(*) FROM cscan_panelists Where active=1 And parent_panelist_id=0 AND panelist_id NOT IN(select panelist_id from cscan_removing_panelists where product_count<=0)";
$rs_check = $DRW->query($sql_check, $DRW_read);
$totalrow = $DRW->fetch_row($rs_check);
$total_active_panelists = $totalrow[0];
?>
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<table width='100%' border='0' cellspacing='0' cellpadding='8' class='text' rules='none' bordercolor='#B9B9B9' align='center' style='border-collapse:collapse'>
    <tr><td class="adminhead" align='center'>Consumer Non Participation Monthly Report</td></tr>
    <tr><td align='right'>

            <a href="consumer_scoring_monthly_report.php" class="button" style="padding:5px;text-decoration: none;float:right;margin-right: 10px; color: #fff;">Back</a>
            
        </td></tr>
    <tr>
        <td class="bodyText">
            <form method="request" name="rangeForm" action="<?php echo $_SERVER['PHP_SELF']; ?>" style="display:inline;">    
                <table width='100%' border="0" cellspacing="0" cellpadding="10" class="text">
                    <tr>
                        <td align="left" style="padding-left: 40px;" colspan="2"><strong>Search By Month:</strong>
                            &nbsp;&nbsp;
                            <select name="month_report" class="" id="month1" style="width:130px;" onchange="validateMonth();">
                                <!--<option value="">Select Month</option>-->
                                <?php generate_month($monthArray, $end_month, $end_year, $start_month, $start_year, $month_report); ?>
                            </select>

                        </td>
                    </tr>

                    <tr>
                        <td align="left" style="padding-left: 75px;" colspan="2"><strong>Search By:</strong>
                            &nbsp;&nbsp;                            
                            <input type="text" id="competi_id" placeholder="Panelist ID" style="width:80%;" name="competi_id"   class="input_box2345" value="<?php echo $competi_id; ?>" />
                            <span style="text-align:center;width:100%;float:left;">( Please enter multiple panelist id with comma separated. )</span>
                        </td>
                    </tr>
                    <tr>
                        <td align="right" width='30%' style="padding-left: 66px;"  >
                            <input class="button" style="width:60px;" type="submit" name="search" value="Search" />
                            <?php //if (!empty($_REQUEST['panelist_type']) || !empty($_REQUEST['competi_id'])): ?>
                            <input class="button" style="width:60px;" type="submit" name="clear" value="Clear" />
                            <?php // endif; ?>
                        </td>
                        <?php if(isset($_SESSION['sort']) && !empty($_SESSION['sort'])){ ?>
                        <input type="hidden" name="sort" value="<?php echo $sort; ?>">
                        <input type="hidden" name="order" value="<?php echo $order; ?>">
                        <?php } ?>
                        <td align="right"  width='10%'><input class="button" style="width:60px;margin-right: 10px;" type="submit" name="export" value="Export" />
                        </td>
                    </tr>
                    <tr>
                        <td align="left" style="padding-left: 75px;" colspan="2" class='text'>
                            <strong>Total Active Panelists:</strong> <?php echo $total_active_panelists;?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <strong>Non Participated Panelists:</strong> <?php echo $numrows;?>
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
                            <td align="left" style="width:12%;" class="adminhead" height='15px' ><b>Panelist ID</b><?php doSort($sort, $order, 1, $p, $competi_id,$month_report); ?></td>
                            <td align="left" style="width:8%;" class="adminhead" height='15px' ><b>Date</b><?php //doSort($sort, $order, 9, $p, $competi_id,$month_report); ?></td>
                            <td align="left" style="width:12%;" class="adminhead" height='15px' ><b>First Name</b><?php doSort($sort, $order, 2, $p, $competi_id,$month_report); ?></td>
                            <td align="left" style="width:12%;" class="adminhead" height='15px' ><b>Last Name</b><?php doSort($sort, $order, 3, $p, $competi_id,$month_report); ?></td>
                            <td align="left" style="width:10%;" class="adminhead" height='15px' ><b>Address</b><?php //doSort($sort, $order,4 , $p, $competi_id, $panelist_type);  ?></td>
                            <td align="left" style="width:8%;" class="adminhead" height='15px' ><b>City</b><?php doSort($sort, $order, 5, $p, $competi_id,$month_report); ?></td>
                            <td align="left" style="width:8%;" class="adminhead" height='15px' ><b>State</b><?php doSort($sort, $order, 6, $p, $competi_id,$month_report); ?></td>
                            <td align="left" style="width:8%;" class="adminhead" height='15px' ><b>Zip</b><?php doSort($sort, $order, 7, $p, $competi_id,$month_report); ?></td>
                            <td align="left" style="width:10%;" class="adminhead" height='15px' ><b>Email</b><?php doSort($sort, $order, 8, $p, $competi_id,$month_report); ?></td>
                            <td align="left" style="width:5%;" class="adminhead" height='15px' ><b>Bucket</b><?php //doSort($sort, $order, 9, $p, $competi_id,$month_report);  ?></td>

                        </tr>

                        <?php
                        $className = '';
                        while ($row = $DRW->fetch_assoc($rs)) {
                            $insert_date = date('M-Y', strtotime($month_report));
                            $insert_date1 = $month_report;
                            
                            if ($className == 'selected-bg')
                                $className = 'white-bg';
                            else
                                $className = 'selected-bg';
                            ?>
                            <tr valign=top class="<?php echo $className; ?>">
                                <td align="left">
                                    <?php echo $row['competi_id']; ?>                                 
                                </td>
                                <td align="left"><?php echo $insert_date; ?>
                                </td>
                                <td align="left"><?php echo $row['first_name']; ?></td>
                                <td align="left"><?php echo $row['last_name']; ?></td>
                                <td align="left"><?php echo $row['address']; ?></td>
                                <td align="left"><?php echo $row['city']; ?></td>
                                <td align="left"><?php echo $row['state']; ?></td>
                                <td align="left"><?php echo $row['postalcode']; ?></td>
                                <td align="left"><?php echo $row['email']; ?>
                                </td>
                                <td align="left">0</td>
                                
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
<form name="refreshdatafrm" id="refreshdatafrm" method="post" action="">
    <input type="hidden" name="refresh" id="refresh" value="1">

</form>
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