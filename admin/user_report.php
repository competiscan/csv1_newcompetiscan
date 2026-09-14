<?php
$ALLOW_GROUPS = array(53);
require_once("../auth_auth.php");
function array2csv(array &$array){
   if (count($array) == 0) {
     return null;
   }
   ob_start();
   $df = fopen("php://output", 'w');
   fputcsv($df, $array['date_range']);
   fputcsv($df, array("","",""));
   fputcsv($df, $array['total']);
   fputcsv($df, array("","",""));
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
$fdt = date('Y-m-d', strtotime("-6 day"));
$tdt = date('Y-m-d');
$order_by = 'alert_name ASC';
$order = 0;
$sort = $company_name = $user_id = '';

if(!empty($_REQUEST)){
    if(!empty($_REQUEST['fdt']))
        $fdt = trim($_REQUEST['fdt']);
    if(!empty($_REQUEST['tdt']))
        $tdt = trim($_REQUEST['tdt']);
    if(!empty($_REQUEST['sort']))
        $sort = trim($_REQUEST['sort']);
    if(!empty($_REQUEST['order']))
        $order = trim($_REQUEST['order']);
    if(!empty($_REQUEST['uid']))
        $user_id = trim($_REQUEST['uid']);
    if(!empty($_REQUEST['cname']))
        $company_name = trim($_REQUEST['cname']);
        $company_name = urldecode($company_name);
    if(!empty($sort)){  
        switch($sort){
            case '1':
                $order_by = 'alert_name';
                break;
            case '2':
                $order_by = 'is_send';
                break;
            case '3':
                $order_by = 'is_opened';
                break;
            case '4':
                $order_by = 'is_clicked';
                break;
            case '5':
                $order_by = 'insert_date';
                break;
            default:
                $order_by = 'alert_name';
                break;                
        }
        if(empty($order)){
            $order_by .= ' ASC'; 
            //$order = 1;
        }else{
            $order_by .= ' DESC'; 
            //$order = 0;
        }
    } 
}
if(!empty($_POST['export']) && trim($_POST['export']) == 'Export'){
    $arrExport = array();
    $exp_sql = "SELECT alert_name, is_send, is_opened, is_clicked, insert_date FROM cscan_email_track WHERE user_id='".$user_id."' AND DATE_FORMAT(insert_date, '%Y-%m-%d') BETWEEN '".$fdt."' AND '".$tdt."' AND is_send = 1 ORDER BY $order_by";
    $exp_rs = $DRW->query($exp_sql,$DRW_read);
    $exp_numquery = $DRW->query("SELECT FOUND_ROWS()", $DRW_read);
    $exp_nrow = $DRW->fetch_row($exp_numquery);
    $exp_numrows = $exp_nrow[0];
    if(!empty($exp_numrows)){        
        //total email sent
        $exp_total_sql = "SELECT CONCAT(u.firstName, ' ', u.lastName) AS userName, count(em.id) AS total_sent, SUM(em.is_opened) AS total_opened, SUM(em.is_clicked) AS total_clicked FROM cscan_email_track AS em INNER JOIN cscan_users AS u ON (em.user_id=u.userID) WHERE em.user_id='".$user_id."' AND DATE_FORMAT(em.insert_date, '%Y-%m-%d') BETWEEN '".$fdt."' AND '".$tdt."' AND em.is_send = 1";
        $exp_q_total = $DRW->query($exp_total_sql,$DRW_read);
        $exp_rs_total = $DRW->fetch_assoc($exp_q_total);
        $userName = $exp_rs_total['userName'];
        $exp_total_sent = $exp_rs_total['total_sent'];
        $exp_total_opened = $exp_rs_total['total_opened'];
        $exp_total_clicked = $exp_rs_total['total_clicked'];
        $arrExport['date_range'] = array("From Date: $fdt", "To Date: $tdt");
        $arrExport['total'] = array("User Name: $userName","Total E-mail Sent: $exp_total_sent", "Total Email Opened: $exp_total_opened", "Total Email Clicked: $exp_total_clicked");
        $arrExport['data'][] = array("Alert Name", "E-mail Sent", "E-mail Opened", "E-mail Clicked", "Sent Date");
        while($exp_row = $DRW->fetch_assoc($exp_rs)){
            $arrExport['data'][] = array($exp_row['alert_name'], $exp_row['is_send'], $exp_row['is_opened'], $exp_row['is_clicked'], date("Y-m-d",strtotime($exp_row['insert_date'])));
        }
    }
    download_send_headers("user_report_" . date("Y-m-d") . ".csv");
    echo array2csv($arrExport);
    die();
}
include 'top.php';
$limit = 50;
if(isset($_GET['p'])) $p = trim($_GET['p']);
else $p = 0;
function doSort($sort, $order, $dosort, $p, $user_id,$company_name,$fdt, $tdt, $spacer = ' : ') {
    if(empty($order)){
        $order = 1;
    }else{ 
        $order = 0;
    }
    if ($sort == ($dosort * -1) || $sort != $dosort) {
        print "$spacer<a href=\"" . $_SERVER['PHP_SELF'] . "?uid=$user_id&fdt=$fdt&tdt=$tdt&cname=$company_name&sort=$dosort&order=$order&p=$p\" class=\"blue\">sort</a>";
    } else {
        print "$spacer<a href=\"" . $_SERVER['PHP_SELF'] . "?uid=$user_id&fdt=$fdt&tdt=$tdt&cname=$company_name&sort=$dosort&order=$order&p=$p\" class=\"blue\">sort</a>";
    }
}
if(empty($user_id)){
    $numrows = 0;
}else{
    $sql = "SELECT alert_name, is_send, is_opened, is_clicked, insert_date  FROM cscan_email_track WHERE user_id='".$user_id."' AND DATE_FORMAT(insert_date, '%Y-%m-%d') BETWEEN '".$fdt."' AND '".$tdt."' AND is_send = 1 ORDER BY $order_by  LIMIT $p, $limit";
    $rs = $DRW->query($sql,$DRW_read);
    $exp_total_sql = "SELECT count(*) AS cnt FROM cscan_email_track AS em INNER JOIN cscan_users AS u ON (em.user_id=u.userID) WHERE em.user_id='".$user_id."' AND DATE_FORMAT(em.insert_date, '%Y-%m-%d') BETWEEN '".$fdt."' AND '".$tdt."' AND em.is_send = 1";
    $numrows = $DRW->fetch_assoc($DRW->query($exp_total_sql))['cnt'];
}
?>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<table width='100%' border='0' cellspacing='0' cellpadding='8' class='text' rules='none' bordercolor='#B9B9B9' align='center' style='border-collapse:collapse'>
    <tr><td class="adminhead" align='center'>User Report <span style="float:right;"><a href="company_report.php?<?php echo $_SERVER['QUERY_STRING'];?>" style="color:#fff;"><< Back</a></span></td></tr>
    <!-- search and right buttons start-->
    <tr>
        <td class="bodyText">
            <form method="get" name="rangeForm" action="<?php echo $_SERVER['PHP_SELF']; ?>" style="display:inline;">
                <table width='100%' border="0" cellspacing="0" cellpadding="1" class="text">
                    <tr>
                        <input type="hidden" name="uid" value="<?php echo $user_id;?>" />
                        <td align="left" width='20%'><strong>Date Range:</strong></td>
                        <td align="right" width='10%'><strong>From Date:</strong></td>
                        <td width='15%'><input type="text" id="fdt" readonly='true' name="fdt" size="20" maxlength="10" class="input_box" value="<?php echo $fdt; ?>" /></td>
                        <td align="right" width='10%'><strong>To Date:</strong></td>
                        <input type="hidden" name="cname" value="<?php echo $company_name;?>" />
                        <td width='15%'><input type="text" id="tdt" readonly='true' name="tdt" size="20" maxlength="10" class="input_box" value="<?php echo $tdt; ?>" /></td>
                        <td align="right" width='20%'><input class="button" style="width:60px;" type="submit" name="search" value="Search" />
                    </tr>
                </table>
            </form>
        </td>
    </tr>
    <?php
    //$numrows= 0;
    if(!empty($numrows)){
        //total email sent
        $total_sql = "SELECT CONCAT(u.firstName, ' ', u.lastName) AS userName, count(em.id) AS total_sent, SUM(em.is_opened) AS total_opened, SUM(em.is_clicked) AS total_clicked FROM cscan_email_track AS em INNER JOIN cscan_users AS u ON (em.user_id=u.userID) WHERE em.user_id='".$user_id."' AND DATE_FORMAT(em.insert_date, '%Y-%m-%d') BETWEEN '".$fdt."' AND '".$tdt."' AND em.is_send = 1";
        $q_total = $DRW->query($total_sql,$DRW_read);
        $rs_total = $DRW->fetch_assoc($q_total);
        $total_sent = $rs_total['total_sent'];
        $total_opened = $rs_total['total_opened'];
        $total_clicked = $rs_total['total_clicked'];
        $userName = $rs_total['userName'];
    ?>
    <tr>
        <td>
            <table width='100%' border='0' cellspacing='0' cellpadding='8' class='text' rules='none' bordercolor='#B9B9B9' align='center' style='border-collapse:collapse'>
                <tr><td colspan="5">&nbsp;</td></tr>
                <tr>
                    <td colspan="5">
                        <table width='100%' border="0" cellspacing="0" cellpadding="1" class="text">
                            <tr>
                                <td align="left" width='40%'><strong>User Name: <?php echo $userName;?></strong></td>
                                <td align="left" width='15%'><strong>Total E-mail Sent: <?php echo $total_sent;?></strong></td>
                                <td align="left" width='15%'><strong>Total E-mail Opened: <?php echo $total_opened;?></strong></td>
                                <td align="left" width='15%'><strong>Total E-mail Clicked: <?php echo $total_clicked;?></strong></td>
                                <td align="right" width='15%'><form name="export" method="post"><input class="button" style="width:60px;" type="submit" name="export" value="Export" /></form></td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <!-- search and right buttons close-->
                <tr>
                    <td align="left" width='31%' class="adminhead" height='15px' ><b>Alert Name</b><?php doSort($sort,$order,1,$p,$user_id,$company_name,$fdt,$tdt); ?></td>
                    <td align="left" width='18%' class="adminhead" height='15px' ><b>E-mail Sent</b><?php doSort($sort,$order,2,$p,$user_id,$company_name,$fdt,$tdt); ?></td>
                    <td align="left" width='18%' class="adminhead" height='15px' ><b>E-mail Opened</b><?php doSort($sort,$order,3,$p,$user_id,$company_name,$fdt,$tdt); ?></td>
                    <td align="left" width='18%' class="adminhead" height='15px' ><b>E-mail Clicked</b><?php doSort($sort,$order,4,$p,$user_id,$company_name,$fdt,$tdt); ?></td>
                    <td align="left" width='18%' class="adminhead" height='15px' ><b>Sent Date</b><?php doSort($sort,$order,5,$p,$user_id,$company_name,$fdt,$tdt); ?></td>
                </tr>
                <?php
                $className='';
                while($row = $DRW->fetch_assoc($rs)){
                    //echo '<pre>';print_r($row);die;
                    if ($className=='selected-bg') $className='white-bg';
                    else $className='selected-bg';
                    $siteurl= 'http://'.$_SERVER['HTTP_HOST'].'/'.str_replace('admin/email_track_report.php','',$_SERVER['SCRIPT_NAME']);
                    //echo $siteurl;die;
                    ?>
                    <tr valign=top class="<?php echo $className; ?>">
                        <td align="left"><?php echo $row['alert_name'];?></td>  
                        <td align="left"><?php echo $row['is_send'];?></td>                             
                        <td align="left"><?php echo $row['is_opened'];?></td>
                        <td align="left"><?php echo $row['is_clicked'];?></td>
                        <td align="left"><?php echo date("Y-m-d",strtotime($row['insert_date']));?></td>
                    </tr>
                <?php }?>
            </table>
        </td>
    </tr>    
    <tr>
        <td colspan="8">
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
                if($limstart>0){
                    if($limstart>=$limiter) $prev = $limstart - $limiter;
                    else $prev = 0;
                    $firstlink = "[<a href=\"{$_SERVER['PHP_SELF']}?p=0\">First</a>]";
                    $prevlink = "<a href=\"{$_SERVER['PHP_SELF']}?p=$prev\">&laquo; Prev $limiter</a>";
                }
                // middle loop through total results
                $numbers = ceil($rowcnt/$limiter);
                $loopstart = ceil($limstart/$limiter);
                if($loopstart<($show-1)) $loopstart = 0; // begin, do not move until 4
                if($numbers<$show) $loopend = $numbers; // loopend is less than $show
                else $loopend = $loopstart+$show;
                if($loopend>$numbers && $loopstart!=0) { // end, show last $show
                    $loopstart = $numbers - $show;
                    $loopend = $numbers;
                }
                for($i=$loopstart; $i<$loopend; $i++){
                    $startnum = $limiter * $i;
                    if($startnum!=$limstart) {
                        $middlelinks .= "<a href=\"{$_SERVER['PHP_SELF']}?p=$startnum&uid=$user_id&fdt=$fdt&tdt=$tdt&cname=$company_name&order=$order\">".($i+1)."</a> ";
                    }
                    else $middlelinks .= ($i+1).' ';
                }
                //next and last if not on last
                if($limstart<$rowcnt && (($limstart+($limiter*2))<$rowcnt || ($rowcnt - ($limstart + $limiter))>0)){
                    $next = $limstart + $limiter;
                    $nextlink = "<a href=\"{$_SERVER['PHP_SELF']}?p=$next&uid=$user_id&fdt=$fdt&tdt=$tdt&cname=$company_name&order=$order\">Next $limiter &raquo;</a>";
                    $lastlink = "[<a href=\"{$_SERVER['PHP_SELF']}?p=".(($numbers-1)*$limiter)."&uid=$user_id&fdt=$fdt&tdt=$tdt&cname=$company_name&order=$order\">Last</a>]";
                }
                if($middlelinks!='') $middlelinks = "[ $middlelinks ] &nbsp;";
                print "<tr><td align=\"center\" class=\"bodytext\">$firstlink &nbsp; $prevlink &nbsp; $middlelinks $nextlink &nbsp; $lastlink</td></tr>";
                print "<tr><td align=\"center\" class=\"bodytext\">Showing results ".($limstart+1)." to ";
                if($limstart+$limiter < $rowcnt) print ($limstart+$limiter);
                else print $rowcnt;
                print " of $rowcnt</td></tr>";
                ?>
            </table>
	</td>
    </tr>    
    <?php 
    }else{?>
        <tr><td colspan='4' align='center' class='error'>No record(s) found.</td></tr>
<?php }?>
    <script type="text/JavaScript">
        $( function() {
            $( "#fdt" ).datepicker({
                dateFormat: 'yy-mm-dd',
                showOn: "button",
                buttonImage: "../images/calendar.gif",
                buttonImageOnly: true,
                buttonText: "Select from date",
                maxDate: new Date()
            });
            $( "#tdt" ).datepicker({
                dateFormat: 'yy-mm-dd',
                showOn: "button",
                buttonImage: "../images/calendar.gif",
                buttonImageOnly: true,
                buttonText: "Select to date",
                maxDate: new Date()
            });
        });
    </script>
</table>
<?php include 'bottom.php';?>