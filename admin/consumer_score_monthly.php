<?php
$ALLOW_GROUPS = array(82);
require_once("../auth_auth.php");
if(!empty($_REQUEST['id'])){
    $id      =   $_REQUEST['id'];
    $pdate      =   $_REQUEST['pdate'];
    $sql_panelist =   "SELECT SQL_CALC_FOUND_ROWS id, panelist_id, competi_id, SUM(direct_mail_piece) as direct_mail_piece, SUM(direct_mail_point) as direct_mail_point, SUM(email_piece) as email_piece, SUM(email_piece_point) as email_piece_point, SUM(digital_point) as digital_point, SUM(total_point) as total_point, insert_date 
        FROM cscan_consumer_scoring_daily_report  where competi_id = '".$id."' AND LEFT(insert_date,7)>='" . $pdate . "' AND LEFT(insert_date,7)<='" . $pdate . "' GROUP BY panelist_id";
    $result = $DRW->query($sql_panelist,$DRW_read2); 
    //$rowData     =   $DRW->fetch_assoc($result);
    
    ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<head>
<title>Competiscan Consumer Score Monthly</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<script language="JavaScript" src="../includes/jsFunctions.js" type="text/JavaScript"></script>
<link href="../includes/styleSheet.css" rel="stylesheet" type="text/css" />
</head>
<body style="margin:10px;">
    <?php
if(!empty($result)){
        ?>
    <table width="100%" border="0" cellspacing="0" cellpadding="5" class="text">
	  <tr>
            <td align="left" style="width:12%;" class="adminhead" height='15px' ><b>Panelist ID</b></td>
            <td align="left" style="width:12%;" class="adminhead" height='15px' ><b>Date</b></td>
            <td align="left" style="width:10%;" class="adminhead" height='15px' ><b>Direct Mail Pieces</b></td>
            <td align="left" style="width:10%;" class="adminhead" height='15px' ><b>Direct Mail Point</b></td>
            <td align="left" style="width:10%;" class="adminhead" height='15px' ><b>Email Pieces</b></td>
            <td align="left" style="width:10%;" class="adminhead" height='15px' ><b>Email Point</b></td>
            <td align="left" style="width:10%;" class="adminhead" height='15px' ><b>Digital Point</b></td>
           <!-- <td align="left" style="width:10%;" class="adminhead" height='15px' ><b>Bonus Point</b></td>-->
            <!--<td align="left" style="width:10%;" class="adminhead" height='15px' ><b>Total Point</b> </td>-->
            <!--<td align="left" style="width:10%;" class="adminhead" height='15px' ><b>Bags Remaining</b></td>-->
        </tr>
             <?php
                $className = '';
                while ($row = $DRW->fetch_assoc($result)) {
                    $ids = $row['id'];
                    $direct_mail_piece = $row['direct_mail_piece'];
                    $direct_mail_point = $row['direct_mail_point'];
                    $email_piece = $row['email_piece'];
                    $email_piece_point = $row['email_piece_point'];
                    $digital_point = $row['digital_point'];
                    //$bag_remaining = $row['bag_remaining'];
                    //$bonus_point = $row['bonus_point'];
                    //$total_point = ($row['total_point']);
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
                       <!-- <td align="left"><?php echo $bonus_point; ?>
                        </td>-->
                        <!--<td align="left"><?php echo $total_point; ?></td>-->
                        <!--<td align="left"><?php echo $bag_remaining; ?>
                        </td>-->
                    </tr>
              <?php }?>
            
            <tr><td colspan="2">&nbsp;</td></tr>
        </table>
    <?php
    }
 }?>
    <br></br>
  <a href="#" onclick="self.close(); return false;">close</a>
</body>
</html>

