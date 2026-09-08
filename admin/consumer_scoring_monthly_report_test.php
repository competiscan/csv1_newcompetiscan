<?php
$ALLOW_GROUPS = array(82);
require_once("../auth_auth.php");
require_once('../includes/ehLog.php');
$msg = '';
$success = '';
if (!empty($_REQUEST['msg'])) {
    $msg = $_REQUEST['msg'];
}
if (!empty($_POST['refresh'])) {
    $refresh = $_POST['refresh'];
   //exec("/usr/bin/php consumer_scoring_monthly_cron.php");
    exec("cd ../; /usr/bin/php consumer_scoring_monthly_cron.php > /dev/null 2>&1 &");
    sleep(3);
    ob_end_clean();
    header("Location: {$_SERVER['PHP_SELF']}");
    exit;
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

$tdt = date('Y-m-d');
$order_by = '';
//$order_by = ' ORDER BY ';
$order_bytxt=' ';

$order = 0;
$sort = '';
$competi_id = '';
$conditions = ' Where 1 ';
$panelist_type = '';
if (!empty($_REQUEST['competi_id'])) {
    $competi_id = trim($_REQUEST['competi_id']);
    $seasrcharray = array("'", '"', ' ');
    $replacearray = array("", "", "");
    $competiarray = explode(",", str_replace($seasrcharray, $replacearray, $competi_id));
    $newarraystr = trim(implode("','", $competiarray));
    $conditions .= "  AND competi_id in( '" . $newarraystr . "') ";
    //$conditions.=    "  AND competi_id like '%".$competi_id."%' ";
}
if (!empty($_REQUEST['panelist_type'])) {
    $panelist_type = $_REQUEST['panelist_type'];
    if ($panelist_type == '1') {
        $conditions .= "  AND total_point>='" . MAX_RESET_POINT . "' ";
    } else if ($panelist_type == '2') {
        $conditions .= "  AND (bag_remaining=0 OR bag_remaining is NULL)";
    }
}
//  $tdt = trim($_REQUEST['tdt']);
if (!empty($_REQUEST['clear']) && trim($_REQUEST['clear']) == 'Clear') {
    // $fdt = date('Y-m-d', strtotime("-6 day"));
    $tdt = date('Y-m-d');
    header("location:consumer_scoring_monthly_report_test.php");
}
//$cur_month = $cur_year = $conditions = '';
$filter = array();
if (!empty($_REQUEST)) {
    if (!empty($_REQUEST['sort']))
        $sort = trim($_REQUEST['sort']);
    if (!empty($_REQUEST['order']))
        $order = trim($_REQUEST['order']);
    if (!empty($sort)) {
        switch ($sort) {
            case '1':
                $order_by = 'competi_id';
                break;
            case '2':
                $order_by = 'direct_mail_piece';
                break;
            case '3':
                $order_by = 'direct_mail_point';
                break;
            case '4':
                $order_by = 'email_piece';
                break;
            case '5':
                $order_by = 'email_piece_point';
                break;
            case '6':
                $order_by = 'digital_point';
                break;
            case '7':
                $order_by = 'total_point';
                break;
            case '8':
                $order_by = 'bag_remaining';
                break;
            case '9':
                $order_by = 'bonus_point';
                break;
            case '10':
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
if(!empty($order_by))
   $order_bytxt=" ORDER BY ";
if (!empty($_REQUEST['export']) && trim($_REQUEST['export']) == 'Export') {//pr($_POST);die;
    $arrExport = array();
    //$conditions =   "  WHERE entry_date>='".$fdt."' AND entry_date<='".$tdt."' ";
    $exp_sql = "SELECT panelist_id, competi_id, direct_mail_piece, 
                direct_mail_point, email_piece, email_piece_point, 
                digital_point, bonus_point,total_point, bag_remaining, insert_date 
                FROM cscan_consumer_scoring_monthly_report 
                 $conditions  $order_bytxt $order_by";
    $exp_rs = $DRW->query($exp_sql, $DRW_read2);
    if (!empty($exp_rs)) {
        //total email sent        
        $arrExport['data'][] = array("Panelist ID", "Date", "Direct Mail Piece",
            "Direct Mail Point", "EMail Piece", "Email Point", "Digital Point", "Bonus Point", "Total Point", "Bags Remaining");
        while ($exp_row = $DRW->fetch_assoc($exp_rs)) {
            $direct_mail_piece = $exp_row['direct_mail_piece'];
            $insert_date = date('M-Y', strtotime($exp_row['insert_date']));
            $direct_mail_point = $exp_row['direct_mail_point'];
            $email_piece = $exp_row['email_piece'];
            $email_piece_point = $exp_row['email_piece_point'];
            $digital_point = $exp_row['digital_point'];
            $bag_remaining = $exp_row['bag_remaining'];
            $bonus_point = $exp_row['bonus_point'];
            $total_point = $exp_row['total_point'];
            if (empty($bag_remaining))
                $bag_remaining = 0;
            $arrExport['data'][] = array($exp_row['competi_id'], $insert_date, $direct_mail_piece,
                $direct_mail_point, $email_piece, $email_piece_point,
                $digital_point, $bonus_point, $total_point, $bag_remaining,
            );
        }
    }
    download_send_headers("consumer_scoring_monthly_report" . date("Y-m-d") . ".csv");
    echo array2csv($arrExport);
    die();
}
include 'top.php';
$limit = 50;
if (isset($_GET['p']))
    $p = trim($_GET['p']);
else
    $p = 0;

function doSort($sort, $order, $dosort, $p, $competi_id, $panelist_type, $spacer = ' : ') {
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
        print "$spacer<a href=\"" . $_SERVER['PHP_SELF'] . "?competi_id={$competi_id}&panelist_type={$panelist_type}&sort=$dosort&order=$order&p=$p$addparam\" class=\"blue\">sort</a>";
    } else {
        print "$spacer<a href=\"" . $_SERVER['PHP_SELF'] . "?competi_id={$competi_id}&panelist_type={$panelist_type}&sort=$dosort&order=$order&p=$p$addparam\" class=\"blue\">sort</a>";
    }
}

if(empty(trim($order_bytxt)) && empty(trim($order_by))){
    $order_bytxt =' ORDER BY ';
    $order_by=' panelist_id ASC ';
}

$sql = "SELECT SQL_CALC_FOUND_ROWS id, panelist_id, competi_id, direct_mail_piece, direct_mail_point, email_piece, email_piece_point, 
        digital_point,bonus_point, total_point, bag_remaining, insert_date 
        FROM cscan_consumer_scoring_monthly_report 
        $conditions $order_bytxt $order_by LIMIT $p, $limit
        ";
//echo $sql;       
$rs = $DRW->query($sql, $DRW_read2);
$numquery = $DRW->query("SELECT FOUND_ROWS()", $DRW_read2);
$nrow = $DRW->fetch_row($numquery);
$numrows = $nrow[0];
?>
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<table width='100%' border='0' cellspacing='0' cellpadding='8' class='text' rules='none' bordercolor='#B9B9B9' align='center' style='border-collapse:collapse'>
    <tr><td class="adminhead" align='center'>Consumer Scoring Monthly Report</td></tr>
    <tr><td align='right'>
             
            <a href="consumer_scoring_report.php" class="button" style="padding:5px;text-decoration: none;float:right;margin-right: 10px; color: #fff;">Back</a>
            <?php 
            if(running_php_cmd('consumer_scoring_monthly_cron.php')){
                    echo '<div style="margin:5px 100px 0px 0px;"><em>Refreshing Data . . .</em></div>';
            }
            else{
            ?>
            <a href="#" class="button" style="padding:5px;text-decoration: none;float:right;margin-right: 100px; color: #fff;" onclick="return refreshdata();">Refresh</a>
            <?php }?>
        </td></tr>
    <tr>
        <td class="bodyText">
            <form method="request" name="rangeForm" action="<?php echo $_SERVER['PHP_SELF']; ?>" style="display:inline;">    
                <table width='100%' border="0" cellspacing="0" cellpadding="10" class="text">
                    <tr>
                        <td align="left" colspan="3"><strong>Search By:</strong>
                            &nbsp;&nbsp;
                            <strong>Panelist ID:</strong>
                            <input type="text" id="competi_id" style="width:80%;" name="competi_id"   class="input_box2345" value="<?php echo $competi_id; ?>" />
                            <span style="text-align:center;width:100%;float:left;">( Please enter multiple panelist id with comma separated. )</span>
                        </td>
                    </tr>
                    <tr>
                        <td  width='30%' style="padding-left: 66px;" colspan="2" ><strong>Panelist Type:</strong>
                            <select name="panelist_type" id="panelist_type" style="width:300px;">
                                <option value="">All</option>
                                <option value="1"<?php if ($panelist_type == '1') { ?> selected="selected" <?php } ?>> &gt; <?php echo MAX_RESET_POINT; ?> Points</option>
                                <option value="2" <?php if ($panelist_type == '2') { ?> selected="selected" <?php } ?>> 0 Bags</option>
                            </select>
                            <input class="button" style="width:60px;" type="submit" name="search" value="Search" />
<?php if (!empty($_REQUEST['panelist_type']) || !empty($_REQUEST['competi_id'])): ?>
                                <input class="button" style="width:60px;" type="submit" name="clear" value="Clear" />
<?php endif; ?>
                        </td>
                        <td align="right"  width='10%'><input class="button" style="width:60px;margin-right: 10px;" type="submit" name="export" value="Export" />
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
                            <td align="left" style="width:12%;" class="adminhead" height='15px' ><b>Panelist ID</b><?php doSort($sort, $order, 1, $p, $competi_id, $panelist_type); ?></td>
                            <td align="left" style="width:12%;" class="adminhead" height='15px' ><b>Date</b><?php doSort($sort, $order, 10, $p, $competi_id, $panelist_type); ?></td>
                            <td align="left" style="width:10%;" class="adminhead" height='15px' ><b>Direct Mail Pieces</b><?php doSort($sort, $order, 2, $p, $competi_id, $panelist_type); ?></td>
                            <td align="left" style="width:10%;"  style="width:5%;" class="adminhead" height='15px' ><b>Direct Mail Point</b><?php doSort($sort, $order, 3, $p, $competi_id, $panelist_type); ?></td>
                            <td align="left" style="width:10%;" class="adminhead" height='15px' ><b>Email Pieces</b><?php doSort($sort, $order, 4, $p, $competi_id, $panelist_type); ?></td>
                            <td align="left" style="width:10%;" class="adminhead" height='15px' ><b>Email Point</b><?php doSort($sort, $order, 5, $p, $competi_id, $panelist_type); ?></td>
                            <td align="left" style="width:10%;" class="adminhead" height='15px' ><b>Digital Point</b><?php doSort($sort, $order, 6, $p, $competi_id, $panelist_type); ?></td>
                            <td align="left" style="width:10%;" class="adminhead" height='15px' >
                                <b>Bonus Point</b><?php doSort($sort, $order, 9, $p, $competi_id, $panelist_type); ?></td>
                            <td align="left" style="width:10%;" class="adminhead" height='15px' ><b>Total Point</b> <?php doSort($sort, $order, 7, $p, $competi_id, $panelist_type); ?></td>
                            <td align="left" style="width:10%;" class="adminhead" height='15px' ><b>
                                    Bags Remaining</b><?php doSort($sort, $order, 8, $p, $competi_id, $panelist_type); ?></td>
                        </tr>
                        <?php
                        $className = '';
                        while ($row = $DRW->fetch_assoc($rs)) {
                            $ids = $row['id'];
                            $direct_mail_piece = $row['direct_mail_piece'];
                            $direct_mail_point = $row['direct_mail_point'];
                            $email_piece = $row['email_piece'];
                            $email_piece_point = $row['email_piece_point'];
                            $digital_point = $row['digital_point'];
                            $bag_remaining = $row['bag_remaining'];
                            $bonus_point = $row['bonus_point'];
                            $total_point = ($row['total_point']);
                            $insert_date = date('M-Y', strtotime($row['insert_date']));
                            if ($className == 'selected-bg')
                                $className = 'white-bg';
                            else
                                $className = 'selected-bg';
                            ?>
                            <tr valign=top class="<?php echo $className; ?>">
                                <td align="left">
        <?php echo $row['competi_id']; ?>
                                </td>
                                <td align="left"><?php echo $insert_date; ?></td>
                                <td align="left"><?php echo $direct_mail_piece; ?></td>
                                <td align="left"><?php echo $direct_mail_point; ?></td>
                                <td align="left"><?php echo $email_piece; ?></td>
                                <td align="left"><?php echo $email_piece_point; ?></td>
                                <td align="left"><?php echo $digital_point; ?></td>
                                <td align="left"><?php echo $bonus_point; ?>
                                </td>
                                <td align="left"><?php echo $total_point; ?></td>
                                <td align="left"><?php echo $bag_remaining; ?>
                                </td>
                            </tr>
    <?php }
    ?>
    <?php if (!empty($_REQUEST['b'])) { ?>
                            <tr><td colspan="11" class="adminhead" >&nbsp;</td></tr>
                            <tr>
                                <td align="right" colspan="9">
                                    <input class="button" onclick="checkbonusPoint();" type="button" name="update_bonus_point" value="Update Bonus Point"></td>
                                <td colspan="2">&nbsp;</td> 
                            </tr>
    <?php } ?>
    <?php if (!empty($_REQUEST['r'])) { ?>
                            <tr><td colspan="11" class="adminhead" >&nbsp;</td></tr>
                            <tr>
                                <td align="right" colspan="10">
                                    <input class="button" onclick="checkRemainingBags();" type="button" name="update_remaining_bag" value="Reset Bags"></td>
                                <td>&nbsp;</td> 
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
                        $firstlink = "[<a href=\"{$_SERVER['PHP_SELF']}?p=0&competi_id={$competi_id}&panelist_type={$panelist_type}&sort={$sort}&order={$order}$addparam$addparam2\">First</a>]";
                        $prevlink = "<a href=\"{$_SERVER['PHP_SELF']}?p=$prev&competi_id={$competi_id}&panelist_type={$panelist_type}&sort={$sort}&order={$order}$addparam$addparam2\">&laquo; Prev $limiter</a>";
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
                            $middlelinks .= "<a href=\"{$_SERVER['PHP_SELF']}?p=$startnum&competi_id={$competi_id}&panelist_type={$panelist_type}&sort={$sort}&order={$order}$addparam$addparam2\">" . ($i + 1) . "</a> ";
                        } else
                            $middlelinks .= ($i + 1) . ' ';
                    }
                    //next and last if not on last
                    if ($limstart < $rowcnt && (($limstart + ($limiter * 2)) < $rowcnt || ($rowcnt - ($limstart + $limiter)) > 0)) {
                        $next = $limstart + $limiter;
                        $nextlink = "<a href=\"{$_SERVER['PHP_SELF']}?p=$next&competi_id={$competi_id}&panelist_type={$panelist_type}&sort={$sort}&order={$order}$addparam$addparam2\">Next $limiter &raquo;</a>";
                        $lastlink = "[<a href=\"{$_SERVER['PHP_SELF']}?p=" . (($numbers - 1) * $limiter) . "&competi_id={$competi_id}&panelist_type={$panelist_type}&sort={$sort}&order={$order}$addparam$addparam2\">Last</a>]";
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
<?php }else {
    ?>
        <tr><td colspan='11' align='center' class="error" style="background-color:#ccc;" height='15px' >No record(s) found.</td></tr>
<?php } ?>
</table>
<form name="refreshdatafrm" id="refreshdatafrm" method="post" action="">
    <input type="hidden" name="refresh" id="refresh" value="1">
   
</form>
<?php

include 'bottom.php';
?>
<script type="text/javascript">
    function refreshdata(){
        if(confirm('Are you sure to refresh data?')){
            document.refreshdatafrm.submit();
        }
        
    }
</script>
    
    