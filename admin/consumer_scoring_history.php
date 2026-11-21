<?php
$ALLOW_GROUPS = array(82);
require_once("../auth_auth.php");
//include 'top.php';
//$conditions= " where panelist_id=13798 Order by insert_date DESC";
$conditions='';
if(!empty($_REQUEST['pid'])){
    $id      =   $_REQUEST['pid'];
    $conditions= " where panelist_id='".$id."' Order by insert_date DESC";
 }
$limit = 50;
if (isset($_GET['p']))
    $p = trim($_GET['p']);
else
    $p = 0;

$sql = "SELECT SQL_CALC_FOUND_ROWS id, panelist_id, competi_id, direct_mail_piece, direct_mail_point, email_piece, email_piece_point, 
        digital_point,bonus_point, total_point, bag_remaining, entry_date,insert_date 
        FROM cscan_consumer_scoring_report_history  
        $conditions LIMIT $p, $limit
        ";     
$rs = $DRW->query($sql, $DRW_read2);
$numquery = $DRW->query("SELECT FOUND_ROWS()", $DRW_read2);
$nrow = $DRW->fetch_row($numquery);
$numrows = $nrow[0];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<head>
<title>Competiscan Email</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<script language="JavaScript" src="../includes/jsFunctions.js" type="text/JavaScript"></script>
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<link href="../includes/styleSheet.css" rel="stylesheet" type="text/css" />
</head>
<body style="margin:10px;">

<table width='100%' border='0' cellspacing='0' cellpadding='8' class='text' rules='none' bordercolor='#B9B9B9' align='center' style='border-collapse:collapse'>
    <!--<tr><td class="adminhead" align='center'>Consumer Scoring Report History</td></tr> 
    <tr><td align='right'>
            <a href="consumer_scoring_report.php" class="button" style="padding:5px;text-decoration: none;float:right;margin-right: 10px; color: #fff;">Back</a>
        </td>
    </tr>-->
<?php if (!empty($numrows)) { ?>
        <tr>
            <td>
                <form name="frmreport" id="frmreport" method="post" action="">
                    <table width='100%' border='0' cellspacing='0' cellpadding='8' class='text' rules='none' bordercolor='#B9B9B9' align='center' style='border-collapse:collapse'>
                       <tr>
                            <td align="left" style="width:12%;" class="adminhead" height='15px' ><b>Date</b></td>
                            <td align="left" style="width:10%;" class="adminhead" height='15px' ><b>Direct Mail Pieces</b></td>
                            <td align="left" style="width:10%;"  style="width:5%;" class="adminhead" height='15px' ><b>Direct Mail Point</b></td>
                            <td align="left" style="width:10%;" class="adminhead" height='15px' ><b>Email Pieces</b></td>
                            <td align="left" style="width:10%;" class="adminhead" height='15px' ><b>Email Point</b></td>
                            <td align="left" style="width:10%;" class="adminhead" height='15px' ><b>Digital Point</b></td>
                            <td align="left" style="width:10%;" class="adminhead" height='15px' ><b>Bonus Point</b></td>
                            <td align="left" style="width:10%;" class="adminhead" height='15px' ><b>Total Point</b></td>
                            <td align="left" style="width:10%;" class="adminhead" height='15px' ><b>Bags Remaining</b></td>
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
                            $insert_date=$row['insert_date'];
                            $total_point = ($row['total_point']);
                            if ($className == 'selected-bg')
                                $className = 'white-bg';
                            else
                                $className = 'selected-bg';
                            ?>
                            <tr valign=top class="<?php echo $className; ?>">
                                
                                <td align="left">
                                    
                                    <?php echo $insert_date; ?>
                                   
                                </td>
                                <td align="left"><?php echo $direct_mail_piece; ?></td>
                                <td align="left"><?php echo $direct_mail_point; ?></td>
                                <td align="left"><?php echo $email_piece; ?></td>
                                <td align="left"><?php echo $email_piece_point; ?></td>
                                <td align="left"><?php echo $digital_point; ?></td>
                                <td align="left"><?php echo $bonus_point; ?></td>
                                <td align="left"><?php echo $total_point; ?></td>
                                <td align="left"><?php echo $bag_remaining; ?></td>
                                
                            </tr>
                            <?php }
                        ?>
                            
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
                        $firstlink = "[<a href=\"{$_SERVER['PHP_SELF']}?p=0&pid={$id}\">First</a>]";
                        $prevlink = "<a href=\"{$_SERVER['PHP_SELF']}?p=$prev&pid={$id}\">&laquo; Prev $limiter</a>";
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
                            $middlelinks .= "<a href=\"{$_SERVER['PHP_SELF']}?p=$startnum&pid={$id}\">" . ($i + 1) . "</a> ";
                        } else
                            $middlelinks .= ($i + 1) . ' ';
                    }
                    //next and last if not on last
                    if ($limstart < $rowcnt && (($limstart + ($limiter * 2)) < $rowcnt || ($rowcnt - ($limstart + $limiter)) > 0)) {
                        $next = $limstart + $limiter;
                        $nextlink = "<a href=\"{$_SERVER['PHP_SELF']}?p=$next&pid={$id}\">Next $limiter &raquo;</a>";
                        $lastlink = "[<a href=\"{$_SERVER['PHP_SELF']}?p=" . (($numbers - 1) * $limiter) ."&pid={$id}\">Last</a>]";
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
 </br>
 <div style="text-align:left; "><a href="#" onclick="self.close(); return false;">close</a></div>
  
 </body>
</html>
<?php ///include 'bottom.php'; ?>
