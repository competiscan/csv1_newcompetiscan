<?php
$ALLOW_GROUPS = array(53);
require_once("../auth_auth.php");
function array2csv(array &$array){
   if (count($array) == 0) {
     return null;
   }
   ob_start();
   $df = fopen("php://output", 'w');
   fputcsv($df, $array['date']);
   fputcsv($df, array("",""));
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
$cdt = date('Y-m-d');
$order_by = 'pieces DESC';
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
            case '3':
                $order_by = 'pieces';
                break;
            default:
                $order_by = 'pieces';
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
    $exp_sql = "SELECT SQL_CALC_FOUND_ROWS p.`panelist_id` , p.`competi_id` , p.`parent_panelist_id` , c.`filename`, count(*) as pieces
            FROM `chicagorecords` c
            LEFT JOIN `cscan_panelists` p ON ( p.panelist_id = c.panelist_id ) 
            WHERE DATE_FORMAT(c.crm_import_date, '%Y-%m-%d') = '".$cdt."' AND p.panelist_id IS NOT NULL
            GROUP BY p.panelist_id ORDER BY $order_by";
    $exp_rs = $DRW->query($exp_sql,$DRW_read);
    $records = [];
    while($row = $DRW->fetch_assoc($exp_rs)){
        $records[] = $row;
    }
    $tree = buildTree($records);
    if(count($tree[0])>0){
        $arrExport['date'] = array("Date: $cdt");
        $arrExport['data'][] = array("Panelist ID", "Sub Panelist ID", "Pieces");
        foreach($tree as $row){
            if(empty($row['parent_panelist_id'])){
                $arrExport['data'][] = array($row['competi_id'], "-", $row['pieces']);
            }            
            if(!empty($row['children'])>0){
                foreach($row['children'] as $row){
                    $arrExport['data'][] = array("-", $row['competi_id'], $row['pieces']);
                }
            }
        }
    }
    download_send_headers("panelist_direct_mail_participation_report_" . date("Y-m-d") . ".csv");
    echo array2csv($arrExport);
    die();
}
include 'top.php';
$limit = 500;
if(isset($_GET['p'])) $p = trim($_GET['p']);
else $p = 0;
function doSort($sort, $order, $dosort, $p, $cdt, $spacer = ' : ') {
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
function buildTree(array $elements, $parentId = 0) {
    global $DRW, $DRW_read;
    //pr($elements);die;
    $branch = $panelists = $noparent= $panelists2 = array();
    foreach ($elements as $element) {
        $panelists[] = $element['panelist_id'];
    }
    $panelists= array_unique($panelists);
    foreach ($elements as $element) {
        if(!empty($element['parent_panelist_id']) && !in_array($element['parent_panelist_id'],$panelists)){
            $panelists[] = $element['parent_panelist_id'];
            $sql = "SELECT competi_id FROM cscan_panelists WHERE panelist_id='".$element['parent_panelist_id']."'";
            $rs = $DRW->query($sql,$DRW_read);
            if($DRW->num_rows($rs)>0){
                $nrow = $DRW->fetch_assoc($rs);
                $cid = $nrow['competi_id'];
            }

            $newElement['panelist_id'] = $element['parent_panelist_id'];
            $newElement['competi_id'] = $cid;
            $newElement['parent_panelist_id'] = 0;
            $newElement['filename'] = '';
            $newElement['pieces'] = 0;
            $elements[] = $newElement;
        }
    }
    foreach ($elements as $element) {
        if ($element['parent_panelist_id'] == $parentId) {
            $children = buildTree($elements, $element['panelist_id']);
            if ($children) {
                $element['children'] = $children;
            }
            $branch[] = $element;
        }
    }
    return $branch;
}
$sql = "SELECT SQL_CALC_FOUND_ROWS p.`panelist_id` , p.`competi_id` , p.`parent_panelist_id` , c.`filename`, count(*) as pieces
        FROM `chicagorecords` c
        LEFT JOIN `cscan_panelists` p ON ( p.panelist_id = c.panelist_id ) 
        WHERE DATE_FORMAT(c.crm_import_date, '%Y-%m-%d') = '".$cdt."' AND p.panelist_id IS NOT NULL
        GROUP BY p.panelist_id ORDER BY $order_by LIMIT $p, $limit";
$rs = $DRW->query($sql,$DRW_read);
$numquery = $DRW->query("SELECT FOUND_ROWS()", $DRW_read);
$nrow = $DRW->fetch_row($numquery);
$numrows = $nrow[0];
?>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<table width='100%' border='0' cellspacing='0' cellpadding='8' class='text' rules='none' bordercolor='#B9B9B9' align='center' style='border-collapse:collapse'>
    <tr><td class="adminhead" align='center'>Panelist Direct Mail Report</td></tr>
    <!-- search and right buttons start-->
    <tr>
        <td class="bodyText">
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
                    <td align="right"><?php if($numrows > 0){?><form name="export" method="post"><input class="button" style="width:60px;" type="submit" name="export" value="Export" /></form><?php }?></td>
                </tr>
            </table>
        </td>
    </tr>
    <?php
    //$numrows= 0;
    if(!empty($numrows)){        
        $records = [];
        while($row = $DRW->fetch_assoc($rs)){
            $records[] = $row;
        }
        $tree = buildTree($records);
    ?>
    <tr>
        <td>
            <table width='100%' border='0' cellspacing='0' cellpadding='8' class='text' rules='none' bordercolor='#B9B9B9' align='center' style='border-collapse:collapse'>
                <tr><td colspan="3">&nbsp;</td></tr>
                <tr>
                    <td align="left" width='30%' class="adminhead" height='15px' ><b>Panelist ID</b></td>
                    <td align="left" width='40%' class="adminhead" height='15px' ><b>Sub Panelist ID</b></td>
                    <td align="left" class="adminhead" height='15px' ><b>Pieces</b><?php doSort($sort,$order,3,$p,$cdt); ?></td>
                </tr>
                <?php
                $className='';                
                foreach($tree as $row){
                    if ($className=='selected-bg') $className='white-bg';
                    else $className='selected-bg';
                    ?>
                    <?php
                    if(empty($row['parent_panelist_id'])){
                    ?>
                        <tr valign=top class="<?php echo $className; ?>">
                            <td><?php echo $row['competi_id'];?></td>  
                            <td align="left">-</td>                             
                            <td align="left"><?php echo $row['pieces'];?></td>
                        </tr>
                    <?php }
                    if(!empty($row['children'])>0){
                        foreach($row['children'] as $row){
                        ?>
                        <tr valign=top class="<?php echo $className; ?>">
                            <td>-</td>  
                            <td align="left"><?php echo $row['competi_id'];?></td>                             
                            <td align="left"><?php echo $row['pieces'];?></td>
                        </tr>
                    <?php }}?>
                <?php }?>
            </table>
        </td>
    </tr>    
<!--    <tr>
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
    </tr>    -->
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