<?php
require_once('vendor/autoload.php');

use mikehaertl\wkhtmlto\Pdf;

$makePDF = isset($_GET['makepdf']) ? true : false;

if ($makePDF) {
    ob_start();
}
$start_time = microtime(true);
require_once('includes/globalSession.php');
require_once('includes/checklogin.php');
require_once 'product_doc_tracker.php';
track_user();

$productID = (float) $_GET['id'];
$currentpage_url='http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

$hosts = $_SERVER['HTTP_HOST'];
if ($hosts == 'localhost') {
    $baseurl = 'http://localhost/competiscan.com';
}else if ($hosts == 'uat3.competiscan.com') {
    $baseurl = 'http://uat3.competiscan.com';
} else if ($hosts == 'demo.competiscan.com') {
    $baseurl = 'http://demo.competiscan.com';
} else {
    $baseurl = 'https://www.competiscan.com';
}
if(!defined('ENV')){
    define('ENV',getenv('SERVER_NAME'));
} 
if(ENV == 'localhost' || ENV == 'demo.competiscan.com'){
    $displayS3PdfUrl='http://files1.competiscan.com/';
}else{
    $displayS3PdfUrl='https://files.competiscan.com/';
}
############# Start: Email Tracking ########
$tracking_id = (!empty($_GET['trmsg'])) ? trim($_GET['trmsg']) : "";
if ($tracking_id) {
    if (!is_numeric($tracking_id)) {
        $tracking_id = base64_decode(base64_decode(base64_decode($tracking_id)));
    }
    $update_tracking_sql = "UPDATE cscan_email_track SET is_opened =1, is_clicked = 1 WHERE id= '" . $tracking_id . "'";
    $DRW->query($update_tracking_sql, $DRW_main);
}
############ End: Email Tracking ########
$productQuery = "SELECT * FROM cscan_product_detail WHERE productID=$productID";
$productQuery = $DRW->query($productQuery, $DRW_read);
$productRs = $DRW->fetch_array($productQuery);

if (isset($_REQUEST['ssid'])) {
    $ssid = (float) $_REQUEST['ssid'];
} else {
    $ssid = 0;
}
track_product($_SESSION['sess_userID'], (int) $productID);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <?php if ($makePDF) { ?>
            <base href="<?php echo ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http') ?>://<?php echo $_SERVER['HTTP_HOST'] ?>">
            <?php } ?>
            <title>Product Detail</title>
            <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
            <script type="text/javascript" src="<?php echo $baseurl; ?>/includes/jsFunctions.js"></script>
            <script type="text/javascript" src="<?php echo $baseurl; ?>/includes/ajax.js"></script>
            <script src="<?php echo $baseurl; ?>/includes/productDetail.js?v=20140201" type="text/JavaScript"></script>
            <?php
            if ($productRs['mChannelID'] == 5 || $productRs['mChannelID'] == 7) {
                ?>
                <script type="text/javascript" src="<?php echo $baseurl; ?>/includes/swfobject.js"></script>
                <?php
            }
            ?>
            <link href="<?php echo $baseurl; ?>/includes/competiscan_stylesheet.css" rel="stylesheet" type="text/css" />
            <style type="text/css">
                <!--
                .image {border: 1px solid #ccc;height: 52em;overflow: auto;width: 16em;}
                .collapsed { display: none; }
                .expanded { font-family: verdana; font-size: 11px; text-decoration: none;  color : #000000;}
                table.likeresults td { border-width: 1px; padding: 4px; border-style: dotted; border-bottom-color:#D80000; border-left:none; border-right:none; border-top:none; }
                .bodytext_small { font-family: arial; font-size: 10px; color: #505050; text-decoration: none; line-height: 18px; }
                -->
            </style>
    </head>
    <body onload="hideSWFDiv();">
        <?php
        include_once("includes/analyticstracking.php");
        if ($productRs['productID'] != '') {

            /* For all digital panelist id */

            $alldigpan_arr = array();
            $sql_checkdig = "SELECT GROUP_CONCAT(panelist_id) FROM cscan_product_detail where mChannelID IN (5,7,9,10) and panelist_id!='' and panelist_id is not null ;";
            $result_check = $DRW->query($sql_checkdig, $DRW_read);
            $row_panid = $DRW->fetch_row($result_check);
            if (!empty($row_panid[0])) {
                $alldigpan = $row_panid[0];
                $alldigpan_arr = explode(',', $alldigpan);
            }

            /* End for all digital panelist id */



            $resultC = $DRW->query("SELECT companyName FROM cscan_company pa,cscan_company_product pp
		WHERE pa.companyID=pp.companyID AND pp.productID=$productID AND primary_co=1", $DRW_read);
            $dataC = $DRW->fetch_row($resultC);
            $company = $dataC[0];

            $sector = sectorName($productRs['sectorID']);
            $sectorArray = explode(',', $productRs['sectorID']);
            $category = categoryName($productRs['categoryID']);
            if ($category == '') {
                $category = 'N/A';
            }
            $subcategory = subCategoryName($productRs['subCategoryID']);
            if ($subcategory == '') {
                $subcategory = 'N/A';
            }
            $mediaChannel = mediaChannelName($productRs['mChannelID']);
            if ($mediaChannel == '') {
                $mediaChannel = 'N/A';
            }
            $mediaPanel = mediaPanelName($productRs['mPanelID']);
            if ($mediaPanel == '') {
                $mediaPanel = 'N/A';
            }
            $stateName = stateName($productRs['state']);
            $panelist_edc = '';
            $worksite = $productRs['worksiteVoluntary'];
            if ($worksite == 0) {
                $worksite = 'No';
            } else {
                $worksite = 'Yes';
            }
            $affinityAssociation = $productRs['affinityAssociation'];
            if ($affinityAssociation == 0) {
                $affinityAssociation = 'No';
            } else {
                $affinityAssociation = 'Yes';
            }
            $mailtype = mediaType($productRs['mTypeID']);
            if ($mailtype == '') {
                $mailtype = 'N/A';
            }
            if ($productRs['mChannelID'] == 5 || $productRs['mChannelID'] == 7) {
                $document_id = 2;
            } else {
                $document_id = 1;
            }
            $thepdf = 'productDocuments_latest.php?id=' . $productID . '&amp;did=1';
            $combined = 'productDetail-latest.php?id=' . $productID . '&amp;did=1&makepdf=1';
            if (isset($_REQUEST['pdf_key']) && $_REQUEST['pdf_key'] != '') {
                $thepdf .= '#search=' . rawurlencode('"' . $_REQUEST['pdf_key'] . '"');
            }

            $query2 = "SELECT document_size_byte,document_filename,document_path,document_content_type,document_placement FROM cscan_document WHERE productID=$productID AND document_id=$document_id";
            $query_result2 = $DRW->query($query2, $DRW_read);
            $data2 = $DRW->fetch_row($query_result2);
            $document_size_byte = (int) $data2[0];
            $document_filename = $data2[1];
            $document_path = $data2[2];
            $document_content_type = $data2[3];
            $document_placement = $data2[4];
            if (empty($document_placement)) {
                $document_placement = '200x200';
            }

            $sizeofPDFinKB = $document_size_byte / 1024;
            $sizeofPDFinMB = $sizeofPDFinKB / 1024;
            if ($sizeofPDFinMB < 1) {
                $DisplaySize = round($sizeofPDFinKB, 2) . " KB";
            } else {
                $DisplaySize = round($sizeofPDFinMB, 2) . " MB";
            }
            if (preg_match('/flash/i', $document_content_type)) {
                $is_flash = true;
            } else {
                $is_flash = false;
            }
            if (preg_match('/image/i', $document_content_type)) {
                $is_image = true;
            } else {
                $is_image = false;
            }
            ?>
            <div id="div1">
                <div style="padding-bottom:6px;"><img src="images/competiscan-logo.png" border="0" style="max-height: 50px;"/></div>
                <?php if (!$makePDF): ?>
                    <table border="0" cellspacing="0" cellpadding="4">
                        <tr>
                            <td class="bodytext"><a onclick="printAll(<?php echo $productID; ?>, '<?php echo $thepdf; ?>', 1); return false;" class="HyperLink" href="#" title="Print Details">Print Details</a></td>
                            <td class="bodytext"><?php
                                if ($productRs['mChannelID'] == 5 || $productRs['mChannelID'] == 7 || $productRs['mChannelID'] == 9 || $productRs['mChannelID'] == 10) {
                                    echo '&nbsp;';
                                } else {
                                    ?>
                                    <a onclick="printAll(<?php echo $productID; ?>, '<?php echo $thepdf; ?>', 2); return false;" href="#" class="HyperLink" title="Print PDF">Print PDF</a>
                                    <?php
                                }
                                ?>
                            </td>
                            <?php //if(ENV == 'localhost' || ENV == 'demo.competiscan.com'){ ?>
                            <td class="bodytext"><a onclick="printAll(<?php echo $productID; ?>, '<?php echo $combined; ?>', 2);
                                            return false;" class="HyperLink" href="#" title="Print All">Print All</a></td>
                            <?php  //} ?>              
                        </tr>
                    </table>
                <?php endif; ?>
                <div style="margin:0px;padding:0px;border:solid 1px #0055E3;width:99%;">
                    <table width="100%" cellpadding="4" cellspacing="0" class="likeresults" border="0">
                        <tr>
                            <td valign="top" class="text" colspan="2" style="background:#0055E3;color:#FFFFFF;font-size:0.8em;"><?php
                                if ($productRs['productHeadline'] == '') {
                                    if ($productRs['mChannelID'] == 5 || $productRs['mChannelID'] == 7) {
                                        $productRs['productHeadline'] = 'An Online Ad from ' . htmlspecialchars($company);
                                    } else {
                                        $productRs['productHeadline'] = 'See complete product details';
                                    }
                                }
                                // echo htmlspecialchars($productRs['productHeadline']);

                                echo mb_convert_encoding(html_entity_decode(str_replace("\xE2\x80\x8B", "", $productRs['productHeadline']), ENT_QUOTES, "UTF-8"), "HTML-ENTITIES", "UTF-8");
                                ?></td>
                        </tr>
                        <tr>
                            <?php
                            $queryI = "SELECT img_companyID FROM cscan_img WHERE productID=$productID AND img_id=1";
                            $query_resultI = $DRW->query($queryI, $DRW_read);
                            $dataI = $DRW->fetch_row($query_resultI);
                            $img_companyID = (float) $dataI[0];
                            if (!empty($img_companyID)) {
                                $pi = 'cid=' . $img_companyID;
                            } else {
                                $pi = 'id=' . $productID;
                            }

                            if (($productRs['mChannelID'] == 5 || $productRs['mChannelID'] == 7) && ($is_flash || $is_image))
                            {
                                ?>
                                <td valign="top" colspan="2"><img src="<?php echo $baseurl; ?>/productImg_latest.php?<?php echo $pi; ?>" style="border: solid 1px #000000;float:left;" alt="" />
                                    <?php
                                    if ($document_size_byte >= 0) {
                                        if ($is_flash) {
                                            list($fwidth, $fheight) = explode('x', $document_placement);
                                            list($fwidth, $fheight) = setWidthHeight($fwidth, $fheight, 400, 200);
                                            ?>
                                            <div style="border: solid 1px #000000;float:right;" id="flashDiv">
                                                <div id="flashContent">
                                                    <a href="http://www.adobe.com/go/getflashplayer" target="_blank"><img src="http://www.adobe.com/images/shared/download_buttons/get_flash_player.gif" alt="Get Adobe Flash player" border="0" /></a>
                                                </div>
                                            </div>
                                            <script type="text/javascript">
                                                <!--
                                            var flashvars = {};
                                                var params = {
                                                    loop: "true",
                                                    menu: "false",
                                                    quality: "high",
                                                    wmode: "transparent"
                                                };
                                                var attributes = {};
                                                swfobject.embedSWF("<?php echo 'productDocuments_latest.php?id=' . $productID . '&did=' . $document_id; ?>", "flashContent", "<?php echo $fwidth; ?>", "<?php echo $fheight; ?>", "9.0.0", "includes/expressInstall.swf", flashvars, params, attributes);
                                                //-->
                                            </script>
                                            <div id="flashBlock" style="width:<?php echo $fwidth; ?>px;height:<?php echo $fheight; ?>px;display:none;"></div>
                                            <?php
                                        } else {
                                            ?>
                                            <div style="border: solid 1px #000000;float:right;"><img src="<?php echo 'productDocuments_latest.php?id=' . $productID . '&amp;did=' . $document_id; ?>" alt="" /></div>
                                            <?php
                                        }
                                    }
                                    ?>
                                </td>
                                <?php
                            }else{
                                ?>
                                <td valign="top"><img src="<?php echo $baseurl; ?>/productImg_latest.php?<?php echo $pi; ?>" style="border: solid 1px #000000;" alt="" /></td>
                                <td class="bodytext" valign="bottom">
                                    <?php
                                    if ($document_size_byte >= 0 && !$makePDF && $productRs['mChannelID'] != 10)
                                    { 
                                            
                                            if (($productRs['mChannelID'] == '5') || ($productRs['mChannelID'] == '9')) {
                                                if ($thepdf != '') { ?>
                                                    <iframe border="0" src="<?php echo $thepdf; ?>">
                                                    </iframe>
                                                    <div style="float:left;margin-top:5px;"><a href="JavaScript:void(0);" class="bluelink" onclick="OpenNewWindowPopup(<?php echo $productID;?>); return false;">View in full screen</a></div>
                                                <?php }
                                            } else { ?>
                                                <a class="bluelink" href="<?php echo $thepdf; ?>" onclick="printAll(<?php echo $productID; ?>, '<?php echo $thepdf; ?>', 3); return false;">View PDF Content <img src="images/pdf.jpg" border="0" style="vertical-align:top;" /></a> 
                                      <?php } ?> &nbsp; <?php if (($productRs['mChannelID'] != '5') && ($productRs['mChannelID'] != '9') && ($productRs['mChannelID'] != '10')) { ?> [<?php echo $DisplaySize; ?>] <?php } ?> 
                                          <?php $mpannelid_arr=@explode(',',$productRs['mPanelID']);
                                            if($productRs['mChannelID']==3 AND (in_array(1,$mpannelid_arr) OR in_array(2,$mpannelid_arr))){ 
                                                $query3 = "select productID from cscan_product_detail where approved_date>='2020-12-01 00:00:00' AND  productID=$productID";   
                                                $query_result3 = $DRW->query($query3,$DRW_read);
                                                $numrows3=$DRW->num_rows($query_result3);                            
                                                if($numrows3>0){
                                                    $query2 = "select muid from cscan_product_email where productID=$productID";   
                                                    $query_result2 = $DRW->query($query2,$DRW_read);
                                                    $numrows2=$DRW->num_rows($query_result2);                            
                                                    if($numrows2>0){?>                                                                                    
                                                        &nbsp;<a class="bluelink" href="<?php //echo 'processed-html.php?id='.$productID; ?>" onclick="htmlWin(<?php echo $productID; ?>); return false;" title="Processed HTML">Processed HTML<img width="20" height="20" src="processed-html-2.png" border="0" style="vertical-align:top;" /></a>
                                                    <?php }
                                                }
                                            } ?> <br />
                                      <?php
                                    } else if ($productRs['mChannelID'] == 10 || $productRs['mChannelID'] == 9 || $productRs['mChannelID'] == 5) {
                                        $content = '&nbsp;';
                                        $viewfullLink='';
                                        $content_link='';
                                        $sql2 = "SELECT document_path,document_content_type,document_filename FROM cscan_document WHERE productID=$productID AND document_id=1";
                                        $rs2 = $DRW->query($sql2, $DRW_read);
                                        $row2 = $DRW->fetch_row($rs2);
                                        $document_content_type = $row2[1];
                                        $document_filename = $row2[2];
                                        $img_link = $row2[0];
                                        $video_link = $row2[0] . $document_filename;
                                        

                                        if ($document_content_type == 'html' && $row2[0] != '') {
                                            $img_link = $row2[0];
                                        } else if ($row2[0] != '') {
                                            //$img_link = $baseurl . $row2[0] . $document_filename;
                                            $img_link = $displays3URL.substr($row2[0],1).$document_filename;
                                            
                                        } else {
                                            $img_link = '';
                                        }

                                        ################### Start S3 Implementation Code #########################
                                        $s3VideoURL = $displays3URL.substr($video_link,1);

                                        if ($document_content_type == 'video/mp4' && $img_link != '') {
                                            $content = '<video width="350" height="150" controls> <source src="' . $s3VideoURL . '" type="video/mp4"> Your browser does not support the video tag.</video>';
                                        ################### End S3 Implementation Code #########################
                                        } else if ($img_link != '') {
                                            $content = '<iframe border="0" src="' . $img_link . '" ></iframe>';
                                        } else {
                                            $content = '';
                                        }
                                        if($content!=''){                                            
                                            $content_link='<div style="float:left;margin-top:5px;"><a class="bluelink" onclick="OpenNewWindowPopup('.$productID.')" href="JavaScript:void(0);">View in full screen</a></div>';
                                        }
                                        echo $content.$content_link;
                                    } else {
                                        echo '&nbsp;';
                                    }
                                    ?>
                                </td>
                                
                      <?php }
                            ?>
                        </tr>
                        <tr>
                            <td class="bodytext" valign="top" width="50%"><strong>Entry ID:</strong> <?php
                                if ($_SESSION['sess_plevel'] > 0) {
                                    if ($productRs['is_digital'] == '1') {
                                        if ($productRs['mChannelID'] == '5') {
                                            $add = '&add=1';
                                        } else if ($productRs['mChannelID'] == '9') {
                                            $add = '&add=2';
                                        } else if ($productRs['mChannelID'] == '10') {
                                            $add = '&add=3';
                                        }
                                        echo "<a href=\"admin/addproduct-digital.php?id=$productID$add\" target=\"_blank\" class=\"bluelink\">";
                                    }elseif ($productRs['mChannelID']==3) {
                                        $adminProductUrl="https://cs.competiscan.com/addproduct/".$productID."?ec_content_type=email";  
                                        echo '<a href="'.$adminProductUrl.'" target="_blank" class="bluelink">';
                                    } else {
                                        
                                        echo "<a href=\"admin/addproduct.php?id=$productID\" target=\"_blank\" class=\"bluelink\">";
                                    }
                                }
                                echo $productRs['entryID'];
                                if ($_SESSION['sess_plevel'] > 0) {
                                    echo '</a>';
                                } ?>
                            </td>
                            <td class="bodytext" valign="top"><?php
                                $mail_volume_tot = 0;

                                //if (($productRs['mPanelID'] == 1 || $productRs['mPanelID'] == 2) && ($productRs['mChannelID'] == 1 || $productRs['mChannelID'] == 3)) {//consumer ||  Emp/Biz direct mail
                                //changes for panelist id show for digital

                                if (($productRs['mPanelID'] == 1 || $productRs['mPanelID'] == 2 || $productRs['mPanelID'] == 4 || $productRs['mPanelID'] == 6) && ($productRs['mChannelID'] != 6 )) {//consumer ||  Emp/Biz direct mail
                                    $ppdatetext = '';
                                    $dmajoin = '';
                                    $edcjoin = '';
                                    if ($ssid > 0) {
                                        $awhere = '';
                                        $state = '';
                                        $gender = '';
                                        $age = '';
                                        $income_mult = '';
                                        $DMA_ID_mult = '';
                                        $savedQ = "SELECT addedToDatabase,month1,month2,search_panelist_date,state,gender,age,income_mult,DMA_ID_mult,mPanelID,edc_id_mult FROM cscan_search WHERE ID='" . $ssid . "'";
                                        $rs = $DRW->query($savedQ, $DRW_read);
                                        $data = $DRW->fetch_row($rs);
                                        $addedToDatabase = $data[0];
                                        $month1 = $data[1];
                                        $month2 = $data[2];
                                        $search_panelist_date = $data[3];
                                        $state = $data[4];
                                        $gender = $data[5];
                                        $age = $data[6];
                                        $income_mult = $data[7];
                                        $DMA_ID_mult = $data[8];
                                        $mPanelID = $data[9];
                                        $edc_id_mult = $data[10];
                                        $mPanelIDArray = explode(',', $mPanelID);

                                        //if ((count($mPanelIDArray) == 1 && (in_array(1, $mPanelIDArray) || in_array(2, $mPanelIDArray))) || (count($mPanelIDArray) == 2 && in_array(1, $mPanelIDArray) && in_array(2, $mPanelIDArray))) {
                                        if ((count($mPanelIDArray) == 1 && (in_array(1, $mPanelIDArray) || in_array(2, $mPanelIDArray) || in_array(4, $mPanelIDArray) || in_array(6, $mPanelIDArray))) || (count($mPanelIDArray) == 2 && in_array(1, $mPanelIDArray) && in_array(2, $mPanelIDArray) && in_array(4, $mPanelIDArray) && in_array(6, $mPanelIDArray))) {
                                            $consumer_only = true;
                                        } else {
                                            $consumer_only = false;
                                        }

                                        if ($month1 != '' || $month2 != '') {
                                            $month = "$month1,$month2";
                                        } else {
                                            $month = '';
                                        }
                                        if ($search_panelist_date || $consumer_only) {
                                            if ($addedToDatabase != '') {
                                                if ($addedToDatabase == 'week')
                                                    $ppdatetext .= ' pp.ppdate>=CONCAT(DATE_SUB(CURDATE(),INTERVAL 7 DAY),\' 00:00:00\') AND ';
                                                elseif ($addedToDatabase == '2week')
                                                    $ppdatetext .= ' pp.ppdate>=CONCAT(DATE_SUB(CURDATE(),INTERVAL 14 DAY),\' 00:00:00\') AND ';
                                                elseif ($addedToDatabase == '1month')
                                                    $ppdatetext .= ' pp.ppdate>=CONCAT(DATE_SUB(CURDATE(),INTERVAL 1 MONTH),\' 00:00:00\') AND ';
                                                elseif ($addedToDatabase == '3month')
                                                    $ppdatetext .= ' pp.ppdate>=CONCAT(DATE_SUB(CURDATE(),INTERVAL 3 MONTH),\' 00:00:00\') AND ';
                                                elseif ($addedToDatabase == '6month')
                                                    $ppdatetext .= ' pp.ppdate>=CONCAT(DATE_SUB(CURDATE(),INTERVAL 6 MONTH),\' 00:00:00\') AND ';
                                                elseif ($addedToDatabase == '1year')
                                                    $ppdatetext .= ' pp.ppdate>=CONCAT(DATE_SUB(CURDATE(),INTERVAL 1 YEAR),\' 00:00:00\') AND ';
                                            }elseif ($month != '') {
                                                $monthArray = explode(',', $month);
                                                $month_1 = $monthArray[0];
                                                $month_2 = $monthArray[1];
                                                if ($month_1 == '') {
                                                    $month_1 = $month_2;
                                                } elseif ($month_2 == '') {
                                                    $month_2 = $month_1;
                                                }
                                                $ppdatetext .= " (pp.ppdate BETWEEN '$month_1-01 00:00:00' AND '$month_2-31 23:59:59') AND ";
                                            }
                                            if (!empty($state)) {
                                                $tmpArray = explode(',', $state);
                                                $ppdatetext .= " (";
                                                foreach ($tmpArray as $v) {
                                                    if ($v != '') {
                                                        $ppdatetext .= " pp.ppstateID=" . (int) $v . " OR ";
                                                    }
                                                }
                                                $ppdatetext = substr($ppdatetext, 0, -4);
                                                $ppdatetext .= ") AND ";
                                            }

                                            if (!empty($gender)) {
                                                $ppdatetext .= " pp.pgender='$gender' AND ";
                                            }

                                            $mult = array('ppageID' => $age, 'pincomeID' => $income_mult, 'dmap.code' => $DMA_ID_mult, 'edc_id' => $edc_id_mult);
                                            foreach ($mult as $field => $val) {
                                                if ($val != '') {
                                                    $tmpwhere = '';
                                                    $tmpArray = explode(',', $val);
                                                    foreach ($tmpArray as $v) {
                                                        if ($v != '') {
                                                            if ($field == 'dmap.code') {
                                                                $tmpwhere .= " $field='" . $v . "' OR ";
                                                            } else {
                                                                $tmpwhere .= " $field=" . (int) $v . " OR ";
                                                            }
                                                        }
                                                    }

                                                    if ($field == 'isBiz') {
                                                        $awhere .= $tmpwhere;
                                                    } else {
                                                        if ($field == 'dmap.code') {
                                                            $dmajoin = ' JOIN cscan_dma_code_postalcode dmap ON (pp.pppostalcode=dmap.pppostalcode)';
                                                            //$dmajoin = " JOIN cscan_panelists_appends ON (cscan_panelists_appends.panelist_id=pp.panelist_id) ";
                                                            $ppdatetext .= " (" . substr($tmpwhere, 0, -4) . ") AND ";
                                                        } elseif ($field == 'edc_id') {
                                                            //$edcjoin = ' JOIN cscan_edc_postalcode edc ON(pp.pppostalcode=edc.pppostalcode)';
                                                            //$edcjoin = ' JOIN ( SELECT DISTINCT panelist_id as distinct_panelist_id FROM cscan_panelists_product JOIN cscan_edc_postalcode ON(cscan_panelists_product.pppostalcode=cscan_edc_postalcode.pppostalcode) WHERE ('.substr($tmpwhere,0,-4).') ) AS t1 ON(pp.panelist_id=distinct_panelist_id)';
                                                            $temptable = "CREATE TEMPORARY TABLE `EDCTempTable` (
                                                                    panelist_id int(10) unsigned NOT NULL DEFAULT '0',
                                                                    PRIMARY KEY (panelist_id)
                                                            )";
                                                            $DRW->query($temptable, $DRW_main);
                                                            $edcq = 'SELECT DISTINCT panelist_id as distinct_panelist_id FROM cscan_panelists_product JOIN cscan_edc_postalcode ON(cscan_panelists_product.pppostalcode=cscan_edc_postalcode.pppostalcode) WHERE (' . substr($tmpwhere, 0, -4) . ')';
                                                            $edcrows = $DRW->query($edcq, $DRW_read);
                                                            while ($edcrs = $DRW->fetch_row($edcrows)) {
                                                                $DRW->query("INSERT INTO EDCTempTable (panelist_id) VALUES ('" . $DRW->real_escape_string($edcrs[0]) . "')", $DRW_main);
                                                            }
                                                            $edcjoin = ' JOIN EDCTempTable ON (pp.panelist_id=EDCTempTable.panelist_id)';
                                                        }
                                                    }
                                                }
                                            }
                                            if ($awhere != '') {
                                                $ppdatetext .= " (" . substr($awhere, 0, -4) . ") AND ";
                                            }
                                        }
                                    }

                                    if ($productRs['mChannelID'] == 1 && $productRs['delmethid'] != 4 && $productRs['delmethid'] != 5 && $productRs['mTypeID'] != 3) {//also in mail_volume_inc.php (mTypeID: Statement only here)
                                        $yearMonthArray = array();
                                        $sql_MV = "SELECT SUM(ppmv),LEFT(ppdate,7) as ym,SUM(ppmv_w) FROM cscan_panelists_product pp$dmajoin$edcjoin WHERE {$ppdatetext}productID=$productID group by ym";
                                        //$sql_MV = "SELECT SUM(ppmv),LEFT(ppdate,7) as ym,SUM(ppmv_w) FROM cscan_panelists_product WHERE productID=$productID group by ym";
                                        $resultMV = $DRW->query($sql_MV, $DRW_read);
                                        while ($rowMV = $DRW->fetch_row($resultMV)) {
                                            $mvValue = $rowMV[0];
                                            $mvYearMonth = $rowMV[1];
                                            if ($_SESSION['sess_userID'] == 9480 || $_SESSION['sess_userID'] == 8270) {
                                                $mvValue = $rowMV[2];
                                            }
                                            $y = substr($mvYearMonth, 0, 4);
                                            $m = substr($mvYearMonth, 5, 2);

                                            $yearMonthArray[$y][$m] = $mvValue;
                                            $mail_volume_tot += $mvValue;
                                        }
                                    }

                                    if ($productRs['mChannelID'] == '3' && $productRs['delmethid'] != 4 && $productRs['mTypeID'] != 5 && $productRs['mTypeID'] != 3) {//also in mail_volume_inc.php (mTypeID: Statement only here)
                                        $yearMonthArray = array();
                                        $sql_MV = "SELECT SUM(ppeve),LEFT(ppdate,7) as ym FROM cscan_panelists_product pp$dmajoin$edcjoin WHERE {$ppdatetext}productID=$productID group by ym";
                                        //$sql_MV = "SELECT SUM(ppmv),LEFT(ppdate,7) as ym,SUM(ppmv_w) FROM cscan_panelists_product WHERE productID=$productID group by ym";
                                        $resultMV = $DRW->query($sql_MV, $DRW_read);
                                        while ($rowMV = $DRW->fetch_row($resultMV)) {
                                            $mvValue = $rowMV[0];
                                            $mvYearMonth = $rowMV[1];
//                                            if ($_SESSION['sess_userID'] == 9480 || $_SESSION['sess_userID'] == 8270) {
//                                                $mvValue = $rowMV[2];
//                                            }
                                            $y = substr($mvYearMonth, 0, 4);
                                            $m = substr($mvYearMonth, 5, 2);

                                            $yearMonthArray[$y][$m] = $mvValue;
                                            $mail_volume_tot += $mvValue;
                                        }
                                    }



                                    if ($mail_volume_tot > 0 && $productRs['mPanelID'] != 2) {
                                        $table = '';
                                        $ValueScoretext = '';
                                        $pproductFICOtext = '';
                                        $prow = 0;
                                        $calculationlabel = 'EMV';
                                        $calculationtext = 'Estimated Mail Volume';
                                        $calculationfield = 'ppmv';
                                        $calculationtype = 'Mail';
                                        if ($productRs['mChannelID'] == '3') {
                                            $calculationlabel = 'EVE';
                                            $calculationtext = 'Email Volume Estimates';
                                            $calculationfield = 'ppeve';
                                            $calculationtype = 'Email';
                                        }
                                        $table .= "<table id=\"thecalc_table\" border=\"0\" cellspacing=\"2\" cellpadding=\"4\" class=\"bodytext_small\" style=\"width:100%;\">
				<tr><td class=\"bodytext\" width=\"40%\"><strong>Historical " . $calculationlabel . "</strong></td><td style=\"border:none;\">&nbsp;</td><td class=\"bodytext\"><strong>" . $calculationtype . " Piece Observance</strong></td></tr>";

                                        $table .= "<tr><td style=\"border:none;\"><table border=\"0\" cellspacing=\"1\" cellpadding=\"3\" class=\"bodytext_small\" width=\"100%\">";
                                        $table .= '<tr><td><strong>' . $calculationtext . '</strong></td><td>' . number_format($mail_volume_tot) . '</td></tr>';
                                        foreach ($yearMonthArray as $y => $mv) {
                                            $table .= '<tr><td><strong>' . $y . ' ' . $calculationlabel . '</strong></td><td>';
                                            $tmp = 0;
                                            foreach ($mv as $m => $v) {
                                                $tmp += $v;
                                            }
                                            $table .= number_format($tmp) . '</td></tr>';
                                            $prow++;
                                        }
                                        $table .= "</table></td><td style=\"border:none;\">&nbsp;</td><td style=\"border:none;\"><table border=\"0\" cellspacing=\"1\" cellpadding=\"3\" class=\"bodytext_small\"><tr>
				<td>&nbsp;</td><td><strong>JAN</strong></td><td><strong>FEB</strong></td><td><strong>MAR</strong></td><td><strong>APR</strong></td><td><strong>MAY</strong></td><td><strong>JUN</strong></td><td><strong>JUL</strong></td><td><strong>AUG</strong></td><td><strong>SEP</strong></td><td><strong>OCT</strong></td><td><strong>NOV</strong></td><td><strong>DEC</strong></td></tr>";
                                        foreach ($yearMonthArray as $y => $mv) {
                                            $table .= "<tr><td><strong>$y</strong></td>";
                                            for ($i = 1; $i <= 12; $i++) {
                                                $table .= "<td align=\"center\"";
                                                if (strlen($i) < 2)
                                                    $i = '0' . $i;
                                                if (isset($mv[$i]) && $mv[$i] > 0)
                                                    $table .= ' style="background-color:#0055E3;color:#FFFFFF;font-weight:bold;">X'; //number_format($mv[$i]);
                                                else
                                                    $table .= '>&nbsp;';
                                                $table .= "</td>";
                                            }
                                            $table .= "</tr>";
                                        }
                                        $table .= "</table></td></tr>
				<tr><td colspan=\"3\" style=\"border:none;\">&nbsp;</td></tr>
				<tr><td colspan=\"3\" class=\"bodytext\"><strong>" . $calculationtext . " Transparency</strong></td></tr>
				<tr><td colspan=\"3\" style=\"border:none;\"><table border=\"0\" cellspacing=\"1\" cellpadding=\"3\" class=\"bodytext_small\" width=\"100%\">
				<tr><td valign=\"bottom\" style=\"border:none;\" align=\"center\"><strong>Panelist</strong></td>
                                <td valign=\"bottom\" align=\"center\"><strong>Age</strong></td>
                                <td valign=\"bottom\" align=\"center\"><strong>Income</strong></td>
                                <td valign=\"bottom\" align=\"center\"><strong>State / Province</strong></td>";
                                ####### Start For FICO, CreditVision and Vantage Score ######### */
                                //if(ENV == 'localhost' || ENV == 'demo.competiscan.com' || $_SESSION['sess_userID']=='45037' || $_SESSION['sess_userID']=='31464'){ 
                                    if(isset($_SESSION['sess_search_additional_field']) AND in_array('fico',$_SESSION['sess_search_additional_field'])){ 
                                        $table .= "<td valign=\"bottom\" align=\"center\"><strong>FicoScore</strong></td>";
                                    }
                                    if(isset($_SESSION['sess_search_additional_field']) AND in_array('credit_vision',$_SESSION['sess_search_additional_field'])){ 
                                        $table .="<td valign=\"bottom\" align=\"center\"><strong>CreditVision</strong></td>";
                                    }
                                    if(isset($_SESSION['sess_search_additional_field']) AND in_array('vantage_score',$_SESSION['sess_search_additional_field'])){ 
                                        $table .="<td valign=\"bottom\" align=\"center\"><strong>VantageScore</strong></td>";
                                    }
                                //}
                                ####### End For FICO, CreditVision and Vantage Score ######### */
                                if(!in_array('ValueScore',$_SESSION['sess_search_exclude'])){ 
                                    $table .= "<td valign=\"bottom\" align=\"center\"><strong>ValueScore&reg;</strong></td>";
                                }
                                $table .= "<td valign=\"bottom\" align=\"center\"><strong>ISO Risk Quality Index-Auto</strong></td>
                                <td valign=\"bottom\" align=\"center\"><strong>ISO Risk Quality Index-Home</strong></td>
                                <td valign=\"bottom\" align=\"center\"><strong>" . $calculationlabel . "</strong></td></tr>"; // <td valign=\"bottom\" align=\"center\"><strong>Gender</strong></td>
                                       ####### Start For FICO, CreditVision and Vantage Score ######### */
                                        /*$sql_P = "SELECT SUM(" . $calculationfield . "),COUNT(*) as pieces,ppage,incomeName,stateName,pgender,SUM(ppmv_w),competi_id,pp.panelist_id,pproductFICO,LEFT(pp.ppdate,7) as ym
					FROM cscan_panelists_product pp JOIN cscan_incometype ci ON (pp.pincomeID=ci.incomeID) JOIN cscan_state cs ON (pp.ppstateID=cs.stateID) JOIN cscan_panelists pa ON (pa.panelist_id=pp.panelist_id)$dmajoin$edcjoin
					WHERE {$ppdatetext}productID=$productID
					GROUP BY pp.panelist_id ORDER BY ppage,incomeName,stateName";*/
                                       ####### End For FICO, CreditVision and Vantage Score ######### */
                                        /*$sql_P = "SELECT SUM(" . $calculationfield . "),COUNT(*) as pieces,ppage,incomeName,stateName,pgender,SUM(ppmv_w),competi_id,pp.panelist_id,pproductFICO
					FROM cscan_panelists_product pp JOIN cscan_incometype ci ON (pp.pincomeID=ci.incomeID) JOIN cscan_state cs ON (pp.ppstateID=cs.stateID) JOIN cscan_panelists pa ON (pa.panelist_id=pp.panelist_id)$dmajoin$edcjoin
					WHERE {$ppdatetext}productID=$productID
					GROUP BY pp.panelist_id ORDER BY ppage,incomeName,stateName"; // AND LEFT(ppdate,7)<'".date('Y-m')."'   GROUP BY ppage,pp.ir_ID,pp.ppstateID
                                        */
                                        //Get Panelist Demographics
                                        $sql_P = "SELECT SUM(" . $calculationfield . "),COUNT(*) as pieces,ppage,incomeName,stateName,pgender,SUM(ppmv_w),competi_id,pp.panelist_id,pproductFICO,LEFT(pp.ppdate,7) as ym,DATEDIFF(CURDATE(),pa.birthdate) as agedays
					FROM cscan_panelists_product pp JOIN cscan_panelists pa ON (pa.panelist_id=pp.panelist_id) JOIN cscan_incometype ci ON (pa.incomeID=ci.incomeID) JOIN cscan_state cs ON (pa.stateID=cs.stateID)$dmajoin$edcjoin
					WHERE {$ppdatetext}productID=$productID
					GROUP BY pp.panelist_id ORDER BY ppage,incomeName,stateName";
                                       $result_P = $DRW->query($sql_P, $DRW_read);
                                        while ($row = $DRW->fetch_row($result_P)) {
                                            $ppmv = $row[0];
                                            $pieces = $row[1];
                                            $age = $row[2];
                                            $ir_name = $row[3];
                                            $stateName2 = $row[4];
                                            $pgender = $row[5];
                                            //Start Get Panelist Demographics
                                            if($age<=0){
                                            $age=floor($row[11] / 365);
                                            /*$ageObj = new \HS\Age($DRW);
                                            $ageObj->setAge($ppage);
                                            $ppageID = $ageObj->getGroupsAsCommaDelimitedString($ppage);*/
                                            } 
                                            //End Get Panelist Demographics
                                            if ($_SESSION['sess_userID'] == 9480 || $_SESSION['sess_userID'] == 8270) {
                                                $ppmv = $row[6];
                                            }
                                            $competi_id = $row[7];
                                            $pid = $row[8];
                                            $pproductFICO = $row[9];
                                            ####### Start For FICO, CreditVision and Vantage Score ######### */
                                            $ppdate_ym = $row[10];
                                            ####### End For FICO, CreditVision and Vantage Score ######### */
                                            /* if(empty($pproductFICO)){
                                              $pproductFICO = '&nbsp;';
                                              }
                                              else{
                                              $query_pt ="SELECT DATE_FORMAT(MAX(ppdate),'%m/%d/%Y') FROM cscan_panelists_product WHERE panelist_id=$pid AND pproductFICO<>''";
                                              $result_pt = $DRW->query($query_pt,$DRW_read);
                                              $row_pt = $DRW->fetch_row($result_pt);
                                              $pproductFICOtext .= '<div id="pproductFICO_inner'.$pid.'" style="display:none;padding:4px;">Most Recent FICO: '.$row_pt[0].'</div>';
                                              $pproductFICO = '<a href="#" class="bluelink" onclick="showpproductFICO('.$pid.'); return false;" style="font-size: 10px;" id="pproductFICOlink'.$pid.'">'.$pproductFICO.'</a>';
                                              } */
                                            $query_pt = "SELECT ValueScore_for_Household,RAPA_EMLC_ZIP_REL,RAHO_HOMLC_ZIP_REL FROM cscan_panelists_appends WHERE panelist_id=$pid";
                                            $result_pt = $DRW->query($query_pt, $DRW_read);
                                            $row_pt = $DRW->fetch_row($result_pt);
                                            if (!empty($row_pt[0])) {
                                                $ficotext = $row_pt[0];
                                                $query_pt2 = "SELECT description,VSfH_average FROM cscan_valuescore_for_household WHERE code='$row_pt[0]'";
                                                $result_pt2 = $DRW->query($query_pt2, $DRW_read);
                                                $row_pt2 = $DRW->fetch_row($result_pt2);
                                                $ValueScoretext .= '<div id="ValueScore_inner' . $pid . '" style="display:none;padding:4px;">' . $row_pt[0] . ' = ' . $row_pt2[0] . ' (' . $row_pt2[1] . ')</div>';
                                                $ficotext = '<a href="#" class="bluelink" onclick="showValueScore(' . $pid . '); return false;" style="font-size: 10px;" id="ValueScorelink' . $pid . '">' . $row_pt[0] . '</a>';
                                            } else {
                                                $ficotext = '&nbsp;';
                                            }
                                            if (!empty($row_pt[1])) {
                                                $RAPA_EMLC_ZIP_REL_txt = '<a href="#" class="bluelink" onclick="showISO(' . $pid . '); return false;" style="font-size: 10px;" id="ISOlink1' . $pid . '">' . $row_pt[1] . '</a>';
                                            } else {
                                                $RAPA_EMLC_ZIP_REL_txt = '&nbsp;';
                                            }
                                            if (!empty($row_pt[2])) {
                                                $RAHO_HOMLC_ZIP_REL_txt = '<a href="#" class="bluelink" onclick="showISO(' . $pid . '); return false;" style="font-size: 10px;" id="ISOlink2' . $pid . '">' . $row_pt[2] . '</a>';
                                            } else {
                                                $RAHO_HOMLC_ZIP_REL_txt = '&nbsp;';
                                            }

                                            if ($pgender == '')
                                                $pgender = '&nbsp;';
                                            if ($ppmv > 0) {
                                                $table .= "<tr><td style=\"border:none;\" nowrap=\"nowrap\">";
                                                /* ######## For panelist show ########## */

                                                $result_C = $DRW->query("SELECT DISTINCT affinityName FROM cscan_affinity pa,cscan_panelist_affinity pp
    WHERE pp.panelist_id=$pid AND pa.affinityID=pp.affinityID", $DRW_read);
                                                $dataC = $DRW->fetch_row($result_C);

                                                $result_C2 = $DRW->query("SELECT DISTINCT companyName FROM cscan_company pa,cscan_panelist_company pp
    WHERE pp.panelist_id=$pid AND pa.companyID=pp.companyID", $DRW_read);
                                                $dataC2 = $DRW->fetch_row($result_C2);
                                                $result_C3 = $DRW->query("SELECT pm_date,stateID1,stateID2,postalcode1,postalcode2 FROM cscan_panelists_mover WHERE panelist_id=$pid", $DRW_read);
                                                $dataC3 = $DRW->fetch_row($result_C3);

                                                $isdigital_pan = '';
                                                //$isdigital_pan= CheckPanelistDigital($productID);
                                                $isdigital_pan = $productRs['panelist_sort'];

                                                // $sql_checkdig = "select productID from cscan_product_detail where mChannelID IN (5,7,9,10) and FIND_IN_SET($pid,panelist_id)";
                                                //  $result_check = $DRW->query($sql_checkdig, $DRW_read);
                                                if (in_array($pid, $alldigpan_arr)) {
                                                    $bold = '<strong>';
                                                    $bold2 = '</strong>';
                                                } else {
                                                    $bold = '';
                                                    $bold2 = '';
                                                }



                                                /*
                                                  if($isdigital_pan=='1'){
                                                  $bold='<strong>';
                                                  $bold2='</strong>';
                                                  }else{
                                                  $bold='';
                                                  $bold2='';

                                                  } */
                                                if (count($dataC) > 0 || count($dataC2) > 0 || count($dataC3) > 0) {
                                                    //$table .= "<a href=\"#\" onclick=\"showCalcCos_digital($pid); return false;\" class=\"bluelink\" style=\"font-size: 10px;\" id=\"thecalccoslink$pid\">$bold $competi_id $bold2</a>";

                                                    if ($bold != '') {
                                                        $table .= "<a href=\"#\" onclick=\"showCalcCos_digital($pid); return false;\" class=\"bluelink\" style=\"font-size: 10px; color:black;\" id=\"thecalccoslink$pid\">$bold $competi_id $bold2</a>";
                                                    } else {
                                                        $table .= "<a href=\"#\" onclick=\"showCalcCos_digital($pid); return false;\" class=\"bluelink\" style=\"font-size: 10px;\" id=\"thecalccoslink$pid\">$bold $competi_id $bold2</a>";
                                                    }
                                                } else {
                                                    if ($bold != '') {
                                                        $table .= "<span class=\"bluelink\" style=\"font-size: 10px; color:black;\" id=\"thecalccoslink$pid\">$bold $competi_id $bold2</span> ";
                                                    } else {
                                                        $table .= "<span class=\"bluelink\" style=\"font-size: 10px; color:#00a4e4;\" id=\"thecalccoslink$pid\">$bold $competi_id $bold2</span> ";
                                                    }
                                                }

                                                $table .= "</td><td align=\"center\" valign=\"top\">$age</td><td align=\"center\" valign=\"top\">$ir_name</td><td align=\"center\" valign=\"top\">$stateName2</td>";
						####### Start For FICO, CreditVision and Vantage Score ######### */
                                                //if(ENV == 'localhost' || ENV == 'demo.competiscan.com' || $_SESSION['sess_userID']=='45037' || $_SESSION['sess_userID']=='31464'){  
                                                    $sql_additional = $DRW->query("SELECT fico_score,vantage_score,credit_vision FROM cscan_panelists_additional_score where LEFT(score_date,7)='".$ppdate_ym."' and panelist_id='".$pid."'", $DRW_read);
                                                    $additional_result = $DRW->fetch_row($sql_additional);
                                                    $fico_score1 = $additional_result[0];
                                                    $vantage_score1 = $additional_result[1];
                                                    $credit_vision1 = $additional_result[2];
                                                    
                                                    if(isset($_SESSION['sess_search_additional_field']) AND in_array('fico',$_SESSION['sess_search_additional_field'])){ 
                                                     $table .= "<td align=\"center\" valign=\"top\">$fico_score1</td>";
                                                    }
                                                    if(isset($_SESSION['sess_search_additional_field']) AND in_array('credit_vision',$_SESSION['sess_search_additional_field'])){ 
                                                     $table .= "<td align=\"center\" valign=\"top\">$credit_vision1</td>";
                                                    }
                                                    if(isset($_SESSION['sess_search_additional_field']) AND in_array('vantage_score',$_SESSION['sess_search_additional_field'])){ 
                                                     $table .= "<td align=\"center\" valign=\"top\">$vantage_score1</td>";
                                                    }
                                                //}
                                                ####### End For FICO, CreditVision and Vantage Score ######### */
                                                if(!in_array('ValueScore',$_SESSION['sess_search_exclude'])){ 
                                                    $table .= "<td align=\"center\" valign=\"top\">$ficotext</td>";
                                                }
                                                $table .= "<td align=\"center\" valign=\"top\">$RAPA_EMLC_ZIP_REL_txt</td>
						<td align=\"center\" valign=\"top\">$RAHO_HOMLC_ZIP_REL_txt</td>
						<td align=\"center\" valign=\"top\">" . number_format($ppmv) . "</td></tr>"; // <td align=\"center\" valign=\"top\">$pgender</td>
                                                $prow++;
                                            }
                                        }
                                        $table .= "</table></td></tr></table>";
                                        echo '<strong>' . $calculationtext . ':</strong> <a href="#" onclick="showCalc(' . $prow . '); return false;" class="bluelink" id="thecalclink">' . number_format($mail_volume_tot) . '</a>'; //$productRs['mail_volume']
                                        echo '<div style="display:none;position:absolute;background:#ffffff;padding:0px;border:solid 2px #000000;z-index:100;width:635px;" id="thecalc"><div style="float:right;height:26px;"><a href="#" onclick="hideCalc(); return false;" class="bluelink">close</a> &nbsp; </div><div style="clear:both;height:1px"><img src="images/spacer.gif" width="1" height="1" /></div><div id="thecalc_inner" style="padding:4px;">' . $table . '</div></div>';
                                        echo '<div style="display:none;position:absolute;background:#E8E8FF;padding:0px;border:solid 1px #000000;z-index:102;width:420px;" id="ValueScore"><div style="float:right;height:26px;"><a href="#" onclick="hideValueScore(); return false;" class="bluelink">close</a> &nbsp; </div><div style="clear:both;height:1px"><img src="images/spacer.gif" width="1" height="1" /></div>' . $ValueScoretext . '</div>';
                                        //echo '<div style="display:none;position:absolute;background:#E8E8FF;padding:0px;border:solid 1px #000000;z-index:102;width:420px;" id="pproductFICO"><div style="float:right;height:26px;"><a href="#" onclick="hidepproductFICO(); return false;" class="bluelink">close</a> &nbsp; </div><div style="clear:both;height:1px"><img src="images/spacer.gif" width="1" height="1" /></div>'.$pproductFICOtext.'</div>';
                                        echo '<div style="display:none;position:absolute;background:#E8E8FF;padding:0px;border:solid 1px #000000;z-index:103;width:500px;" id="ISO"><div style="float:right;height:26px;"><a href="#" onclick="hideISO(); return false;" class="bluelink">close</a> &nbsp; </div><div style="clear:both;height:1px"><img src="images/spacer.gif" width="1" height="1" /></div><div id="ISO_inner" style="padding:4px;">ISO&copy; defines risk as the expected amount of loss for either a Personal Auto or Homeowners claim.  Within a geography (State/Province, County, or ZIP), the likelihood of Personal Auto or Homeowner claims is not uniform.  Some areas will have higher/lower tendencies of insurance related losses.  ISO&copy;  ERI (environmental relativity index) helps insurers identify the low and high relative areas or loss potential.</div></div>';
                                    } else {
                                        echo '<span id="thecalc">&nbsp;</span>';
                                        // Added for showing panelists id for Insurance producer/Financial advisor and mortgage broker audience.
                                        if($productRs['mPanelID'] == 4 || $productRs['mPanelID'] == 6){
                                            $sql_P = "SELECT DISTINCT competi_id,pp.panelist_id FROM cscan_panelists_product pp JOIN cscan_panelists pa ON (pa.panelist_id=pp.panelist_id)$dmajoin$edcjoin
                                                      WHERE {$ppdatetext}productID=$productID ORDER BY competi_id";
                                        }else{
                                            $sql_P = "SELECT DISTINCT competi_id,pp.panelist_id FROM cscan_panelists_product pp JOIN cscan_panelists pa ON (pa.panelist_id=pp.panelist_id)$dmajoin$edcjoin
                            WHERE {$ppdatetext}productID=$productID AND contactTypeID=2 ORDER BY competi_id";
                                        }
                                        $result_P = $DRW->query($sql_P, $DRW_read);
                                        while ($row = $DRW->fetch_row($result_P)) {
                                            $competi_id = $row[0];
                                            $pid = $row[1];

                                            $result_C = $DRW->query("SELECT DISTINCT affinityName FROM cscan_affinity pa,cscan_panelist_affinity pp
    WHERE pp.panelist_id=$pid AND pa.affinityID=pp.affinityID", $DRW_read);
                                            $dataC = $DRW->fetch_row($result_C);

                                            $result_C2 = $DRW->query("SELECT DISTINCT companyName FROM cscan_company pa,cscan_panelist_company pp
    WHERE pp.panelist_id=$pid AND pa.companyID=pp.companyID", $DRW_read);
                                            $dataC2 = $DRW->fetch_row($result_C2);
                                            $result_C3 = $DRW->query("SELECT pm_date,stateID1,stateID2,postalcode1,postalcode2 FROM cscan_panelists_mover WHERE panelist_id=$pid", $DRW_read);
                                            $dataC3 = $DRW->fetch_row($result_C3);

                                            $isdigital_pan = '';
                                            //$isdigital_pan= CheckPanelistDigital($productID);
                                            $isdigital_pan = $productRs['panelist_sort'];

                                            //  $sql_checkdig2 = "select productID from cscan_product_detail where mChannelID IN (5,7,9,10) and FIND_IN_SET($pid,panelist_id)";
                                            // $result_check2 = $DRW->query($sql_checkdig2, $DRW_read);

                                            if (in_array($pid, $alldigpan_arr)) {
                                                $bold = '<strong>';
                                                $bold2 = '</strong>';
                                            } else {
                                                $bold = '';
                                                $bold2 = '';
                                            }
                                            /*
                                              if($isdigital_pan=='1'){
                                              $bold='<strong>';
                                              $bold2='</strong>';
                                              }else{
                                              $bold='';
                                              $bold2='';

                                              } */
                                            if (count($dataC) > 0 || count($dataC2) > 0 || count($dataC3) > 0) {
                                                ////Panelist Profile?
                                                // echo "<a href=\"#\" onclick=\"showCalcCos_digital($pid); return false;\" class=\"bluelink\" style=\"font-size: 10px;\" id=\"thecalccoslink$pid\">$bold$competi_id$bold2</a> ";
                                                if ($bold != '') {
                                                    echo "<a href=\"#\" onclick=\"showCalcCos_digital($pid); return false;\" class=\"bluelink\" style=\"font-size: 10px; color:black;\" id=\"thecalccoslink$pid\">$bold$competi_id$bold2</a> ";
                                                } else {
                                                    echo "<a href=\"#\" onclick=\"showCalcCos_digital($pid); return false;\" class=\"bluelink\" style=\"font-size: 10px;\" id=\"thecalccoslink$pid\">$bold$competi_id$bold2</a> ";
                                                }
                                            } else {
                                                if ($bold != '') {
                                                    echo "<span class=\"bluelink\" style=\"font-size: 10px; color:black;\" id=\"thecalccoslink$pid\">$bold $competi_id $bold2</span> ";
                                                } else {
                                                    echo "<span class=\"bluelink\" style=\"font-size: 10px; color:#00a4e4;\" id=\"thecalccoslink$pid\">$bold $competi_id $bold2</span> ";
                                                }
                                            }
                                        }
                                    }
                                    echo '<div style="display:none;position:absolute;background:#E8E8FF;padding:0px;border:solid 1px #000000;z-index:101;width:420px;" id="thecalccos"><div style="float:right;height:26px;"><a href="#" onclick="hideCalcCos(); return false;" class="bluelink">close</a> &nbsp; </div><div style="clear:both;height:1px"><img src="images/spacer.gif" width="1" height="1" /></div><div id="thecalccos_inner" style="padding:4px;"></div></div>';

                                    if (in_array(315, $sectorArray)) {
                                        $edcids = array();
                                        $sql_P = "SELECT DISTINCT edc_id FROM cscan_panelists_product pp JOIN cscan_edc_postalcode edc ON(pp.pppostalcode=edc.pppostalcode)$dmajoin$edcjoin WHERE {$ppdatetext}productID=$productID";
                                        $resultC = $DRW->query($sql_P, $DRW_read);
                                        while ($dataC = $DRW->fetch_row($resultC)) {
                                            $edcids[] = $dataC[0];
                                        }
                                        if (count($edcids) > 0) {
                                            $ids = implode(',', $edcids);
                                            $panelist_edc = get_EDCName($ids);
                                        }
                                    }
                                } elseif ($productRs['mChannelID'] == 6 && !empty($productRs['external_link'])) {
                                    echo "<a href=\"{$productRs['external_link']}\" target=\"_blank\" class=\"bluelink\">" . htmlspecialchars($productRs['external_link']) . "</a>";
                                } else {
                                    echo '&nbsp;';
                                }
                                ?>

                            </td>


                        </tr>
    <?php
    ######### Start: Digital spend/impressions ########
    //if(!empty($productID)){
    //echo date('Y-m-d H:i:s');
    //$sql_imp = "SELECT cdc.creative_id FROM cscan_digital_creative cdc INNER JOIN cscan_digital_observation cdo ON(cdc.ad_md5 = cdo.ad_md5) WHERE cdc.productId='".$productID."' AND cdo.estimated_spend IS NOT NULL AND cdo.estimated_impressions IS NOT NULL AND cdo.panelist_id IS NOT NULL LIMIT 1";
    //$res_imp_query = $DRW->query($sql_imp, $DRW_digital);
    //if($DRW->num_rows($res_imp_query) > 0){//echo '</br>'.date('Y-m-d H:i:s');
    
    /*if (!empty($productID) && ($productRs['is_digital'] == 1) && in_array($productRs['mChannelID'], [5, 9, 10])) {
        $flag = false;
        $sql_creative_tbl = "SELECT table_name FROM cscan_digital_creative_tables";
        $res_creative_tbl = $DRW->query($sql_creative_tbl, $DRW_digital);
        $ad_md5 = '';
        if ($DRW->num_rows($res_creative_tbl) > 0) {
            while ($res_creative_row = $DRW->fetch_assoc($res_creative_tbl)) {
                $creative_table = $res_creative_row['table_name'];
                $sql_md5 = "SELECT ad_md5 FROM $creative_table WHERE productID = '" . $productID . "'";
                $query_md5 = $DRW->query($sql_md5, $DRW_digital);
                if ($DRW->num_rows($query_md5) > 0) {
                    $res_md5 = $DRW->fetch_assoc($query_md5);
                    $ad_md5 = $res_md5['ad_md5'];
                    break;
                }
            }
        }
        if (!empty($ad_md5)) {
            $arr_panelists = array();
            $imp_sql = "SELECT cpp.panelist_id FROM cscan_panelists_product cpp INNER JOIN cscan_panelists cp ON(cpp.panelist_id = cp.panelist_id) WHERE cpp.productID = '" . $productID . "'";
            $imp_query = $DRW->query($imp_sql, $DRW_read);
            if ($DRW->num_rows($imp_query) > 0) {
                while ($imp_res = $DRW->fetch_assoc($imp_query)) {//print_r($imp_res);die;
                    $panelist_id = $imp_res['panelist_id'];
                    $arr_panelists[] = $panelist_id;
                }
            }
            if (!empty($arr_panelists)) {
                $panelists = implode(",", $arr_panelists);
                //list observations tables
                $sql_obse_tbl = "SELECT table_name FROM cscan_digital_observation_tables";
                $res_obse_tbl = $DRW->query($sql_obse_tbl, $DRW_digital);
                $arr_sql = array();
                $impressions = '';
                while ($res_obse_row = $DRW->fetch_assoc($res_obse_tbl)) {
                    $obse_table = $res_obse_row['table_name'];
                    $sql_impression = "SELECT estimated_spend FROM " . $obse_table . " WHERE ad_md5 = '" . $ad_md5 . "' AND panelist_id IN ($panelists) AND estimated_spend IS NOT NULL AND estimated_impressions IS NOT NULL LIMIT 1";
                    $res_union_query = $DRW->query($sql_impression, $DRW_digital);
                    if ($DRW->num_rows($res_union_query) > 0) {
                        $flag = true;
                        break;
                    }
                }
            }
        }
        $spend_dig='';
        $impression_dig='';
        
        if(!$flag){
            $sql_sp_imp_dig = "SELECT id,spend,impression FROM cscan_digital_spend_impression WHERE productID = '" . $productID . "' AND spend>0 AND impression>0";
            $res_sp_imp_dig = $DRW->query($sql_sp_imp_dig, $DRW_read);
            if ($DRW->num_rows($res_sp_imp_dig) > 0) {
                $flag = true; 
                $data_sp_imp_dig = $DRW->fetch_assoc($res_sp_imp_dig);
                $spend_dig = $data_sp_imp_dig['spend'];
                $impression_dig = $data_sp_imp_dig['impression'];
            }
            
        } */
       
        if (!empty($productID) && in_array($productRs['mChannelID'], [5, 9, 10])) {
            $flag = false;
            $spend_dig='';
            $impression_dig='';
            $sql_sp_imp_dig = "SELECT id,spend,impression FROM cscan_digital_spend_impression WHERE productID = '" . $productID . "' AND spend>0 AND impression>0";
            $res_sp_imp_dig = $DRW->query($sql_sp_imp_dig, $DRW_read);
            if ($DRW->num_rows($res_sp_imp_dig) > 0) {
                $flag = true; 
                $data_sp_imp_dig = $DRW->fetch_assoc($res_sp_imp_dig);
                $spend_dig = $data_sp_imp_dig['spend'];
                $impression_dig = $data_sp_imp_dig['impression'];
            }
            $sql_mobile_tbl = "SELECT SUM(estimated_spend) as estimated_spend,SUM(estimated_impressions) as estimated_impressions FROM cscan_mobile_digital_spend_impressions where product_id='" . $productID . "'";
            $res_mobile_tbl = $DRW->query($sql_mobile_tbl, $DRW_read);
            if ($DRW->num_rows($res_mobile_tbl) > 0) {
                $flag = true;
            }
            
        if ($flag) {
            ?>
                                <tr>
                                    <td class="bodytext" width="50%" valign="top"><strong>Panelist Level Estimated:</strong></td>
                                    <td class="bodytext">
                                         <?php if($spend_dig!='' AND $impression_dig!=''){
                                            echo '<strong>Total Estimated Spend / Impressions:</strong>&nbsp;&nbsp;&nbsp;&dollar;'.number_format($spend_dig).' / '.number_format($impression_dig);
                                        }else{ ?>
                                        <a href="#" onclick="showIMPR(<?= $productID ?>);
                                            return false;" class="bluelink" id="IMPRlink">Spend (&dollar;) / Impressions</a>
                                        <div style="display: none; position: absolute; background: rgb(255, 255, 255) none repeat scroll 0% 0%; padding: 0px; border: 2px solid rgb(0, 0, 0); z-index: 100; width: 635px; left: 35px; top: 364px; opacity: 1;" id="IMPR">
                                            <div style="float:right;height:26px;">
                                                <a href="#" onclick="hideIMPR();
                                                    return false;" class="bluelink">close</a> &nbsp;
                                            </div>
                                            <div style="clear:both;height:1px"><img src="images/spacer.gif" width="1" height="1"></div>
                                            <div id="IMPR_inner" style="padding:4px;">
                                                <table id="IMPR_table" class="bodytext_small" style="width:100%;" cellspacing="2" cellpadding="4" border="0">

                                                </table>
                                            </div>
                                        </div>
                                        <?php } ?>
                                    </td>
                                </tr>
            <?php
        }
    }
    ######### End: Digital spend/impressions ########
    ?>
                        <?php
                        /*
                          $result_CD = $DRW->query("SELECT is_mobile FROM cscan_product_detail WHERE is_digital=1 AND productID=$productID",$DRW_read);
                          $dataCD = $DRW->fetch_row($result_CD);

                          if(!empty($dataCD)){
                          $is_mobile=$dataCD[0];
                          }else{
                          $is_mobile='0';
                          }
                         */
                        $is_mobile = $productRs['is_mobile'];

                        if ($is_mobile == 2) {
                            $showdevice = 'Mobile';
                        } else if ($is_mobile == 1) {
                            $showdevice = 'Desktop';
                        } else {
                            $showdevice = '';
                        }

                        //$result_DOM = $DRW->query("SELECT simple_domain FROM cscan_product_detail WHERE is_digital=1 AND productID=$productID",$DRW_read);
                        //$dataDOM = $DRW->fetch_row($result_DOM);


                        $simple_domain = trim($productRs['simple_domain']);
                        if ($simple_domain != '') {
                            $showdomain = $productRs['simple_domain'];
                        } else {
                            $showdomain = '';
                        }
                        /*######## To hide digital source 25-01-2019 as per by Nate########*/
                        $showdevice='';
                        /*######## End To hide digital source ########*/
                        if ($showdomain != '' || $showdevice != '') {
                            /* ####### For show digital source and simple domain ####### */
                            ?>

                            <tr>
                                <td class="bodytext" valign="top" width="50%">
                            <?php if ($showdevice != '') { ?>
                                        <strong>Digital Source:</strong>&nbsp;&nbsp;<?php echo $showdevice;
                } else {
                    echo '&nbsp;';
                } ?>
                                </td>
                                <td class="bodytext" valign="top" width="50%">
        <?php if ($showdomain != '') { ?>
                                        <strong>Simple Domain:</strong>&nbsp;&nbsp;<?php echo $showdomain;
        } else {
            echo '&nbsp';
        } ?>
                                </td>
                            </tr>
                        <?php
                        }
                        /* ####### For show digital source and simple domain ####### */


                        ######### Start: Show SEM details (Search key, Url,title and description)  ########


                        /*if (!empty($productID) && ($productRs['is_digital'] == 1) && in_array($productRs['mChannelID'], [9])) {
                            $flag_sem = false;
                            $sql_creative_tbl = "SELECT table_name FROM cscan_digital_creative_tables";
                            $res_creative_tbl = $DRW->query($sql_creative_tbl, $DRW_digital);
                            $ad_md5 = '';
                            if ($DRW->num_rows($res_creative_tbl) > 0) {
                                while ($res_creative_row = $DRW->fetch_assoc($res_creative_tbl)) {
                                    $creative_table = $res_creative_row['table_name'];
                                    $sql_md5 = "SELECT ad_md5 FROM $creative_table WHERE productID = '" . $productID . "'";
                                    $query_md5 = $DRW->query($sql_md5, $DRW_digital);
                                    if ($DRW->num_rows($query_md5) > 0) {
                                        $res_md5 = $DRW->fetch_assoc($query_md5);
                                        $ad_md5 = $res_md5['ad_md5'];
                                        break;
                                    }
                                }
                            }
                            if (!empty($ad_md5)) {

                                //list sem details tables
                                $sql_semdet_tbl = "SELECT table_name FROM cscan_semdetails_tables";
                                $res_semdet_tbl = $DRW->query($sql_semdet_tbl, $DRW_digital);

                                while ($res_semdet_row = $DRW->fetch_assoc($res_semdet_tbl)) {
                                    $semdet_table = $res_semdet_row['table_name'];
                                    $sql_semdet = "SELECT sem_headline,sem_url,sem_description,sem_search_key FROM " . $semdet_table . " WHERE ad_md5 = '" . $ad_md5 . "' AND sem_headline IS NOT NULL LIMIT 1";
                                    $res_sem_det = $DRW->query($sql_semdet, $DRW_digital);
                                    if ($DRW->num_rows($res_sem_det) > 0) {
                                        $data_sem_det = $DRW->fetch_assoc($res_sem_det);
                                        $flag_sem = true;
                                        break;
                                    }
                                }
                            }*/
                        if (!empty($productID) && in_array($productRs['mChannelID'], [9])) {
                                $flag_sem = false;
                                $sql_md5_sem_desc = "SELECT sem_headline,sem_url,sem_search_key,sem_description FROM cscan_semdetails WHERE product_id = '" .$productID."'";
                                $query_md5_sem_desc = $DRW->query($sql_md5_sem_desc, $DRW_read);
                                if ($DRW->num_rows($query_md5_sem_desc) > 0) {
                                    $data_sem_det= $DRW->fetch_assoc($query_md5_sem_desc);
                                    $flag_sem = true;
        
                                }
                            if ($flag_sem) {
                                ?>

                                <tr>
                                    <td class="bodytext" valign="top" width="50%">
                                        <?php if (isset($data_sem_det['sem_search_key']) && $data_sem_det['sem_search_key'] != '') { ?>
                                            <strong>SEM Search Key:</strong>&nbsp;&nbsp;<?php
                                            echo $data_sem_det['sem_search_key'];
                                        } else {
                                            echo '&nbsp;';
                                        }
                                        ?>
                                    </td>
                                    <td class="bodytext" valign="top" width="50%">
                                        <?php if ($data_sem_det['sem_url'] != '') { ?>
                                            <strong>SEM Url:</strong>&nbsp;&nbsp;<?php
                                            echo $data_sem_det['sem_url'];
                                        } else {
                                            echo '&nbsp';
                                        }
                                        ?>
                                    </td>
                                </tr>

                                <tr>
                                    <td class="bodytext" valign="top" width="50%">
                                        <?php if ($data_sem_det['sem_headline'] != '') { ?>
                                            <strong>SEM Headline:</strong>&nbsp;&nbsp;<?php
                                            echo $data_sem_det['sem_headline'];
                                        } else {
                                            echo '&nbsp;';
                                        }
                                        ?>
                                    </td>
                                    <td class="bodytext" valign="top" width="50%">
                                        <?php if ($data_sem_det['sem_description'] != '') { ?>
                                            <strong>SEM Description:</strong>&nbsp;&nbsp;<?php
                            echo $data_sem_det['sem_description'];
                        } else {
                            echo '&nbsp';
                        }
                                        ?>
                                    </td>
                                </tr>
                                <?php
                            }
                        }
                        ######### End:  Show SEM details (Search key, Url,title and description) ########
                        ?>
                        <?php
                        $showArray = array();

                        $showArray[] = "<strong>Media Channel:</strong> " . htmlspecialchars($mediaChannel);
                        $showArray[] = "<strong>Audience:</strong> " . htmlspecialchars($mediaPanel);                        
                        $showArray[] = "<strong>Primary Company:</strong> " . htmlspecialchars($company);
                        
                       
                        $secondCompany = '';
                        $resultC = $DRW->query("SELECT companyName FROM cscan_company pa,cscan_company_product pp
		WHERE pa.companyID=pp.companyID AND pp.productID=$productID AND primary_co<>1 ORDER BY primary_co ASC,companyName ASC", $DRW_read);
                        while ($dataC = $DRW->fetch_row($resultC)) {
                            if ($secondCompany != '') {
                                $secondCompany .= '; ';
                            }
                            $secondCompany .= $dataC[0];
                        }
                        if ($secondCompany == "") {
                            $secondCompany = 'N/A';
                        }
                        
                        $showArray[] = "<strong>Additional Companies:</strong> " . htmlspecialchars($secondCompany);
                       
                       

                        $divtext = '';
                        $aff_ids = '';
                        $aff_ids_s = '';
                        $aff_cids = array();
                        if ($productRs['mChannelID'] == 6) {
                            $showArray[] = "<strong>Sector:</strong> " . htmlspecialchars($sector);
                            $showArray[] = "<strong>Category:</strong> " . htmlspecialchars($category);
                            $showArray[] = "<strong>Sub Category:</strong> " . htmlspecialchars($subcategory);
                            if (!in_array('sub_sub', $_SESSION['sess_search_exclude']) && !empty($productRs['subSubCategoryID'])) {
                                $ssubcategory = subCategoryName($productRs['subSubCategoryID']);
                                if (!empty($ssubcategory) && $ssubcategory != 'Not mentioned') {
                                    $showArray[] = "<strong>Sub Sub Category:</strong> " . htmlspecialchars($ssubcategory);
                                }
                            }
                            if (in_array(4, $sectorArray) || in_array(5, $sectorArray)) {
                                $showArray[] = "<strong>Worksite/Voluntary:</strong> " . $worksite;
                            }
                            if (preg_match('/facebook\\.com/i', $productRs['external_link'])) {
                                $netname = 'Facebook';
                                $labeln = 'Facebook Page Name';
                                $labelu = 'Number of Updates';
                                $labelf = 'Number of Fans';
                            } elseif (preg_match('/twitter\\.com/i', $productRs['external_link'])) {
                                $netname = 'Twitter';
                                $labeln = 'Twitter Handle';
                                $labelu = 'Number of Tweets';
                                $labelf = 'Number of Followers';
                            } elseif (preg_match('/instagram\\.com/i', $productRs['external_link'])) {
                                $netname = 'Instagram';
                                $labeln = 'Instagram Handle';
                                $labelu = 'Number of Posts';
                                $labelf = 'Number of Followers';
                            } elseif (preg_match('/linkedin\\.com/i', $productRs['external_link'])) {
                                $netname = 'LinkedIn';
                                $labeln = 'LinkedIn Handle';
                                $labelu = 'Number of Posts';
                                $labelf = 'Number of Followers';
                            }else {
                                $netname = '';
                                $labeln = '';
                                $labelu = '';
                                $labelf = '';
                            }
                            if (!empty($netname)) {
                                $showArray[] = "<strong>Network Name:</strong> " . htmlspecialchars($netname);
                                /* if(!empty($productRs['social_media_name'])){
                                  $showArray[] = "<strong>$labeln:</strong> ".htmlspecialchars($productRs['social_media_name']);
                                  } */
                                if (!empty($productRs['external_updates'])) {
                                    $showArray[] = "<strong>$labelu:</strong> " . htmlspecialchars($productRs['external_updates']);
                                }
                                if (!empty($productRs['external_fans'])) {
                                    $showArray[] = "<strong>$labelf:</strong> " . htmlspecialchars($productRs['external_fans']);
                                }
                                $showArray[] = "<strong>Date Updated:</strong> " . htmlspecialchars(substr($productRs['entryID'], 5, 2) . '/' . substr($productRs['entryID'], 8, 2) . '/' . substr($productRs['entryID'], 0, 4));
                            }
                            if (!empty($productRs['socialmedia_adtype'])) {
                                $socialmedia_adtypeArray = array(1 => 'Sponsored', 2 => 'Corporate');
                                $socialmedia_adtypevalue = '';
                                foreach ($socialmedia_adtypeArray as $key => $keyval) {
                                    if ($productRs['socialmedia_adtype'] == $key) {
                                        $socialmedia_adtypevalue = $keyval;
                                    }
                                }
                                $showArray[] = "<strong>Social Media Ad Type:</strong> " . $socialmedia_adtypevalue;
                            }
                        } else {
                            $showArray[] = "<strong>State/Province:</strong> " . htmlspecialchars($stateName);
                            if (!empty($panelist_edc)) {
                                $showArray[] = "<strong>EDC / LDC / TDSP:</strong> " . htmlspecialchars($panelist_edc);
                            }
                            $showArray[] = "<strong>Sector:</strong> " . htmlspecialchars($sector);
                            $showArray[] = "<strong>Category:</strong> " . htmlspecialchars($category);
                            $showArray[] = "<strong>Sub Category:</strong> " . htmlspecialchars($subcategory);
                            if (!in_array('sub_sub', $_SESSION['sess_search_exclude']) && !empty($productRs['subSubCategoryID'])) {
                                $ssubcategory = subCategoryName($productRs['subSubCategoryID']);
                                if (!empty($ssubcategory) && $ssubcategory != 'Not mentioned') {
                                    $showArray[] = "<strong>Sub Sub Category:</strong> " . htmlspecialchars($ssubcategory);
                                }
                            }
                            if (in_array(4, $sectorArray) || in_array(5, $sectorArray)) {
                                $showArray[] = "<strong>Worksite/Voluntary:</strong> " . $worksite;
                            }
                            $showArray[] = "<strong>Affinity/Association:</strong> " . $affinityAssociation;

                            $resultC = $DRW->query("SELECT pa.affinityID,affinityName FROM cscan_affinity pa,cscan_affinity_product pp
			WHERE pa.affinityID=pp.affinityID AND pp.productID=$productID ORDER BY affinityName", $DRW_read);
                            while ($dataC = $DRW->fetch_row($resultC)) {
                                if ($aff_ids != '') {
                                    $aff_ids .= ', ';
                                    $aff_ids_s = 's';
                                }
                                $aff_ids .= htmlspecialchars($dataC[1]);
                                $resultC2 = $DRW->query("SELECT AffinityCategoryID FROM cscan_aff_cat WHERE affinityID=$dataC[0]", $DRW_read);
                                while ($dataC2 = $DRW->fetch_row($resultC2)) {
                                    if (!in_array($dataC2[0], $aff_cids) && !empty($dataC2[0])) {
                                        $aff_cids[] = $dataC2[0];
                                    }
                                }
                            }
                            if ($aff_ids != '') {
                                $showArray[] = "<strong>Affinity/Association Name$aff_ids_s:</strong> " . $aff_ids;
                            }
                            if (count($aff_cids) > 0) {
                                if (count($aff_cids) > 1) {
                                    $aff_ids_s = 's';
                                } else {
                                    $aff_ids_s = '';
                                }
                                $showArray[] = "<strong>Affinity/Association Category Name$aff_ids_s:</strong> " . getAffinityCategoryName(implode(',', $aff_cids));
                            }

                            if (($productRs['mChannelID'] == 5 || $productRs['mChannelID'] == 7) AND $showdomain == '') {
                                if ($is_flash) {
                                    $document_content_type_show = 'Flash';
                                } else {
                                    $document_content_type_show = $document_content_type;
                                }
                                $showArray[] = "<strong>Technology:</strong> $document_content_type_show";
                                $showArray[] = "<strong>Size (pixel):</strong> $document_placement";
                                //$showArray[] = "<strong>Filename:</strong> $document_filename";

                                $sitecount = 0;
                                $sitesArray = array();
                                /* if ($productRs['mChannelID']==5) {
                                  $divtext .= '<div style="display:none;position:absolute;background:#ffffff;padding:0px;border:solid 2px #000000;z-index:100;width:300px;" id="thesites"><div style="float:right;height:26px;"><a href="#" onclick="hideSites(); return false;" class="bluelink">close</a> &nbsp; </div><div style="clear:both;height:1px"><img src="images/spacer.gif" width="1" height="1" /></div>
                                  <div id="thesites_inner" style="padding:4px;"><table id="thesites_table" border="0" cellspacing="2" cellpadding="4" class="likeresults" style="width:100%;">
                                  <tr><td class="bodytext_small"><strong>Site</strong></td><td class="bodytext_small"><strong>Date</strong></td></tr>';

                                  $resultC = $DRW->query("SELECT sites_name,DATE_FORMAT(sp_observation,'%m/%d/%Y %h:%i %p'),sp_url,sites_category_name,ss.sites_id,sp_observation FROM cscan_sites_product sp,cscan_sites ss LEFT JOIN cscan_sites_category sc USING(sites_category_id)
                                  WHERE sp.productID=$productID AND ss.sites_id=sp.sites_id ORDER BY sp_observation DESC",$DRW_read);

                                  while($dataC = $DRW->fetch_row($resultC)){
                                  $sites_name = $dataC[0];
                                  $sp_observationf = $dataC[1];
                                  $sp_url = $dataC[2];
                                  $sites_category_name = $dataC[3];
                                  $sites_id = $dataC[4];
                                  $sp_observation = $dataC[5];

                                  if(!in_array($sites_name,$sitesArray)){
                                  $sitesArray[] = htmlspecialchars($sites_name);
                                  }

                                  $divtext .= '<tr><td class="bodytext_small" valign="top"><a href="#" class="bluelink" style="font-size: 10px;" onclick="showObservation('.$productID.','.$sites_id.',\''.urlencode($sp_observation).'\'); return false;">'.htmlspecialchars($sites_name)."</a></td><td class=\"bodytext_small\" valign=\"top\">$sp_observationf</td></tr>";
                                  $sitecount++;
                                  }
                                  $divtext .= '</table></div></div>';
                                  }
                                  if ($productRs['mChannelID']==7) { */
                                $divtext .= '<div style="display:none;position:absolute;background:#ffffff;padding:0px;border:solid 2px #000000;z-index:100;width:300px;" id="thesites"><div style="float:right;height:26px;"><a href="#" onclick="hideSites(); return false;" class="bluelink">close</a> &nbsp; </div><div style="clear:both;height:1px"><img src="images/spacer.gif" width="1" height="1" /></div>
                                <div id="thesites_inner" style="padding:4px;"><table id="thesites_table" border="0" cellspacing="2" cellpadding="4" class="likeresults" style="width:100%;">
                                <tr><td class="bodytext_small"><strong>Traffic Sources</strong></td></tr>';

                                $resultC = $DRW->query("SELECT traffic_sources FROM cscan_product_detail WHERE productID=$productID AND traffic_sources <> '' AND traffic_sources IS NOT NULL", $DRW_read);

                                while ($dataC = $DRW->fetch_row($resultC)) { //should only run once (right now?)
                                    $sources_text = $dataC[0];

                                    $divtext .= '<tr><td class="bodytext_small" valign="top">' . nl2br(htmlspecialchars($sources_text)) . "</td></tr>";
                                    $sitecount = floor(strlen($sources_text) / 56) + 1; //very rough approximation
                                }
                                $divtext .= '</table></div></div>';
                                //}
                                if ($sitecount > 0) {
                                    /* if ($productRs['mChannelID']==5) {
                                      $stext = ($sitecount == 1 ? 'Observation' : 'Observations');
                                      $showArray[] = "<strong>Sites:</strong> ".implode(', ',$sitesArray);
                                      $showArray[] = "<strong>This record has <a href=\"#\" onclick=\"showSites($sitecount); return false;\" class=\"bluelink\" id=\"thesitelink\">$sitecount $stext</a></strong>";
                                      }
                                      if ($productRs['mChannelID']==7) { */
                                    $showArray[] = "<strong>View this record's <a href=\"#\" onclick=\"showSites($sitecount); return false;\" class=\"bluelink\" id=\"thesitelink\">Traffic Sources</a></strong>";
                                    //}
                                }
                            }

                            if (in_array(4, $sectorArray) || in_array(5, $sectorArray)) {
                                $gname = '';
                                $groupArray = get_groupSizeArray();
                                $groupArray['0'] = 'N/A';
                                $gsizeArray = explode(',', $productRs['groupSize']);
                                $groupCount = count($gsizeArray);
                                $docomma = false;
                                foreach ($gsizeArray as $gsize) {
                                    if ($gsize == 0 && $groupCount > 1) {
                                        continue;
                                    } elseif (isset($groupArray[$gsize])) {
                                        if ($docomma) {
                                            $gname .= ', ';
                                        } else {
                                            $docomma = true;
                                        }
                                        $gname .= $groupArray[$gsize];
                                    }
                                }
                                $showArray[] = "<strong>Group Size:</strong> " . htmlspecialchars($gname);
                                $offerOriginArray = get_offerOriginArray();
                                $offerOriginArray['0'] = 'N/A';
                                $showArray[] = "<strong>Offer Origin:</strong> " . htmlspecialchars($offerOriginArray[$productRs['offerOrigin']]);
                            }
                            $langName = languageName($productRs['compaignLanguage']);
                            if ($langName == "") {
                                $langName = 'N/A';
                            }
                            $showArray[] = "<strong>Campaign Language:</strong> " . htmlspecialchars($langName);

                            if ($productRs['mChannelID'] == 2) {//Trade Mag // && $productRs['publication']!=''
                                $print_typeArray = array();
                                $pubcount = 0;
                                $divtext .= '<div style="display:none;position:absolute;background:#ffffff;padding:0px;border:solid 2px #000000;z-index:100;width:300px;" id="thepubs"><div style="float:right;height:26px;"><a href="#" onclick="hidePublication(); return false;" class="bluelink">close</a> &nbsp; </div><div style="clear:both;height:1px"><img src="images/spacer.gif" width="1" height="1" /></div>
			<div id="thepubs_inner" style="padding:4px;"><table id="thepubs_table" border="0" cellspacing="2" cellpadding="4" class="likeresults" style="width:100%;">
			<tr><td class="bodytext_small"><strong>Publication</strong></td><td class="bodytext_small"><strong>Date</strong></td></tr>';

                                $resultC = $DRW->query("SELECT publicationName,DATE_FORMAT(monthYear,'%m/%d/%Y'),print_typeID FROM cscan_publication pa,cscan_publication_product pp
				WHERE pa.publicationID=pp.publicationID AND pp.productID=$productID ORDER BY monthYear DESC,publicationName", $DRW_read);
                                while ($dataC = $DRW->fetch_row($resultC)) {
                                    $publicationName = $dataC[0];
                                    $monthYear = $dataC[1];
                                    $print_typeID = $dataC[2];

                                    $divtext .= '<tr><td class="bodytext_small" valign="top">' . htmlspecialchars($publicationName) . "</td><td class=\"bodytext_small\" valign=\"top\">$monthYear</td></tr>";
                                    $pubcount++;

                                    $query_pt = "SELECT print_typeName FROM cscan_print_type WHERE print_typeID=$print_typeID";
                                    $result_pt = $DRW->query($query_pt, $DRW_read);
                                    $row_pt = $DRW->fetch_row($result_pt);
                                    if ($row_pt[0] != '' && !in_array($row_pt[0], $print_typeArray)) {
                                        $print_typeArray[] = $row_pt[0];
                                    }
                                }

                                $divtext .= '</table></div></div>';
                                if ($pubcount > 0) {
                                    $ptext = 'Publication';
                                    if ($pubcount != 1) {
                                        $ptext .= 's';
                                    }
                                    $showArray[] = "<strong>This record has <a href=\"#\" onclick=\"showPublication($pubcount); return false;\" class=\"bluelink\" id=\"thepublink\">$pubcount $ptext</a></strong>";
                                }

                                $showArray[] = "<strong>Publication Type:</strong> " . htmlspecialchars(implode(', ', $print_typeArray));
                            }

                            if ($productRs['mPanelID'] == 1 || $productRs['mPanelID'] == 2) { //Consumer
                                $ageName = getAgeName($productRs['age']);
                                if (!is21FilterOn()) {
                                    if ($ageName != '') {
                                        $showArray[] = "<strong>Age:</strong> " . htmlspecialchars($ageName);
                                    }

                                    /* if($productRs['gender']=='M' || $productRs['gender']=='F' || $productRs['gender']=='B'){ //'None'
                                      if($productRs['gender']=='M') {
                                      $g = 'Male';
                                      }
                                      elseif($productRs['gender']=='F') {
                                      $g = 'Female';
                                      }
                                      else {
                                      $g = 'Male, Female';
                                      }
                                      $showArray[] = "<strong>Gender:</strong> ".$g;
                                      } */

                                    $incomeName = getIncomeName($productRs['incomeID']);
                                    if ($incomeName != '') {//'Any'
                                        $showArray[] = "<strong>Income:</strong> " . htmlspecialchars($incomeName);
                                    }
                                }
                                $showArray[] = "<strong>Mailing Type:</strong> " . $mailtype;
                            }
                            if (($productRs['mPanelID'] == 1 || $productRs['mPanelID'] == 2) || $_SESSION['sess_plevel'] == 1 || $_SESSION['sess_plevel'] == 2) {
                                $faceAmountName = getFaceAmountName($productRs['fa_ids']);
                                if ($faceAmountName != '') {//'Any'
                                    $showArray[] = "<strong>Face Amount:</strong> " . htmlspecialchars($faceAmountName);
                                }

                                $termLengthName = getTermLengthName($productRs['tl_ids']);
                                if ($termLengthName != '') {//'Any'
                                    $showArray[] = "<strong>Term Length:</strong> " . htmlspecialchars($termLengthName);
                                }
                            }

                            if ($mail_volume_tot > 0 && $productRs['mPanelID'] != 2 && $productRs['mChannelID'] != '3') {
                                $dmspend = doSpend($mail_volume_tot, $document_size_byte);
                                $showArray[] = "<strong>Estimated Spend:</strong> \$" . number_format($dmspend);
                            }
                        }
                        $variantArray = array();
                        getAllVariantsArray((int) $productID, $variantArray);

                        $vs = count($variantArray);
                        if ($vs > 1) {
                            $varind = count($showArray);
                            $showArray[$varind] = '';
                            $thisvarnt = $variantArray[$productID]['desc'];
                            unset($variantArray[$productID]);
                            $vs--;

                            uasort($variantArray, "inner_strnatcmp");

                            $showArray[$varind] .= "<strong>This record has <a href=\"#\" onclick=\"showVarnt($vs); return false;\" class=\"bluelink\" id=\"thevarntlink\">$vs variant";
                            if ($vs != 1) {
                                $showArray[$varind] .= 's';
                            }
                            $showArray[$varind] .= "</a></strong>";

                            $divtext .= '<div style="display:none;position:absolute;background:#ffffff;padding:0px;border:solid 2px #000000;z-index:100;width:300px;" id="thevarnts"><div style="float:right;height:26px;"><a href="#" onclick="hideVarnt(); return false;" class="bluelink">close</a> &nbsp; </div><div style="clear:both;height:1px"><img src="images/spacer.gif" width="1" height="1" /></div>
		<div id="thevarnts_inner" style="padding:4px;"><table id="thevarnts_table" border="0" cellspacing="2" cellpadding="4" class="likeresults" style="width:100%;">
		<tr><td class="bodytext_small"><strong>Entry ID</strong></td><td class="bodytext_small"><strong>Description</strong></td></tr>';
                            foreach ($variantArray as $p => $e) {
                                $divtext .= "<tr><td class=\"bodytext_small\" valign=\"top\" nowrap=\"nowrap\"><a class=\"bluelink\" style=\"font-size: 10px;\" onclick=\"hideVarnt(); return true;\" href=\"productDetail.php?id=$p\">" . $e['entryID'] . "</a></td><td class=\"bodytext_small\" valign=\"top\">";
                                if ($e['desc'] != '') {
                                    $divtext .= $e['desc'];
                                } else {
                                    $divtext .= getVariant($productRs['vid'], $productRs['variant_desc']);
                                }
                                $divtext .= '</td></tr>';
                            }
                            $divtext .= '</table></div></div>';
                        }

                        require_once('admin/additionalDetails.php');

                        $addltext = '';
                        $addlcount = 0;
                        foreach ($addlArray as $o) {
                            if (in_array($o->id, $sectorArray) || in_array($o->id, explode(',', $productRs['categoryID']))) {
                                $sql = "SELECT * FROM " . $o->table . " WHERE productID=$productID";
                                $result = $DRW->query($sql, $DRW_read);
                                if ($DRW->num_rows($result) > 0) {
                                    $dataAssoc = $DRW->fetch_assoc($result);
                                }
                                while ($o->getNext()) {
                                    $field = $o->getField();
                                    if ($field == '') {
                                        $addltext .= '<tr><td class="bodytext" valign="top" colspan="2"><strong>' . htmlspecialchars($o->label . ' - ' . $o->getTitle()) . '</strong></td></tr>';
                                        $addlcount++;
                                    } else {
                                        if (isset($dataAssoc[$field])) {
                                            $val = $o->doProcess($dataAssoc[$field]);
                                        } else {
                                            $val = '';
                                        }
                                        if ($o->getDisplay() || (!is_null($val) && $val !== '')) {
                                            if (is_null($val) || $val === '') {
                                                $val = '&nbsp;';
                                            }
                                            $addltext .= '<tr><td class="bodytext_small" valign="top" style="padding-left:10px;"><strong>' . htmlspecialchars($o->getTitle()) . '</strong></td><td class="bodytext_small" valign="top">' . $val . '</td></tr>';
                                            $addlcount++;
                                        }
                                    }
                                }
                            }
                        }

                        if ($productRs['mChannelID'] == 1 && ($productRs['mPanelID'] == 1 || $productRs['mPanelID'] == 2)) {
                            if ($productRs['delmethid'] != 5 || !in_array('citi', $_SESSION['sess_search_exclude'])) {
                                $showArray[] = "<strong>Delivery Method:</strong> " . getDelMeth($productRs['delmethid']);
                            }
                        }
                        if (!in_array('prescription', $_SESSION['sess_search_exclude']) && $productRs['prescription']) {
                            $showArray[] = "<strong>Rx:</strong> Yes";
                        }
                        if (!in_array('prescreen', $_SESSION['sess_search_exclude']) && $productRs['is_prescreen']) {
                            $showArray[] = "<strong>Pre-Screen &amp; Opt-Out Notice:</strong> Yes";
                        }
                        if ($productRs['FeeProduct']) {
                            $showArray[] = "<strong>Fee Product:</strong> Yes";
                        }
                        if (!empty($productRs['FeeProductType'])) {
                            $a = explode(',', $productRs['FeeProductType']);
                            if (count($a) > 0) {
                                $show = '';
                                foreach ($a as $f) {
                                    if ($show != '') {
                                        $show .= ', ';
                                    }
                                    $show .= getFeeProductTypeName($f);
                                }
                                if (!empty($show)) {
                                    $showArray[] = "<strong>Ancillary Products:</strong> $show";
                                }
                            }
                        }

                        if ($_SESSION['sess_plevel'] == 1 || $_SESSION['sess_plevel'] == 2) {
                            if (!empty($productRs['riders'])) { //if(in_array(4,$sectorArray) || in_array(5,$sectorArray)){
                                $showArray[] = "<em><strong>Riders:</strong> " . getriders($productRs['riders']) . '</em>';
                            }
                            if (in_array('citi', $_SESSION['sess_search_exclude'])) {
                                $temp = explode(',', $productRs['agentCommunicationID']);
                                $hides = array(36, 37, 38, 39);
                                foreach ($hides as $hide) {
                                    $ind = array_search($hide, $temp);
                                    if ($ind !== false) {
                                        unset($temp[$ind]);
                                    }
                                }
                                $productRs['agentCommunicationID'] = implode(',', $temp);
                            }
                            $showArray[] = "<em><strong>Communication Type:</strong> " . agentName($productRs['agentCommunicationID']) . '</em>';
                        }

                        if ($addlcount > 0 || (($_SESSION['sess_plevel'] == 1 || $_SESSION['sess_plevel'] == 2) && (trim($productRs['incentive_ongoing']) != '' || trim($productRs['incentive']) != ''))) {
                            if (trim($productRs['incentive_ongoing']) != '') {
                                $addltext = '<tr><td class="bodytext_small" valign="top" style="padding-left:10px;"><strong>Ongoing Incentive</strong></td><td class="bodytext_small" valign="top">' . htmlspecialchars($productRs['incentive_ongoing']) . '</td></tr>' . $addltext;
                                $addlcount++;
                            }
                            if (trim($productRs['incentive']) != '') {
                                if ($addlcount > 0) {
                                    $ince = 'Sign-on Incentive';
                                } else {
                                    $ince = 'Incentive';
                                }
                                $addltext = '<tr><td class="bodytext_small" valign="top" style="padding-left:10px;"><strong>' . $ince . '</strong></td><td class="bodytext_small" valign="top">' . htmlspecialchars($productRs['incentive']) . '</td></tr>' . $addltext;
                                $addlcount++;
                            }

                            $showArray[] = array('addtl' => '<a href="#" onclick="showAddl(' . $addlcount . '); return false;" class="bluelink" id="theaddllink">Additional Details</a>');

                            if ($makePDF) {
                                $divtext .= '<div id="theaddl"><h3>Additional Details</h3>';
                            } else {
                                $divtext .= '<div style="display:none;position:absolute;background:#ffffff;padding:0px;border:solid 2px #000000;z-index:100;width:300px;" id="theaddl"><div style="float:right;height:26px;"><a href="#" onclick="hideAddl(); return false;" class="bluelink">close</a> &nbsp; </div>';
                            }
                            $divtext .= '<div style="clear:both;height:1px"><img src="images/spacer.gif" width="1" height="1" /></div>
		<div id="theaddl_inner" style="padding:4px;"><table id="theaddl_table" border="0" cellspacing="2" cellpadding="4" class="likeresults" style="width:100%;">' . $addltext . '</table></div></div>';
                        }

                        if (count($showArray) > 0) {
                            foreach ($showArray as $key => $val) {
                                $mod = $key % 2;
                                if ($mod == 0) {
                                    echo "<tr>";
                                }
                                if (is_array($val) && isset($val['addtl'])) {
                                    if ($makePDF) {
                                        $val = $divtext;
                                    } else {
                                        $val = $val['addtl'];
                                    }
                                }

                                echo "<td class=\"bodytext\" valign=\"top\">$val</td>";
                                if ($mod != 0) {
                                    echo "</tr>";
                                }
                            }
                            if ($mod == 0) {
                                echo "<td class=\"bodytext\" valign=\"top\">&nbsp;</td></tr>";
                            }
                        }
                        ?>
                                <?php if (!$makePDF): ?>
                            <tr>
                                <td align="left" valign="top" class="bodytext"><a href="<?php echo "sendLink.php?id=" . $productID . '&amp;send_mode=1'; ?>" class="bluelink" title="Click this if you want to send the products details of this product as a link to your colleague" onclick="sendColleague(<?php echo $productID; ?>, 1);
                                                return false;">Send this as a link to your colleague</a></td>
                                <td align="left" valign="top" class="bodytext"><?php
                                    if ($document_size_byte >= 0 && ($productRs['mChannelID'] == 5 || $productRs['mChannelID'] == 7) && ($is_flash || $is_image)) {
                                        echo '<a href="productDocuments_latest.php?id=' . $productID . '&amp;did=' . $document_id . '" onclick="var winy = window.open(\'productDocuments_latest.php?id=' . $productID . '&amp;did=' . $document_id . '\'); winy.focus(); return false;" class="bluelink">Download Creative Execution</a>';

                                        /* $query2 = "SELECT document_size_byte FROM cscan_document WHERE productID=$productID AND document_id=1";
                                          $query_result2 = $DRW->query($query2,$DRW_read);
                                          $data2 = $DRW->fetch_row($query_result2);
                                          $document_size_byte2 = (int)$data2[0];

                                          $sizeofPDFinKB2=$document_size_byte2/1024;
                                          $sizeofPDFinMB2=$sizeofPDFinKB2/1024;
                                          if($sizeofPDFinMB2<1) {
                                          $DisplaySize2=round($sizeofPDFinKB2,2)." KB";
                                          }
                                          else {
                                          $DisplaySize2=round($sizeofPDFinMB2,2)." MB";
                                          }
                                          if($DisplaySize2>0){
                                          echo ' (<a class="bluelink" href="'.$thepdf.'" onclick="printAll('.$productID.',\''.$thepdf.'\',3); return false;">PDF</a>)';
                                          } */
                                    } else {
                                        echo '&nbsp;';
                                    }
                                    ?></td>
                            </tr>
                                                                                  <?php
                                                                                  if ($_SESSION['sess_plevel'] == 1 || $_SESSION['sess_plevel'] == 2) {
                                                                                      ?>
                                <tr>
                                    <td align="left" valign="top" class="bodytext"><a href="<?php echo "sendLink.php?id=" . $productID . '&amp;send_mode=2'; ?>" class="bluelink" style="font-style:italic;" title="Click this if you want to send the products details of this product as a QA link to your colleague" onclick="sendColleague(<?php echo $productID; ?>, 2);
                                                        return false;">Send this as a QA link to your colleague</a><br /><form name="qaform" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" style="display:inline;"><label style="font-style:italic;"><input type="checkbox" name="qa" value="1" onclick="doQA(<?php echo $productID; ?>);"<?php if (!empty($productRs['is_qa'])) echo ' checked="checked"'; ?> />QA Certified</label></form></td>
                                    <td align="left" valign="top" class="bodytext"><?php
                                        if ($document_size_byte >= 0 && $productRs['mChannelID'] != 10 && $productRs['mChannelID'] != 9 && $productRs['mChannelID'] != 5) {
                                            ?>
                                            <a href="pdfContentDetail.php?id=<?php echo $productID; ?>" onclick="productContent(<?php echo $productID; ?>); return false;" class="bluelink" style="font-style:italic;">View Product Content in text format</a>
                                            <?php
                                        } else {
                                            echo '&nbsp;';
                                        }
                                        ?></td>
                                </tr>
            <?php
        }
        ?>
                    <?php endif; ?>
                    </table>
                </div>
                <table border="0" cellspacing="0" cellpadding="4">
                    <tr><td>&nbsp;</td></tr>
                               <?php if (!$makePDF): ?>
                        <tr><td><a href="#" class="HyperLink" onclick="window.close();
                                        return false;" title="Click to Close this window">Close-Window</a><?php
                        if ($_SESSION['sess_plevel'] == 1) {
                            echo ' <span style="color:#ffffff;font-family: arial;font-size: 11px;">(' . number_format((microtime(true) - $start_time), 3) . ' Seconds)</span>';
                        }
                        ?></td></tr>
            <?php endif; ?>
                </table>
            </div>
            <?php echo ($makePDF ? '' : $divtext); ?>
            <?php
        } else {
            echo "Product has been discontinued.";
        }

        function inner_strnatcmp($a, $b) {
            return strnatcmp($b['entryID'], $a['entryID']);
        }

        function setWidthHeight($width, $height, $maxWidth, $maxHeight) {
            $ret = array($width, $height);
            $ratio = $width / $height;
            if ($width > $maxWidth || $height > $maxHeight) {
                $ret[0] = $maxWidth;
                $ret[1] = ceil($ret[0] / $ratio);

                if ($ret[1] > $maxHeight) {
                    $ret[1] = $maxHeight;
                    $ret[0] = ceil($ret[1] * $ratio);
                }
            }
            return $ret;
        }
        ?>
        <script type="text/javascript">
        function OpenNewWindowPopup(pid){
        //alert('nnnnnnn');
        var baseurl='<?php echo $baseurl;?>';
        //alert(baseurl);
        //return false;
        //var winy = window.open(url,"menubar=yes, status=no, location=no, toolbar=yes, left=0, top=0, scrollbars=yes, resizable=yes");
        var winy = window.open(baseurl+'/productDocuments_latest.php?id='+pid,"video","menubar=yes, status=no, location=no, toolbar=yes, left=0, top=0, scrollbars=yes, resizable=yes");
	winy.focus();
        }
        </script>
        
    </body>
</html>
<?php
if ($makePDF) {
    try {
        require_once 'HTTP/Download.php';
        $pdfString = ob_get_contents();
        $pdf = new Pdf($pdfString);

        $tmpFilename = date('YmdHis');
        $tmpPDFPartOne = 'tmpPDF/' . $tmpFilename . '.pdf';

        if (!$pdf->saveAs($tmpPDFPartOne)) {
            echo $pdf->getError();
        }

        $finalFile = $tmpPDFPartOne;
        if ($document_id != 2) {
            //##################Start FETCH S3 PDF ######################//
            if(strpos($document_path,'/')=='0'){
                $document_path  = substr($document_path,1);
                $exp_path= explode('/',$document_path);
                $yearpath=$exp_path[1];
                $monthpath=$exp_path[2];
                $productid=$exp_path[3];
                $datepath = $yearpath ."/".$monthpath;
                $root = dirname(__FILE__).'/';
                $newpdfpart = $root.'tmpPDF/'.$document_filename;
                 $s3checkfile = $s3->doesObjectExist($bucket_name, $document_path.$document_filename);
                //echo "ok".$s3checkfile; die;
                if ($s3checkfile){
                 try {
                         // Get the object.
                         $result = $s3->getObject([
                             'Bucket' => $bucket_name,
                             'Key'    => $document_path.$document_filename,
                             'SaveAs' => $newpdfpart,

                         ]);
                       //echo "<pre>";
                        //print_r($result); 
                        //echo "<pre>";die;
                     } catch (S3Exception $e) {
                         echo $e->getMessage() . PHP_EOL;
                     }

                 }
            }
             
           $finalFile = "tmpPDF/" . $tmpFilename . "-merged.pdf"; 
           $tmpPDFPartTwo = $root ."tmpPDF/" . $document_filename; 
            //################## END FETCH S3 PDF ######################//
            //$tmpPDFPartTwo = dirname(__FILE__) . "$document_path$document_filename";
            $exitCode = false;
            passthru("gs -dBATCH -dNOPAUSE -q -sDEVICE=pdfwrite -sOutputFile=tmpPDF/" . $tmpFilename . "-merged.pdf $tmpPDFPartOne $tmpPDFPartTwo", $exitCode);
        }

        ob_end_clean();
        $dl = new HTTP_Download();
        $dl->setFile($finalFile);
        $dl->setLastModified(date('Y-m-d H:i:s'));
        $dl->setContentType('application/pdf');
        $dl->setCacheControl('public');
        $dl->setCache(true);
        $dl->setContentDisposition(HTTP_DOWNLOAD_INLINE, "CompetiscanProductCombined_$productID.pdf");
        $dl->send();
        unlink($tmpPDFPartOne);
        if ($tmpPDFPartOne != $finalFile) {
            unlink($finalFile);
        }
        if (is_file($newpdfpart)) {
           @unlink($newpdfpart);
        }
    } catch (Exception $e) {
        die($e->getMessage());
    }
}
?>
<script type="text/JavaScript">
function htmlWin(pid) {
	var winy = window.open('processed-html.php?id='+pid,"pdf"+pid,"menubar=yes, status=no, location=no, toolbar=yes, left=0, top=0, scrollbars=yes, resizable=yes, width=900, height=600");
	winy.focus();
}
    
</script>