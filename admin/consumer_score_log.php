<?php
$ALLOW_GROUPS = array(82);
require_once("../auth_auth.php");
if(!empty($_REQUEST['id'])){
    $id      =   $_REQUEST['id'];
    $sqlrest =   " SELECT id,competi_id, reset_by,reset_date,bagupdate_by,bagupdate_date,add_bonus_point_by,add_bonus_point_date
                    FROM cscan_consumer_scoring_report_total where panelist_id = '".$id."' 
                 ";
    $rsreset = $DRW->query($sqlrest,$DRW_read2); 
    $row     =   $DRW->fetch_assoc($rsreset);
    
    ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<head>
<title>Competiscan Email</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<script language="JavaScript" src="../includes/jsFunctions.js" type="text/JavaScript"></script>
<link href="../includes/styleSheet.css" rel="stylesheet" type="text/css" />
</head>
<body style="margin:10px;">
    <?php

    
    if(!empty($row)){
        $competi_id =   $row['competi_id'];
        $reset_by   =   $row['reset_by'];
        $resetname  =   fetchadminnameByID($reset_by);
        $reset_date =   $row['reset_date'];
        $bagupdate_by=  $row['bagupdate_by'];
        $bagupdate_name  =   fetchAdminnameByID($bagupdate_by);
        $bagupdate_date=    $row['bagupdate_date'];
        $add_bonus_point_by     =   fetchAdminnameByID($row['add_bonus_point_by']);
        $add_bonus_point_date   =   $row['add_bonus_point_date'];
        ?>
    
        <table width="100%" border="0" cellspacing="0" cellpadding="5" class="text">
	 <tr>
	 	<td class="adminhead"><strong>Reset Point Date</strong></td>
	    <td class="adminhead"><strong>Reset Point By</strong></td>
	  </tr>
             <tr class="selected-bg">
                  <?php if(!empty($reset_date)){ ?>
                 <td><?php echo $reset_date;?></td>
                <td><?php echo $resetname;?></td>
                <?php }else{ ?>
                        <td colspan="2" align="center" style="color:#F18458;font-weight:bold;"> No Record Exist</td>
                <?php }?>
	  </tr>
            <tr><td colspan="2">&nbsp;</td></tr>
            
            <tr>
	 	<td class="adminhead"><strong>Reset Bag Date</strong></td>
	    <td class="adminhead"><strong>Reset Bag By</strong></td>
	  </tr>
            
             <tr class="white-bg">
                <?php if(!empty($bagupdate_date)){ ?>
                        <td><?php echo $bagupdate_date;?></td>
                        <td><?php echo $bagupdate_name;?></td>
                    <?php }else{ ?>
                        <td colspan="2" align="center" style="color:#F18458;font-weight:bold;"> No Record Exist</td>
                <?php }?>
            </tr>
             <tr><td colspan="2">&nbsp;</td></tr>
            <tr>
	 	<td class="adminhead"><strong>Bonus Point Add Date</strong></td>
	    <td class="adminhead"><strong>Bonus Point Add By</strong></td>
	  </tr>
            
             <tr class="white-bg">
                <?php if(!empty($add_bonus_point_date)){ ?>
                        <td><?php echo $add_bonus_point_date;?></td>
                        <td><?php echo $add_bonus_point_by;?></td>
                    <?php }else{ ?>
                        <td colspan="2" align="center" style="color:#F18458;font-weight:bold;"> No Record Exist</td>
                <?php }?>
            </tr>
            
           
        </table>
    <?php
    }
 }?>
    <br></br>
  <a href="#" onclick="self.close(); return false;">close</a>
</body>
</html>
<?php 

function fetchAdminnameByID($id){
    global $DRW,$DRW_main,$DRW_read2,$DRW_digital;
    $username   =   '';
    $sqlrest =   " SELECT userName
                    FROM cscan_admin_users where userID = '".$id."' 
                 ";
    $rsreset = $DRW->query($sqlrest,$DRW_read2); 
    $row     =   $DRW->fetch_assoc($rsreset);
    if(!empty($row)){
        $username   = ucwords($row['userName']);
    }
    return $username;
}
?>
