<?php
$ALLOW_GROUPS = array(41);
require_once("../auth_auth.php");
include 'top.php';
$limit = 50;
if (isset($_REQUEST['p']))
    $p = $_SESSION['manageCategory_p'] = $_REQUEST['p'];
//elseif(isset($_SESSION['manageCategory_p'])) $p = $_SESSION['manageCategory_p'];
else
    $p = 0;
$sort = '';

$message = '';
$capture = '';
$delmsg = '';
$movemsg = '';
$i = 0;

/* For table split */

if (isset($_REQUEST['creative_tables']) && ($_REQUEST['creative_tables'] != '')) {
    $_SESSION['creativetbl'] = $_REQUEST['creative_tables'];
    $p = 0;
    $selecttable = $_SESSION['creativetbl'];
} else if (isset($_SESSION['creativetbl']) && $_SESSION['creativetbl'] != '') {
    $selecttable = $_SESSION['creativetbl'];
} else {
    $selecttable = 'cscan_digital_creative';
    $_SESSION['creativetbl'] = 'cscan_digital_creative';
}

$sql_digtal_tbl = "SELECT id,table_name FROM cscan_digital_creative_tables where table_name='" . $_SESSION['creativetbl'] . "' ";
$res_dig_tbl = $DRW->query($sql_digtal_tbl, $DRW_digital);
$row_dig = $DRW->fetch_array($res_dig_tbl);
$creative_tblid = $row_dig[0];


$sql_dig_tbl = "SELECT id,table_name FROM cscan_digital_creative_tables";
$res_tbl = $DRW->query($sql_dig_tbl, $DRW_digital);

/* End for table split */

if (isset($_REQUEST['delID']) && ($_REQUEST['delID'] > 0)) {

    $delID = $_REQUEST['delID'];
    $capture = $_REQUEST['capture'];
    $delete = '';
    $move = '';
    if (isset($_REQUEST['submit1'])) {
        $delete = $_REQUEST['submit1'];
    }
    if (isset($_REQUEST['move'])) {
        $move = $_REQUEST['move'];
    }

    if (!is_array($delID)) {
        $delID = array($delID);
    }
    for ($i = 0; $i < count($delID); $i++) {
        $delThis = $delID[$i];
        $sqls = "SELECT creative_id FROM " . $selecttable . " WHERE creative_id = '$delThis' AND (capture_status=2 OR capture_status=3)";
        $ress = $DRW->query($sqls, $DRW_digital);

        if ($delete == 'Delete') {
            if ($DRW->num_rows($ress) <= 0){ 
                //$DRW->query("delete from  cscan_digital_creative WHERE creative_id = '$delThis'", $DRW_main);
                $DRW->query("UPDATE " . $selecttable . " SET capture_status=3 WHERE creative_id = '$delThis'", $DRW_digital);
                $delmsg = 1;
            }else{
                $delmsg = 1;
            }
        }
        if ($move == 'Move to Search Engine Marketing Filter') {
            
            if ($DRW->num_rows($ress) <= 0){
                $DRW->query("UPDATE " . $selecttable . " SET capture_status=0 WHERE creative_id = '$delThis'", $DRW_digital);

                $movemsg = 1;
            }else{
                $movemsg = 1;                
            }
        }
    }

    if ($movemsg == '1') {
        $message = "<b>$i</b> Record(s) has been moved in Search Engine Marketing filter.";
    } elseif ($delmsg == '1') {
        $message = "<b>$i</b> Record(s) has been deleted.";
    }
}


/*##### start for panelist search ######  */
$search_pan=array();
$pan_found='';
$pan_search_req='';
if (isset($_REQUEST['pan_search']) && (trim($_REQUEST['pan_search'])!='')) {
    $p = 0;
    $pan_search_req=trim($_REQUEST['pan_search']);
    $_SESSION['pan_search']=$pan_search_req;
}else if ((isset($_REQUEST['pan_search']) && (trim($_REQUEST['pan_search'])=='')) || !isset($_SESSION['pan_search'])) {
     $_SESSION['pan_search']='';
}


   if (isset($_SESSION['pan_search']) && (trim($_SESSION['pan_search'])!='')) {
    $pan_search_req=$_SESSION['pan_search'];
        if(strstr($pan_search_req,',')){
           $pan_search="'".str_replace(",","','",$pan_search_req)."'"; 
            //$pan_str=explode(',',$pan_search);
            //$pan_search="'".implode("','",$pan_search)."'";
           // echo $pan_search; die;
                        
        }else{
            $pan_search="'".$pan_search_req."'";
        }
        $sql_pan = "SELECT panelist_id FROM cscan_panelists WHERE competi_id IN(".$pan_search.")";
       $rs_pan = $DRW->query($sql_pan, $DRW_read);
        $resultCount_pan = $DRW->num_rows($rs_pan);
        if($resultCount_pan>0){
            while ($row_pan = $DRW->fetch_array($rs_pan)) {
                $search_pan[]=$row_pan['panelist_id'];            
            }
            $search_pan=array_unique($search_pan);
        }else{
            $pan_found='Please enter valid panelist.';            
        }
    }
        

$where_pan='';
//print_r($search_pan); die;
if(count($search_pan)>0){
    for($i=0;$i<count($search_pan);$i++){
    
        $competi_id=$search_pan[$i]; 
        $where_pan .="(CONCAT(',',panelist_id,',') REGEXP ',".$competi_id.",')";
        if(($i+1)!=count($search_pan)){
            $where_pan .=" OR ";
        }
    }
}
if($where_pan!=''){
    $where_pan=' AND ('.$where_pan.')';
}
//echo $where_pan;
/*##### End for panelist search ######  */

$sql = "SELECT creative_id,creative_path,ad_md5,digital_channel,file_type,DATE_FORMAT(lastUpdated,'%m/%d/%Y - %h:%i %p') AS lastUpdated,DATE_FORMAT(capture_date,'%m/%d/%Y - %h:%i %p') AS capture_date1 FROM " . $selecttable . " WHERE digital_channel like '%Search Engine Marketing%' AND capture_status=1 $where_pan ORDER BY capture_date DESC  LIMIT $p, $limit";
$rs = $DRW->query($sql, $DRW_digital);
$resultCount = $DRW->num_rows($rs);

$numquery = "Select COUNT(creative_id) as numrows FROM " . $selecttable . " WHERE digital_channel like '%Search Engine Marketing%' AND capture_status=1 $where_pan";

$numquery = $DRW->query($numquery, $DRW_digital);
$nrow = $DRW->fetch_row($numquery);
$numrows = $nrow[0];
?>
<form method='post' name='frm1'>
    <table width='100%' border='0' cellspacing='0' cellpadding='5' class='text' rules='none' bordercolor='#B9B9B9' align='center' style='border-collapse:collapse'>
        <tr><td class="adminhead" align='center' colspan='7'>SEARCH ENGINE MARKETING CAPTURE PRODUCT</td></tr>
        <!-- search and right buttons start-->
        
        <tr>
             <td colspan='6'>
                <table class="text" border="0" cellspacing="0" cellpadding="1">
                    <tbody><tr>
                        <td align="right"><strong>Search by Panelist Id:</strong></td>
                        <td><input name="pan_search" id="pan_search" size="40" maxlength="255" class="input_box" value="<?php echo $_SESSION['pan_search'];?>" type="text"></td>
                        <td>&nbsp;</td>
			<td><input class="button" style="width:60px;" name="search_Submit" value="Search" type="submit">
                            &nbsp;&nbsp;&nbsp;&nbsp;
                            <input class="button" style="width:70px" name="show_All" onclick="return Remove_SearchField();" value="Show All" type="submit"></td>
                        
                        </tr>
                    
                        <tr><td>&nbsp;</td><td colspan="3" style="color:red; font-weight:bold;"><?php if($pan_found!=''){ echo '<br>'.$pan_found;}else{ echo '&nbsp;';}?></td></tr>
                 
                    
                </tbody></table>
            </td>
        </tr>
        
        <tr>
            <td colspan='6'>
                <table border='0' width='100%' cellspacing="0" cellpadding="0">
                    <tr valign='top'>
                        <td align='right' colspan='2'>
                            <table width="100%" border="0" cellspacing="0" cellpadding="0" class="text">

                                <tr>
                                    <td align='center' width="35%" style="padding-bottom: 10px;padding-top: 10px;"><input class='button' style='width:60px' type='submit' name='submit1' ID='delBt' value='Delete' onclick=' return confirmDel()'></td>
                                    <td align='left' width="35%"><input class='button'  type='submit' name='move' ID='moveBt' value='Move to Search Engine Marketing Filter' onclick=' return confirmMove()'></td>
                                    <td align='right' width="30%"><a href="ad_sem_display.php" style="text-decoration:none;"><input class='button' style='width:140px' type='button' name='submit1' ID='delBt' value='Go to Filter Page'></a><br /></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <!-- search and right buttons close-->

        <tr>
            <td width='1%' class="adminhead" height='15px'><b><input type='checkbox' name='setUnset' onclick='setAll()'></td>
                    <td width='23%' class="adminhead" height='15px' >&nbsp;</td>        
                    <td align="center" width='24%' class="adminhead" height='15px' >Creative Path</td>
                    <td align="right" width='9%' class="adminhead" height='15px' >File Type</td>
                    <td align="right" width='10%' class="adminhead" height='15px' ><b>Source</td>
                    <td align="center" width='23%' class="adminhead" height='15px' >Date Captured</td>
                    <td align="center" width='20%' class="adminhead" height='15px' >&nbsp;</td>

        </tr>
        <tr><td colspan='6' align='center' class='error'><?php echo $message; ?></td></tr>
        <?php
        if ($resultCount > 0) {
            $className = '';
            while ($row = $DRW->fetch_array($rs)) {
                $ID = $row['creative_id'];
                $creative_path = $row['creative_path'];
                $file_type = $row['file_type'];
                $lastUpdated = $row['lastUpdated'];
                $capture_date = $row['capture_date1'];

                if ($className == 'selected-bg')
                    $className = 'white-bg';
                else
                    $className = 'selected-bg';
                $ad_md5 = $row['ad_md5'];
                $devicename = ShowDeviceBymd5($ad_md5);
//                if($devicename=='Desktop')
//                     $devicename = 'Online Display';
                if ($devicename == '')
                    $devicename = 'Mobile';
                ?> 
                <tr valign=top class="<?php echo $className; ?>">
                    <td><input type='checkbox' name='delID[]' value='<?php echo $ID; ?>'></td>
                    <td>
                        <div style="max-width: 350px; max-height: 100px; overflow: scroll;">  
                            <img src="<?php echo $creative_path; ?>"/>
                        </div>
                              <!-- <iframe border="0" src="<?php //echo $creative_path;  ?>" width="250" height="100">
                               </iframe>  -->  
                    </td>

                    <td><a class='hlinks' href="<?php echo $creative_path; ?>"  onclick="window.open('<?php echo $creative_path; ?>', 'newwindow', 'width=700, height=650'); return false;" title='Click here to view.'><b><?php echo $creative_path; ?></b></a></td>
                    <td align="right"><?php echo $file_type; ?></td> 
                    <td align="right"><?php echo $devicename; ?></td>
                    <td align="right"><?php echo $capture_date; ?></td>
                    <td align="right"><a style="text-decoration:none;" href="addproduct-digital.php?old=<?php echo $ID; ?>&add=2&tbl=<?php echo $creative_tblid; ?>"><input class='button' style='width:120px;' type='button' name='moveproduct' value='Move To Product'></a></td>
                </tr>
                <?php
            }
            echo "<input type='hidden' name='submit' value='1'>";
            echo "<input type='hidden' id='capture' name='capture' value=''>";
            ?>

            <tr>
                <td colspan="6">
                    <table border="0" width="100%" cellspacing = "0"  cellpadding ="5">
                        <tr>
                            <td>&nbsp;</td>
                        </tr>
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
                            $firstlink = "[<a href=\"{$_SERVER['PHP_SELF']}?p=0\">First</a>]";
                            $prevlink = "<a href=\"{$_SERVER['PHP_SELF']}?p=$prev\">&laquo; Prev $limiter</a>";
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
                                $middlelinks .= "<a href=\"{$_SERVER['PHP_SELF']}?p=$startnum\">" . ($i + 1) . "</a> ";
                            } else
                                $middlelinks .= ($i + 1) . ' ';
                        }
//next and last if not on last
                        if ($limstart < $rowcnt && (($limstart + ($limiter * 2)) < $rowcnt || ($rowcnt - ($limstart + $limiter)) > 0)) {
                            $next = $limstart + $limiter;
                            $nextlink = "<a href=\"{$_SERVER['PHP_SELF']}?p=$next\">Next $limiter &raquo;</a>";
                            $lastlink = "[<a href=\"{$_SERVER['PHP_SELF']}?p=" . (($numbers - 1) * $limiter) . "\">Last</a>]";
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
                </td></tr>
<?php
}
else {
    echo "<tr><td colspan=6 class='error' align=center>No record found.</td></tr>";
    echo "<script>el = document.getElementById('delBt'); el.style.display='none';</script>";
    echo "<script>el2 = document.getElementById('capBt'); el2.style.display='none';</script>";
}
?>
    </table>

</form>

<!-- For split table -->
<table>
    <tr>
        <td colspan="5" align="left" style="padding-left:30px;">
            <form method='post' name='form2' id='form2'>

                Creative Table: <select name="creative_tables" onchange="form2.submit();">
<?php while ($row = $DRW->fetch_array($res_tbl)) { ?>
                        <option value="<?php echo $row[1]; ?>" <?php if ($_SESSION['creativetbl'] == $row[1]) { ?> selected="selected" <?php } ?>><?php echo $row[1]; ?></option>
<?php } ?>
                </select>
            </form>

        </td>
    </tr>
</table>
<!-- End for split table --> 

<script type="text/JavaScript">
    <!--
    function confirmDel()
    { 
        goAheadFlag = 0;
        for(i=0;i<document.frm1.elements.length;i++)
        {
            if(document.frm1.elements[i].checked == true)
            {
            goAheadFlag = 1;
            }
        }
            if(goAheadFlag)
            {
                if(confirm("Are you sure to delete ?"))
                {
                    return true;
                }
                else
                {
                    return false;
                }
            }
            else
            {
                alert('Please select at least one record to delete !!!');
                return false;
            }
    }

    function confirmMove()
    { 
        goAheadFlag = 0;
        for(i=0;i<document.frm1.elements.length;i++)
        {
            if(document.frm1.elements[i].checked == true)
            {
                goAheadFlag = 1;
            }
        }
        if(goAheadFlag)
        {
            if(confirm("Are you sure to move these records into Search Engine Marketing filter?"))
            {
                return true;
            }
            else
            {
                return false;
            }
        }
        else
        {
            alert('Please select at least one record to move in to Search Engine Marketing filter !!!');
            return false;
        }
    }


    function confirmCap()
    { 
        goAheadFlag2 = 0;
        for(i=0;i<document.frm1.elements.length;i++)
        {
            if(document.frm1.elements[i].checked == true)
            {
                goAheadFlag2 = 1;
            }
        }
        if(goAheadFlag2)
        {
            if(confirm("Are you sure to capture ?"))
            {
                document.frm1.capture.value=1;
                return true;
            }
            else
            {
                document.frm1.capture.value ='';
                return false;
            }
        }
        else
        {
            alert('Please select at least one record to capture !!!');
            document.frm1.capture.value = '';
            return false;
        }
    }

    function setAll()
    {
        if(document.frm1.setUnset.value == 'on')
        {
            for(i=1;i<document.frm1.elements.length;i++)
            {
                document.frm1.elements[i].checked = true;
            }
            document.frm1.setUnset.value = '';
        }
        else
        {
            for(i=1;i<document.frm1.elements.length;i++)
            {
                document.frm1.elements[i].checked = false;
            }
                document.frm1.setUnset.value = 'on';
        }
    }
    //-->
    function Remove_SearchField() {
        document.frm1.pan_search.value ='';
        } 
</script>
<?php
include 'bottom.php';
?>