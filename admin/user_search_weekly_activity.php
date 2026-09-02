<?php
#### for global groups  #####
$ALLOW_GROUPS = array(54);

require_once("../auth_auth.php");
require_once("../includes/functions.php");
$newts = strtotime('-6 days');
$fdt = date('Y-m-d',$newts);
$tdt = date('Y-m-d');

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
$limit = 20;
if(isset($_GET['p'])) $p = trim($_GET['p']);
else $p = 0;
$userID='';
$numrows='';
$username       =   '';
$emailAddress   =   '';
$companyName    =   '';
$uname  = '';
$email  = '';
$company_name = ''; 
        if(!empty($_REQUEST['fdt']))
            $fdt = trim($_REQUEST['fdt']);
        if(!empty($_REQUEST['tdt']))
            $tdt = trim($_REQUEST['tdt']);
        if(!isset($_SESSION['userdetails'])){
            $_SESSION['userdetails']='';
        }
        if(!isset($_SESSION['useractivity'])){
            $_SESSION['useractivity']='';
        }
        
        if(!empty($_POST['clear']) && trim($_POST['clear']) == 'Clear'){
            $_REQUEST['userdetails']    =   '';
            $_SESSION['userdetails'] = trim($_REQUEST['userdetails']); 
            $_REQUEST['useractivity']   =   '';
            $_SESSION['useractivity'] = trim($_REQUEST['useractivity']);
            $newts = strtotime('-6 days');
            $fdt = date('Y-m-d',$newts);
            $tdt = date('Y-m-d');
        }
        
        //$_SESSION['userdetails'] = (isset($_REQUEST['userdetails']))? (trim($_REQUEST['userdetails'])):''; 
        //$_SESSION['useractivity'] = (isset($_REQUEST['useractivity']))? (trim($_REQUEST['useractivity'])):''; 
        
        if(isset($_REQUEST['userdetails'])){
            $_SESSION['userdetails'] =$_REQUEST['userdetails'];
        }
        if(isset($_REQUEST['useractivity'])){
            $_SESSION['useractivity'] =$_REQUEST['useractivity'];
        }
        $to_date = date('Y-m-d', strtotime($tdt . ' +1 day'));

        $conditions=" where sa.querydate>='".$fdt."' AND sa.querydate<'".$to_date."' AND LOWER(su.companyName)!='suntec' AND LOWER(su.companyName)!='competiscan' AND LOWER(su.companyName)!='competiscan (research)' AND LOWER(su.companyName)!='nmg'";

        if(!empty($_SESSION['userdetails'])){    
            $conditions .=" AND su.emailAddress like '".$_SESSION['userdetails']."%'";      

        }
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
            $sql = "SELECT sa.activity_id,sa.userID,sa.querydate,sa.total_download ,su.firstName,su.lastName,su.emailAddress,su.companyName FROM cscan_search_activity sa 
                    left join cscan_users su on(su.userID=sa.userID) $conditions  ORDER BY sa.querydate desc ";
            
            $rs = $DRW->query($sql,$DRW_read);
            $searchtable='cscan_search_activity'; 
            $arrExport['data'][] = array("", "Report From $fdt to $tdt", "","","","");
            $arrExport['data'][] = array("", "", "","","","",);
            $arrExport['data'][] = array("Search Date", "Search Criteria","User Name","Email Address","Company", "Total Download");
          
            while($row = $DRW->fetch_assoc($rs)){
                 list($displayKeywords) = getKeywords($row['activity_id'],$searchtable);                 
                 $uname_exp  = $row['firstName'].' '.$row['lastName'];
                 $email_exp  = $row['emailAddress'];
                 $company_name_exp = $row['companyName'];
                 $arrExport['data'][] = array($row['querydate'], html_entity_decode(strip_tags($displayKeywords)),$uname_exp,$email_exp,$company_name_exp, (!empty($row['total_download'])) ? $row['total_download']: 0);
            }    
            download_send_headers("user_weekly_activity_" . date("Y-m-d") .rand(). ".csv");
            echo array2csv($arrExport);
            die();
        } 
        
       $sql = "SELECT sa.activity_id,sa.userID,sa.querydate,sa.total_download ,su.firstName,su.lastName,su.emailAddress,su.companyName FROM cscan_search_activity sa 
        left join cscan_users su on(su.userID=sa.userID) $conditions  ORDER BY sa.querydate desc LIMIT $p, $limit";  
       $rs = $DRW->query($sql,$DRW_read);
       $sql_count = "SELECT count(*) FROM cscan_search_activity sa 
        left join cscan_users su on(su.userID=sa.userID) $conditions  ";       
       $numquery = $DRW->query($sql_count, $DRW_read);
       $nrow = $DRW->fetch_row($numquery);
       $numrows = $nrow[0];
?>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<table width='100%' border='0' cellspacing='0' cellpadding='8' class='text' rules='none' bordercolor='#B9B9B9' align='center' style='border-collapse:collapse'>
    <tr><td class="adminhead" align='center'>User Search Weekly Activity</td></tr>
    <!-- search and right buttons start-->
    <tr>
        <td class="bodyText">
            <form method="post" name="rangeForm" action="<?php echo $_SERVER['PHP_SELF']; ?>" style="display:inline;">
                <table width='100%' border="0" cellspacing="0" cellpadding="1" class="text">
                    <tr>
                        
                        <td align="left" width='10%'><strong>Search By:</strong> &nbsp;</td>
                         
                        
                        <td align="right" width='10%'><strong>User:</strong> &nbsp;
                        </td>
                        <td align="right" width='20%'>
                            <input type="text" style="width:300px;" name="userdetails" placeholder="Please enter the email address." id="userdetails" value="<?php echo $_SESSION['userdetails'];?>">
                        </td>
                        
                        <td align="right"> &nbsp;<strong>Activity:</strong>
                        </td>
                        <td align="right">
                           
                            <select name="useractivity" id="useractivity" style="width:150px;">
                                <option value="">All</option>
                                <option value="1"  <?php if($_SESSION['useractivity']==1){?> selected="selected" <?php }?>>Downloaded</option>
                                <option value="2" <?php if($_SESSION['useractivity']==2){?> selected="selected" <?php }?>>Only Search</option>
                            </select>
                        </td>
                        
                        <td align="left" width='20%'>
                             &nbsp;<input class="button" style="width:60px;" type="submit" name="search" value="Search" />
                            <?php //if(isset($_REQUEST['userdetails']) && $_REQUEST['userdetails']!=''):?>
                            &nbsp;&nbsp;<input class="button" style="width:60px;" type="submit" name="clear" value="Clear" />
                           <?php //endif;?>
                        </td>
                        
                        <td align="right">
                             <?php if(!empty($numrows)):?>
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
                    <tr><td colspan="7" align="right">&nbsp;<a href="user_search_activity.php" style="text-decoration:none;"><input class="button" style="width:80px;" type="button" name="Back" value="Back" /></a></td></tr>
                </table>
            </form>
            
        </td>
    </tr>
   
    <tr>
        <td>
            <table width='100%' border='0' cellspacing='0' cellpadding='8' class='text' rules='none' bordercolor='#B9B9B9' align='center' style='border-collapse:collapse'>
                <tr><td colspan="3">&nbsp;</td></tr>
                <tr>
                    <td align="left" width='20%' class="adminhead" height='15px' ><b>Search Date</b></td>
                    <td align="left" width='40%' class="adminhead" height='15px' ><b>Search Criteria</b></td>
                    <td align="left" width='20%' class="adminhead" height='15px' ><b>User</b></td>
                    <td align="left" width='15%' class="adminhead" height='15px' ><b>Company</b></td>
                    <td align="left" width='5%' class="adminhead" height='15px' ><b>Total Downlod</b></td>
                </tr>
                 <?php if(!empty($numrows)){ ?>
                <?php
                $className='';
                $searchtable='cscan_search_activity';
                while($row = $DRW->fetch_assoc($rs)){
                    if ($className=='selected-bg') $className='white-bg';
                    else $className='selected-bg';
                    list($displayKeywords) = getKeywords($row['activity_id'],$searchtable);
                    
                    $uname  = $row['firstName'].' '.$row['lastName'];
                    $email  = $row['emailAddress'];
                    $company_name   = $row['companyName']; 
                 ?>
                    <tr valign=top class="<?php echo $className; ?>">
                        <td><?php echo $row['querydate'];?></td>  
                        <td align="left"><?php echo ($displayKeywords);?></td>
                        <td align="left"><?php echo $uname.'<br>('.$email.')';?></td>
                        <td align="left"><?php echo $company_name;?></td>
                        <td align="left"><?php echo (!empty($row['total_download']))?$row['total_download']:0;?></td>
                    </tr>
                <?php }?>
                    <tr>
                        <td colspan="5">
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
                                    $firstlink = "[<a href=\"{$_SERVER['PHP_SELF']}?p=0&fdt={$fdt}&tdt={$tdt}\">First</a>]";
                                    $prevlink = "<a href=\"{$_SERVER['PHP_SELF']}?p=$prev&fdt={$fdt}&tdt={$tdt}\">&laquo; Prev $limiter</a>";
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
                                        $middlelinks .= "<a href=\"{$_SERVER['PHP_SELF']}?p=$startnum&fdt={$fdt}&tdt={$tdt}\">".($i+1)."</a> ";
                                    }
                                    else $middlelinks .= ($i+1).' ';
                                }
                                //next and last if not on last
                                if($limstart<$rowcnt && (($limstart+($limiter*2))<$rowcnt || ($rowcnt - ($limstart + $limiter))>0)){
                                    $next = $limstart + $limiter;
                                    $nextlink = "<a href=\"{$_SERVER['PHP_SELF']}?p=$next&fdt={$fdt}&tdt={$tdt}\">Next $limiter &raquo;</a>";
                                    $lastlink = "[<a href=\"{$_SERVER['PHP_SELF']}?p=".(($numbers-1)*$limiter)."&fdt={$fdt}&tdt={$tdt}\">Last</a>]";
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
                        <tr><td colspan='5' align='center' class='error selected-bg'>No record(s) found.</td></tr>
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