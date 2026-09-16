<?php
//echo 'abc';die;
$ALLOW_GROUPS = array(57);
require_once("../auth_auth.php");
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ini_set("memory_limit", "-1");
set_time_limit(0);
function pr($str){
    echo '<pre>';print_r($str);
}
$sql_last = "SELECT DATE_FORMAT(datetime, '%Y-%m-%d') AS last_date FROM cscan_damaxmail_duplicate ORDER BY datetime DESC LIMIT 1";
$q_last = $DRW->query($sql_last,$DRW_read2);
if($DRW->num_rows($q_last)>0){
    $rs_last = $DRW->fetch_assoc($q_last);
    $cdt = $rs_last['last_date'];
}else{
    $cdt = date('Y-m-d');
}
$order_by = 'percentage_match DESC';
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
                $order_by = 'productName';
                break;
            case '3':
                $order_by = 'percentage_match';
                break;
            case '6':
                $order_by = 'datetime';
                break;
            default:
                $order_by = 'percentage_match';
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
$sql = "SELECT SQL_CALC_FOUND_ROWS pp.competi_id,pd.productName,pdd.id,pdd.productID,pdd.panelist_id,pdd.percentage_match,pdd.duplicate_filename,pdd.filename,pdd.approved_file,pdd.datetime
        FROM cscan_damaxmail_duplicate pdd
        INNER JOIN cscan_product_detail pd ON(pdd.productID=pd.productID)
        LEFT JOIN cscan_panelists pp ON(pdd.panelist_id=pp.panelist_id)
        WHERE DATE_FORMAT(datetime, '%Y-%m-%d') = '".$cdt."'
        ORDER BY $order_by LIMIT $p,$limit";

$rs = $DRW->query($sql,$DRW_read2);
$numquery = $DRW->query("SELECT FOUND_ROWS()", $DRW_read2);
$nrow = $DRW->fetch_row($numquery);
$numrows = $nrow[0];
?>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<table width='100%' border='0' cellspacing='0' cellpadding='8' class='text' rules='none' bordercolor='#B9B9B9' align='center' style='border-collapse:collapse'>
    <tr class="adminhead">
        <td align='left' colspan="2">
            <a href="da_duplicates.php" style="color: #ffffff;"><< DA(Chicago) Duplicates</a>
        </td>
        <td align='center' colspan="3">
            <a href="maxmail_duplicates.php" style="color: #ffffff;">DA(Maxmaxil) Duplicates</a>
        </td>
        <td align='right' colspan="2">
            <a href="maxmail_sd_duplicates.php" style="color: #ffffff;">DA(Maxmaxil Sameday) Duplicates >></a>
        </td>
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
                <tr><td colspan="7">&nbsp;</td></tr>
                <tr>
                    <td align="left" class="adminhead" height='15px' ><b>Product Name</b></br><?= doSort($sort,$order,1,$p,$cdt);?></td>
                    <td align="left" class="adminhead" height='15px' ><b>Panelist</b></br>&nbsp;</td>
                    <td align="left" class="adminhead" height='15px' ><b>Match(%)</b></br><?= doSort($sort,$order,3,$p,$cdt);?></td>
                    <td align="left" class="adminhead" height='15px' ><b>Approved File</b></br>&nbsp;</td>
                    <td align="left" class="adminhead" height='15px' ><b>Deleted File</b></br>&nbsp;</td>
                    <td align="left" class="adminhead" height='15px' ><b>Date</b></br><?= doSort($sort,$order,6,$p,$cdt);?></td>
                    <td align="left" class="adminhead" height='15px' ><b>Action</b></br>&nbsp;</td>
                </tr>
                <?php
                $className='';                
                 while($row = $DRW->fetch_assoc($rs)){//pr($row);die;
                    if ($className=='selected-bg') $className='white-bg';
                    else $className='selected-bg';
                    ?>
                    <?php
                    if(empty($row['parent_panelist_id'])){//echo $row['filename'];die;
                        $path = str_replace("admin","",dirname(__FILE__));
                        $f1 = str_replace($path,"",$row['approved_file']);
                        $f2 = str_replace($path,"",$row['duplicate_filename']);
                        $approved_file = basename($row['approved_file']);
                        $duplicate_file = str_replace("\\","/",str_replace("z:\\dachicagorecordsftp\\", "", $row['filename']));
                        $duplicate_file = basename($duplicate_file);
                    ?>
                        <tr valign=top class="<?php echo $className; ?>">
                            <td><?php echo '<a href="addproduct.php?id='.$row['productID'].'" target="_blank">'.$row['productName'].'</a>';?></td>
                            <td><?= $row['competi_id'];?></td>
                            <td><?= $row['percentage_match'];?></td>
                            <td><?= $approved_file;?></td>
                            <td><?= $duplicate_file;?></td>
                            <td><?= $row['datetime'];?></td>
                            <td><?php
                                echo '<a href="pdfcompare.php?type=maxmail&u1='.urlencode($f1).'&u2='.urlencode($f2).'" target="_blank">Compare</a>';
                            ?></td>
                        </tr>
                    <?php }?>
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