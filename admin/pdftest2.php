<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'top.php';
require_once '../includes/functions.php';
require_once('sphinxapi.php');
function pr($str){
    echo '<pre>';print_r($str);
}
function startSphinxmew() {
    $cl = new SphinxClient();
    $cl->SetMatchMode(SPH_MATCH_ANY);
    $cl->SetSortMode(SPH_SORT_RELEVANCE, '@id ASC');
    $cl->setLimits(0,5);
    return $cl;
}
$stringArray = array();
if (isset($_REQUEST['pid']) && $_REQUEST['pid'] != '') {
    $pid = $_REQUEST['pid'];    
    $sqlquery = "SELECT dts_val FROM cscan_document_text_search where productID='" . $pid . "' limit 0,1 ";
    $resultset = $DRW->query($sqlquery, $DRW_read);
    $dataset = $DRW->fetch_row($resultset);
    if (!empty($dataset)) {
        $dts_val = $dataset[0];
        $dataarray  =   array_unique(explode(" ",$dts_val));
        foreach($dataarray as $key=>$val){
            if(strlen($val)>5){
                $stringArray[]  =   $val;
            }
        }
    }
}
echo $dts_val;die;
$data = array();
$num = 1;
$totalnumber = count($stringArray);
foreach($stringArray as $string){
    $cl = startSphinxmew();
    pr($cl);die;
    $q = '"' . $cl->EscapeString($string) . '"';
    $result = $cl->Query($q ,'base_index_prod');
    //pr($res);die;
    foreach ($result['matches'] as $key=>$keydata){  
        $data[] =   $keydata['attrs']['productid'];
    }
}
//pr($data);
$newData =   array_count_values($data);
//pr($newData);
arsort($newData);
//pr($newData);
$allprduct_ids= array_slice($newData,0,10,true);
//pr($allprduct_ids);
//die;
?>
<table width="100%" border="0" cellspacing="0" cellpadding="5" class="text">
    <tr class="head1">

        <td width="20%" class="adminhead"><strong>Product Name</strong></td>
        <td width="20%" class="adminhead"><strong>Company Name</strong></td>
        <td width="20%" class="adminhead"><strong>Media</strong><strong> / Sector</strong> <strong>/ Audience</strong></td>
        <td class="adminhead"><strong>Date</strong></td>
        <td class="adminhead"><strong>Last User</strong></td>
        <td class="adminhead"><strong>Entry ID</strong></td>
        <td class="adminhead"><strong>Source ID</strong></td>
        <td class="adminhead"><strong>Percentage</strong></td>
    </tr>



<?php
$className = '';
foreach ($allprduct_ids as $key => $val) {
    $sqlselect = "SELECT pd.productID,productName,pd.sectorID,categoryID,entryID,addedToDatabase,DATE_FORMAT(actual_addedToDatabase,'%m/%d/%Y') as actual_addedToDatabasef,admin_userID,productStatus,pd.mPanelID,pd.mChannelID,DMSource,DATE_FORMAT(approved_date,'%m/%d/%Y') as approved_datef,product_priority,special_handling FROM cscan_product_detail pd  where productID ='" . $key . "'";
    $rs = $DRW->query($sqlselect, $DRW_read);
    $row = $DRW->fetch_array($rs);
    // print_r($row);
    //echo $val . '====' . $totalnumber . "==<br>";
    $percentage = round(($val * 100) / $totalnumber);
    $productID = $row['productID'];
    $entryID = $row['entryID'];
    $productName = $row['productName'];
    $categoryID = $row['categoryID'];
    $sectorID = $row['sectorID'];
    $addedToDatabase = $row['addedToDatabase'];
    $actual_addedToDatabase = $row['actual_addedToDatabasef'];
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
        <tr class="<?php echo $className; ?>" valign="top" <?php
        if ($productStatus != 1) {
            echo ' style="background-color:#E8E8FF;"';
        }
        ?>>

            <td valign="top"><img src="../images/arrow.gif" id="<?php echo 'pimg' . $productID; ?>" alt="" title="Preview this Product" style="cursor:pointer;" onclick="doPreview('<?php echo $productID; ?>',<?php echo $img_createddate_ts; ?>);" onmouseover="showPreview(<?php echo $productID; ?>,<?php echo $img_createddate_ts; ?>);
                                            return true;" onmouseout="hidePreview(<?php echo $productID; ?>); return true;" />
    <!--		<a class="hlinks" onclick="return removeNewTab(this);" data-href="addproduct.php?id=<?php //echo $productID; ?>"><strong><?php //echo ucfirst($productName); ?></strong></a></td>-->
                <a class="hlinks"  href="addproduct.php?id=<?php echo $productID; ?>"><strong><?php echo ucfirst($productName); ?></strong></a></td>
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
            <td><?php echo $percentage . '%'; ?></td>
        </tr>
                <?php
                $detail = '';
            }
            ?>
</table>
    <?php
    include 'massupdatetool.php';
    include 'bottom.php';
    ?>