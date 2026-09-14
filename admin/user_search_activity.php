<?php
#### for global groups  #####
$ALLOW_GROUPS = array(54);

require_once("../auth_auth.php");
require_once("../includes/functions.php");
$fdt = $tdt = date('Y-m-d');

// $_SESSION['userdetails']='';
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

if(!empty($_REQUEST)){
    if(isset($_REQUEST['userdetails'])){
       $_SESSION['userdetails'] = trim($_REQUEST['userdetails']);            
    }        
}
if(!isset($_POST['export'])){
include 'top.php';
}
$userID='';
$numrows='';
$username       =   '';
$emailAddress   =   '';
$companyName    =   '';
if(!empty($_REQUEST['fdt']))
    $fdt = trim($_REQUEST['fdt']);
if(!empty($_REQUEST['tdt']))
    $tdt = trim($_REQUEST['tdt']);
if(!empty($_POST['clear']) && trim($_POST['clear']) == 'Clear'){
    $_REQUEST['userdetails']    =   '';
    $_SESSION['userdetails'] = trim($_REQUEST['userdetails']); 
    $_REQUEST['useractivity']   =   '';
    $_SESSION['useractivity'] = trim($_REQUEST['useractivity']);
    $fdt = $tdt = date('Y-m-d');
}

$_SESSION['userdetails'] = (isset($_REQUEST['userdetails']))? (trim($_REQUEST['userdetails'])):''; 
$_SESSION['useractivity'] = (isset($_REQUEST['useractivity']))? (trim($_REQUEST['useractivity'])):''; 

    
if(!empty($_SESSION['userdetails'])){
     //$users_conditions=" select userID,CONCAT(firstName,' ',lastName)as username,emailAddress,companyName from cscan_users where lastName='".$_SESSION['userdetails']."' OR  emailAddress='".$_SESSION['userdetails']."' OR firstName='".$_SESSION['userdetails']."' limit 1 ";
     $users_conditions=" select userID,CONCAT(firstName,' ',lastName)as username,emailAddress,companyName from cscan_users where emailAddress like '".$_SESSION['userdetails']."%' limit 1 ";
     
     $row_rs  =   $DRW->query($users_conditions,$DRW_read);
     $nrow = $DRW->fetch_assoc($row_rs);
     if(!empty($nrow['userID'])){
        $userID         =   $nrow['userID'];
        $username       =   $nrow['username'];
        $emailAddress   =   $nrow['emailAddress'];
        $companyName    =   $nrow['companyName'];  
        $to_date = date('Y-m-d', strtotime($tdt . ' +1 day'));
        $conditions=" where userID='".$userID."' AND querydate>='".$fdt."' AND querydate<'".$to_date."'"; 
        if(!empty($_SESSION['useractivity'])){
            if($_SESSION['useractivity']=='1'){
                $conditions.= " AND total_download>0 ";
            }
            else if($_SESSION['useractivity']=='2'){
                $conditions.= " AND total_download is null ";
            }
        }
        
        if(!empty($_POST['export']) && trim($_POST['export']) == 'Export'){//pr($_POST);die;
            $arrExport = array();
            $sql = "SELECT activity_id,querydate,total_download FROM cscan_search_activity $conditions  ORDER BY querydate desc ";
            $rs = $DRW->query($sql,$DRW_read);
            $searchtable='cscan_search_activity'; 
            $arrExport['data'][] = array("User Name: $username", "Email Address: $emailAddress", "Company:  $companyName");
            $arrExport['data'][] = array("", "", "");
            $arrExport['data'][] = array("Search Date", "Search Criteria", "Total Download");
          
            while($row = $DRW->fetch_assoc($rs)){
                 list($displayKeywords) = getKeywords($row['activity_id'],$searchtable);
                 $arrExport['data'][] = array($row['querydate'], strip_tags($displayKeywords), (!empty($row['total_download'])) ? $row['total_download']: 0);

            }    
            download_send_headers("user_activity_" . date("Y-m-d") .rand(). ".csv");
            echo array2csv($arrExport);
            die();
        } 
        
       $sql = "SELECT activity_id,querydate,total_download FROM cscan_search_activity $conditions  ORDER BY querydate desc ";
       $rs = $DRW->query($sql,$DRW_read);
       $numquery = $DRW->query("SELECT FOUND_ROWS()", $DRW_read);
       $nrow = $DRW->fetch_row($numquery);
       $numrows = $nrow[0];
    
    }
}

?>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<table width='100%' border='0' cellspacing='0' cellpadding='8' class='text' rules='none' bordercolor='#B9B9B9' align='center' style='border-collapse:collapse'>
    <tr><td class="adminhead" align='center'>User Search Activity</td></tr>
    <!-- search and right buttons start-->
    <tr>
        <td class="bodyText">
            <form method="post" name="rangeForm" action="<?php echo $_SERVER['PHP_SELF']; ?>" style="display:inline;">
                <table width='100%' border="0" cellspacing="0" cellpadding="1" class="text">
                    <tr>
                        
                        <td align="left" width='10%'><strong>Search By:</strong></td>
                         
                        
                        <td align="right" width='10%'><strong>User:</strong>
                        </td>
                        <td align="right" width='20%'>
                            <input type="text" style="width:300px;" name="userdetails" placeholder="Please enter the email address." id="userdetails" value="<?php echo $_SESSION['userdetails'];?>">
                        </td>
                        
                        <td align="right"><strong>Activity:</strong>
                        </td>
                        <td align="right">
                           
                            <select name="useractivity" id="useractivity" style="width:150px;">
                                <option value="">All</option>
                                <option value="1"  <?php if($_SESSION['useractivity']==1){?> selected="selected" <?php }?>>Downloaded</option>
                                <option value="2" <?php if($_SESSION['useractivity']==2){?> selected="selected" <?php }?>>Only Search</option>
                            </select>
                        </td>
                        
                        <td align="left" width='20%'>
                            <input class="button" style="width:60px;" type="submit" name="search" value="Search" />
                            <?php if(isset($_REQUEST['userdetails']) && $_REQUEST['userdetails']!=''):?>
                            <input class="button" style="width:60px;" type="submit" name="clear" value="Clear" />
                           <?php endif;?>
                        </td>
                        
                        <td align="right">
                             <?php if(isset($_REQUEST['userdetails']) && $_REQUEST['userdetails']!=''):?>
                            <input class="button" style="width:60px;" type="submit" name="export" value="Export" />
                            <?php endif;?>
                        </td>
                    </tr>
                    <tr><td colspan="7">&nbsp;</td></tr>
                    <tr>
                        <td align="left" width='10%'><strong>Date Range:</strong></td>
                        <td align="center" colspan="4"><strong>From Date:</strong>
                        <input type="text" id="fdt" readonly='true' style="width:150px;" name="fdt" size="20" maxlength="10" class="input_box2345" value="<?php echo $fdt; ?>" />
                        &nbsp;&nbsp;<strong>To Date:</strong>
                        <input type="text" id="tdt" readonly='true' name="tdt" size="20" style="width:150px;" maxlength="10" class="input_box234" value="<?php echo $tdt; ?>" />
                        </td>
                        <td colspan="2">&nbsp;</td>                        
                    </tr>
                    <tr><td colspan="7">&nbsp;</td></tr>
                    <tr><td colspan="7" align="right">&nbsp;<a href="user_search_weekly_activity.php" style="text-decoration:none;"><input class="button" style="width:160px;" type="button" name="Go For Weekly Report" value="Go For Weekly Report" /></a></td></tr>
                </table>
            </form>
            
        </td>
    </tr>
   
    
    
    <tr>
    <?php if(!empty($username)){?>    
    <tr>
        <td>
            <table width='100%' border='0' cellspacing='0' cellpadding='8' class='text' rules='none' bordercolor='#B9B9B9' align='center' style='border-collapse:collapse;margin:20px 0px -20px 0px;'>
                <tr><th>User Name:</th>
                    <td> <?php echo $username;?>
                    </td>   
                    <th>Email Address:</th>
                    <td> <?php echo $emailAddress;?>
                    </td>   
                    <th>Company:</th>
                    <td> <?php echo $companyName;?>
                    </td>   
                    
                    
                    
                </tr>
                
            </table>
        </td>
    </tr>
    <?php }?>
    <tr>
        <td>
            <table width='100%' border='0' cellspacing='0' cellpadding='8' class='text' rules='none' bordercolor='#B9B9B9' align='center' style='border-collapse:collapse'>
                <tr><td colspan="3">&nbsp;</td></tr>
                <tr>
                    <td align="left" width='20%' class="adminhead" height='15px' ><b>Search Date</b></td>
                    <td align="left" width='65%' class="adminhead" height='15px' ><b>Search Criteria</b></td>
                    <td align="left" width='15%' class="adminhead" height='15px' ><b>Total Downlod</b></td>
                </tr>
                 <?php if(!empty($numrows)){ ?>
                <?php
                $className='';
                $searchtable='cscan_search_activity';
                while($row = $DRW->fetch_assoc($rs)){
                    if ($className=='selected-bg') $className='white-bg';
                    else $className='selected-bg';
                    list($displayKeywords) = getKeywords($row['activity_id'],$searchtable);
                 ?>
                    <tr valign=top class="<?php echo $className; ?>">
                        <td><?php echo $row['querydate'];?></td>  
                        <td align="left"><?php echo ($displayKeywords);?></td>
                        <td align="left"><?php echo (!empty($row['total_download']))?$row['total_download']:0;?></td>
                    </tr>
                <?php }?>
                    
                <?php 
                    }else{?>
                        <tr><td colspan='4' align='center' class='error selected-bg'>No record(s) found.</td></tr>
                <?php }?>    
            </table>
        </td>
    </tr>  

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
                buttonText: "Select to date",
                maxDate: new Date()
            });
        });
    </script>
<?php include 'bottom.php';?>