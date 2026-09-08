<?php
//echo 'abc';die;
//$ALLOW_GROUPS = array(57);
require_once("../auth_auth.php");
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ini_set("memory_limit", "-1");
set_time_limit(0);
function pr($str){
    echo '<pre>';print_r($str);
}

$sql_last = "SELECT competi_id,email FROM cscan_panelists";
$q_last = $DRW->query($sql_last,$DRW_read);
if($DRW->num_rows($q_last)>0){
    while($rs_last = $DRW->fetch_assoc($q_last)){ 
            $panelist_id = $rs_last['competi_id'];
            $email = $rs_last['email'];
            $sql_contact= "SELECT ip_address FROM cscan_contacts_pre where email ='$email'"; 
            $query_cnt = $DRW->query($sql_contact,$DRW_read);
            $rs_data = $DRW->fetch_array($query_cnt);
            $ip_address = $rs_data['ip_address'];
            if($ip_address!=''){
            $query = "INSERT INTO cscan_ip_temp SET panelist_id ='$panelist_id',ip_address='$ip_address'";
            $resp = $DRW->query($query,$DRW_main);
            }
    }
   echo "data inserted";exit;
}else{
    echo "no record"; exit;
}
die;

$order_by = 'created_at DESC';
$order = 0;
$sort = '';

if(!empty($_REQUEST)){
    if(!empty($_REQUEST['cdt']))
        $cdt = trim($_REQUEST['cdt']);
    if(!empty($_REQUEST['sort']))
        $sort = trim($_REQUEST['sort']);
    if(!empty($_REQUEST['order']))
        $order = trim($_REQUEST['order']);
    if(!empty($sort)){  
        switch($sort){
            case '1':
                $order_by = 'imap_total_count';
                break;
            case '2':
                $order_by = 'imap_cp_count';
                break;
            case '3':
                $order_by = 'imap_pp_count';
                break;
            case '4':
                $order_by = 'imap_mbp_count';
                break; 
            case '5':
                $order_by = 'imap_pdp_count';
                break;
            case '6':
                $order_by = 'citi_ftp_total_count';
                break;
            case '7':
                $order_by = 'moved_citi_ftp_count';
                break;
            case '8':
                $order_by = 'citi_duplicate_file_count';
                break;
            case '9':
                $order_by = 'total_entryId_count';
                break;
            case '10':
                $order_by = 'client_email_alert_count';
                break;
            default:
                $order_by = 'created_at';
                break;
                
        }
        if(empty($order)){
            $order_by .= ' ASC'; 
        }else{
            $order_by .= ' DESC'; 
        }
    }    
}
include 'top.php';
$limit = 50;
if(isset($_GET['p'])) $p = trim($_GET['p']);
else $p = 0;
function doSort($sort, $order, $dosort, $p, $cdt, $spacer = '') {
    if(empty($order)){
        $order = 1;
    }else{ 
        $order = 0;
    }
    if ($sort == ($dosort * -1) || $sort != $dosort) {
        print "$spacer<a href=\"" . $_SERVER['PHP_SELF'] . "?cdt=$cdt&sort=$dosort&order=$order&p=$p\" class=\"blue\">sort</a>";
    } else {
        print "$spacer<a href=\"" . $_SERVER['PHP_SELF'] . "?cdt=$cdt&sort=$dosort&order=$order&p=$p\" class=\"blue\">sort</a>";
    }
}

$sql = "SELECT * FROM cscan_daily_status_emails where DATE_FORMAT(created_at, '%Y-%m-%d')= '".$cdt."' ORDER BY $order_by LIMIT $p,$limit";
//echo $sql; die;
$rs = $DRW->query($sql,$DRW_read);
$numquery = $DRW->query("SELECT FOUND_ROWS()", $DRW_read);
$nrow = $DRW->fetch_row($numquery);
$numrows = $nrow[0];
?>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<table width='100%' border='0' cellspacing='0' cellpadding='8' class='text' rules='none' bordercolor='#B9B9B9' align='center' style='border-collapse:collapse'>
    <tr class="adminhead">
       <!-- <td align='left' colspan="2">
            <a href="maxmail_sd_duplicates.php" style="color: #ffffff;"><< DA(Maxmaxil Sameday) Duplicates</a>
        </td>-->
        <td align='center' colspan="3">
            <a href="daily_status_email.php" style="color: #ffffff;">Daily Status Email Alert</a>
        </td>
        <!--<td align='right' colspan="2">
            <a href="maxmail_duplicates.php" style="color: #ffffff;">DA(Maxmaxil) Duplicates >></a>
        </td>-->
    </tr>
    <!-- search and right buttons start-->
    <tr>
        <td class="bodyText" colspan="7">
            <table width='100%' border="0" cellspacing="0" cellpadding="1" class="text">
                <tr>
                    <form method="get" name="rangeForm" action="<?php echo $_SERVER['PHP_SELF']; ?>" style="display:inline;">
                        <td align="left" width='20%'><strong>Search By:</strong></td>
                        <td align="right" width='10%'><strong>Date:</strong></td>
                        <td align="left" width='20%'>
                            <input type="text" id="cdt" readonly='true' name="cdt" size="20" maxlength="10" class="input_box" value="<?php echo $cdt; ?>" />
                        </td>
                        <td align="left">
                            <input class="button" style="width:60px;" type="submit" name="search" value="Search" />
                        </td>
                    </form>
                </tr>
            </table>
        </td>
    </tr>
    <?php
    if(!empty($numrows)){
    ?>
    <tr>
        <td colspan="7">
            <table width='100%' border='0' cellspacing='0' cellpadding='8' class='text' rules='none' bordercolor='#B9B9B9' align='center' style='border-collapse:collapse'>
                <tr><td colspan="">&nbsp;</td></tr>
                <tr>
                    <td align="left" class="adminhead" height='5px' ><b>Imap(tc)</b></br><?= doSort($sort,$order,1,$p,$cdt);?></td>
                    <td align="left" class="adminhead" height='5px' ><b>Imap(pdp)</b></br><?= doSort($sort,$order,2,$p,$cdt);?></td>
                    <td align="left" class="adminhead" height='5px' ><b>Imap(cp)</b></br><?= doSort($sort,$order,3,$p,$cdt);?></td>
                    <td align="left" class="adminhead" height='5px' ><b>Imap(mbp)</b></br><?= doSort($sort,$order,6,$p,$cdt);?></td>
                    <td align="left" class="adminhead" height='5px' ><b>Citi(tc)</b></br><?= doSort($sort,$order,6,$p,$cdt);?></td>
                    <td align="left" class="adminhead" height='5px' ><b>Citi(mc)</b></br><?= doSort($sort,$order,6,$p,$cdt);?></td>
                    <td align="left" class="adminhead" height='5px' ><b>Citi(dc)</b></br><?= doSort($sort,$order,6,$p,$cdt);?></td>
                    <td align="left" class="adminhead" height='5px' ><b>Status</b></br><?= doSort($sort,$order,6,$p,$cdt);?></td>
                    <td align="left" class="adminhead" height='5px' ><b>Date</b></br><?= doSort($sort,$order,6,$p,$cdt);?></td>
                </tr>
                <?php
                $className='';                
                 while($row = $DRW->fetch_assoc($rs)){//pr($row);die;
                    if ($className=='selected-bg') $className='white-bg';
                    else $className='selected-bg';
                    ?>
                    
                        <tr valign=top class="<?php echo $className; ?>">
                           
                            <td><?= $row['imap_total_count'];?></td>
                            <td><?= $row['imap_pdp_count'];?></td>
                            <td><?= $row['imap_cp_count'];?></td>
                            <td><?= $row['imap_mbp_count'];?></td>
                            <td><?= $row['imap_pp_count'];?></td>
                            <td><?= $row['citi_ftp_total_count'];?></td>
                            <td><?= $row['imap_cp_count'];?></td>
                            <td><?= $row['status'];?></td>
                            <td><?= $row['created_at'];?></td>
                            
                        </tr>
                    <?php }?>
                <?php// }?>
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
                    $firstlink = "[<a href=\"{$_SERVER['PHP_SELF']}?p=0&cdt={$cdt}&sort={$sort}&order={$order}\">First</a>]";
                    $prevlink = "<a href=\"{$_SERVER['PHP_SELF']}?p=$prev&cdt={$cdt}&sort={$sort}&order={$order}\">&laquo; Prev $limiter</a>";
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
                        $middlelinks .= "<a href=\"{$_SERVER['PHP_SELF']}?p=$startnum&cdt={$cdt}&sort={$sort}&order={$order}\">".($i+1)."</a> ";
                    }
                    else $middlelinks .= ($i+1).' ';
                }
                //next and last if not on last
                if($limstart<$rowcnt && (($limstart+($limiter*2))<$rowcnt || ($rowcnt - ($limstart + $limiter))>0)){
                    $next = $limstart + $limiter;
                    $nextlink = "<a href=\"{$_SERVER['PHP_SELF']}?p=$next&cdt={$cdt}&sort={$sort}&order={$order}\">Next $limiter &raquo;</a>";
                    $lastlink = "[<a href=\"{$_SERVER['PHP_SELF']}?p=".(($numbers-1)*$limiter)."&cdt={$cdt}&sort={$sort}&order={$order}\">Last</a>]";
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
            $( "#cdt" ).datepicker({
                dateFormat: 'yy-mm-dd',
                showOn: "button",
                buttonImage: "../images/calendar.gif",
                buttonImageOnly: true,
                buttonText: "Select from date",
                maxDate: new Date()
            });
        });
    </script>
</table>
<?php include 'bottom.php';?>