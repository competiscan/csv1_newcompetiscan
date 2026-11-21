<?php
require_once __DIR__ . '/../vendor/autoload.php';
$ALLOW_GROUPS = array(4);
require_once("../auth_auth.php");
require_once '../class/Trend.php';
if (!empty($_REQUEST['pcopy_pop'])) {
    $ONLOAD = "doPCopy_pop('" . $_REQUEST['pcopy_pop'] . "');";
}
include 'top.php';
if(!defined('ENV')){
    define('ENV',getenv('SERVER_NAME'));
}
require_once '../includes/functions.php';
require_once '../includes/AdminPermission.php';
$permChecker = new AdminPermission();
ini_set('memory_limit', '-1');
$limit = 20;
$wheresearchproduct='';
$productidsarray=array();
$PDFContent='';
$process_pdf_html='';
 // Restrict media channel email case
$disabledButton='';
#####Satrt for Anotation Tool Link Permission######
$category_id = array();
$sector_id = array();
if(isset($_REQUEST['sector'])) {
        $sector_id = $_SESSION['sess_sectorid'] = $_REQUEST['sector'];
        
}
if(isset($_REQUEST['category'])) {
	$category_id = $_REQUEST['category'];
        $category_id = $_SESSION['sess_catid']=array_values(array_filter($category_id));
}
$andCondSector='';
if(!empty($_SESSION['sess_sectorid'])) { 
        //$sector_id = @implode(" ,",$_SESSION['sess_sectorid']);
        //$andCondSector = " and pd.sectorID REGEXP ".$sector_id;
        $andCondSector.=' AND (( (CONCAT(",",pd.sectorID,",") REGEXP ",0," ) OR (CONCAT(",",pd.sectorID,",") REGEXP ",," ) )'; 
        for($i=0; $i < count($_SESSION['sess_sectorid']); $i++){
              $sectid=$_SESSION['sess_sectorid'][$i];
              $andCondSector .=" OR (CONCAT(',',pd.sectorID,',') REGEXP ',$sectid,')";
             
        }
       $andCondSector.=')'; 
        
        
}
$andCondCategory='';
if(!empty($_SESSION['sess_catid'])) {
        //$category_id = @implode(" ,",$_SESSION['sess_catid']);
        //$andCondCategory = " and pd.categoryID REGEXP ".$category_id;
    $andCondCategory.=' AND (( (CONCAT(",",pd.categoryID,",") REGEXP ",0," ) OR (CONCAT(",",pd.categoryID,",") REGEXP ",," ) )'; 
    for($i=0; $i < count($_SESSION['sess_catid']); $i++){
          $cateid=$_SESSION['sess_catid'][$i];
          $andCondCategory .=" OR (CONCAT(',',pd.categoryID,',') REGEXP ',$cateid,')";

    }
   $andCondCategory.=')';
        
}
#####END for Anotation Tool Link Permission######
if (isset($_REQUEST['p']))
    $p = $_SESSION['manageproduct_p'] = $_REQUEST['p'];
elseif (isset($_SESSION['manageproduct_p']))
    $p = $_SESSION['manageproduct_p'];
else
    $p = 0;

if (isset($_REQUEST['sort']))
    $sort = $_SESSION['manageproduct_sort'] = (int) $_REQUEST['sort'];
elseif (isset($_SESSION['manageproduct_sort']))
    $sort = $_SESSION['manageproduct_sort'];
else
    $sort = 4;

$message = '';
$extratitle = '';

if (isset($_REQUEST['pstat'])) {
    if (!isset($_REQUEST['p']) && isset($_SESSION['pstat']) && $_SESSION['pstat'] != $_REQUEST['pstat']) {
        $p = $_SESSION['manageproduct_p'] = 0;
        $sort = $_SESSION['manageproduct_sort'] = 4;
    }
    $_SESSION['pstat'] = (int) $_REQUEST['pstat'];
} elseif (!isset($_SESSION['pstat'])) {
    $_SESSION['pstat'] = 1;
}
if (isset($_REQUEST['cstat'])) {
    if (!isset($_REQUEST['p']) && isset($_SESSION['cstat']) && $_SESSION['cstat'] != $_REQUEST['cstat']) {
        $p = $_SESSION['manageproduct_p'] = 0;
        $sort = $_SESSION['manageproduct_sort'] = 4;
    }
    $_SESSION['cstat'] = (int) $_REQUEST['cstat'];
} elseif (!isset($_SESSION['cstat'])) {
    $_SESSION['cstat'] = 0;
}

if (isset($_REQUEST['assigned_admin_userID'])) {
    $_SESSION['assigned_admin_userID'] = (int) $_REQUEST['assigned_admin_userID'];
    $_SESSION['last_admin_userID'] = 0;
} elseif (isset($_REQUEST['last_admin_userID'])) {
    $_SESSION['last_admin_userID'] = (int) $_REQUEST['last_admin_userID'];
    $_SESSION['assigned_admin_userID'] = 0;
}

switch ($_SESSION['cstat']) {
    case 5:
        $ctext = 'Core ';
        break;
    case 6:
        $ctext = 'Non-core ';
        break;
    case 7:
        $ctext = 'Online ';
        break;
    case 8:
        $ctext = 'AFFINION ';
        break;
    case 9:
        $ctext = 'Telecom ';
        break;
    case 10:
        $ctext = 'Consumer Insight ';
        break;
    case 11:
        $ctext = 'CITI ';
        break;
    case 12:
        $ctext = 'Mobile ';
        break;
    case 219:
        $ctext = 'Travel &amp; Leisure ';
        break;
    case 266:
        $ctext = 'Retail ';
        break;
    case 315:
        $ctext = 'Energy ';
        break;
    case 13:
        $ctext = 'Glacier ';
        break;
    case 522:
        $ctext = 'Emerging ';
        break;
    default:
        $ctext = '';
}

if (isset($_SESSION['pstat']) && $_SESSION['pstat'] != 0) {
    if ($_SESSION['pstat'] == -2) {
        $addedtext = "productStatus=" . (-1 * $_SESSION['pstat']);
    } else {
        $addedtext = "productStatus=" . $_SESSION['pstat'];
    }
    switch ($_SESSION['pstat']) {
        case 1:
            $extratitle = ': ' . $ctext . 'Approved';
            break;
        case 2:
            $extratitle = ': ' . $ctext . 'Unapproved';
            break;
        case -2:
            $extratitle = ': ' . $ctext . 'Non-Panelist Unapproved';
            break;
        case 3:
            $extratitle = ': ' . $ctext . 'Reprocessed';
            break;
        case 4:
            $extratitle = ': ' . $ctext . 'Problem';
            break;
        case -1:
            $extratitle = ': ' . $ctext . 'Unused';
            break;
        case 5:
            $extratitle = ': Core FTP';
            break;
        case 10:
            $extratitle = ': Glacier';
            break;
        case 6:
            if ($_SESSION['cstat'] == 8) {
                $addedtext = "(productStatus>0 AND productStatus<>1 AND productStatus<>2 AND productStatus<>3 AND productStatus<>4 AND productStatus<>10)";
                $extratitle .= ': AFFINION FTP';
            } elseif ($_SESSION['cstat'] == 10) {
                $addedtext = "(productStatus>0 AND productStatus<>1 AND productStatus<>2 AND productStatus<>3 AND productStatus<>4 AND productStatus<>10)";
                $extratitle .= ': Consumer Insight FTP';
            } elseif ($_SESSION['cstat'] == 11) {
                $addedtext = "(productStatus>0 AND productStatus<>1 AND productStatus<>2 AND productStatus<>3 AND productStatus<>4 AND productStatus<>10)";
                $extratitle .= ': CITI FTP';
            } elseif ($_SESSION['cstat'] == 13) {
                $addedtext = "(productStatus>0 AND productStatus=10 )";
                $extratitle .= ': Glacier';
            } else {
                $extratitle = ': Non-core FTP';
            }
            break;
    }
} else {
    if ($ctext != '') {
        $extratitle = ': ' . $ctext;
    }
    $addedtext = "1=1";
}

$sect_j = '';
$where_j = '';
if ($_SESSION['pstat'] != 5 && $_SESSION['pstat'] != 6) {
    $sect_j = '';
    $where_j = '';

    $partsArray = array();
    //$partsArray[] = "scsc_sectorID=0";
    #############  for sector concat ##################
     $partsArray[] = ' ( (CONCAT(",",pd.sectorID,",") REGEXP ",0," ) OR (CONCAT(",",pd.sectorID,",") REGEXP ",," ) )'; 
    foreach ($AUTH_DATA['SID'] as $sid) {
       // $partsArray[] = "scsc_sectorID=$sid";
         #############  for sector concat ##################
        $partsArray[] = " (CONCAT(',',pd.sectorID,',') REGEXP ',$sid,')";
    }

    if (count($partsArray) > 0) {
        $where_j .= ' AND (' . implode(' OR ', $partsArray) . ')';
    }

    //$sect_j = ' JOIN cscan_scsc_product as scsc ON (pd.productID=scsc.productID)';

    $where = $where_j;
    if (!empty($_SESSION['manageproducts_sector'])) {
       // $where .= ' AND (scsc_sectorID=' . $_SESSION['manageproducts_sector'] . ')';
        $manageproducts_sector=$_SESSION['manageproducts_sector'];
        // $where .= ' AND (scsc_sectorID=' . $_SESSION['manageproducts_sector'] . ')';
         $where .="AND (CONCAT(',',pd.sectorID,',') REGEXP ',$manageproducts_sector,')";
    }
    /*
    if ($_SESSION['cstat'] == 315) {
        $where .= ' AND ((scsc_sectorID=0 AND (DMSource LIKE \'%\\_EN\\_%\')) OR scsc_sectorID=315)';
    } elseif ($_SESSION['cstat'] == 266) {
        $where .= ' AND ((scsc_sectorID=0 AND (DMSource LIKE \'%\\_RL\\_%\')) OR scsc_sectorID=266)';
    } elseif ($_SESSION['cstat'] == 219) {
        $where .= ' AND ((scsc_sectorID=0 AND (DMSource LIKE \'%\\_TL\\_%\')) OR scsc_sectorID=219)';
    } elseif ($_SESSION['cstat'] == 9) {
        $where .= ' AND ((scsc_sectorID=0 AND (DMSource LIKE \'%telecom%\' OR DMSource LIKE \'%\\_TC\\_%\')) OR scsc_sectorID=9)';
    */
    
        #############  for sector concat ##################
    
    if ($_SESSION['cstat'] == 315) {
        $where .= ' AND ((((CONCAT(",",pd.sectorID,",") REGEXP ",0," ) OR (CONCAT(",",pd.sectorID,",") REGEXP ",," )) AND (DMSource LIKE \'%\\_EN\\_%\')) OR (CONCAT(",",pd.sectorID,",") REGEXP ",315," ))';
    } 
     /* Start Emerging Section */
    elseif ($_SESSION['cstat'] == 522) {
        //if(ENV == 'localhost' || ENV == 'demo.competiscan.com'){
            $where .= ' AND ((CONCAT(",",pd.sectorID,",") REGEXP ",522," ) OR (CONCAT(",",pd.sectorID,",") REGEXP ",525," ) OR (CONCAT(",",pd.sectorID,",") REGEXP ",530," ))';
        //}
    }
    /* End Emerging Section*/
    elseif ($_SESSION['cstat'] == 266) {
         /* Start Emerging Section 522*/
        //if(ENV == 'localhost' || ENV == 'demo.competiscan.com'){
        $where .= ' AND ((((CONCAT(",",pd.sectorID,",") REGEXP ",0," ) OR (CONCAT(",",pd.sectorID,",") REGEXP ",," )) AND (DMSource LIKE \'%\\_RL\\_%\')) OR (CONCAT(",",pd.sectorID,",") REGEXP ",266," ))';   
        /*}else{
        $where .= ' AND ((((CONCAT(",",pd.sectorID,",") REGEXP ",0," ) OR (CONCAT(",",pd.sectorID,",") REGEXP ",," )) AND (DMSource LIKE \'%\\_RL\\_%\')) OR (CONCAT(",",pd.sectorID,",") REGEXP ",266," ) OR (CONCAT(",",pd.sectorID,",") REGEXP ",525," ))';
        }*/
    } elseif ($_SESSION['cstat'] == 219) {
        $where .= ' AND ((((CONCAT(",",pd.sectorID,",") REGEXP ",0," ) OR (CONCAT(",",pd.sectorID,",") REGEXP ",," )) AND (DMSource LIKE \'%\\_TL\\_%\')) OR (CONCAT(",",pd.sectorID,",") REGEXP ",219," ))';
    } elseif ($_SESSION['cstat'] == 9) {
        $where .= ' AND ((((CONCAT(",",pd.sectorID,",") REGEXP ",0," ) OR (CONCAT(",",pd.sectorID,",") REGEXP ",," ))  AND (DMSource LIKE \'%telecom%\' OR DMSource LIKE \'%\\_TC\\_%\')) OR (CONCAT(",",pd.sectorID,",") REGEXP ",9," ))';
    } elseif ($_SESSION['cstat'] == 6) {
        $where .= ' AND ((((CONCAT(",",pd.sectorID,",") REGEXP ",0," ) OR (CONCAT(",",pd.sectorID,",") REGEXP ",," )) AND (DMSource LIKE \'%non%\' OR DMSource LIKE \'%\\_NC\\_%\'))';
        $sql = "SELECT sectorID FROM cscan_sector WHERE sectorSearchActive=1 AND parentID=0 AND is_core=0";
        $rs = $DRW->query($sql, $DRW_read);
        while ($row = $DRW->fetch_array($rs)) {
            $id = $row[0];
             /* Start Emerging Section 522*/
            //if(ENV == 'localhost' || ENV == 'demo.competiscan.com'){
                if ($id != 9 && $id != 219 && $id != 266 && $id != 525 && $id != 315 && $id != 522) {
                   // $where .= ' OR scsc_sectorID=' . $id;
                    #############  for sector concat ##################
                    $where .=" OR (CONCAT(',',pd.sectorID,',') REGEXP ',$id,')";
                }
            /*}else{
                if ($id != 9 && $id != 219 && $id != 266 && $id != 525 && $id != 315) {
                   // $where .= ' OR scsc_sectorID=' . $id;
                    #############  for sector concat ##################
                    $where .=" OR (CONCAT(',',pd.sectorID,',') REGEXP ',$id,')";
                }
            }*/
        }
        $where .= ')';
    } elseif ($_SESSION['cstat'] == 5) {
        $where .= ' AND (( ( (CONCAT(",",pd.sectorID,",") REGEXP ",0," ) OR (CONCAT(",",pd.sectorID,",") REGEXP ",," )) AND DMSource NOT LIKE \'%non%\' AND DMSource NOT LIKE \'%\\_NC\\_%\' AND DMSource NOT LIKE \'%telecom%\' AND DMSource NOT LIKE \'%\\_TC\\_%\' AND DMSource NOT LIKE \'%\\_TL\\_%\')';
        $sql = "SELECT sectorID FROM cscan_sector WHERE sectorSearchActive=1 AND parentID=0 AND is_core=1";
        $rs = $DRW->query($sql, $DRW_read);
        while ($row = $DRW->fetch_array($rs)) {
            $id = $row[0];
            /* Start Emerging Section 522*/
           // if(ENV == 'localhost' || ENV == 'demo.competiscan.com'){
                if ($id != 530) {
                //$where .= ' OR scsc_sectorID=' . $id;
                 #############  for sector concat ##################
                $where .=" OR (CONCAT(',',pd.sectorID,',') REGEXP ',$id,')";
                }
            /*}else{
               //$where .= ' OR scsc_sectorID=' . $id;
                 #############  for sector concat ##################
                $where .=" OR (CONCAT(',',pd.sectorID,',') REGEXP ',$id,')"; 
            }*/
        }
        $where .= ')';
    }
    $addedtext .= $where;
}
if ($_SESSION['pstat'] != 1) {
    $extraorderby = 'product_priority DESC,special_handling DESC,';
} else {
    $extraorderby = '';
}
if (isset($_SESSION['assigned_admin_userID']) && $_SESSION['assigned_admin_userID'] != 0) {
    $assigned_admin_userID = $_SESSION['assigned_admin_userID'];

    $addedtext .= " AND assigned_admin_userID=" . $_SESSION['assigned_admin_userID'];
} else {
    $assigned_admin_userID = 0;
}
if (isset($_SESSION['last_admin_userID']) && $_SESSION['last_admin_userID'] != 0) {
    $last_admin_userID = $_SESSION['last_admin_userID'];

    $addedtext .= " AND admin_userID=" . $_SESSION['last_admin_userID'];
} else {
    $last_admin_userID = 0;
}

if (!isset($_SESSION['product_searchText']) || isset($_REQUEST['show_All'])) {
    $_SESSION['product_searchText'] = '';
    $_SESSION['company_search_text'] = '';
    $_SESSION['state_search_id'] = 0;
    $_SESSION['country_search_id'] = '';
    $_SESSION['ocr_search_text'] = '';
    $_SESSION['product_DMSource'] = '';
    $_SESSION['product_panelist_ids'] = '';
    $_SESSION['mc_search_id'] = 0;
    #####for Anotation Tool Link Permission######
    $_SESSION['sess_sectorid']=array();
    $_SESSION['sess_catid']=array();
        
} elseif (isset($_REQUEST['search_text']) || isset($_REQUEST['company']) || isset($_REQUEST['state']) || isset($_REQUEST['country'])) {
    $_SESSION['product_searchText'] = trim($_REQUEST['search_text']);
    $_SESSION['company_search_text'] = trim($_REQUEST['company']);
    $_SESSION['state_search_id'] = (int) $_REQUEST['state'];
    $_SESSION['country_search_id'] = $_REQUEST['country'];
    if (!isset($_SESSION['ocr_search_text']) || $_SESSION['ocr_search_text'] != $_REQUEST['ocr']) {
        $DRW->query("DELETE FROM cscan_search_adminproduct WHERE uid={$AUTH_DATA['userID']}", $DRW_main);
    }
    $_SESSION['ocr_search_text'] = trim($_REQUEST['ocr']);
    $_SESSION['product_DMSource'] = trim($_REQUEST['DMSource']);
    $_SESSION['product_panelist_ids'] = trim($_REQUEST['panelist_ids']);
    $_SESSION['mc_search_id'] = (int) $_REQUEST['mc'];
}
if (!isset($_SESSION['country_search_id']))
    $_SESSION['country_search_id'] = 'US';
?>
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<table width="100%" border="0" cellspacing="0" cellpadding="5" class="text">
    <tr><td class="adminhead" align="center"><div id="pcontainer">PRODUCT MANAGEMENT<?php echo $extratitle; ?></div></td></tr>

    <!-- search and right buttons start-->
    <tr>
        <td class="bodyText">
            <form method="post" name="prodForm" action="<?php echo $_SERVER['PHP_SELF']; ?>" style="display:inline;" id="manage_product">
                <table border="0" cellspacing="0" cellpadding="1" class="text">
                    <tr>
                        <td align="right"><strong>Search by Product or Entry ID:</strong></td>
                        <td><input type="text" name="search_text" size="40" maxlength="255" class="input_box" value="<?php echo htmlspecialchars($_SESSION['product_searchText'], ENT_QUOTES); ?>" /></td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td align="right"><strong>DM/TM Source:</strong></td>
                        <td><input type="text" name="DMSource" size="40" maxlength="255" class="input_box" value="<?php echo htmlspecialchars($_SESSION['product_DMSource'], ENT_QUOTES); ?>" /></td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td align="right"><strong>Panelists:</strong></td>
                        <td><input type="text" name="panelist_ids" size="40" class="input_box" value="<?php echo htmlspecialchars($_SESSION['product_panelist_ids'], ENT_QUOTES); ?>" /></td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td align="right"><strong>Company:</strong></td>
                        <td><input type="text" name="company" size="40" maxlength="255" class="input_box" autocomplete="off" onkeyup="startTimer('showMatch(\'checkcos.php\',document.forms.prodForm.company)');" onblur="setTimeout('hideCos()', 1000);" value="<?php echo htmlspecialchars($_SESSION['company_search_text'], ENT_QUOTES); ?>" /></td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td align="right"><strong>State/Province:</strong></td>
                        <td><select name="state" size="1" class="input_box"><option value="0">&nbsp;</option>
<?php
getStates($_SESSION['state_search_id']);
?>
                            </select></td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td align="right"><strong>Country:</strong></td>
                        <td><?php
echo '<label><input type="radio" name="country" value=""';
if (empty($_SESSION['country_search_id'])) {
    echo " checked=\"checked\"";
}
print ' />All</label>';
$sql = "SELECT DISTINCT countryCode,country FROM cscan_state JOIN ISO31661_alpha2code ON (code=countryCode) ORDER BY country";
$rs = $DRW->query($sql, $DRW_read);
while ($row = $DRW->fetch_row($rs)) {
    print ' <label><input type="radio" name="country" value="' . $row[0] . '"';
    if ($_SESSION['country_search_id'] == $row[0]) {
        echo " checked=\"checked\"";
    }
    print ' />' . htmlspecialchars($row[1]) . '</label>';
}
?></td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td align="right"><strong>OCR:</strong></td>
                        <td><input type="text" name="ocr" size="40" maxlength="255" class="input_box" value="<?php echo htmlspecialchars($_SESSION['ocr_search_text'], ENT_QUOTES); ?>" /></td>
                        <td>&nbsp;</td>
                    </tr>
                    <!-- Start for Anotation Tool Link Permission--->
                   <?php  if(checkGroup(108)){ ?>
                    <tr>
                        <td align="right"><strong>Sector:</strong></td>
                        <td><select name ="sector[]" id ="sector_list" onChange ="getCategory();" multiple="multiple" size="3" class="combo_box">
                            <?php 
                                $sector = getSector();
                                foreach($sector as $id=>$name){
                                   /* if(!in_array($id,$_SESSION['sess_sector'])){
                                            continue;
                                        }*/
                                        if(checkSector($id)){ 
                                            ?>
                                            <option  <?php if(in_array($id,$_SESSION['sess_sectorid'])) { echo "selected"; } ?> value="<?php echo $id;?>" ><?php echo htmlspecialchars($name, ENT_QUOTES); ?></option> 
                               <?php }
                                }
                                ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td align="right"><strong>Category:</strong></td>
                        <td>
                            <select id ="category_list" name ="category[]"  multiple="multiple" size="3" class="combo_box"><option value="0" <?php $o_count = count($_SESSION['sess_sectorid']); if($o_count ==0 || count($_SESSION['sess_catid'])==0) { echo "selected"; } ?>>--Any--</option>
                             <?php 
                               if(!empty($_SESSION['sess_sectorid'])){
                               $sector_cat_id = @implode(',',$_SESSION['sess_sectorid']);
                               $category =getCategoryMulti($sector_cat_id);
                                foreach($category as $id=>$name){
                                    if(checkCategory($id)){ ?>
                                            <option  <?php if(in_array($id,$_SESSION['sess_catid'])) { echo "selected"; } ?> value="<?php echo $id;?>" ><?php echo htmlspecialchars($name, ENT_QUOTES); ?></option> 
                               <?php }
                                }
                               }
                                ?>
                           </select>
                        </td>
                    </tr>
                   <?php } ?>
                    <!-- END Start for Anotation Tool Link Permission--->
                    <tr>
                        <td align="right"><strong>Media Channel:</strong></td>
                        <td><select name="mc" size="1" class="input_box"><option value="0">&nbsp;</option>
                            <?php
                            $media_channel = getMediaChannel();
                            foreach ($media_channel as $id => $name) {
                                echo "<option value=\"$id\"";
                                if ($_SESSION['mc_search_id'] == $id) {
                                    echo " selected=\"selected\"";
                                }
                                echo ">" . htmlspecialchars($name) . "</option>";
                            }
                            ?>
                            </select></td>
                        <td><input class="button" style="width:60px;" type="submit" name="search_Submit" value="Search" onclick="return check_searchform();" />
                            &nbsp;&nbsp;&nbsp;&nbsp;
                            <input class="button" style="width:70px" type="submit" name="show_All" value="Show All" /></td>
                    </tr>
                </table>
                <input type="hidden" name="p" value="0" />
            </form>
        </td>
    </tr>
    <tr>
        <td class="bodyText">
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="changeadmin" style="display:inline;"><strong>Assigned User:</strong> <select class="combo_box" name="assigned_admin_userID" onchange="document.changeadmin.submit();"><option value="0">All</option>
<?php
$useroptions = array();
$sql = "select userID,userName,is_assign_queue from cscan_admin_users WHERE user_status=1 ORDER BY userName";
$rs = $DRW->query($sql, $DRW_read);
while ($row = $DRW->fetch_row($rs)) {
    print "<option value = \"$row[0]\"";
    if ($row[0] == $assigned_admin_userID)
        print " selected=\"selected\"";
    print ">";
    if ($row[2])
        print '*';
    print "$row[1]</option>";
    $useroptions[$row[0]] = $row[1];
}
?></select>
            </form>	
            &nbsp;
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="changeadmin2" style="display:inline;"><strong>Last User:</strong> <select class="combo_box" name="last_admin_userID" onchange="document.changeadmin2.submit();"><option value="0">All</option>
                    <?php
                    foreach ($useroptions as $key => $val) {
                        print "<option value = \"$key\"";
                        if ($key == $last_admin_userID)
                            print " selected=\"selected\"";
                        print ">$val</option>";
                    }
                    ?></select>
            </form>

        </td>
    </tr>
    <tr>
        <td align="center" class="bodyText">
            <form method="post" name="delForm_but" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <table width="100%" border="0" cellspacing="0" cellpadding="0" class="text">
                    <tr>
                        <td><strong>Note</strong>: Click on the product name to modify the details of that product.</td>
                        <td align="right" width="15%">
<?php
if ($permChecker->userCanMassUpdate()) {
    print '<input class="button" style="width:90px;" type="button" value="Edit" onclick="javascript:massUpdate(); return false;" />';
}
?>
                        </td>
                        <td align="right" width="15%">
                            <?php if(checkGroup(60)){?>
                            <input class="button" style="width:130px;" type="button" value="Add Product" onclick="location.href = 'addproduct.php?new=1'; return false;" /></td>
                            <?php }?>
                        <td align="right" width="10%"><?php
                         if(checkGroup(23)){
                             print "<input class=\"button\" style=\"width:60px;\" type=\"submit\" name=\"submit1\" id=\"delBt\" value=\"Delete\" onclick=\"confirmDel(); return false;\" />";
                         }
                         ?></td>
                    </tr>
                </table>
            </form>
        </td>
    </tr>
    <tr>
        <td>
            <form method="post" name="delForm" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <table width="100%" border="0" cellspacing="0" cellpadding="5" class="text">

                    <!-- search and right buttons close-->
<?php
# Start of block to delete product
if (isset($_POST['submit_but']) || isset($_GET['delID'])) { // && checkGroup(23)
    if (isset($_GET['delID'])) {
        $del = array($_GET['delID']);
    } else {
        $del = $_POST['delID'];
    }
    $deleteProductLog=DeletedProductIdLog($del);
    $message = deleteProduct($del);
    ################ for track product delete################
    if(!empty($message) && count($del)>=10){         
        foreach ($del as $pid) {
            $sqldelete = "DELETE FROM cscan_product_detail where productID IN ($pid)";
            $data = [
                'auth_id' => $GLOBALS['AUTH_DATA']['userID'],
                'auth_name'=>authUserName($GLOBALS['AUTH_DATA']['userID']),
                'deleted_id' => (int) $pid,
                'sql_query' => $sqldelete,
                'ip_address' => ipAddress(),
                'user_agent' => $_SERVER['HTTP_USER_AGENT'],
                'delete_type' => 'Product Delete',
                'is_mobile' => isMobile(),
                'insert_date' => date("Y-m-d H:i:s")
            ];
            trackDelete($data);
            //$emailData[] = $data;
        }
//        if(count($emailData)>0){
//            $html = '<table width="100%" border="1">';
//            $html .= '<tr><td>Auth ID</td><td>User Name</td><td>Deleted ID</td><td>SQL Query</td><td>IP Address</td><td>User Agent</td><td>Module</td><td>Is Mobile</td><td>Dealted Date (CST)</td></tr>';
//
//            foreach($emailData as $tr){
//                if(is_array($tr) && count($tr)>0){
//                   $html .= '<tr>';
//                   foreach($tr as $td){
//                       $html .= '<td>'.$td.'</td>'; 
//                   }
//                   $html .= '</tr>';
//                }
//            }                    
//            $html .= '</table>';
//
//            sendDevAlert('Caution! Data Deleted From Manage Product',$html);
//        }
    }
    ################ end for track product delete################
}

$sql = "SELECT SQL_CALC_FOUND_ROWS DISTINCT pd.productID,productName,pd.sectorID,categoryID,entryID,addedToDatabase,actual_addedToDatabase as actual_addedToDatabasef,admin_userID,productStatus,pd.mPanelID,pd.mChannelID,DMSource,DATE_FORMAT(approved_date,'%m/%d/%Y') as approved_datef,product_priority,special_handling";
$numquery = "SELECT COUNT(DISTINCT pd.productID) as numrows";
################### disable for the new changes dispaly only 300 records  ########################################
//$sql = "SELECT DISTINCT A.productID AS productID,productName,pd.sectorID,categoryID,entryID,addedToDatabase,DATE_FORMAT(actual_addedToDatabase,'%m/%d/%Y') AS actual_addedToDatabasef,admin_userID,productStatus,pd.mPanelID,pd.mChannelID,DMSource,DATE_FORMAT(approved_date, '%m/%d/%Y') AS approved_datef,product_priority,special_handling";
//$numquery = "SELECT COUNT(DISTINCT pd.productID) as numrows";
$jointext = '';
$sql2 = '';
################### disable for the new changes dispaly only 300 records  ######################################## 
if ($_SESSION['product_searchText'] != '') {
    $search_key = mysqlLike($_SESSION['product_searchText']);
    $sql2 .= " AND (productName LIKE '%$search_key%' OR entryID LIKE '%$search_key%')";
}
if ($_SESSION['product_DMSource'] != '') {
    $search_key = mysqlLike($_SESSION['product_DMSource']);
    $sql2 .= " AND (DMSource LIKE '$search_key%')";
}
if ($_SESSION['product_panelist_ids'] != '') {
    $vs = explode(',', $_SESSION['product_panelist_ids']);
    $ors = array();
    foreach ($vs as $v) {
        $v = trim($v);
        if (!empty($v)) {
            $v = mysqlLike($v);
            $ors[] = "(competi_id LIKE '" . $v . "%')";
        }
    }
    $cos = array();
    if (count($ors) > 0) {
        $sqlc = "select panelist_id from cscan_panelists WHERE (" . implode(' OR ', $ors) . ")";
        $rsc = $DRW->query($sqlc, $DRW_read);
        while ($rowc = $DRW->fetch_row($rsc)) {
            $cos[] = $rowc[0];
        }
        if (count($cos) == 0) {
            $cos[] = '0';
        }
    }
    $jointext .= " JOIN cscan_panelists_product pp ON (pd.productID=pp.productID)";
    $sql2 .= " AND pp.panelist_id IN (" . implode(',', $cos) . ")";
}
if ($_SESSION['company_search_text'] != '') {
    $search_key = mysqlLike($_SESSION['company_search_text']);
    $cos = array();
    $sqlc = "select companyID from cscan_company WHERE companyName LIKE '$search_key%'";
    $rsc = $DRW->query($sqlc, $DRW_read);
    while ($rowc = $DRW->fetch_row($rsc)) {
        $cos[] = $rowc[0];
    }
    if (count($cos) == 0) {
        $cos[] = '0';
    }
    $jointext .= " JOIN cscan_company_product co2 ON (co2.productID=pd.productID)";
    $sql2 .= " AND co2.companyID IN (" . implode(',', $cos) . ")";
}
if (!empty($_SESSION['country_search_id']) || !empty($_SESSION['state_search_id'])) {
   // $jointext .= " JOIN cscan_product_detail_state ON (cscan_product_detail_state.productID=pd.productID)";
    if (!empty($_SESSION['country_search_id'])) {
        if ($_SESSION['country_search_id'] == 'US') {
            //$sql2 .= " AND (cscan_product_detail_state.countryCode_copy='" . $DRW->real_escape_string($_SESSION['country_search_id']) . "' OR cscan_product_detail_state.countryCode_copy='')";
            $sql2 .= " AND (pd.countryCode_copy='" . $DRW->real_escape_string($_SESSION['country_search_id']) . "' OR pd.countryCode_copy='')";
        } else {
           // $sql2 .= " AND (cscan_product_detail_state.countryCode_copy='" . $DRW->real_escape_string($_SESSION['country_search_id']) . "')";
            $sql2 .= " AND (pd.countryCode_copy='" . $DRW->real_escape_string($_SESSION['country_search_id']) . "')";
        }
    }
    if (!empty($_SESSION['state_search_id'])) {
       // $sql2 .= " AND cscan_product_detail_state.stateID=" . $_SESSION['state_search_id'];
        $sql2 .= " AND pd.state=" . $_SESSION['state_search_id'];
    }
}

if ($_SESSION['ocr_search_text'] != '') {
    $searchKey = $_SESSION['ocr_search_text'];
    $search_id = session_id();
//    $count_save_sql = "SELECT COUNT(*) FROM cscan_search_adminproduct WHERE sid='$search_id' AND uid={$AUTH_DATA['userID']}";
//    $rs = $DRW->query($count_save_sql, $DRW_read);
//    $data = $DRW->fetch_row($rs);
//    $numrow = (int) $data[0];
    
    /*
      if($numrow==0 && !empty($SPHINX_name)){
      $s = startSphinx();

      $inds = 'base_index_'.$SPHINX_name.',delta_index_'.$SPHINX_name;
      if(strpos($searchKey,'*')!==false){
      $inds .= ',base_index_'.$SPHINX_name.'star,delta_index_'.$SPHINX_name.'star';
      }

      $ps = parseSphinx($s,$searchKey);

      if(trim($ps)!=''){
      $currcount = 0;
      $step = $total = 20000;
      $s->setLimits(0,1,1);
      $result = $s->query($ps,$inds);

      if(isset($result['matches'])){
      $total = (float)$result['total_found'];
      $count = 0;
      $minID = 0;
      $count_save_sql = "SELECT MAX(dts_id) FROM cscan_document_text_search";
      $rs = $DRW->query($count_save_sql,$DRW_read);
      $data = $DRW->fetch_row($rs);
      $maxID = $data[0];
      for($offset=0;$offset<=$maxID;$offset+=$step){
      $s = startSphinx();
      $s->setLimits(0,$step,$step);
      $s->setIDRange($minID+1,$maxID);
      $result = $s->query($ps,$inds);
      if(isset($result['matches'])){
      foreach($result['matches'] as $dts_id=>$match){
      $query = "REPLACE INTO cscan_search_adminproduct (uid,sid,productID) VALUES ({$AUTH_DATA['userID']},'$search_id',{$match['attrs']['productid']})";
      $DRW->query($query,$DRW_main);

      $minID = $dts_id;
      $currcount++;
      }
      if($currcount>=$total){
      break;
      }
      }
      $err = $s->getLastError();
      $war = $s->getLastWarning();
      if(!empty($err) || !empty($war)){
      //echo "$err | $war"; exit;
      break;
      }
      }
      }
      }
      }
     */





    if (!empty($SPHINX_name)) {
        $s = startSphinx();

       // $inds = 'base_index_' . $SPHINX_name . ',delta_index_' . $SPHINX_name;
        //$inds = 'base_index_prod_latest,delta_index_prod_latest';
        if($_SESSION['pstat']==1){
            $inds = 'base_index_' . $SPHINX_name . ',delta_index_' . $SPHINX_name;
        }else{
            $inds = 'base_index_prod_nonapproved,delta_index_prod_nonapproved';
        }
        
        if (strpos($searchKey, '*') !== false) {
            $inds .= ',base_index_' . $SPHINX_name . 'star,delta_index_' . $SPHINX_name . 'star';
        }

        $ps = parseSphinx($s, $searchKey);

        if (trim($ps) != '') {
            $currcount = 0;
           // $step = $total = 150000;
             $step = $total = 50000;
            $s->setLimits(0, 1, 1);
            $result = $s->query($ps, $inds);

            if (!empty($result['matches'])) {
                $total = (float) $result['total_found'];
                $count = 0;
                $minID = 0;
                $count_save_sql = "SELECT MAX(dts_id) FROM cscan_document_text_search";
                $rs = $DRW->query($count_save_sql, $DRW_read);
                $data = $DRW->fetch_row($rs);
                $maxID = $data[0];
                  $DRW->query('START TRANSACTION', $DRW_main); 
                for ($offset = 0; $offset <= $maxID; $offset += $step) {
                    $s = startSphinx();
                    $s->setLimits(0, $step, $step);
                    $s->setIDRange($minID + 1, $maxID);
                    $result = $s->query($ps, $inds);
                    if (isset($result['matches'])) {
                        foreach ($result['matches'] as $dts_id => $match) {
//                            $query = "REPLACE INTO cscan_search_adminproduct (uid,sid,productID) VALUES ({$AUTH_DATA['userID']},'$search_id',{$match['attrs']['productid']})";
//                            $DRW->query($query, $DRW_main);

                            $minID = $dts_id;
                            $currcount++;
                            
                             $productidsarray[] =   $match['attrs']['productid'];
                            
                            
                        }
                        if ($currcount >= $total) {
                            break;
                        }
                    }
                    $err = $s->getLastError();
                    $war = $s->getLastWarning();
                    if (!empty($err) || !empty($war)) {
                        //echo "$err | $war"; exit;
                        break;
                    }
                }
                
                 $DRW->query('COMMIT', $DRW_main); //$DRW->commit();
            }
            
               if ($search_id != '') {
                    //  $_SESSION['totalfetchid']   =  count(array_unique($productidsarray));
                    $productidsarray   =  array_unique($productidsarray);
                    if (!empty($productidsarray)) {
                        //$forceIndexaddedToDatabase = "  force index(idx_productID) ";
                        //$forceindex = 1;

                        $andUnion = '';
                        $chunkdata=10000;
                        if($total>600000){
                                $chunkdata=50000;
                        }
                        $newarray = array_chunk($productidsarray, $chunkdata);
                        // echo count($productidsarray).'===='.count($newarray); exit;
                        for ($u = 2; $u < 100; $u++) {
                            if (count($newarray) >= $u) {

                                $andUnion.="union ( SELECT dd.productID   FROM  cscan_product_detail dd  WHERE dd.productID IN(" . implode(',', ($newarray[$u - 1])) . "))";
                            }else{
                                continue;
                            }
                        }
                        //$wheresearchproduct =   " AND pd.productID in (".$productidsstring.") ";

                        $andcond = " select B.productID FROM  (SELECT dd.productID FROM  cscan_product_detail dd  WHERE dd.productID IN(" . implode(',', $newarray[0]) . ") " . $andUnion . ")B";
                        $wheresearchproduct = " AND pd.productID in (" . $andcond . ") ";
                    }

                    //print_r($result);echo"hhhh";exit;
                    //if(empty($productidsarray) && isset($result['total_found']) && $result['total_found']=='0'){
                    if (empty($productidsarray)) {
                        $andcond = '-1';
                        $wheresearchproduct = " AND pd.productID in (" . $andcond . ") ";
                    }
                }
            
        }
    }



  //  $jointext .= ' JOIN cscan_search_adminproduct se ON(se.sid=\'' . $search_id . '\' AND se.uid=' . $AUTH_DATA['userID'] . ' AND pd.productID=se.productID)';
}
if ($_SESSION['mc_search_id'] != 0) {
    $sql2 .= " AND pd.mChannelID=" . $_SESSION['mc_search_id'];
}
 #####Satrt for Anotation Tool Link Permission######
if(!empty($_SESSION['sess_sectorid']) AND $_SESSION['sess_sectorid'] != 0){
    $sql2 .= $andCondSector;
}
if(!empty($_SESSION['sess_catid']) AND$_SESSION['sess_catid'] != 0){
    $sql2 .= $andCondCategory;
}
 ##### End for Anotation Tool Link Permission######

$sql2 .=$wheresearchproduct;

$from = " FROM cscan_product_detail pd{$jointext}$sect_j";
############# disable for the new changes dispaly only 300 records  ######################################## 
// $fromsel = " FROM ( SELECT distinct pd.productID  FROM cscan_product_detail pd{$jointext}$sect_j";
//$fromnum = " FROM cscan_product_detail pd{$jointext}$sect_j";
//$from='';
########## disable for the new changes dispaly only 300 records  ######################################## 
switch (abs($sort)) {
    case 2:
        $from .= " LEFT JOIN cscan_company_product co ON(pd.productID=co.productID AND co.primary_co=1) LEFT JOIN cscan_company cc ON(co.companyID=cc.companyID)";

        break;
    case 3:
    case 5:
    case 7:
        $from .= " LEFT JOIN cscan_mpanel mp ON(pd.mPanelID=mp.mPanelID) LEFT JOIN cscan_mchannel mc ON(pd.mChannelID=mc.mChannelID) LEFT JOIN cscan_sector cs ON(pd.sectorID=cs.sectorID)";
        break;
}
$from .= " WHERE $addedtext";

if ($_SESSION['cstat'] == 7) {
    $from .= " AND pd.mChannelID=5 And pd.is_digital='0' And pd.is_mobile='1' ";
} elseif ($_SESSION['cstat'] == 12) {
    $from .= "  AND pd.mChannelID=5 And pd.is_digital='0' And pd.is_mobile='2'";
} else {
    if ($_SESSION['cstat'] == 8) {
        $from .= " AND is_affinion=1";
    } elseif ($_SESSION['cstat'] == 10) {
        $from .= " AND consumer_insights=1";
    } elseif ($_SESSION['cstat'] == 11) {
        $from .= " AND is_citi=1";
    } elseif ($_SESSION['cstat'] == 13) {
        $from .= " ";
    } elseif($_SESSION['cstat']==14){
			$from .= " AND pd.mChannelID=5 And pd.is_digital='1' ";
    }else {
        $from .= " AND pd.mChannelID<>5 AND pd.mChannelID<>10 AND pd.mChannelID<>9 ";
        if ($_SESSION['pstat'] == -2) {
            $from .= " AND is_subp=1";
            //Added only for NonCore Non-Panelist Unapproved
            if($_SESSION['pstat'] == -2 && $_SESSION['cstat'] == 6){
                 $from .= " AND is_citi<>1";
            }
        } else {
           //  if ($_SESSION['pstat'] == 2 ) {
             
            if (($_SESSION['pstat'] == 2) && ($_SESSION['cstat']==5||$_SESSION['cstat']==6 || $_SESSION['cstat']==9)) {
               
                $from .= " AND is_subp<>1";
              
            }
            $from .= " AND is_citi<>1";
            if ($_SESSION['pstat'] > 0 && $_SESSION['pstat'] != 1 && $_SESSION['pstat'] != 3 && $_SESSION['pstat'] != 4) {
                $from .= " AND consumer_insights<>1";
            }
        }
    }
}
$sql .= $from . $sql2;
$numquery .= $from . $sql2;
########## disable for the new changes dispaly only 300 records  ######################################## 
//$sql .= $fromsel.$from.$sql2;
//$numquery .= $fromnum.$from.$sql2;
########## disable for the new changes dispaly only 300 records  ######################################## 
//echo $numquery;
############ for remove the count query ##########################  
//$numquery = $DRW->query($numquery,$DRW_read);
//$nrow = $DRW->fetch_row($numquery);
//$numrows = $nrow[0];
############ for remove the count query ##########################
$overallPID = '';
$limitstart = 0;
$limitend = 500;

$sqldetailsel = " (SELECT productID FROM cscan_product_detail 
                ORDER BY actual_addedToDatabase DESC  ) ";

/*
  for ($u =2; $u < 3; $u++) {
  $limitstart=$limitend;
  $limitstart=($limitstart*$u);
  $sqldetailsel.="union ( SELECT productID FROM cscan_product_detail
  ORDER BY actual_addedToDatabase DESC limit $limitstart,500)";


  }
 */

//$sql.=" AND pd.productID IN($sqldetailsel)";
if ($sort < 0) {
    $ascdesc = 'DESC';
    $ascdesc2 = 'ASC';
} else {
    $ascdesc = 'ASC';
    $ascdesc2 = 'DESC';
}
switch (abs($sort)) {
    case 1:
        $sql .= " ORDER BY productName $ascdesc,entryID_sort1 $ascdesc2, entryID_sort2 $ascdesc2";
        break;
    case 2:
        $sql .= " ORDER BY cc.companyName $ascdesc,entryID_sort1 $ascdesc2, entryID_sort2 $ascdesc2";
        break;
    case 3:
        $sql .= " ORDER BY mChannelName $ascdesc,sectorName $ascdesc,mPanelName $ascdesc,entryID_sort1 $ascdesc2, entryID_sort2 $ascdesc2";
        break;
    //case 4: //see default
    case 5:
        $sql .= " ORDER BY mPanelName $ascdesc,mChannelName $ascdesc,sectorName $ascdesc,entryID_sort1 $ascdesc2, entryID_sort2 $ascdesc2";
        break;
    case 6:
        $sql .= " ORDER BY entryID_sort1 $ascdesc2, entryID_sort2 $ascdesc2, DMSource $ascdesc";
        break;
    case 7:
        $sql .= " ORDER BY sectorName $ascdesc,mChannelName $ascdesc,mPanelName $ascdesc,entryID_sort1 $ascdesc2, entryID_sort2 $ascdesc2";
        break;
    case 8:
        $sql .= " ORDER BY approved_date $ascdesc2";
        break;
    case 9:
        $sql .= " ORDER BY DMSource $ascdesc";
        break;
    default:
        $sql .= " ORDER BY {$extraorderby}actual_addedToDatabase $ascdesc2";
}
//$sql .= " LIMIT $p,$limit";




$sql .= " LIMIT $p,$limit";
########## disable for the new changes dispaly only 300 records  ######################################## 
//$sql.=" LIMIT $p,300) A, cscan_product_detail pd WHERE A.productID = pd.productID";
//$sql .= " LIMIT 0,$limit";
########## disable for the new changes dispaly only 300 records  ######################################## 
echo $sql;
//exit;  
$rs = $DRW->query($sql, $DRW_read);
############ for remove the count query ##########################
$numquery = $DRW->query("SELECT FOUND_ROWS()", $DRW_read);
$nrow = $DRW->fetch_row($numquery);
$numrows = $nrow[0];
$resultCount = $DRW->num_rows($rs);
############ for remove the count query ##########################
$count = 1 + $p;
$currPage = (($p / $limit) + 1);

function doSort($sort, $dosort, $spacer = '<br />') {
    if ($sort == ($dosort * -1) || $sort != $dosort) {
        print "$spacer<a href=\"" . $_SERVER['PHP_SELF'] . "?sort=$dosort&p=0\" class=\"blue\">sort</a>";
    } else {
        print "$spacer<a href=\"" . $_SERVER['PHP_SELF'] . "?sort=-$dosort&p=0\" class=\"blue\">sort</a>";
    }
}

?>

                    <tr class="head1">
                        <td class="adminhead" width="3%"><?php
                    if (checkGroup(45) || checkGroup(23))
                        print "<input type=\"checkbox\" name=\"setUnset\" onclick=\"setAll()\" />";
                    else
                        print '&nbsp;';
                    ?></td>
                        <td width="20%" class="adminhead"><strong>Product Name</strong><?php doSort($sort, 1); ?></td>
                        <td width="20%" class="adminhead"><strong>Company Name</strong><?php doSort($sort, 2); ?></td>
                        <td width="20%" class="adminhead"><strong>Media</strong><?php doSort($sort, 3, '&nbsp;'); ?><strong> / Sector</strong><?php doSort($sort, 7, '&nbsp;'); ?> <strong>/ Audience</strong><?php doSort($sort, 5, '&nbsp;'); ?></td>
                        <td class="adminhead"><strong>Date</strong><?php
                    if ($_SESSION['pstat'] == 1) {
                        doSort($sort, 4, '&nbsp;');
                        echo ' / <strong>Approved</strong>';
                        doSort($sort, 8, '&nbsp;');
                    } else {
                        doSort($sort, 4);
                    }
                    ?></td>
                        <td class="adminhead"><strong>Last User</strong></td>
                        <td class="adminhead"><strong>Entry ID</strong><?php doSort($sort, 6); ?></td>
                        <td class="adminhead"><strong>Source ID</strong><?php if ($_SESSION['pstat'] != 5 && $_SESSION['pstat'] != 6) doSort($sort, 9); ?></td>
                    </tr>
                    <tr><td colspan="8" align="center" class="error"><?php echo $message; ?></td></tr>
                            <?php
                            if ($resultCount > 0) {
                                $className = '';
                                while ($row = $DRW->fetch_array($rs)) {
                                    $productID = $row['productID'];
                                    $entryID = $row['entryID'];
                                    $productName = $row['productName'];
                                    $categoryID = $row['categoryID'];
                                    $sectorID = $row['sectorID'];
                                    $addedToDatabase = $row['addedToDatabase'];
                                    $actual_addedToDatabase = date('m/d/Y',strtotime($row['actual_addedToDatabasef']));
                                    $admin_userID = $row['admin_userID'];
                                    $productStatus = $row['productStatus'];
                                    $mediaPanel = mediaPanelName($row['mPanelID']);
                                    $mediaChannel = mediaChannelName($row['mChannelID']);
                                    $DMSource = $row['DMSource'];
                                    $approved_date = $row['approved_datef'];
                                    $product_priority = $row['product_priority'];
                                    $special_handling = $row['special_handling'];

                                    $sectorName = sectorName($sectorID);
                                    //$categoryName = sectorName($categoryID);
                                    //if($categoryName == "") $categoryName ="N/A";
                                    if ($productName == '')
                                        $productName = 'N/A';
                                    if ($mediaPanel == '')
                                        $mediaPanel = 'N/A';
                                    if ($mediaChannel == '')
                                        $mediaChannel = 'N/A';

                                    $resultC = $DRW->query("SELECT companyName FROM cscan_company pa,cscan_company_product pp 
				WHERE pa.companyID=pp.companyID AND pp.productID=$productID AND primary_co=1", $DRW_read);
                                    $dataC = $DRW->fetch_row($resultC);
                                    $company = $dataC[0];

                                    if ($className == 'selected-bg')
                                        $className = 'white-bg';
                                    else
                                        $className = 'selected-bg';

                                    $queryI = "SELECT UNIX_TIMESTAMP(img_document_createddate) FROM cscan_img_document WHERE productID=$productID AND document_id=1 AND img_document_default=1";
                                    $query_resultI = $DRW->query($queryI, $DRW_read);
                                    $dataI = $DRW->fetch_row($query_resultI);
                                    $img_createddate_ts = (float) $dataI[0];
                                   ?>
                                   <?php 
                                   ################# Start for Anotation Tool Link Permission################
                                   //if(ENV == 'localhost' || ENV == 'demo.competiscan.com'){
                                       $anotationtoollink='';
                                        if(checkGroup(108)){
                                            $expSectArray=explode(',',$sectorID);
                                            $expCatArray=explode(',',$categoryID);
                                            //echo "MediaChanel==".$row['mChannelID']."===SectorID==".$row['sectorID']."==CategoryID==".$row['categoryID'];
                                          if(($row['mChannelID']==3) AND ($row['mPanelID']==1|| $row['mPanelID']==2)) { 
                                          //if(($row['mChannelID']==3) AND ($row['mPanelID']==1|| $row['mPanelID']==2)  AND (in_array(266,$expSectArray)) AND (in_array(269,$expCatArray))) { 
                                              $queryProdEmail = "select muid from cscan_product_email where productID=$productID";   
                                              $query_result_prod_email = $DRW->query($queryProdEmail,$DRW_read);
                                               $numrows2=$DRW->num_rows($query_result_prod_email);                            
                                               if($numrows2>0){
                                                  $prodEmailData=$DRW->fetch_row($query_result_prod_email);
                                                  $muID=$prodEmailData[0];
                                                  $adminUserName=$GLOBALS['AUTH_DATA']['userName'];
                                                  
                                                  $adminUserID=$GLOBALS['AUTH_DATA']['userID'];
                                                  //$displayAnotationLink='https://ml-anotation.competiscan.com/v2/muid-data/'.$muID;
                                                  $displayAnotationLink=ANNOTATIONTOOLDATAURL.$muID;
                                                  $anotationtoollink='<br/>&nbsp;&nbsp;&nbsp;&nbsp;<a class="bluelink" target="_blank" href="'.$displayAnotationLink.'"  title="Anotation Tool Link"><img width="12" height="12" src="../settings-anotation.png" border="0" style="vertical-align:top;" /></a>';
                                              }
                                          }

                                       }
                                   //}
                                   ?>
                                 
                            <tr class="<?php echo $className; ?>" valign="top" <?php
                            if ($productStatus != 1) {
                                echo ' style="background-color:#E8E8FF;"';
                            }
                            ?>>
                                <td valign="top"><?php
                            if (checkGroup(45)|| checkGroup(23) || ($actual_addedToDatabase == date('m/d/Y') && $admin_userID == $AUTH_DATA['userID']))
                                print "<input type=\"checkbox\" name=\"delID[]\" value=\"$productID\" />";
                            else
                                print '&nbsp;';
                            ?>
                                </td>
                                <td valign="top"><img src="../images/arrow.gif" id="<?php echo 'pimg' . $productID; ?>" alt="" title="Preview this Product" style="cursor:pointer;" onclick="doPreview('<?php echo $productID; ?>',<?php echo $img_createddate_ts; ?>);" onmouseover="showPreview(<?php echo $productID; ?>,<?php echo $img_createddate_ts; ?>);
                                        return true;" onmouseout="hidePreview(<?php echo $productID; ?>); return true;" />
                <!--		<a class="hlinks" onclick="return removeNewTab(this);" data-href="addproduct.php?id=<?php //echo $productID;?>"><strong><?php //echo ucfirst($productName);?></strong></a></td>-->
                                    <a class="hlinks"  href="addproduct.php?id=<?php echo $productID; ?>"><strong><?php echo ucfirst($productName); ?></strong></a><?php echo $anotationtoollink;?></td>
                                <td><?php
                            echo ($company != '') ? $company : '&nbsp;';
                            ?></td>
                                <td><?php echo $mediaChannel . ' / ' . $sectorName . ' / ' . $mediaPanel; ?></td>
                                <td><?php
                            echo $actual_addedToDatabase;
                            if ($productStatus == 1 && $approved_date != $actual_addedToDatabase && $approved_date != '00/00/0000') {
                                echo '<br />' . $approved_date;
                            }
                            ?></td>
                                <td><?php
                            $userquery = "SELECT userName FROM cscan_admin_users WHERE userID=$admin_userID";
                            $userquery = $DRW->query($userquery, $DRW_read);
                            if ($DRW->num_rows($userquery) > 0) {
                                $unam = $DRW->fetch_row($userquery);
                                $userName = $unam[0];
                            } else
                                $userName = '';
                            if ($userName != '')
                                print "<a href=\"#\" onclick=\"logPop(0,$productID,0); return false;\">$userName</a>";
                            else
                                print '&nbsp;';
                            ?></td>
                                <td valign="top"><?php
                                    $showDMSource = true;
                                    //$DMSource = preg_replace('/^\\d+_\\d+_\\d+_/','',$DMSource);
                                    $DMSource = preg_replace('/_?core2?$/', '', $DMSource);
                                    if ($entryID != '') {
                                        echo $entryID;
                                    } elseif ($DMSource != '') {
                                        echo '<span style="font-size:smaller;';
                                        if (($product_priority || $special_handling) && $productStatus != 1) {
                                            echo 'color:#B5364B;';
                                        }
                                        echo '">(' . $DMSource . ')</span>';
                                        $showDMSource = false;
                                    } else {
                                        echo '&nbsp;';
                                    }
                                    ?></td>
                                <td valign="top"><?php
                                    $sqltmp = "SELECT muid,isTmp FROM cscan_product_email WHERE productID='" . $DRW->real_escape_string($productID) . "' ORDER BY muid DESC";
                                    $rstmp = $DRW->query($sqltmp, $DRW_read);
                                    if ($DRW->num_rows($rstmp) > 0) {
                                        while ($rowtmp = $DRW->fetch_row($rstmp)) {
                                            if ($rowtmp[1] == 1)
                                                print "<a href=\"manage_tmp_product.php?search_text=$rowtmp[0]tmp&state=0&company=\">$rowtmp[0]tmp</a> ";
                                            else
                                                print "<a href=\"/email.php?muid=$rowtmp[0]\" target=\"_blank\">$rowtmp[0]</a> ";
                                        }
                                    }
                                    elseif ($showDMSource && $DMSource != '') {
                                        echo '<span style="font-size:smaller;';
                                        if (($product_priority || $special_handling) && $productStatus != 1) {
                                            echo 'color:#B5364B;';
                                        }
                                        echo '">(' . $DMSource . ')</span>';
                                    } else {
                                        $sqltmp = "SELECT id FROM chicagorecords WHERE productID='" . $DRW->real_escape_string($productID) . "'";
                                        $rstmp = $DRW->query($sqltmp, $DRW_read);
                                        if ($DRW->num_rows($rstmp) > 0) {
                                            $rowtmp = $DRW->fetch_row($rstmp);
                                            echo $rowtmp[0] . 'crm';
                                        } else {
                                            echo '&nbsp;';
                                        }
                                    }
                                    ?></td>
                            </tr>
                                    <?php
                                    $detail = '';
                                }
                            } else {
                                ?>
                        <tr><td colspan="8" class="error" align="center">No Product Found.
                                <script type="text/javascript">
                                    <!--
                                  var el = document.getElementById('delBt');
                                    if (el) {
                                        el.style.display = 'none';
                                    }
                                    //-->
                        </script></td></tr>
                                <?php
                            }
                            ?>
                    <tr>
                        <td colspan="8">
                            <table border="0" width="100%" cellspacing = "0"  cellpadding ="5">
                                <tr>
                                    <td colspan = "2"> &nbsp;</td>
                                </tr>
<?php
if ($sort > 0)
    $sorttext = '&sort=' . $sort;
else
    $sorttext = '';
$firstlink = '[First]';
$prevlink = '[Prev]';
$nextlink = '[Next]';
$lastlink = '[Last]';
$middlelinks = '';
$limstart = $p;
$limiter = $limit;
$rowcnt = $numrows;
$show = 10;
if ($rowcnt > 0) {
    //first and previous only if not on first
    if ($limstart > 0) {
        if ($limstart >= $limiter)
            $prev = $limstart - $limiter;
        else
            $prev = 0;
        $firstlink = "[<a href=\"{$_SERVER['PHP_SELF']}?p=0$sorttext\">First</a>]";
        $prevlink = "<a href=\"{$_SERVER['PHP_SELF']}?p=$prev$sorttext\">&laquo; Prev $limiter</a>";
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
            $middlelinks .= "<a href=\"{$_SERVER['PHP_SELF']}?p=$startnum$sorttext\">" . ($i + 1) . "</a> ";
        } else
            $middlelinks .= ($i + 1) . ' ';
    }
    //next and last if not on last
    if ($limstart < $rowcnt && (($limstart + ($limiter * 2)) < $rowcnt || ($rowcnt - ($limstart + $limiter)) > 0)) {
        $next = $limstart + $limiter;
        $nextlink = "<a href=\"{$_SERVER['PHP_SELF']}?p=$next$sorttext\">Next $limiter &raquo;</a>";
        $lastlink = "[<a href=\"{$_SERVER['PHP_SELF']}?p=" . (($numbers - 1) * $limiter) . "$sorttext\">Last</a>]";
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
}
?>
                            </table></td></tr>
                </table>
                <input type="hidden" name="submit_but" value="1" /></form>
        </td>
    </tr>
</table>
        <script type="text/javascript">
<!--
                                function confirmDel() {
                                    var goAheadFlag = 0;
                                    for (var i = 0; i < document.delForm.elements.length; i++) {
                                        if (document.delForm.elements[i].checked == true) {
                                            goAheadFlag = 1;
                                            break;
                                        }
                                    }
                                    if (goAheadFlag) {
                                        if (confirm("Are you sure you want to delete?")) {
                                            document.delForm.submit();
                                        } else {
                                            return false;
                                        }
                                    } else {
                                        alert("Please select at least one record to delete !!!");
                                        return false;
                                    }
                                    return true;
                                }

                                function setAll() {
                                    if (document.delForm.setUnset.value == 'on') {
                                        for (var i = 1; i < document.delForm.elements.length; i++) {
                                            document.delForm.elements[i].checked = true;
                                        }
                                        document.delForm.setUnset.value = '';
                                    } else {
                                        for (var i = 1; i < document.delForm.elements.length; i++) {
                                            document.delForm.elements[i].checked = false;
                                        }
                                        document.delForm.setUnset.value = 'on';
                                    }
                                }

                                function check_searchform() {
                                    var search = document.prodForm.search_text.value = trimspace(document.prodForm.search_text.value);
                                    var searchDM = document.prodForm.DMSource.value = trimspace(document.prodForm.DMSource.value);
                                    var search2 = document.prodForm.company.value = trimspace(document.prodForm.company.value);
                                    var search3 = document.prodForm.state.selectedIndex;
                                    var search4 = document.prodForm.ocr.value = trimspace(document.prodForm.ocr.value);
                                    var search5 = document.prodForm.panelist_ids.value = trimspace(document.prodForm.panelist_ids.value);
                                    var search6 = document.prodForm.mc.selectedIndex;
                                    var search7 = '';
                                    for (var i = 0; i < document.prodForm.country.length; i++) {
                                        if (document.prodForm.country[i].value != '' && document.prodForm.country[i].checked) {
                                            search7 = 'yes';
                                            break;
                                        }
                                    }
                                    if (search == '' && search2 == '' && search3 < 1 && search4 == '' && searchDM == '' && search5 == '' && search6 < 1 && search7 == '') {
                                        alert("Please enter some value to search");
                                        document.prodForm.search_text.focus();
                                        return false;
                                    }
                                    return true;
                                }
                                function logPop(mid, pid, istmp) {
                                    var wind = window.open('admin_log.php?mid=' + mid + '&pid=' + pid + '&istmp=' + istmp, "winpop", "left=0, top=0, scrollbars=yes, resizable=yes, width=400, height=300");
                                    wind.focus();
                                }
                                function checkCompanyWV() {
                                    return true;
                                }
                                if (checkIE6()) {
                                    document.write('<iframe id="ieframe" src="javascript:\'<html><head><title><\/title><\/head><body>&nbsp;<\/body><\/html>\';" scrolling="no" frameborder="0" style="display:none;position:absolute;border:solid 1px #ffffff;background:#0055E3;padding:4px;color:#ffffff;z-index:99;"><\/iframe>');
                                }
                                function doPCopy_pop(loc) {
                                    var winy = window.open(loc, null, "scrollbars=yes, resizable=yes, height=100,width=450,status=yes,toolbar=no,menubar=no,location=no,addressbar=yes");
                                    winy.focus();
                                }

//-->
    </script>
    <script>
    function getCategory() {
            var str='';
            var cate_id='';
            var sect_id='';
            var val=document.getElementById('sector_list');
            for (i=0;i< val.length;i++) { 
                if(val[i].selected){
                    str += val[i].value + ','; 
                }
            }
          
            if(str.length>0){
               
                sect_id=str.slice(0,str.length -1);
            }
                    
            var cat_val=document.getElementById('category_list');
            var str2='';
            if(cat_val.length >0){
                for (i=0;i< cat_val.length;i++) { 
                        if(cat_val[i].selected){
                            str2 += cat_val[i].value + ','; 
                        }
                    }
                cate_id=str2.slice(0,str2.length -1);    
             }
            
            
            if(sect_id!=''){               
                $.ajax({          
                        type: "POST",
                        url: "ajax-get-scsc.php",
                        data: {sid:sect_id,cate_id:cate_id,action:'sector',},
                        success: function(data){
                                $('#category_list').html(data);
                                //getSubCategory();
                                //getSubToSubCategory();
                                }
                    });
           
            }else{
                //alert('ppppp');
               $('#category_list').html("<option selected value=''>--Any--</option>");  
              
            }
        }
</script>
    <div id="showbox_cos" style="display:none;position:absolute;border:solid 1px #fff;background:#14734F;padding:4px;color:#fff;z-index:100;"></div>
<?php
include 'massupdatetool.php';
include 'bottom.php';
