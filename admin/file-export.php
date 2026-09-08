<?php
$ALLOW_GROUPS = array(54);
require_once("../auth_auth.php");
ini_set("memory_limit", "-1");
set_time_limit(0);
ini_set('max_execution_time', 5000);

/*if(isset($_REQUEST['bid'],$_REQUEST['uid']) && $_REQUEST['bid']!="" && $_REQUEST['uid']!=""){
    echo $_REQUEST['bid']; 
    echo $_REQUEST['uid']; die;
}*/

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

function array2csv(array &$array){
   if (count($array) == 0) {
     return null;
   }
   ob_start();
   $df = fopen("php://output", 'w');
   //fputcsv($df, $array['date']);
   //fputcsv($df, array("",""));
   foreach ($array['data'] as $row) {
      fputcsv($df, $row);
   }
   fclose($df);
   return ob_get_clean();
}

if(!empty($_POST['export']) && trim($_POST['export']) == 'Export'){
    if(isset($_REQUEST['bid'],$_REQUEST['uid']) && $_REQUEST['bid']!="" && $_REQUEST['uid']!=""){
     $bid= $_REQUEST['bid'];
     $uid= $_REQUEST['uid'];
     $start=$_REQUEST['start'];
     $end=$_REQUEST['end'];
    $arrExport = array();
    $exp_sql = "SELECT  DISTINCT 
    pd.company AS Company,
    pd.secondCompany AS 'Second Company',
    csec.sectorName AS 'Sector Name',
    csec3.sectorName AS 'Primary Sector Name',
    IF((SELECT GROUP_CONCAT(csec1.sectorName) FROM cscan_sector csec1 WHERE  find_in_set(csec1.sectorID,TRIM(pd.categoryID))) IS NULL, '',(SELECT GROUP_CONCAT(csec1.sectorName) FROM cscan_sector csec1 WHERE find_in_set(csec1.sectorID,TRIM(pd.categoryID))))  AS 'Category Name',
    csec4.sectorName AS 'Primary Category Name',
    IF((SELECT GROUP_CONCAT(csec2.sectorName) FROM cscan_sector csec2 WHERE  find_in_set(csec2.sectorID,TRIM(pd.subCategoryID))) IS NULL, '',(SELECT GROUP_CONCAT(csec2.sectorName) FROM cscan_sector csec2 WHERE find_in_set(csec2.sectorID,TRIM(pd.subCategoryID))))  AS 'Sub Category Name',
    csec5.sectorName AS 'Primary Sub Category Name',pd.entryID AS 'Entry ID',CONCAT('https://www.competiscan.com/index.php?product=',pd.productID) as 'EntryID Link',
    pd.productHeadline AS 'Product Headline',
    IF((SELECT GROUP_CONCAT(cacom.type) FROM cscan_agent_communication cacom WHERE  find_in_set(cacom.ID,TRIM(pd.agentCommunicationID))) IS NULL, '',(SELECT GROUP_CONCAT(cacom.type) FROM cscan_agent_communication cacom WHERE  find_in_set(cacom.ID,TRIM(pd.agentCommunicationID))))  AS 'Communications Type',
    cmch.mChannelName AS 'Media Channel',
    pd.simple_domain AS 'Simple Domain',
    cmp.mPanelName AS Audience,
    IF((SELECT GROUP_CONCAT(cst.stateName) FROM cscan_state cst WHERE  find_in_set(cst.stateID,TRIM(pd.state))) IS NULL, '',(SELECT GROUP_CONCAT(cst.stateName) FROM cscan_state cst WHERE  find_in_set(cst.stateID,TRIM(pd.state))))  AS 'State/Province',
    IF(cscount.country IS NULL or cscount.country = '', cscount3.country, cscount.country) AS 'Country',
    pd.compaignLanguage AS 'Compaign Language',
    cmtp.mTypeName AS 'Mailing Type',
    (CASE  WHEN pd.affinityAssociation = 1 THEN 'Yes' ELSE 'No' END) AS 'Affinity/Association',
    IF((SELECT GROUP_CONCAT(pa3.affinityName) FROM cscan_affinity pa3,cscan_affinity_product pp3 
    WHERE pa3.affinityID = pp3.affinityID AND pp3.productID = pd.productID GROUP BY pp3.productID) IS NULL, '',
    (SELECT GROUP_CONCAT(pa3.affinityName) FROM cscan_affinity pa3,cscan_affinity_product pp3 
    WHERE pa3.affinityID = pp3.affinityID AND pp3.productID = pd.productID GROUP BY pp3.productID)) AS 
    'Affinity/Association Name',
    IF(csafc.AffinityCategoryName IS NULL or csafc.AffinityCategoryName = '', '', csafc.AffinityCategoryName) AS 'Affinity/Association Category',
    IF(csafsc.AffinityCategoryName IS NULL or csafsc.AffinityCategoryName = '', '', csafsc.AffinityCategoryName) AS 'Affinity/Association Sub Category',
    pd.firstSeen AS 'First Seen',
    pd.lastSeen AS 'Last Seen',
    pd.productName AS 'Product Name',
    (CASE
            WHEN pd.delmethid = 0 THEN cmch.mChannelName
            ELSE cdm.delmethname
    END) AS 'Delivery Method Name',
    (CASE
            WHEN pd.incentive = '' THEN 'N/A'
            ELSE pd.incentive
    END) AS 'Sign-on Incentive',
    (CASE
            WHEN pd.incentive_ongoing = '' THEN 'N/A'
            ELSE pd.incentive_ongoing
    END) AS 'Ongoing Incentive',
    IF((SELECT GROUP_CONCAT(crm.responseMechName) FROM cscan_response_mechanism crm WHERE  find_in_set(crm.responseMechID,TRIM(pd.responseMechID))) IS NULL, '',(SELECT GROUP_CONCAT(crm.responseMechName) FROM cscan_response_mechanism crm WHERE find_in_set(crm.responseMechID,TRIM(pd.responseMechID))))  AS 'Response Mechanism',
    (CASE 
            WHEN pd.external_link REGEXP 'facebook' THEN 'Facebook' 
            WHEN pd.external_link REGEXP 'twitter' THEN 'Twitter' 
            ELSE '' 
    END ) AS 'Network Name',
    pd.external_updates AS 'Number of Updates/Tweets',
    pd.external_fans AS 'Number of Fans/Followers',
    (CASE
            WHEN pd.external_link = '' THEN 'N/A'
            ELSE pd.external_link
    END) AS 'External Link',
    (CASE
            WHEN pd.traffic_sources = '' THEN 'N/A'
            ELSE pd.traffic_sources
    END) AS 'Observed Traffic Sources',
    (CASE
            WHEN pd.is_prescreen = 0 THEN ''
            ELSE 'Yes'
    END) AS 'Pre-Screen',
    (CASE
            WHEN pd.OfferExpiryDate = '0000-00-00' THEN ''
            ELSE pd.OfferExpiryDate
    END) AS 'Offer Expiry Date',
    (CASE
            WHEN pd.socialmedia_adtype = 1 THEN 'Sponsored'
            WHEN pd.socialmedia_adtype = 2 THEN 'Corporate'
            ELSE ''
    END) AS 'Social Media Ad Type'
    FROM cscan_product_detail pd
    INNER JOIN cscan_product_basket cb ON cb.productID = pd.productID
    LEFT JOIN cscan_sector csec ON csec.sectorID = pd.sectorID
    LEFT JOIN cscan_sector csec3 ON (csec3.sectorID=(SELECT scsc_sectorID FROM cscan_scsc_product WHERE productID=pd.productID AND scsc_sort=1))
    LEFT JOIN cscan_sector csec4 ON (csec4.sectorID=(SELECT scsc_categoryID FROM cscan_scsc_product WHERE productID=pd.productID AND scsc_sort=1))
    LEFT JOIN cscan_sector csec5 ON (csec5.sectorID=(SELECT scsc_subCategoryID FROM cscan_scsc_product WHERE productID=pd.productID AND scsc_sort=1))
    LEFT JOIN cscan_mchannel cmch ON cmch.mChannelID = pd.mChannelID 
    LEFT JOIN cscan_mpanel cmp ON cmp.mPanelID = pd.mPanelID 
    LEFT JOIN cscan_state cst ON cst.stateID = pd.state
    LEFT JOIN ISO31661_alpha2code cscount on (cscount.code = cst.countryCode AND cst.stateID=pd.state)
    LEFT JOIN cscan_product_detail_state cscount2 on (cscount2.productID = pd.productID) 
    LEFT JOIN ISO31661_alpha2code cscount3 on (cscount3.code = cscount2.countryCode_copy)
    LEFT JOIN cscan_mtype cmtp ON cmtp.mTypeID = pd.mTypeID 
    LEFT JOIN cscan_affinity_category csafc on csafc.AffinityCategoryID = (
            SELECT catmap.AffinityCategoryID FROM cscan_aff_cat catmap, cscan_affinity_category catmaster WHERE catmap.affinityID = (SELECT pa.affinityID FROM cscan_affinity pa,cscan_affinity_product pp
            WHERE pa.affinityID = pp.affinityID AND pp.productID=pd.productID limit 0,1) AND catmap.AffinityCategoryID = catmaster.AffinityCategoryID and catmaster.parentID = 0 limit 0,1)
    LEFT JOIN cscan_affinity_category csafsc on csafsc.AffinityCategoryID=(
            SELECT catmap2.AffinityCategoryID FROM cscan_aff_cat catmap2, cscan_affinity_category catmaster2 WHERE catmap2.affinityID=(SELECT pa2.affinityID FROM cscan_affinity pa2,cscan_affinity_product pp2
            WHERE pa2.affinityID=pp2.affinityID AND pp2.productID=pd.productID limit 0,1)  AND catmap2.AffinityCategoryID=catmaster2.AffinityCategoryID and catmaster2.parentID<>0 limit 0,1)
    LEFT JOIN cscan_delivery_method cdm ON cdm.delmethid = pd.delmethid 
    WHERE cb.basket_id = '$bid' AND cb.userID = '$uid' limit $start, $end
    "; 

    $exp_rs = $DRW->query($exp_sql,$DRW_read);
        $arrExport['data'][] = array("Company", "Second Company","Sector Name","Primary Sector Name","Category Name","Primary Category Name","Sub Category Name","Primary Sub Category Name","Entry ID","EntryID Link","Product Headline","Communications Type","Media Channel","Simple Domain","Audience","State/Province","Country","Compaign Language","Mailing Type","Affinity/Association","Affinity/Association Name","Affinity/Association Category","Affinity/Association Sub Category","First Seen","Last Seen","Product Name","Delivery Method Name","Sign-on Incentive","Ongoing Incentive","Response Mechanism","Network Name","Number of Updates/Tweets","Number of Fans/Followers","External Link","Observed Traffic Sources","Pre-Screen","Offer Expiry Date","Social Media Ad Type");
        while($exp_row = $DRW->fetch_assoc($exp_rs)){
           //echo "<pre>";\
            //print_r($exp_row);
            $arrExport['data'][] = array($exp_row['Company'], $exp_row['Second Company'], $exp_row['Sector Name'],$exp_row['Primary Sector Name'],$exp_row['Category Name'],$exp_row['Primary Category Name'],$exp_row['Sub Category Name'],$exp_row['Primary Sub Category Name'],$exp_row['Entry ID'],$exp_row['EntryID Link'],$exp_row['Product Headline'],$exp_row['Communications Type'],$exp_row['Media Channel'],$exp_row['Simple Domain'],$exp_row['Audience'],$exp_row['State/Province'],$exp_row['Country'],$exp_row['Compaign Language'],$exp_row['Mailing Type'],$exp_row['Affinity/Association'],$exp_row['Affinity/Association Name'],$exp_row['Affinity/Association Category'],$exp_row['Affinity/Association Sub Category'],$exp_row['First Seen'],$exp_row['Last Seen'],$exp_row['Product Name'],$exp_row['Delivery Method Name'],$exp_row['Sign-on Incentive'],$exp_row['Ongoing Incentive'],$exp_row['Response Mechanism'],$exp_row['Network Name'],$exp_row['Number of Updates/Tweets'],$exp_row['Number of Fans/Followers'],$exp_row['External Link'],$exp_row['Observed Traffic Sources'],$exp_row['Pre-Screen'],$exp_row['Offer Expiry Date'],$exp_row['Social Media Ad Type']);
        
        }
    
    
    download_send_headers("product_report_" . date("Y-m-d").rand(0,100) . ".csv");
    echo array2csv($arrExport);
    die();
    }
}
include 'top.php';
?>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<table width='100%' border='0' cellspacing='0' cellpadding='8' class='text' rules='none' bordercolor='#B9B9B9' align='center' style='border-collapse:collapse'>
    <tr><td class="adminhead" align='center'>File Export</td></tr>
    <!-- search and right buttons start-->
    <tr>
        <td class="bodyText">
            
                <table width='100%' border="0" cellspacing="0" cellpadding="1" class="text">
                    <tr>
                       
                        <td align="right"><form name="export" method="post"><input class="button" style="width:60px;" type="submit" name="export" value="Export" /></form></td>
                    </tr>
                </table>
            
        </td>
    </tr>
</table>
<?php include 'bottom.php';?>
