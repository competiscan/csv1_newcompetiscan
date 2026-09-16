<?php
$ALLOW_GROUPS = array(54);
require_once("../auth_auth.php");

$arrMonths = ['01' => 'January', '02' => 'February', '03' => 'March', '04' => 'April', '05' => 'May', '06' => 'June', '07' => 'July', '08' => 'August', '09' => 'September', '10' => 'October', '11' => 'November', '12' => 'December'];
function array2csv(array &$array){
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
$fdt = date('Y-m-d', strtotime("-6 day"));
$tdt = date('Y-m-d');
$order_by = 'frequency DESC';
$order = 0;
$sort = '';
$competi_id='';
$cur_month = $cur_year = $conditions = '';
$filter = array();

if(!empty($_REQUEST)){
    if(isset($_REQUEST['month'])){
        $cur_month = trim($_REQUEST['month']);       
    }
    if(isset($_REQUEST['year'])){
        $cur_year = trim($_REQUEST['year']);        
    }
    if(!empty($_REQUEST['sort']))
        $sort = trim($_REQUEST['sort']);
    if(!empty($_REQUEST['order']))
        $order = trim($_REQUEST['order']);
    if(!empty($sort)){  
        switch($sort){
            case '1':
                $order_by = 'month';
                break;
            case '2':
                $order_by = 'panelist_id';
                break;
            case '3':
                $order_by = 'competi_id';
                break;
            case '4':
                $order_by = 'frequency';
                break;
            default:
                $order_by = 'frequency';
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
}else{
    $cur_month = date('m');
    $cur_year = date('Y');
}
if(!empty($cur_year)){
    $filter[] = "DATE_FORMAT(a.date_observed, '%Y') = '".$cur_year."'";
}
if(!empty($cur_month)){
     $filter[] = "DATE_FORMAT(a.date_observed, '%m') = '".$cur_month."'";
}
if(!empty($filter)){
    $conditions = 'WHERE ';
    $conditions .= implode(" AND ", $filter);
    
}
if (!empty($_REQUEST['competi_id'])) {
        $competi_id = trim($_REQUEST['competi_id']);
        $seasrcharray = array("'", '"', ' ');
        $replacearray = array("", "", "");
        $competiarray = explode(",", str_replace($seasrcharray, $replacearray, $competi_id));
        $newarraystr = trim(implode("','", $competiarray));
        $conditions .= "  AND b.competi_id in( '" . $newarraystr . "') ";
        
    }
if(!empty($_POST['export']) && trim($_POST['export']) == 'Export'){//pr($_POST);die;
    $arrExport = array();
    $exp_sql = "SELECT SQL_CALC_FOUND_ROWS month(a.date_observed) as month, b.panelist_id, b.competi_id, count(*) AS frequency FROM cscan_digital_observation a INNER JOIN cscan_panelists b ON(a.panelist_id=b.panelist_id) $conditions GROUP BY month, panelist_id ORDER BY $order_by";
    $exp_rs = $DRW->query($exp_sql,$DRW_digital);
    $exp_numquery = $DRW->query("SELECT FOUND_ROWS()", $DRW_digital);
    $exp_nrow = $DRW->fetch_row($exp_numquery);
    $exp_numrows = $exp_nrow[0];
    if(!empty($exp_numrows)){        
        //total email sent        
        $arrExport['data'][] = array("Month", "Panelist ID", "Frequency");
        while($exp_row = $DRW->fetch_assoc($exp_rs)){
            $arrExport['data'][] = array($exp_row['month'], $exp_row['competi_id'], $exp_row['frequency']);
        }
    }
    download_send_headers("panelist_participation_report_" . date("Y-m-d") . ".csv");
    echo array2csv($arrExport);
    die();
}
include 'top.php';
$limit = 50;
if(isset($_GET['p'])) $p = trim($_GET['p']);
else $p = 0;
function doSort($sort, $order, $dosort, $p, $cur_month, $cur_year,$competi_id, $spacer = ' : ') {
    if(empty($order)){
        $order = 1;
    }else{ 
        $order = 0;
    }
    if ($sort == ($dosort * -1) || $sort != $dosort) {
        print "$spacer<a href=\"" . $_SERVER['PHP_SELF'] . "?month={$cur_month}&year={$cur_year}&competi_id={$competi_id}&sort=$dosort&order=$order&p=$p\" class=\"blue\">sort</a>";
    } else {
        print "$spacer<a href=\"" . $_SERVER['PHP_SELF'] . "?month={$cur_month}&year={$cur_year}&competi_id={$competi_id}&sort=$dosort&order=$order&p=$p\" class=\"blue\">sort</a>";
    }
}
$sql = "SELECT SQL_CALC_FOUND_ROWS month(a.date_observed) as month, b.panelist_id, b.competi_id, count(*) AS frequency FROM cscan_digital_observation a INNER JOIN cscan_panelists b ON(a.panelist_id=b.panelist_id) $conditions GROUP BY month, panelist_id ORDER BY $order_by LIMIT $p, $limit";

$rs = $DRW->query($sql,$DRW_digital);
$numquery = $DRW->query("SELECT FOUND_ROWS()", $DRW_digital);
$nrow = $DRW->fetch_row($numquery);
$numrows = $nrow[0];
?>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<table width='100%' border='0' cellspacing='0' cellpadding='8' class='text' rules='none' bordercolor='#B9B9B9' align='center' style='border-collapse:collapse'>
    <tr><td class="adminhead" align='center'>Digital Panelist Participation Report</td></tr>
    <!-- search and right buttons start-->
    <tr>
        <td class="bodyText">
            
                <table width='100%' border="0" cellspacing="0" cellpadding="1" class="text">
                    <tr>
                        <form method="get" name="rangeForm" action="<?php echo $_SERVER['PHP_SELF']; ?>" style="display:inline;">
                        <td align="left" width='8%'><strong>Search By:</strong></td>
                        <td align="left" width='16%'><strong>Month:</strong>
                            <select name="month" size="1" class="input_box" style="width:100px;">
                                <option value=""> All </option>
                                <?php
                                foreach($arrMonths as $key=>$month):
                                    $selected = ($key == $cur_month)?'selected="selected"':"";
                                ?>                                
                                <option value="<?=$key;?>" <?=$selected;?>><?=$month;?></option>
                                <?php endforeach;?>
                            </select>
                        </td>
                        <td align="left" width='15%'><strong>Year:</strong>
                            <select name="year" size="1" class="input_box" style="width:100px;">
                                <option value=""> All </option>
                                <?php
                                $y = 2017;
                                while($y <= date('Y')){
                                    $selected = ($y == $cur_year)?'selected="selected"':"";                                    
                                ?>
                                <option value="<?=$y;?>" <?=$selected;?>><?=$y;?></option>
                                <?php $y++;}?>
                            </select>
                        </td>
                        <td align="left" width='40%' ><strong>Panelist ID:</strong>
                            <input type="text" id="competi_id" style="width:290px;" name="competi_id" placeholder="Please enter multiple panelist id with comma separated."  class="input_box" value="<?php echo $competi_id; ?>" />
                            <!--<span style="text-align:center;width:100%;float:left;">( Please enter multiple panelist id with comma separated. )</span>-->
                        </td>
                        <td align="left" width='20%'>
                            <input class="button" style="width:60px;" type="submit" name="search" value="Search" />
                            <?php if(isset($_REQUEST['month']) || isset($_REQUEST['year']) || isset($_REQUEST['competi_id'])):?>
                            <a href="panelist_participation_records_test.php" class="button" style="width:60px;height: 25px; color:#fff;cursor: pointer;text-decoration: none;box-shadow: 1px 1px 0 #5d5d5d;padding: 2px 8px;">Clear</a>
                            <?php endif;?>
                        </td>
                        </form>
                        <td align="right"><form name="export" method="post"><input class="button" style="width:60px;" type="submit" name="export" value="Export" /></form></td>
                    </tr>
                </table>
            
        </td>
    </tr>
    <?php if(!empty($numrows)){?>
    <tr>
        <td>
            <table width='100%' border='0' cellspacing='0' cellpadding='8' class='text' rules='none' bordercolor='#B9B9B9' align='center' style='border-collapse:collapse'>
                <tr><td colspan="3">&nbsp;</td></tr>
                <tr>
                    <td align="left" width='33%' class="adminhead" height='15px' ><b>Month</b><?php doSort($sort,$order,1,$p,$cur_month, $cur_year, $competi_id); ?></td>
                    <td align="left" width='33%' class="adminhead" height='15px' ><b>Panelist ID</b><?php doSort($sort,$order,2,$p,$cur_month, $cur_year, $competi_id); ?></td>
                    <td align="left" class="adminhead" height='15px' ><b>Frequency</b><?php doSort($sort,$order,3,$p,$cur_month, $cur_year, $competi_id); ?></td>
                </tr>
                <?php
                $className='';
                while($row = $DRW->fetch_assoc($rs)){
                    if ($className=='selected-bg') $className='white-bg';
                    else $className='selected-bg';
                    $siteurl= 'http://'.$_SERVER['HTTP_HOST'].'/'.str_replace('admin/panelist_participation_records_test.php','',$_SERVER['SCRIPT_NAME']);
                    //echo $siteurl;die;
                    ?>
                    <tr valign=top class="<?php echo $className; ?>">
                        <td><?php echo $row['month'];?></td>  
                        <td align="left"><?php echo $row['competi_id'];?></td>
                        <td align="left"><?php echo $row['frequency'];?></td>
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
                    $firstlink = "[<a href=\"{$_SERVER['PHP_SELF']}?p=0&month={$cur_month}&year={$cur_year}&competi_id={$competi_id}&sort={$sort}&order={$order}\">First</a>]";
                    $prevlink = "<a href=\"{$_SERVER['PHP_SELF']}?p=$prev&month={$cur_month}&year={$cur_year}&competi_id={$competi_id}&sort={$sort}&order={$order}\">&laquo; Prev $limiter</a>";
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
                        $middlelinks .= "<a href=\"{$_SERVER['PHP_SELF']}?p=$startnum&month={$cur_month}&year={$cur_year}&competi_id={$competi_id}&sort={$sort}&order={$order}\">".($i+1)."</a> ";
                    }
                    else $middlelinks .= ($i+1).' ';
                }
                //next and last if not on last
                if($limstart<$rowcnt && (($limstart+($limiter*2))<$rowcnt || ($rowcnt - ($limstart + $limiter))>0)){
                    $next = $limstart + $limiter;
                    $nextlink = "<a href=\"{$_SERVER['PHP_SELF']}?p=$next&month={$cur_month}&year={$cur_year}&competi_id={$competi_id}&sort={$sort}&order={$order}\">Next $limiter &raquo;</a>";
                    $lastlink = "[<a href=\"{$_SERVER['PHP_SELF']}?p=".(($numbers-1)*$limiter)."&month={$cur_month}&year={$cur_year}&competi_id={$competi_id}&sort={$sort}&order={$order}\">Last</a>]";
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
</table>
<?php include 'bottom.php';?>