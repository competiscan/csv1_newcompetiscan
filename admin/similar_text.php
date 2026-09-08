<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

//echo 'testing1';
include 'top.php';
//echo 'testing2';
require_once '../includes/functions.php';
//echo 'testing3';
//require_once 'sphinxapi.php';

//echo 'testing';//die;

function startSphinxmew($filter = 'dts_active', $filterval = array(1)) {
    global $SPHINX_server, $SPHINX_port;
    if (empty($SPHINX_server)) {
        $SPHINX_server = 'localhost';
    }
    if (empty($SPHINX_port)) {
        $SPHINX_port = 9312;
    }
    if (!$s = new SphinxClient()) {
        sphinxErr(__LINE__, $s, 'SphinxClient');
    }
    if (!$s->setServer($SPHINX_server, $SPHINX_port)) {
        sphinxErr(__LINE__, $s, 'setServer');
    }
    if (!$s->setMatchMode(SPH_MATCH_EXTENDED)) {
        sphinxErr(__LINE__, $s, 'setMatchMode');
    }
    if (!$s->setRankingMode(SPH_RANK_NONE)) {
        sphinxErr(__LINE__, $s, 'setRankingMode');
    }
    if (!$s->setFilter($filter, $filterval)) {
        sphinxErr(__LINE__, $s, 'setFilter');
    }
    if (!$s->setSortMode(SPH_SORT_RELEVANCE)) {
        sphinxErr(__LINE__, $s, 'setSortMode');
    }
    //$s->setGroupBy('productID',SPH_GROUPBY_ATTR);
    return $s;
}
function pr($str){
    echo '<pre>';print_r($str);
}
function cmp($a, $b){
    if ($a['match_percentage'] == $b['match_percentage']) {
        return 0;
    }
    return ($a['match_percentage'] > $b['match_percentage']) ? -1 : 1;
}
#####################################################
$q = '';
$pid = (!empty($_GET['pid']))?trim($_GET['pid']):'';
//echo $pid;
if($pid){
    $sqlquery 	= "SELECT dts_val FROM cscan_document_text_search where productID='".$pid."' limit 0,1 ";
    $resultset	= $DRW->query($sqlquery, $DRW_read);
    $dataset	= $DRW->fetch_row($resultset);
    //pr($dataset);
    if(!empty($dataset)){
        $search_string = trim($dataset[0]);
//        if(strlen($search_string)< 3000){
//            $string = $search_string;
//        }else{
            if(strlen($search_string)>13000){
                $wordLength = 9;    
            }elseif(strlen($search_string)>11000){
                $wordLength = 8;
            }elseif(strlen($search_string)>9000){
                $wordLength = 7;
            }elseif(strlen($search_string)>7000){
                $wordLength = 6;
            }elseif(strlen($search_string)>5000){
                $wordLength = 5;        
            }else{
                $wordLength = 4;
            }
            $arrStr2 = array();
            $arrStr = explode(" ", $search_string);
            if(!empty($arrStr)){
                foreach($arrStr as $words){
                    if(strlen($words)>$wordLength){
                        $arrStr2[] = $words;
                    }        
                }
            }
            $string = implode(" ", $arrStr2);
//        }
        //$q = '"' . trim($string) . '"';
        $q = $string;
    }   
}else{
    echo 'please pass pid.';die;
}
if($q){
    //echo $q;
    //echo '<br/><br/>';
    $s = startSphinxmew();
    //pr($s);//die;
    $inds = 'base_index_prod';
    //$q = 'Berkshire Hathaway company';
    //$q = "This email was sent to This email was sent by Helzberg Diamond Shops Inc A Berkshire Hathaway company 1825 Swift North Kansas City MO 64116 USA We respect your right to privacy view our policy Customize the Helzberg communications you receive by visiting our Subscription Center Unsubscribe From Helzberg Diamonds Date Sun Oct 23 2016 at 4 15 PM Subject Think Pink Shop Movado Free FedEx shipping on orders of 149 | more ENGAGEMENT WEDDING DIAMONDS JEWELRY WATCHES COLLECTIONS CREATE YOUR OWN CLEARANCE MOVADO FOR WOMEN MOVADO FOR MEN SHARE THE LOVE 1 800 HELZBERG help helzberg com Find A Store Prices  promotions are subject to change Jewelry styles  availability may vary online  in store Merchandise may be magnified to show detail  may -be exactly as pictured Diamond carat weights ct represent the approximate total weight TW of all diamonds in each setting unless noted Diamond solitaire weights may vary between 01  05 carat Diamond total weights may vary between 01  08 carat MOVADO THINK PINK SHOP MOVADO 50 FROM EACH PINK GOLD TONED MOVADO BOLD WATCH PURCHASED IN OCTOBER WILL BENEFIT THE BREAST CANCER RESEARCH FOUNDATION";
    $ps = parseSphinx($s, $q);
//    pr($ps);
//    die;
    $step='200000';
    //$s->setLimits(0, $step, $step);
    $s->setLimits(0, 5);
    //pr($ps);
    //die;
    $query = '"' . $q . '"';
    $res = $s->query($query, $inds);
    //$res = $s->query($ps, 'base_index_prod');
    //echo $ps;
    pr($res);die;
    $data = array();
    $var_1 = $q;
    if(!empty($res['matches'])){
        $i = 0;
        foreach($res['matches'] as $key=>$keydata){
            $sqlquery2 	= "SELECT dts_val FROM cscan_document_text_search where productID='".$keydata['attrs']['productid']."' limit 0,1 ";
            $resultset2	= $DRW->query($sqlquery2, $DRW_read);
            $dataset2	= $DRW->fetch_row($resultset2);
            $var_2 = trim($dataset2[0]);
            echo $var_1.' => '.$var_2.'<br/>';
            //$var_2 = $keydata['attrs']['dts_val'];
            similar_text($var_1, $var_2, $percent); 
            $data[$i]['id'] =   $key;
            $data[$i]['productid'] =   $keydata['attrs']['productid'];            
            $data[$i]['dts_val'] =   $var_2;
            //$data[$i]['dts_val'] =   $keydata['attrs']['dts_val'];
            $data[$i]['match_percentage'] =   trim($percent);
            $i++;
        }
    }
    if(!empty($data)){
        usort($data, "cmp");
    }
}else{
    echo 'hi';die;
}
?>
<html>
<head>
<title>Competiscan: Enhance your competitive skill</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="includes/styleSheet.css" rel="stylesheet" type="text/css" />
<meta name="robots" content="noindex, nofollow" />
<style type="text/css">
    <!--
    .bgx {
    background-repeat: repeat-x;
    }
    .bgy {
    background-repeat: repeat-y;
    }
    -->
</style>
</head>
<body style="background:#FAF6D2;padding:8px;">
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
    //pr($res);die;
    //$res = array();
    if(!empty($data)){
        $className='';
        $i=1;
        foreach($data as $key=>$val){
            if ($i%2 == 0)
                $className = 'white-bg';
            else
                $className = 'selected-bg';
            //pr($db);die;            
            $productID =   $val['productid'];    
            $sqlselect= "SELECT pd.productID,productName,pd.sectorID,categoryID,entryID,addedToDatabase,DATE_FORMAT(actual_addedToDatabase,'%m/%d/%Y') as actual_addedToDatabasef,admin_userID,productStatus,pd.mPanelID,pd.mChannelID,DMSource,DATE_FORMAT(approved_date,'%m/%d/%Y') as approved_datef,product_priority,special_handling FROM cscan_product_detail pd  where productID ='".$key."'";
            $rs = $DRW->query($sqlselect, $DRW_read);
            $row = $DRW->fetch_array($rs);
            $DMSource = $row['DMSource'];
            $entryID = $row['entryID'];
            $productName = $row['productName'];
            $product_priority = $row['product_priority'];
            $special_handling = $row['special_handling'];
            $productStatus = $row['productStatus'];                                    
            $mChannelID = $row['mChannelID'];
            $mPanelID = $row['mPanelID'];
            $sectorID = $row['sectorID'];
            $admin_userID = $row['admin_userID'];
            
            if($mChannelID){
                $mediaChannel = mediaChannelName($mChannelID);
            }else{
                $mediaChannel = 'N/A';
            }                    
           
            if($mPanelID){
                $mediaPanel = mediaPanelName($mPanelID);
            }else{
                $mediaPanel = 'N/A';
            }
            //echo $mediaPanel;die;            
            if($sectorID){
                $sectorName = sectorName($sectorID);
            }else{
                $sectorName = 'N/A';
            }
                        
            if ($productName == '')
                $productName = 'N/A';            
            
            if($admin_userID){
                $userquery = "SELECT userName FROM cscan_admin_users WHERE userID=$admin_userID";
                $userquery = $DRW->query($userquery, $DRW_read);
                if ($DRW->num_rows($userquery) > 0) {
                    $unam = $DRW->fetch_row($userquery);
                    $userName = $unam[0];
                } else
                    $userName = '';
            }else{
                $userName = '';
            }
                        
            $queryI = "SELECT UNIX_TIMESTAMP(img_document_createddate) FROM cscan_img_document WHERE productID=$productID AND document_id=1 AND img_document_default=1";
            $query_resultI = $DRW->query($queryI, $DRW_read);
            $dataI = $DRW->fetch_row($query_resultI);
            $img_createddate_ts = (float) $dataI[0];            
            ?>
        <tr class="<?php echo $className; ?>" valign="top">
            <td valign="top">
                <img src="../images/arrow.gif" id="<?php echo 'pimg' . $productID; ?>" alt="" title="Preview this Product" style="cursor:pointer;" onclick="doPreview('<?php echo $productID; ?>',<?php echo $img_createddate_ts; ?>);" onmouseover="showPreview(<?php echo $productID; ?>,<?php echo $img_createddate_ts; ?>);return true;" onmouseout="hidePreview(<?php echo $productID; ?>); return true;"/>
                <a class="hlinks" href="addproduct.php?id=<?php echo $productID; ?>"><strong><?php echo ucfirst($productName); ?></strong></a>
            </td>
            <td valign="top">
                <?php          
                $resultC = $DRW->query("SELECT companyName FROM cscan_company pa,cscan_company_product pp 
				WHERE pa.companyID=pp.companyID AND pp.productID=$productID AND primary_co=1", $DRW_read);
                $dataC = $DRW->fetch_row($resultC);
                $company = $dataC[0];                  
                if ($company) {
                    echo ucwords($company);
                } else {
                    echo '&nbsp;';
                }
                ?>
            </td>
            <td valign="top"><?php echo $mediaChannel . ' / ' . $sectorName . ' / ' . $mediaPanel; ?></td>
            <td valign="top"><?php echo $row['approved_datef'];?></td>
            <td valign="top"><?php echo $userName;?></td>
            <td valign="top">
                <?php
                $showDMSource = true;
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
                ?>
            </td>
            <td valign="top">
                <?php
                $sqltmp = "SELECT muid,isTmp FROM cscan_product_email WHERE productID='" . $DRW->real_escape_string($productID) . "' ORDER BY muid DESC";
                $rstmp = $DRW->query($sqltmp, $DRW_read);
                if ($DRW->num_rows($rstmp) > 0) {
                    while ($rowtmp = $DRW->fetch_row($rstmp)) {
                        if ($rowtmp[1] == 1)
                            print "<a href=\"manage_tmp_product.php?search_text=$rowtmp[0]tmp&state=0&company=\">$rowtmp[0]tmp</a> ";
                        else
                            print "<a href=\"/email.php?muid=$rowtmp[0]\" target=\"_blank\">$rowtmp[0]</a> ";
                    }
                }elseif ($showDMSource && $DMSource != '') {
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
                ?>
            </td>
            <td><?php echo $val['match_percentage'].'%';?></td>
        </tr>
        <?php $i++;}        
    }else{?>
        <tr class="white-bg" valign="top"><td align="center" colspan="8">No match found!</td></tr>
    <?php }?>
</table>