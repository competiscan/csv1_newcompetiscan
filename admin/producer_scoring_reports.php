<?php
$ALLOW_GROUPS = array(104);
require_once("../auth_auth.php");
$msg = '';
$success = '';
if (!empty($_POST['resetid'])) {
    $id = $_POST['resetid'];
    $totaldata = $_POST['totaldata'];
    if ($totaldata >= MAX_RESET_POINT) {
        $msg = resetdata($id);
    }
}
######### for direct point #################
if (!empty($_POST['directmail_point'])) {
    $userid = $GLOBALS['AUTH_DATA']['userID'];
    $date = date('Y-m-d H:i:s');
    foreach ($_POST['directmail_point'] as $key => $val) {
        $sqlhistory = "INSERT INTO cscan_producer_scoring_report_history (id,panelist_id,competi_id,parent_panelist_id,direct_mail_piece,direct_mail_point,
                                    email_piece,email_piece_point,bonus_point,total_point,bag_remaining,entry_date,bagupdate_by,bagupdate_date,reset_by,reset_date,add_bonus_point_date,add_bonus_point_by
                                    )
                                    SELECT id,panelist_id,competi_id,parent_panelist_id,direct_mail_piece,direct_mail_point,
                                    email_piece,email_piece_point,bonus_point,total_point,bag_remaining,entry_date,bagupdate_by,bagupdate_date,reset_by,reset_date,add_bonus_point_date,add_bonus_point_by
                                    FROM   cscan_producer_scoring_total_reports
                                WHERE  id ='" . $key . "'
                                ";
        $DRW->query($sqlhistory, $DRW_main);
        $addsql = '';
        if ($val > 0) {
            //$addsql = ", add_bonus_point_by='" . $userid . "',add_bonus_point_date='" . $date . "' ";
        }
        $updatesql = "update cscan_producer_scoring_total_reports set
                             direct_mail_point='" . $val . "',
                             total_point=(email_piece_point+bonus_point+" . $val . ")   
                             $addsql     
                             where id = '" . $key . "'
                           ";
        $ressql     =   $DRW->query($updatesql, $DRW_main);
        $sqlselpanelist    =   "SELECT panelist_id FROM cscan_producer_scoring_total_reports
                                WHERE  id ='".$key."'
                            ";
        $rspanelist  =   $DRW->query($sqlselpanelist,$DRW_main);        
        $rowpanelist =   $DRW->fetch_assoc($rspanelist);
        $panelist_id =   $rowpanelist['panelist_id'];
        $updaterep   =   "update cscan_producer_scoring_report set
                             direct_mail_point='" . $val . "'                              
                             where panelist_id = '" . $panelist_id . "'
                           ";
        $ressqlrep = $DRW->query($updaterep, $DRW_main);
       
        
        $msg=5;
        //header("location:consumer_scoring_report.php?msg=3");
    }
}
######### end for Directmail point #################
######### for reset bonus point #################
if (!empty($_POST['bonus_point'])) {
    $userid = $GLOBALS['AUTH_DATA']['userID'];
    $date = date('Y-m-d H:i:s');
    foreach ($_POST['bonus_point'] as $key => $val) {
        $sqlhistory = "INSERT INTO cscan_producer_scoring_report_history (id,panelist_id,competi_id,parent_panelist_id,direct_mail_piece,direct_mail_point,
                                    email_piece,email_piece_point,bonus_point,total_point,bag_remaining,entry_date,bagupdate_by,bagupdate_date,reset_by,reset_date,add_bonus_point_date,add_bonus_point_by
                                    )
                                    SELECT id,panelist_id,competi_id,parent_panelist_id,direct_mail_piece,direct_mail_point,
                                    email_piece,email_piece_point,bonus_point,total_point,bag_remaining,entry_date,bagupdate_by,bagupdate_date,reset_by,reset_date,add_bonus_point_date,add_bonus_point_by
                                    FROM   cscan_producer_scoring_total_reports
                                WHERE  id ='" . $key . "'
                                ";
        $DRW->query($sqlhistory, $DRW_main);
        $addsql = '';
        if ($val > 0) {
            $addsql = ", add_bonus_point_by='" . $userid . "',add_bonus_point_date='" . $date . "' ";
        }
        $updatesql = "update cscan_producer_scoring_total_reports set
                             bonus_point='" . $val . "',
                             total_point=( direct_mail_point+email_piece_point+" . $val . ")   
                             $addsql     
                             where id = '" . $key . "'
                           ";
        $ressql     =   $DRW->query($updatesql, $DRW_main);
        $sqlselpanelist    =   "SELECT panelist_id FROM cscan_producer_scoring_total_reports
                                WHERE  id ='".$key."'
                            ";
        $rspanelist  =   $DRW->query($sqlselpanelist,$DRW_main);        
        $rowpanelist =   $DRW->fetch_assoc($rspanelist);
        $panelist_id =   $rowpanelist['panelist_id'];
        $updaterep   =   "update cscan_producer_scoring_report set
                             bonus_point='" . $val . "'                              
                             where panelist_id = '" . $panelist_id . "'
                           ";
        $ressqlrep = $DRW->query($updaterep, $DRW_main);
       
        
        $msg=3;
        //header("location:consumer_scoring_report.php?msg=3");
    }
}
######### end for reset bonus point #################
######### for reset remaining bags #################
if (!empty($_POST['remainingbags'])) {
    $userid = $GLOBALS['AUTH_DATA']['userID'];
    $date = date('Y-m-d H:i:s');
   
    foreach ($_POST['remainingbags'] as $key => $val) {
        
        $sqlselect = "SELECT id,bag_remaining
                                    FROM   cscan_producer_scoring_total_reports
                                 WHERE  id ='" . $key . "' ";
        $rsselect = $DRW->query($sqlselect, $DRW_read2);
        if ($DRW->num_rows($rsselect) > 0) {
            $rowsel = $DRW->fetch_assoc($rsselect);
            if ($val != $rowsel['bag_remaining']) {
                $sqlhistory = "INSERT INTO cscan_producer_scoring_report_history (id,panelist_id,competi_id,parent_panelist_id,direct_mail_piece,direct_mail_point,
                                    email_piece,email_piece_point,total_point,bag_remaining,entry_date,bagupdate_by,bagupdate_date,reset_by,reset_date,add_bonus_point_date,add_bonus_point_by 
                                    )
                                    SELECT id,panelist_id,competi_id,parent_panelist_id,direct_mail_piece,direct_mail_point,
                                    email_piece,email_piece_point,total_point,bag_remaining,entry_date,bagupdate_by,bagupdate_date,reset_by,reset_date,add_bonus_point_date,add_bonus_point_by
                                    FROM   cscan_producer_scoring_total_reports
                                 WHERE  id ='" . $key . "'
                                ";
                $DRW->query($sqlhistory, $DRW_main);
                $addsql = '';
                if ($val > 0) {
                    $addsql = ", bagupdate_by='" . $userid . "',bagupdate_date='" . $date . "' ";
                }
                $updatesl = "update cscan_producer_scoring_total_reports set
                                      bag_remaining='" . $val . "'
                                      $addsql   
                                      where id = '" . $key . "'
                                    ";
                $rsreset = $DRW->query($updatesl, $DRW_main);
            }
        }
        $msg=1;
        //header("location:producer_scoring_reports.php?msg=2");
    }
}
######### end for reset remaining bags #################

if (!empty($_REQUEST['msg'])) {
    $msg = $_REQUEST['msg'];
}
if (!empty($_POST['resetpoint']) && $_POST['resetpoint'] == 'Reset Point') {
    if (!empty($_POST['competiids'])) {
        foreach ($_POST['competiids'] as $competiids) {
            $competiarray = explode("###", $competiids);
            $competi_id = $competiarray[0];
            $totaldata = $competiarray[1];
            if ($totaldata >= MAX_RESET_POINT) {
                $success = resetdata($competi_id);
            }
        }
        $msg = $success;
    }
}


function resetdata($id) {
    global $DRW, $DRW_main, $DRW_read2, $DRW_digital;
    $msg = '';
    $userid = $GLOBALS['AUTH_DATA']['userID'];
    $sqlrest = " SELECT id,panelist_id, competi_id, 
                    direct_mail_point, email_piece, email_piece_point,bonus_point, total_point, bag_remaining, entry_date
                FROM cscan_producer_scoring_total_reports where panelist_id = '" . $id . "'";
    $rsreset = $DRW->query($sqlrest, $DRW_read2);
    if ($DRW->num_rows($rsreset) > 0) {
        $rowreset = $DRW->fetch_assoc($rsreset);
        if ($rowreset['total_point'] >= MAX_RESET_POINT) {
            $newtotal               =   ($rowreset['total_point'] - MAX_RESET_POINT);
            $newpanelist_id         =   $rowreset['panelist_id'];
            $newbonus_point         =   $rowreset['bonus_point'];
            $newdirect_mail_point   =   $rowreset['direct_mail_point'];
            $newemail_piece         =   $rowreset['email_piece'];
            $newemail_piece_point   =   $rowreset['email_piece_point'];
            $date                   =   date('Y-m-d H:i:s');
            $remaining_total_point  =   MAX_RESET_POINT;
            if ($remaining_total_point > 0) {
                if (($rowreset['bonus_point'] >= $remaining_total_point)) {
                    $newbonus_point = (($rowreset['bonus_point']) - $remaining_total_point);
                    $remaining_total_point = 0;
                } else {
                    $newbonus_point = 0;
                    $remaining_total_point = ($remaining_total_point - $rowreset['bonus_point']);
                }
            }
           
            if ($remaining_total_point > 0) {
                if (($rowreset['email_piece_point'] >= $remaining_total_point)) {
                    $newemail_piece_point = (($rowreset['email_piece_point']) - $remaining_total_point);
                    $newemail_piece = round(($newemail_piece_point / EMAIL_PIECE_MULTIPLIER_PRODUCER), 2);
                    $remaining_total_point = 0;
                } else {
                    $newemail_piece_point = 0;
                    $newemail_piece = 0;
                    $remaining_total_point = ($remaining_total_point - ($rowreset['email_piece_point']));
                }
            }
            
            if ($remaining_total_point > 0) {
                if (($rowreset['direct_mail_point'] >= $remaining_total_point)) {
                    $newdirect_mail_point = ($rowreset['direct_mail_point'] - $remaining_total_point);
                    $remaining_total_point = 0;
                } else {
                    $newdirect_mail_point = 0;
                    $remaining_total_point = ($remaining_total_point - $rowreset['direct_mail_point']);
                }
            }
            
            
            $sqlhistory = "INSERT INTO cscan_producer_scoring_report_history (id,panelist_id,competi_id,parent_panelist_id,direct_mail_piece,direct_mail_point,
                                    email_piece,email_piece_point,bonus_point,total_point,bag_remaining,entry_date,reset_status,bagupdate_by,bagupdate_date,reset_by,reset_date,add_bonus_point_date,add_bonus_point_by
                                    )
                                    SELECT id,panelist_id,competi_id,parent_panelist_id,direct_mail_piece,direct_mail_point,
                                    email_piece,email_piece_point,bonus_point,total_point,bag_remaining,entry_date,1,bagupdate_by,bagupdate_date,reset_by,reset_date,add_bonus_point_date,add_bonus_point_by
                                    FROM   cscan_producer_scoring_total_reports
                                WHERE  id ='" . $id . "' 
                                "; 
            $DRW->query($sqlhistory, $DRW_main);
            $updatesl = "update cscan_producer_scoring_total_reports set
                             total_point='" . $newtotal . "',
                             direct_mail_point='" . $newdirect_mail_point . "',
                             email_piece='" . $newemail_piece . "',
                             email_piece_point='" . $newemail_piece_point . "',
                             reset_by='" . $userid . "',   
                             bonus_point='" . $newbonus_point . "',    
                             reset_date='" . $date . "'    
                             where id = '" . $rowreset['id'] . "'
                           ";
            $rsreset = $DRW->query($updatesl, $DRW_main);
            
           /* $updatebonus = "update cscan_producer_scoring_report set
                             bonus_point='" . $newbonus_point . "',
                             direct_mail_point='" . $newdirect_mail_point . "',
                             email_piece='" . $newemail_piece . "',
                             email_piece_point='" . $newemail_piece_point . "',                            
                             where panelist_id = '" . $newpanelist_id . "'
                           ";
            $rsbonus = $DRW->query($updatebonus, $DRW_main);
            $updatepointreport = "update cscan_producer_scoring_report set
                             bonus_point='0',
                             direct_mail_piece='0',
                             direct_mail_point='0',
                             email_piece='0',
                             email_piece_point='0',                            
                             digital_point='0',
                             total_point='0',
                             bag_remaining='0'
                             where parent_panelist_id = '" . $newpanelist_id . "'
                           ";
            $rspointreport = $DRW->query($updatepointreport, $DRW_main);*/


            if ($rsreset) {
                $msg = 1;
            }
        }
    }
    return $msg;
}



function array2csv(array $array) {
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

$tdt = date('Y-m-d');
$order_by = '';
$order_bytxt='';
$order = 0;
$sort = '';
$competi_id = '';
$conditions = ' Where 1 ';
$panelist_type = '';
if (!empty($_REQUEST['competi_id'])) {
    $competi_id = trim($_REQUEST['competi_id']);
    $seasrcharray = array("'", '"', ' ');
    $replacearray = array("", "", "");
    $competiarray = explode(",", str_replace($seasrcharray, $replacearray, $competi_id));
    $newarraystr = trim(implode("','", $competiarray));
    $conditions .= "  AND psrt.competi_id in( '" . $newarraystr . "') ";
    
}
if (!empty($_REQUEST['panelist_type'])) {
    $panelist_type = $_REQUEST['panelist_type'];
    if ($panelist_type == '1') {
        $conditions .= "  AND total_point>='" . MAX_RESET_POINT . "' ";
    } else if ($panelist_type == '2') {
        $conditions .= "  AND (bag_remaining=0 OR bag_remaining is NULL)";
    }
}
if (!empty($_REQUEST['clear']) && trim($_REQUEST['clear']) == 'Clear') {
    
    $tdt = date('Y-m-d');
    header("location:producer_scoring_reports.php");
}

$filter = array();
if (!empty($_REQUEST)) {
    if (!empty($_REQUEST['sort']))
        $sort = trim($_REQUEST['sort']);
    if (!empty($_REQUEST['order']))
        $order = trim($_REQUEST['order']);
    if (!empty($sort)) {
        switch ($sort) {
            case '1':
                $order_by = 'competi_id';
                break;
            case '2':
                $order_by = 'direct_mail_point';
                break;
            case '3':
                $order_by = 'email_piece';
                break;
            case '4':
                $order_by = 'email_piece_point';
                break;
            case '5':
                $order_by = 'bonus_point';
                break;
            case '6':
                $order_by = 'total_point';
                break;
            case '7':
                $order_by = 'bag_remaining';
                break;
            case '8':
                $order_by = 'first_name';
                break;
            case '9':
                $order_by = 'last_name';
                break;
            case '10':
                $order_by = 'address';
                break;
            case '11':
                $order_by = 'city';
                break;
            case '12':
                $order_by = 'state';
                break;
            case '13':
                $order_by = 'postalcode';
                break;
            case '14':
                $order_by = 'email';
                break;
            default:
                $order_by = 'panelist_id';
                break;
        }
        if (empty($order)) {
            $order_by .= ' ASC';
        } else {
            $order_by .= ' DESC';
        }
    }
}
if(!empty($order_by))
    $order_bytxt=" ORDER BY ";

if (!empty($_REQUEST['export']) && trim($_REQUEST['export']) == 'Export') {//pr($_POST);die;
    $arrExport = array();
    $exp_sql = "SELECT SQL_CALC_FOUND_ROWS id, psrt.panelist_id, psrt.competi_id,direct_mail_point, email_piece, email_piece_point, 
        bonus_point, total_point, bag_remaining,first_name,last_name,address,city,state,postalcode,email 
        FROM cscan_producer_scoring_total_reports psrt left join cscan_panelists cp on psrt.competi_id=cp.competi_id 
                 $conditions GROUP BY  
                panelist_id $order_bytxt $order_by";
    $exp_rs = $DRW->query($exp_sql, $DRW_read2);
    if (!empty($exp_rs)) {
        $arrExport['data'][] = array("Panelist ID","Direct Mail Point", "EMail Piece", "Email Point","Bonus Point", "Total Point", "Bags Remaining","First Name","Last Name","Address","City","State","Zipcode","Email");
        while ($exp_row = $DRW->fetch_assoc($exp_rs)) {
            $direct_mail_point = $exp_row['direct_mail_point'];
            $email_piece = $exp_row['email_piece'];
            $email_piece_point = $exp_row['email_piece_point'];
            $bag_remaining = $exp_row['bag_remaining'];
            $bonus_point = $exp_row['bonus_point'];
            $total_point = $exp_row['total_point'];
            $fname = $exp_row['first_name'];
            $last_name = $exp_row['last_name'];
            $address = $exp_row['address'];
            $city = $exp_row['city'];
            $state = $exp_row['state'];
            $postalcode = $exp_row['postalcode'];
            $email = $exp_row['email'];
            if (empty($bag_remaining))
                $bag_remaining = 0;
            $arrExport['data'][] = array($exp_row['competi_id'],
                $direct_mail_point, $email_piece, $email_piece_point,
                $bonus_point, $total_point, $bag_remaining,$fname,$last_name,$address,$city,$state,$postalcode,$email,
            );
        }
    }
    download_send_headers("producer_scoring_report" . date("Y-m-d") . ".csv");
    echo array2csv($arrExport);
    die();
}
include 'top.php';
$limit = 50;
if (isset($_GET['p']))
    $p = trim($_GET['p']);
else
    $p = 0;

function doSort($sort, $order, $dosort, $p, $competi_id, $panelist_type, $spacer = ' : ') {
    if (empty($order)) {
        $order = 1;
    } else {
        $order = 0;
    }
    $addparam = '';
    if (!empty($_REQUEST['b'])) {
        $addparam = "&b=" . $_REQUEST['b'];
    }
    $addparam2 = '';
    if (!empty($_REQUEST['r'])) {
        $addparam2 = "&r=" . $_REQUEST['r'];
    }
    if ($sort == ($dosort * -1) || $sort != $dosort) {
        print "$spacer<a href=\"" . $_SERVER['PHP_SELF'] . "?competi_id={$competi_id}&panelist_type={$panelist_type}&sort=$dosort&order=$order&p=$p$addparam\" class=\"blue\">sort</a>";
    } else {
        print "$spacer<a href=\"" . $_SERVER['PHP_SELF'] . "?competi_id={$competi_id}&panelist_type={$panelist_type}&sort=$dosort&order=$order&p=$p$addparam\" class=\"blue\">sort</a>";
    }
}

$sql = "SELECT SQL_CALC_FOUND_ROWS id, psrt.panelist_id, psrt.competi_id,direct_mail_point, email_piece, email_piece_point, 
        bonus_point, total_point, bag_remaining, entry_date,first_name,last_name,address,city,state,postalcode,email 
        FROM cscan_producer_scoring_total_reports psrt left join cscan_panelists cp on psrt.competi_id=cp.competi_id 
        $conditions $order_bytxt $order_by LIMIT $p, $limit
        ";
//echo $sql;       
$rs = $DRW->query($sql, $DRW_read2);
$numquery = $DRW->query("SELECT FOUND_ROWS()", $DRW_read2);
$nrow = $DRW->fetch_row($numquery);
$numrows = $nrow[0];
?>
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<table width='100%' border='0' cellspacing='0' cellpadding='8' class='text' rules='none' bordercolor='#B9B9B9' align='center' style='border-collapse:collapse'>
    <tr><td class="adminhead" align='center'>Producer Scoring Report</td></tr>
    <!-- search and right buttons start-->
    <?php if ($msg == '1') {
        ?>
        <tr>  <td align='center' style="color:#14734F;font-weight:bold;font-size:14px;" > Reset data point successfully.</td></tr>
        <?php
    }
    if ($msg == '2') {
        ?>
        <tr>  <td align='center' style="color:#14734F;font-weight:bold;font-size:14px;" > Reset bags successfully.</td></tr>
    <?php
}
if ($msg == '3') {
    ?>
        <tr>  <td align='center' style="color:#14734F;font-weight:bold;font-size:14px;" > Added bonus point successfully.</td></tr>
    <?php
}if ($msg == '5') {
    ?>
        <tr>  <td align='center' style="color:#14734F;font-weight:bold;font-size:14px;" > Added direct mail point successfully.</td></tr>
    <?php
}
?>
   <tr>  <td align='right'>
           <a href="producer_scoring_monthly_reports.php" class="button" style="padding:5px;text-decoration: none;float:right;margin-right: 10px; color: #fff;">Monthly Report</a>
           
       </td></tr>      
        
    <tr>
        <td class="bodyText">
            <form method="request" name="rangeForm" action="<?php echo $_SERVER['PHP_SELF']; ?>" style="display:inline;">    
                <table width='100%' border="0" cellspacing="0" cellpadding="10" class="text">
                    <tr>

                        <td align="left" colspan="3"><strong>Search By:</strong>
                            &nbsp;&nbsp;
                            <strong>Panelist ID:</strong>
                            <input type="text" id="competi_id" style="width:80%;" name="competi_id"   class="input_box2345" value="<?php echo $competi_id; ?>" />
                            <span style="text-align:center;width:100%;float:left;">( Please enter multiple panelist id with comma separated. )</span>
                        </td>
                         <!--<td align="right"  width='5%'><input class="button" style="width:60px;margin-right: 10px;" type="submit" name="import" value="Import" onclick="document.location='producer_score_imports.php'; return false;"/>
                        </td>-->
                    </tr>
                    <tr>
                        <td  width='30%' style="padding-left: 66px;" colspan="2" ><strong>Panelist Type:</strong>
                            <select name="panelist_type" id="panelist_type" style="width:300px;">
                                <option value="">All</option>
                                <option value="1"<?php if ($panelist_type == '1') { ?> selected="selected" <?php } ?>> &gt; <?php echo MAX_RESET_POINT; ?> Points</option>
                                <option value="2" <?php if ($panelist_type == '2') { ?> selected="selected" <?php } ?>> 0 Bags</option>
                            </select>
                            <input class="button" style="width:60px;" type="submit" name="search" value="Search" />
                            <?php if (!empty($_REQUEST['panelist_type']) || !empty($_REQUEST['competi_id'])): ?>
                                    <input class="button" style="width:60px;" type="submit" name="clear" value="Clear" />
                            <?php endif; ?>
                        </td>
                        
                    </tr>
                    <tr>
                      <td align="right"  width='85%'><input class="button" style="width:60px;" type="submit" name="import" value="Import" onclick="document.location='producer_score_imports.php'; return false;"/>
                        </td>  
                        <td align="right"  width='10%'><input class="button" style="width:60px;" type="submit" name="export" value="Export" />
                        </td>
                    </tr>
                </table>
            </form>
        </td>
    </tr>
<?php if (!empty($numrows)) { ?>
        <tr>
            <td>
                <form name="frmreport" id="frmreport" method="post" action="">
                    <table width='100%' border='0' cellspacing='0' cellpadding='8' class='text' rules='none' bordercolor='#B9B9B9' align='center' style='border-collapse:collapse'>
                        <tr><td colspan="15">&nbsp;</td>
                            <td>
                                <input type="submit" class="button" name="resetpoint" value="Reset Point">
                            </td>
                             <!--<td>
                                <input type="submit" class="button" name="resetbags" value="Reset Bags">
                            </td>-->
                         <tr>
                            <td  align="left" style="width:3%;"  class="adminhead" height='15px'><input name="setUnset" onclick="setAll()" value="on" type="checkbox"></td>
                            <td align="left" style="width:10%;" class="adminhead" height='15px' ><b>Panelist ID</b><?php doSort($sort, $order, 1, $p, $competi_id, $panelist_type); ?></td>
                            <td align="left" style="width:5%;"  style="width:5%;" class="adminhead" height='15px' ><b>
                            <?php
                            /*$param = "?d=1";
                            if (!empty($_SERVER['QUERY_STRING'])) {
                                $param = "&d=1";
                            }
                            $param = $_SERVER['PHP_SELF'] . "?competi_id={$competi_id}&panelist_type={$panelist_type}&sort={$sort}&order={$order}&p={$p}&d=1";
                                */
                            ?>
                            <!--<a style="background-color: #14734F;color: #FFFFFF;font-weight: bold;font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 11px;" href="<?php echo $param; ?>">-->
                            Direct Mail Point</b><?php doSort($sort, $order, 2, $p, $competi_id, $panelist_type); ?></td>
                            <td align="left" style="width:5%;" class="adminhead" height='15px' ><b>Email Pieces</b><?php doSort($sort, $order, 3, $p, $competi_id, $panelist_type); ?></td>
                            <td align="left" style="width:5%;" class="adminhead" height='15px' ><b>Email Point</b><?php doSort($sort, $order, 4, $p, $competi_id, $panelist_type); ?></td>
                            <td align="left" style="width:10%;" class="adminhead" height='15px' ><b>
                            <?php
                            $param = "?b=1";
                            if (!empty($_SERVER['QUERY_STRING'])) {
                                $param = "&b=1";
                            }
                            $param = $_SERVER['PHP_SELF'] . "?competi_id={$competi_id}&panelist_type={$panelist_type}&sort={$sort}&order={$order}&p={$p}&b=1";

                            ?>   
                            <a style="background-color: #14734F;color: #FFFFFF;font-weight: bold;font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 11px;" href="<?php echo $param; ?>">
                                        Bonus Point</a></b><?php doSort($sort, $order, 5, $p, $competi_id, $panelist_type); ?></td>
                            <td align="left" style="width:5%;" class="adminhead" height='15px' ><b>Total Point</b> <?php doSort($sort, $order, 6, $p, $competi_id, $panelist_type); ?></td>
                            
                            <td align="left" style="width:5%;" class="adminhead" height='15px' ><b>
                        <?php
                        $param2 = "?r=1";
                        if (!empty($_SERVER['QUERY_STRING'])) {
                            $param2 = "&r=1";
                        }
                        $param2 = $_SERVER['PHP_SELF'] . "?competi_id={$competi_id}&panelist_type={$panelist_type}&sort={$sort}&order={$order}&p={$p}&r=1";
                        ?>   
                                    <a style="background-color: #14734F;color: #FFFFFF;font-weight: bold;font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 11px;" href="<?php echo $param2; ?>">    
                                        Bags Remaining
                                    </a>
                                </b><?php doSort($sort, $order, 7, $p, $competi_id, $panelist_type); ?></td>
                            <td align="left" style="width:5%;" class="adminhead" height='15px' ><b>First Name</b> <?php doSort($sort, $order, 8, $p, $competi_id, $panelist_type); ?></td>
                            <td align="left" style="width:5%;" class="adminhead" height='15px' ><b>Last Name</b> <?php doSort($sort, $order, 9, $p, $competi_id, $panelist_type); ?></td>
                            <td align="left" style="width:10%;" class="adminhead" height='15px' ><b>Address</b> <?php doSort($sort, $order, 10, $p, $competi_id, $panelist_type); ?></td>
                            <td align="left" style="width:5%;" class="adminhead" height='15px' ><b>City</b> <?php doSort($sort, $order, 11, $p, $competi_id, $panelist_type); ?></td>
                            <td align="left" style="width:5%;" class="adminhead" height='15px' ><b>State</b> <?php doSort($sort, $order, 12, $p, $competi_id, $panelist_type); ?></td>
                            <td align="left" style="width:5%;" class="adminhead" height='15px' ><b>Zipcode</b> <?php doSort($sort, $order, 13, $p, $competi_id, $panelist_type); ?></td>
                            <td align="left" style="width:10%;" class="adminhead" height='15px' ><b>Email</b> <?php doSort($sort, $order, 14, $p, $competi_id, $panelist_type); ?></td>
                            <td align="left" style="width:5%;" class="adminhead" height='15px' ><b>Action</b></td>
                        </tr>
                        <?php
                        $className = '';
                        while ($row = $DRW->fetch_assoc($rs)) {
                            $ids = $row['id'];
                            $direct_mail_point = $row['direct_mail_point'];
                            $email_piece = $row['email_piece'];
                            $email_piece_point = $row['email_piece_point'];
                            $bag_remaining = $row['bag_remaining'];
                            $bonus_point = $row['bonus_point'];
                            $total_point = ($row['total_point']);
                            $fname = $row['first_name'];
                            $lname = $row['last_name'];
                            $address = $row['address'];
                            $city = $row['city'];
                            $state = $row['state'];
                            $zipcode = $row['postalcode'];
                            $pemail = $row['email'];
                            
                            if ($className == 'selected-bg')
                                $className = 'white-bg';
                            else
                                $className = 'selected-bg';
                            ?>
                            <tr valign=top class="<?php echo $className; ?>">
                                <td align="left">
                                    <input name="competiids[]" value="<?php echo $row['panelist_id'] . '###' . $total_point; ?>" type="checkbox">
                                </td>
                                <td align="left">
                                   <a href="javascript:void(0)" onclick="openpopup(<?php echo $row['panelist_id']; ?>);">
                                    <?php echo $row['competi_id']; ?>
                                    </a>
                                </td>
                                
                                <td align="left"><?php echo $direct_mail_point; ?>
                                <?php if (!empty($_REQUEST['d'])) {
                                        ?>
                                        <input class="directmail" type="text" style="width:50px;" maxlength="4" name="directmail_point[<?php echo $ids; ?>]" id="directmail_point[]" value="<?php echo $direct_mail_point; ?>">
                                            <?php
                                        }
                                        ?>
                                </td>
                                <td align="left"><?php echo $email_piece; ?></td>
                                <td align="left"><?php echo $email_piece_point; ?></td>
                                <td align="left"><?php echo $bonus_point; ?>
                                    <?php if (!empty($_REQUEST['b'])) {
                                        ?>
                                        <input class="abc" type="text" style="width:50px;" maxlength="4" name="bonus_point[<?php echo $ids; ?>]" id="bonus_point[]" value="<?php echo $bonus_point; ?>">
                                            <?php
                                        }
                                        ?>
                                </td>
                                <td align="left"><?php echo $total_point; ?></td>
                                <td align="left"><?php echo $bag_remaining; ?>
                                    <?php if (!empty($_REQUEST['r'])) {
                                        ?>
                                        <select name="remainingbags[<?php echo $ids; ?>]" id="remainingbags[]">
                                        <?php for ($rb = 0; $rb <= 4; $rb++) { ?>
                                                <option value="<?php echo $rb; ?>" <?php if ($rb == $bag_remaining) { ?> selected="selected" <?php } ?>><?php echo $rb; ?></option>
                                        <?php } ?>
                                        </select>
                                <?php
                            }
                            ?>
                                </td>
                                
                                <td align="left"><?php echo $fname; ?></td>
                                <td align="left"><?php echo $lname; ?></td>
                                <td align="left"><?php echo $address; ?></td>
                                <td align="left"><?php echo $city; ?></td>
                                <td align="left"><?php echo $state; ?></td>
                                <td align="left"><?php echo $zipcode; ?></td>
                                <td align="left"><?php echo $pemail; ?></td>
                                <td align="left">
                                <?php if ($total_point >= MAX_RESET_POINT) { ?>
                                <a href="javascript:void(0);" onclick="clearscore(<?php echo $row['panelist_id']; ?>,<?php echo $total_point; ?>)">Reset Point</a>
                                <?php } ?>
                                </td>
                            </tr>
                            <?php }
                        ?>
                             <?php if (!empty($_REQUEST['d'])) { ?>
                            <tr><td colspan="18" class="adminhead" >&nbsp;</td></tr>
                            <tr>
                                <td align="right" colspan="3">
                                    <input class="button" onclick="checkDirectMailPoint();" type="button" name="update_directmail_point" value="Update Mail Point"></td>
                                <td colspan="2">&nbsp;</td> 
                            </tr>
                        <?php } ?>
                        <?php if (!empty($_REQUEST['b'])) { ?>
                            <tr><td colspan="18" class="adminhead" >&nbsp;</td></tr>
                            <tr>
                                <td align="right" colspan="7">
                                    <input class="button" onclick="checkbonusPoint();" type="button" name="update_bonus_point" value="Update Bonus Point"></td>
                                <td colspan="2">&nbsp;</td> 
                            </tr>
                        <?php } ?>
                        <?php if (!empty($_REQUEST['r'])) { ?>
                            <tr><td colspan="18" class="adminhead" >&nbsp;</td></tr>
                            <tr>
                                <td align="right" colspan="8">
                                    <input class="button" onclick="checkRemainingBags();" type="button" name="update_remaining_bag" value="Reset Bags"></td>
                                <td>&nbsp;</td> 
                            </tr>
                    <?php } ?>    
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
                    $addparam = '';
                    $addparam2 = '';
                    if (!empty($_REQUEST['b'])) {
                        $addparam = "&b=" . $_REQUEST['b'];
                    }
                    if (!empty($_REQUEST['r'])) {
                        $addparam2 = "&r=" . $_REQUEST['r'];
                    }
                    //first and previous only if not on first
                    if ($limstart > 0) {
                        if ($limstart >= $limiter)
                            $prev = $limstart - $limiter;
                        else
                            $prev = 0;
                        $firstlink = "[<a href=\"{$_SERVER['PHP_SELF']}?p=0&competi_id={$competi_id}&panelist_type={$panelist_type}&sort={$sort}&order={$order}$addparam$addparam2\">First</a>]";
                        $prevlink = "<a href=\"{$_SERVER['PHP_SELF']}?p=$prev&competi_id={$competi_id}&panelist_type={$panelist_type}&sort={$sort}&order={$order}$addparam$addparam2\">&laquo; Prev $limiter</a>";
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
                            $middlelinks .= "<a href=\"{$_SERVER['PHP_SELF']}?p=$startnum&competi_id={$competi_id}&panelist_type={$panelist_type}&sort={$sort}&order={$order}$addparam$addparam2\">" . ($i + 1) . "</a> ";
                        } else
                            $middlelinks .= ($i + 1) . ' ';
                    }
                    //next and last if not on last
                    if ($limstart < $rowcnt && (($limstart + ($limiter * 2)) < $rowcnt || ($rowcnt - ($limstart + $limiter)) > 0)) {
                        $next = $limstart + $limiter;
                        $nextlink = "<a href=\"{$_SERVER['PHP_SELF']}?p=$next&competi_id={$competi_id}&panelist_type={$panelist_type}&sort={$sort}&order={$order}$addparam$addparam2\">Next $limiter &raquo;</a>";
                        $lastlink = "[<a href=\"{$_SERVER['PHP_SELF']}?p=" . (($numbers - 1) * $limiter) . "&competi_id={$competi_id}&panelist_type={$panelist_type}&sort={$sort}&order={$order}$addparam$addparam2\">Last</a>]";
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
<form name="resetdata" id="resetdata" method="post" action="">
    <input type="hidden" name="resetid" id="resetid" value="">
    <input type="hidden" name="totaldata" id="totaldata" value="">
</form>
<script type="text/javascript">
    function  clearscore(id, totaldata) {
        if (confirm('Are you sure to reset this panelist score')) {
            document.getElementById('resetid').value = id;
            document.getElementById('totaldata').value = totaldata;
            document.resetdata.submit();
            //$('#resetid').val(id);
            //$('#resetdata').submit();
        }
    }
    function setAll() {
        if (document.frmreport.setUnset.value == 'on') {
            for (var i = 1; i < document.frmreport.elements.length; i++) {
                document.frmreport.elements[i].checked = true;
            }
            document.frmreport.setUnset.value = '';
        } else {
            for (var i = 1; i < document.frmreport.elements.length; i++) {
                document.frmreport.elements[i].checked = false;
            }
            document.frmreport.setUnset.value = 'on';
        }
    }
    function openpopup(id) {
        var wind = window.open('producer_score_reports_log.php?id=' + id, "winpop", "left=0, top=0, scrollbars=yes, resizable=yes, width=400, height=300");
        wind.focus();
    }
    function checkbonusPoint() {
        var str = [];
        $(".abc").each(function () {
            if ($(this).val() >= 1000) {
                str.push(1);
            }
        });
        if (str == '1') {
            if (confirm('There are some value greater than 1000, are you sure to proceed it.')) {
                document.frmreport.submit();
            } else {
                return false;
            }
        } else {
            document.frmreport.submit();
        }
    }
    
    function checkDirectMailPoint() {
        var str = [];
        $(".directmail").each(function () {
            if ($(this).val() >= 2000) {
                str.push(1);
            }
        });
        if (str == '1') {
            if (confirm('There are some value greater than 2000, are you sure to proceed it.')) {
                document.frmreport.submit();
            } else {
                return false;
            }
        } else {
            document.frmreport.submit();
        }
    }
    function checkRemainingBags() {
        if (confirm('Are you sure to reset remaining bags?')) {
            document.frmreport.submit();
        }
    }
</script>
<?php include 'bottom.php'; ?>
