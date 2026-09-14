<?php
$ALLOW_GROUPS = array(86);
require_once("../auth_auth.php");
include("top.php");
require_once("../includes/functions.php");
if(!defined('ENV')){
    define('ENV',getenv('SERVER_NAME'));
} 
if(ENV == 'localhost'){    
    $client_usages='http://localhost/competiscan.com/admin/manageUserTracker.php';
}elseif(ENV == 'demo.competiscan.com'){    
    $client_usages='http://demo.competiscan.com/admin/manageUserTracker.php';
} else {
    $client_usages='https://competiscan.com/admin/manageUserTracker.php'; 
}
function pr($str){
    echo '<pre>';print_r($str);
}
$sql_last = "SELECT DATE_FORMAT(back_date, '%Y-%m-%d') AS last_date FROM cscan_daily_status_emails Where status=1 ORDER BY back_date DESC LIMIT 1";
$q_last = $DRW->query($sql_last,$DRW_read);
if($DRW->num_rows($q_last)>0){  
    $rs_last = $DRW->fetch_assoc($q_last);
    $fdt = $rs_last['last_date'];
    $tdt = $rs_last['last_date'];
}else{
    $fdt = date('Y-m-d');
    $tdt = date('Y-m-d');
}
$order_by = 'back_date DESC';
$order = 0;
//$limit = 50;
if(!empty($_REQUEST)){
    if(!empty($_REQUEST['fdt'])){
         $fdt = trim($_REQUEST['fdt']);
    }
    if(!empty($_REQUEST['tdt'])){
         $tdt = trim($_REQUEST['tdt']);
    }
} 
//select * from *table_name* where *datetime_column* >= '01/01/2009' and *datetime_column* <= curdate()

$sql = "SELECT * FROM cscan_daily_status_emails where status=1 and DATE_FORMAT(back_date, '%Y-%m-%d') >= '".$fdt."' And DATE_FORMAT(back_date, '%Y-%m-%d') <= '".$tdt."' ORDER BY $order_by";
$result = $DRW->query($sql,$DRW_read);
$numquery = $DRW->query("SELECT FOUND_ROWS()", $DRW_read);
$nrow = $DRW->fetch_row($numquery);
$numrows = $nrow[0];
?>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<table border="1" cellspacing='0' cellpadding='7' width="100%" bgcolor="white" style="border-collapse:collapse; border:1px solid #ccc;" class='text' bordercolor="#14734F" align="center">
    <tr class="adminhead"> 
        <td colspan="20" width="100%" align="center" height="25">
            Daily Status Reports
        </td>
    </tr> 
    <tr>
        <td class="bodyText" colspan="16">
            <table width='100%' border="0" cellspacing="0" cellpadding="1" class="text">
                <tr>
                    <form method="get" name="rangeForm" action="<?php echo $_SERVER['PHP_SELF']; ?>" style="display:inline;">
                        <td align="left" width='10%'><strong>Search By:</strong></td>
                        <td align="left" width='10%'><strong> From Date:</strong></td>
                        <td align="left" width='20%'>
                            <input type="text" id="fdt" readonly='true' name="fdt" size="20" maxlength="10" class="input_box" value="<?php echo $fdt; ?>" />
                        </td> 
                        <td align="right" width='10%'><strong> To Date:</strong></td>
                        <td align="right" width='20%'>
                            <input type="text" id="tdt" readonly='true' name="tdt" size="20" maxlength="10" class="input_box" value="<?php echo $tdt; ?>" />
                        </td>
                        <td align="left">
                            <input class="button" style="width:60px;" type="submit" name="search" value="Search" />
                        </td>
                    </form>
                </tr>
            </table>
        </td>
    </tr>
     <tr class="adminhead"> 
            
            <th  colspan="2"   align="center" height="25" style="border-right: 2px solid #ccc;">
            Email
            </th>
            <th colspan="2" align="center" height="25" style="border-right: 2px solid #ccc;">
                Direct Mail
            </th>
            <th colspan="2" align="center" height="25" style="border-right: 2px solid #ccc;">
               Digital Ads
            </th>
           
            <th colspan="2" align="center" height="25" style="border-right: 2px solid #ccc;">
              Email Alert
            </th>
             <th colspan="2" align="center" height="25" style="border-right: 2px solid #ccc;">
                Entry ID
            </th>
            
            <th colspan="2" align="center" height="25" style="border-right: 2px solid #ccc;">
                Visitors
            </th> 
            <!--<th colspan="2" align="center" height="25" style="border-right: 2px solid #ccc;">
                Client
            </th>-->
            <th colspan="1" align="center" height="25" style="border-right: 2px solid #ccc;">
                Date
            </th>
     </tr>
      <?php
    $bgcolor = '';
    if(!empty($numrows))
        { 
        $cnt=1;
    while ($row = $DRW->fetch_array($result)) {
       //echo "<pre>";
        //print_r($row);
        ?>
     <tr <?php if($cnt > 1){ ?>style="border-top:5px solid #ccc;"<?php } ?>>
         <td style="background-color:#ffffe6;">Consumer Panelist</td> <td style="background-color:#ffffe6;"><?php echo $row[2]; ?></td> 
         <td style="background-color:#e6ffe6;">Moved To Citi FTP</td><td style="background-color:#e6ffe6;"><?php echo $row[7]; ?></td>
         <td style="background-color:#eeeedd;">Online Display</td><td style="background-color:#eeeedd;"><?php echo $row[13]; ?></td> 
         <td style="background-color:#ffd9cc;">Engagement</td><td style="background-color:#ffd9cc;"><?php echo $row[10]; ?></td>
          <td style="background-color:#ffe6cc;" valign="top" rowspan="5">Total Entry ID</td><td style="background-color:#ffe6cc;" valign="top" rowspan="5"><?php echo $row[9]; ?></td>
          <td style="background-color:#f2ffe6;">User Visitors</td><td style="background-color:#f2ffe6;"><?php echo $row[11]; ?></td>
          <!--<td valign="top" rowspan="5">Usage</td><td valign="top" rowspan="5"> <a href="<?php echo $client_usages; ?>" target="_blank" >Click Here</a></td>-->
          <td style="background-color:#e6f2ff;" valign="top" rowspan="5"><?php echo date("Y-m-d",strtotime($row['back_date'])); ?></td>
     </tr>
     
    <tr>
         <td style="background-color:#ffffe6;">Provider Panelist </td><td style="background-color:#ffffe6;"><?php echo $row[3]; ?></td>
         <td style="background-color:#e6ffe6;" valign="top" rowspan="4">Duplicate Files</td><td style="background-color:#e6ffe6;" valign="top" rowspan="4"><?php echo $row[8]; ?></td>
         <td style="background-color:#eeeedd;">Search Engine marketings</td><td style="background-color:#eeeedd;"><?php echo $row[14]; ?></td>
         <td style="background-color:#ffd9cc;" valign="top" rowspan="4">Triggered</td><td style="background-color:#ffd9cc;" valign="top" rowspan="4"><?php echo $row[12]; ?></td>
         <td style="background-color:#f2ffe6;" valign="top" rowspan="4">Guest Visitors</td><td style="background-color:#f2ffe6;" valign="top" rowspan="4"><?php echo $row[17]; ?></td>
    </tr>
    
     <tr>
         <td style="background-color:#ffffe6;">Mortage Broker Panelist </td> <td style="background-color:#ffffe6;"><?php echo $row['4']; ?></td>
         <td style="background-color:#eeeedd;" valign="top" rowspan="3">Online Video</td><td style="background-color:#eeeedd;" valign="top" rowspan="3"><?php echo $row[15]; ?></td>
        
     </tr> 
     
    <tr>
         <td style="background-color:#ffffe6;">Producer Panelist </td> <td style="background-color:#ffffe6;"><?php echo $row[5]; ?></td>
     </tr>
     
     <tr>
         <td style="background-color:#ffffe6;">Total</td><td style="background-color:#ffffe6;"><?php echo $row[1]; ?></td> 
     </tr>
     
    <?php $cnt++; } ?>
        
        <?php } else{ ?>
        <tr><td colspan='16' align='center' class='error'>No record(s) found.</td></tr>
    <?php } ?> 
</table>
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
                buttonText: "Select from date",
                maxDate: new Date()
            });
        });
</script>
<?php include 'bottom.php'; ?>