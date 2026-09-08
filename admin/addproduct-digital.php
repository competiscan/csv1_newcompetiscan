<?php
$ALLOW_GROUPS = array(4);
require_once("../auth_auth.php");
$ONLOAD = 'checkAllDeps();';
/*####### START NEW PROMOTION FIELD #########*/
if(isset($_GET['id'])){
	$updID = $_GET['id'];
        $ONLOAD .= 'doPromotionRetailCompany('.$_GET['id'].')';
}
if(isset($_GET['muid'])){
        $ONLOAD .= 'doPromotionRetailCompany('.$_GET['muid'].',isTmp=1)';
	
}
/*####### END NEW PROMOTION FIELD #########*/
include 'top.php';
require_once '../includes/functions.php';
ini_set("memory_limit", "-1");
set_time_limit(0);

require_once('../vendor/autoload.php');
$simple_domain = '';
$error_msg = '';
$url = "addproduct-digital.php";
if (isset($_GET['muid'])) {
    $disabled = ' disabled="disabled"';
    $url = "addproduct.php?muid=" . $_GET['muid'];
    if (isset($_REQUEST['isTmp']))
        $url .= '&isTmp=1';
}
else {
    $disabled = '';
}
if (isset($_GET['id'])) {
    $updID = $_GET['id'];
    $url = "addproduct-digital.php?id=$updID";
} elseif (isset($_GET['pid'])) {
    $updID = $_GET['pid'];
} elseif (isset($_REQUEST['updID'])) {
    $updID = $_REQUEST['updID'];
} else
    $updID = '';

$page_heading = 'ADD NEW PRODUCT';

if ($updID != '') {
    $page_heading = 'UPDATE PRODUCT';
}

if (isset($_REQUEST['new']) && $_REQUEST['new'] != "") {
    $page_heading = 'ADD NEW PRODUCT';
    $updID = '';
}

$fromtemp = false;
$isdevice_mobile = '0';
$oldIDss='';
$selecttable='';

/*  For already processed data redirect to previous page */
    
    if (isset($_REQUEST['old'])) {
            $oldIDss = $_REQUEST['old'];
        }
    if (isset($_REQUEST['img_capture_id'])) {
            $oldIDss = $_REQUEST['img_capture_id'];
        }
    if (isset($_REQUEST['red_digital_id']) and $_REQUEST['red_digital_id'] != '') {
            $addid = $_REQUEST['red_digital_id'];
        }    
    if (isset($_REQUEST['add']) and $_REQUEST['add'] != '') {
         $addid = $_REQUEST['add'];
    }
    
    
    /* For digital table split */
    if (isset($_REQUEST['tbl']) and $_REQUEST['tbl'] != '') {
        $tblid = $_REQUEST['tbl'];
    } else if (isset($_REQUEST['select_dig_tbl']) and $_REQUEST['select_dig_tbl'] != '') {
        $selecttable = $_REQUEST['select_dig_tbl']; 
    } else {
        $tblid = '1';
    }
    if($selecttable=='' && $tblid!=''){
        $sql_dig_tbl = "SELECT table_name FROM cscan_digital_creative_tables where id='" . $tblid . "'";
        $res_tbl = $DRW->query($sql_dig_tbl, $DRW_digital);
        $resrow = $DRW->fetch_row($res_tbl);
        $selecttable = $resrow[0];
    }
    /* End For digital table split */  
    
    
    IF ($oldIDss != '') {
           $sqlsss = "SELECT capture_status FROM " . $selecttable . " where creative_id='" . $oldIDss . "'";
            $rssss = $DRW->query($sqlsss, $DRW_digital);
            $dataCC = $DRW->fetch_row($rssss);
            $capturestatus = $dataCC[0];    
        if($capturestatus=='2' || $capturestatus=='3'){    
            if($addid=='1'){            
                $redirect_url = "ad_online_capture.php";
             }else if($addid=='2'){            
                $redirect_url = "ad_sem_capture.php";
             }else if($addid=='3'){            
                $redirect_url = "ad_video_capture.php";
             }else{
                $redirect_url = "manageproduct-digital.php";                 
             }
            header("Location: $redirect_url");
               exit;
        }
    }
    
 /*  End For already processed data redirect to previous page */  
 /* START ADD PRODUCT CONTENT FOR DIGITAL*/
$PDFContent='';
if(isset($updID,$_REQUEST['add']) and $updID != '' and $_REQUEST['add']!=''){
            if($_REQUEST['add']==1){ 
                $sqlQ = "SELECT digital_text FROM cscan_digital_od_ads_text WHERE productID ='".$updID."'"; 
            }elseif($_REQUEST['add']==2) { 
                $sqlQ = "SELECT sem_description FROM cscan_digital_sem_ads_text WHERE productID ='".$updID."'";
            } elseif ($_REQUEST['add']==3){
                $sqlQ = "SELECT digital_text FROM cscan_digital_video_ads_text WHERE productID ='".$updID."'";    
            }
            $rss = $DRW->query($sqlQ, $DRW_read);
            $dataC = $DRW->fetch_row($rss);
            $PDFContent = $dataC[0];
}
    
 /* START ADD PRODUCT CONTENT FOR DIGITAL*/   
if (isset($_REQUEST['add']) and $_REQUEST['add'] != '') {
    $addid = $_REQUEST['add'];      
     
    if ($addid == '1') {
        $sel_mchanel = '5';
        $isdevice_mobile = '2';
        $oldIDs = '';
        if (isset($_REQUEST['old'])) {
            $oldIDs = $_REQUEST['old'];
        }

        IF ($oldIDs != '') {
            $sqls = "SELECT ad_md5 FROM " . $selecttable . " where creative_id='" . $oldIDs . "'";
            $rss = $DRW->query($sqls, $DRW_digital);
            $dataC = $DRW->fetch_row($rss);
            $ad_md5 = $dataC[0];

            $devicename = ShowDeviceBymd5($ad_md5);
            $simple_domain = ShowDeviceBymd5($ad_md5, 'simple_domain');
            if ($devicename == 'Desktop')
                $isdevice_mobile = '1';
        }
    }else if ($addid == '2') {
        $sel_mchanel = '9';
        $isdevice_mobile = '2';
        $oldIDs = '';
        if (isset($_REQUEST['old'])) {
            $oldIDs = $_REQUEST['old'];
        }

        IF ($oldIDs != '') {
            $sqls = "SELECT ad_md5 FROM " . $selecttable . " where creative_id='" . $oldIDs . "'";
            $rss = $DRW->query($sqls, $DRW_digital);
            $dataC = $DRW->fetch_row($rss);
            $ad_md5 = $dataC[0];

            $devicename = ShowDeviceBymd5($ad_md5);
            $simple_domain = ShowDeviceBymd5($ad_md5, 'simple_domain');
            if ($devicename == 'Desktop')
                $isdevice_mobile = '1';
        }
    }else if ($addid == '3') {
        $sel_mchanel = '10';
        $isdevice_mobile = '2';
        $oldIDs = '';
        if (isset($_REQUEST['old'])) {
            $oldIDs = $_REQUEST['old'];
        }

        IF ($oldIDs != '') {
            $sqls = "SELECT ad_md5 FROM " . $selecttable . " where creative_id='" . $oldIDs . "'";
            $rss = $DRW->query($sqls, $DRW_digital);
            $dataC = $DRW->fetch_row($rss);
            $ad_md5 = $dataC[0];

            $devicename = ShowDeviceBymd5($ad_md5);
            $simple_domain = ShowDeviceBymd5($ad_md5, 'simple_domain');
            if ($devicename == 'Desktop')
                $isdevice_mobile = '1';
        }
    }
}else {
    $sel_mchanel = '';
}
if (isset($_REQUEST['cstat']) and $_REQUEST['cstat'] != '') {
    $cstat = $_REQUEST['cstat'];
    if ($cstat == '14') {
        $sel_mchanel = '5';
    } else if ($cstat == '15') {
        $sel_mchanel = '9';
    } else if ($cstat == '16') {
        $sel_mchanel = '10';
    }
}

include 'addProductPersistenceAndLogic-digital.php';
$disabledButton='';
if($mChannelID==5 ||$mChannelID==9 || $mChannelID==10 ){
  $disabledButton ='disabled'; 
}
include 'addProductFormBuilder-digital.php';
?>
<table width="100%" border="0" cellspacing="0" cellpadding="5" class="text">
    <tr><td align="center" class="adminhead"><?php echo $page_heading; ?></td></tr>
    <tr><td align="right" class="text"><strong><span class="error">* required field</span></strong></td></tr>
    <tr><td align="right" class="text"><strong>* required for approval</strong></td></tr>
<?php
if (isset($_GET['more'])) {
    echo '<tr><td align="center" class="text">New product has been added successfully.</td></tr>';
}
?>
    <tr><td align="center" class="error"><strong><?php
if (isset($_GET['error_pdf'])) {
    echo "The Product PDF was not uploaded.<br />Please check that the file is .pdf format less than 32MB in size.";
}
if (isset($_GET['error_media'])) {
    echo "The Product Media was not uploaded.<br />Please check that the file is .swf, .gif, .jpg, or .png format less than 32MB in size.";
} elseif (isset($_GET['img_err'])) {
    echo "Product Image was not uploaded.<br />Please check that the file is of .jpg, .png, or .gif format less than 2MB in size.";
} elseif (isset($_GET['headline_err'])) {
    echo "Product Headline already exists.";
} elseif ($error_msg != '') {
    echo $error_msg;
} else {
    echo '&nbsp;';
}
?></strong></td></tr>
    <tr>
        <td>
            <form method="post" name="prodForm" action="<?php echo $url; ?>" onsubmit="return validate();" enctype="multipart/form-data"><input type="hidden" name="MAX_FILE_SIZE" value="64000000" />
                <?php
                foreach ($displayKeys as $s => $title) {
                    $style = '';
                    $part = 1;
                    /*####### START NEW PROMOTION FIELD #########*/
                    if ($s != 'top' && $s != 'bottom' && $s!='promotion') {
                        if (!checkSector($s) && !checkCategory($s)) {
                            continue;
                        }
                        if (!in_array($s, $sectorID) && !in_array($s, $categoryID)) {
                            $part = 0;
                            $style = ' style="display:none;"';
                        }
                    }
                    echo '<div id="div_' . $s . '"' . $style . '>';
                    if ($title != '') {
                        echo '<table border="0" width="100%" cellpadding="5" cellspacing="0">';
                        echo '<tr><td class="bodytext" align="right" width="30%"><strong>' . $title . '</strong></td><td width="70%">&nbsp;</td></tr>';
                        echo '</table>';
                    }

                    foreach ($displayArray[$s] as $d => $display) {
                        echo '<div id="div_' . $s . '_' . $d . '" style="overflow:hidden;"><table border="0" width="100%" cellpadding="5" cellspacing="0">';

                        if ($display['value'] == '') {
                            echo '<tr><td class="bodytext" align="right" width="30%"><strong><em>';

                            if ($title != '') {
                                echo $title . ' - ';
                            }

                            echo $display['title'] . '</em></strong></td><td width="70%">&nbsp;</td></tr>';
                        } else {
                            echo '<tr><td class="bodytext" align="right" valign="top" width="30%">' . $display['title'] . '</td><td class="bodytext" valign="top" width="70%">' . $display['value'] . '</td></tr>';
                        }

                        echo '</table></div>' . "\n";
                    }

                    if ($title != '') {
                        echo '<table border="0" width="100%" cellpadding="5" cellspacing="0">';
                        echo '<tr><td colspan="2">&nbsp;</td></tr>';
                        echo '</table>';
                    }
                    echo '</div><input type="hidden" name="part_' . $s . '" value="' . $part . '" />';
                }
                ?>
                <input type="hidden" name="pcopy_pop" value="" /><input type="hidden" name="save" value="1" /><input type="hidden" name="updID" value="<?php echo $updID; ?>" /><input type="hidden" name="curpdf" value="<?php echo $curpdf; ?>" /><input type="hidden" name="curimg" value="<?php echo $curimg; ?>" /><input type="hidden" name="muid" value="<?php echo $muid; ?>" /><input type="hidden" name="old_addedToDatabase" value="<?php echo $addedToDatabase; ?>" /><input type="hidden" name="productStatus" value="1" /><input type="hidden" name="old_productStatus" value="<?php echo $productStatus; ?>" /><input type="hidden" name="old_productStatusDesc" value="<?php echo $productStatusDesc; ?>" /></form>
        </td>
    </tr>
</table>
                <?php
                include 'addProductJSandPopups-digital.php';
                include 'bottom.php';

                if (isset($_REQUEST['old']) && $_REQUEST['old'] != "" && isset($_REQUEST['add']) && $_REQUEST['add'] != "") {
                    $page_heading = 'ADD NEW PRODUCT';
                    $updID = '';
                    $oldID = $_REQUEST['old'];

                    $sqls = "select table_name from cscan_digital_observation_tables";
                    $results = $DRW->query($sqls, $DRW_digital);
                    $panelst_arr = array();
                    $date_observed = array();
                    $allpanelist = array();
                    while ($rows = $DRW->fetch_array($results)) {
                        $tblsname = $rows['table_name'];
                        //$sql="SELECT do.panelist_id,do.date_observed,dcc.creative_path FROM ".$tblsname." do left join ".$selecttable." as dcc on (do.ad_md5=dcc.ad_md5) where dcc.creative_id='".$oldID."' and do.panelist_id!='' group by do.panelist_id"; 
                        $sql = "SELECT do.panelist_id,do.date_observed,dcc.creative_path FROM " . $tblsname . " do left join " . $selecttable . " as dcc on (do.ad_md5=dcc.ad_md5) where dcc.creative_id='" . $oldID . "' and do.panelist_id!='' ";
                        $rs = $DRW->query($sql, $DRW_digital);
                        $resultCount = $DRW->num_rows($rs);
                        if ($resultCount > 0) {

                            while ($dataC = $DRW->fetch_row($rs)) {
                                $panid = $dataC[0];

                                if (in_array($dataC[0], $allpanelist)) {
                                    continue;
                                }
                                $allpanelist[] = $dataC[0];


                                $creative_path = $dataC[2];
                                $orderByField = 'competi_id';
                                $date_observed = $dataC[1];
                                $date_observed = date('Y-m-d', strtotime($date_observed));
                                //echo "SELECT competi_id,panelist_id,first_name,last_name,gender,DATEDIFF(CURDATE(),birthdate) as age,incomeID,stateID,contactTypeID,birthdate,ownbiz FROM cscan_panelists 
                                //WHERE active=1 and panelist_id='".$panid."' ORDER BY $orderByField";
                                //echo"<br><br>";

                                $resultC = $DRW->query("SELECT competi_id,panelist_id,first_name,last_name,gender,DATEDIFF(CURDATE(),birthdate) as age,incomeID,stateID,contactTypeID,birthdate,ownbiz FROM cscan_panelists 
    WHERE active=1 and panelist_id='" . $panid . "' ORDER BY $orderByField", $DRW_read);
                                while ($dataC = $DRW->fetch_row($resultC)) { //echo 'kkkkkkkkk11'; die;
                                    $competi_id = $dataC[0];
                                    $panelist_id = $dataC[1];
                                    $first_name = $dataC[2];
                                    $last_name = $dataC[3];
                                    $gender = strtoupper(substr($dataC[4], 0, 1)); // radio M, F
                                    $age = floor($dataC[5] / 365);
                                    $incomeID = $dataC[6];
                                    $stateID = $dataC[7];
                                    $contactTypeID = $dataC[8];
                                    $birthdate = $dataC[9];
                                    $ownbiz = $dataC[10];

                                    $mChannelID = 1;
                                    if ($contactTypeID == 1) {
                                        $mPanelID = 4;
                                    } elseif ($contactTypeID == 2) {
                                        $mPanelID = 1;
                                    }
                                    if ($incomeID == 0) {
                                        $incomeID = -1;
                                    }
                                    $ageID = -1;
                                    if ($birthdate != '0000-00-00') {
                                        $ageObj = new HS\Age($DRW);
                                        $ageObj->setAge($age);
                                        $ageID = $ageObj->getGroupsAsCommaDelimitedString();
                                        $ageID = str_replace("'", "", $ageID);
                                    }
                                    ?>
                    <script type="text/javascript">
                        checkDeps_mtvariant();
                        addPan_Digital('<?php echo $panelist_id; ?>', '<?php echo $competi_id; ?>', '<?php echo $mChannelID; ?>', '<?php echo $mPanelID; ?>', '<?php echo $gender; ?>', '<?php echo $ageID; ?>', '<?php echo $incomeID; ?>', '<?php echo $stateID; ?>', '<?php echo $ownbiz; ?>', '<?php echo $date_observed; ?>');

                        getDeps('offerOrigin');
                        markInsuranceexchange();
                        checkChannel();
                        doDelMeth();
                        checkDeps_mc();
                    </script>
                    <?php
                }
            }
        }
    }
}
