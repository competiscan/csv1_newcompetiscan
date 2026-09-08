<?php 
$PAGE_HEADING = "Retail Energy Pricing Dashboard";
$TITLE = "Competiscan $PAGE_HEADING";  
$HEAD = '<link rel="stylesheet" href="includes/jquery/jquery-ui.css" />
<script type="text/javascript" src="includes/jquery/jquery.min.js"></script>
<script type="text/javascript" src="includes/jquery/jquery-ui.min.js"></script>
<script type="text/javascript" src="includes/google/jsapi.js"></script>
<script type="text/javascript" src="includes/jquery/jquery.tokeninput.js"></script>
<link rel="stylesheet" href="includes/jquery/token-input.css" />

<style type="text/css">.no-close .ui-dialog-titlebar {display: none;}</style>';

include 'header_top_test.php';
require_once('includes/checklogin.php');

if(empty($_SESSION['sess_dashboard'])) {
	ob_end_clean();
	header("Location: fullsearch.php?searchview=2");
	exit;
}?>
<!--<script type="text/javascript" src="includes/dashboard.js?v=20140515"></script>-->
<?php
function callAPI($method, $url, $data){
    $curl = curl_init();
    switch ($method){
       case "POST":
          curl_setopt($curl, CURLOPT_POST, 1);
          if ($data)
             curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
          break;
       case "PUT":
          curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
          if ($data)
             curl_setopt($curl, CURLOPT_POSTFIELDS, $data);			 					
          break;
        case "DELETE":
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE");
        if ($data) {
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        break;
        case "GET":
            curl_setopt($curl, CURLOPT_URL, $url);
            break;
       default:
          if ($data)
             $url = sprintf("%s?%s", $url, http_build_query($data));
    }
    // OPTIONS:
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json','User-Agent:'.$_SERVER['HTTP_USER_AGENT'], 'X-Forwarded-For:'.$_SERVER['REMOTE_ADDR']));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    // EXECUTE:
    $result = curl_exec($curl);
    if(!$result){die("Connection Failure");}
    curl_close($curl);
    return $result;
 } 

 $edc_list_array=array();
 $term_list_array=array();
 $posted_retail_data=array();
$posted_retail_data['page_number']=1;
if(isset($_REQUEST['entries']) and $_REQUEST['entries']!='') {
$entries=(int)$_REQUEST['entries'];
}
$pagelimit=$posted_retail_data['page_size']=!empty($entries) ? $entries : 10;
if(isset($_GET['p'])) {
	$p = (int)$_GET['p'];
    //$page_num=(int)$p/20;
    $page_num = ceil($p / $pagelimit);
    if($page_num==0){
        $page_num=1;
    }
    $posted_retail_data['page_number'] = $page_num;
}
else {
	$p = 0;
    $posted_retail_data['page_number']=1;
}
echo "<pre>";
print_r($_REQUEST);
echo "</pre>";
$energy_type = [];
if(isset($_REQUEST['clear_button']) and $_REQUEST['clear_button']=='Clear Search') {
	       $_SESSION['electricitynaturalgas']="";
          $_REQUEST['electricitynaturalgas']="";
          $_SESSION['term']="";
          $_REQUEST['term']="";
          $_SESSION['state']="";
          $_REQUEST['state']="";
          $_SESSION['edc']="";
          $_REQUEST['edc']="";
          $_SESSION['producttype']="";
          $_REQUEST['producttype']="";
          $_REQUEST['entries']="10";
        
}
$energy_type = [];
if (!empty($_REQUEST['electricitynaturalgas'])) {

    $energy_type = array_filter($_REQUEST['electricitynaturalgas']);
    $energy_type = array_map('intval', $energy_type);

    $_SESSION['electricitynaturalgas'] = $energy_type;
    $posted_retail_data['energy_type'] = $energy_type;

} elseif (!empty($_SESSION['electricitynaturalgas'])) {

    $energy_type = array_map('intval', $_SESSION['electricitynaturalgas']);
    $posted_retail_data['energy_type'] = $energy_type;
}

$term = [];
if (!empty($_REQUEST['term'])) {

    $energy_type = array_filter($_REQUEST['term']);
    $energy_type = array_map('intval', $energy_type);

    $_SESSION['term'] = $energy_type;
    $posted_retail_data['term'] = $energy_type;

} elseif (!empty($_SESSION['term'])) {

    $energy_type = array_map('intval', $_SESSION['term']);
    $posted_retail_data['term'] = $energy_type;
}

$state = [];
if (!empty($_REQUEST['state'])) {

    $state = array_filter($_REQUEST['state']);
    $state = array_map('intval', $state);

    $_SESSION['state'] = $state;
    $posted_retail_data['state_id'] = $state;

} elseif (!empty($_SESSION['state'])) {

    $state = array_map('intval', $_SESSION['state']);
    $posted_retail_data['state_id'] = $state;
}
$edc = [];
if (!empty($_REQUEST['edc'])) {

    $edc = array_filter($_REQUEST['edc']);
    $edc = array_map('intval', $edc);

    $_SESSION['edc'] = $edc;
    $posted_retail_data['edc_id'] = $edc;

} elseif (!empty($_SESSION['edc'])) {

    $edc = array_map('intval', $_SESSION['edc']);
    $posted_retail_data['edc_id'] = $edc;
}

$producttype =[];
if (!empty($_REQUEST['producttype'])) {

    $producttype = array_filter($_REQUEST['producttype']);
    $producttype = array_map('intval', $producttype);

    $_SESSION['producttype'] = $producttype;
    $posted_retail_data['product_type'] = $producttype;

} elseif (!empty($_SESSION['producttype'])) {

    $producttype = array_map('intval', $_SESSION['producttype']);
    $posted_retail_data['product_type'] = $producttype;
}
$RETAIL_DASHBOARD_UAT="https://api1-uat.competiscan.com/energy-dashboard-client/v1/dashboard-retail-energy-Listing";
echo $posted_retail_dasboard_data=json_encode($posted_retail_data,JSON_UNESCAPED_SLASHES);
//$APIDIGITALURL=DIGITAL_DASHBOARD_UAT.'dashboard_data';
$get_retail_energy_dashoboard_data = callAPI('POST', $RETAIL_DASHBOARD_UAT, $posted_retail_dasboard_data);
$response_retail_dashboard_data = json_decode($get_retail_energy_dashoboard_data, true);
// echo "<pre>";
// print_r($response_retail_dashboard_data['Data']);
// echo "<pre>";
echo $num_of_rows = (int)$response_retail_dashboard_data['total_records'];
//$postdigita_data['export']=true;
//$posted_download= json_encode($postdigita_data);
//$currenttime=time();
//$filename_csv = 'Competiscan_Export_'.$currenttime.'_'.date('Y-m-d').'.csv';
?>
<div id="page">
<div id="save_search" class="bodytext" style="float:right;">
   <form name="dsave_search" action="/dashboard_test.php" method="post" onsubmit="return false;">
      <img name="waitss" id="waitss" src="images/searching.gif" border="0" style="display:none;">
      <strong>Saved Search</strong>
      <span role="status" aria-live="polite" class="ui-helper-hidden-accessible"></span><input type="text" class="input_box ui-autocomplete-input" size="20" maxlength="40" name="dsave_search_name" id="dsave_search_name" value="" autocomplete="off"><img name="show_dsave_search" id="show_dsave_search" src="images/plus.jpg" border="0" style="cursor:pointer;" title="show all">
      <input type="button" name="save_dsave_search" id="save_dsave_search" value="save new" class="submitbutton" style="display:none;">
      <input type="button" name="clear_dsave_search" id="clear_dsave_search" value="clear" class="submitbutton" style="display:none;">
      <input type="button" name="delete_dsave_search" id="delete_dsave_search" value="delete" class="submitbutton" style="display:none;">
      <input type="hidden" name="dsave_search_id" id="dsave_search_id" value="">
   </form>
</div>
<div style="clear:both;"></div>
<div>
<form name="searchForm" id="searchForm" method="post" action="">
<div style="width:33%;float:left;">
   <div class="bodytext">
      <div><strong>Electricity - Natural Gas</strong></div>
      <div>
         <select name="electricitynaturalgas[]" id="electricitynaturalgas" multiple="multiple" size="3" class="combo_box electrical_gas_natural">
           <option value="" 
        <?php if(empty($energy_type)) echo "selected"; ?>>
        Any
    </option>

    <option value="1" 
        <?php if(!empty($energy_type) && in_array(1, $energy_type)) echo "selected"; ?>>
        Electricity
    </option>

    <option value="2" 
        <?php if(!empty($energy_type) && in_array(2, $energy_type)) echo "selected"; ?>>
        Natural Gas
    </option>
         </select>
      </div>
      <div>&nbsp;</div>
   </div>
   <div class="bodytext">
      <div><strong>State</strong></div>
      <div>
         <select name="state[]" id="state" multiple="multiple" size="3" class="combo_box">
             <?php 
               $APISTATEURL=RETAIL_DASHBOARD_UAT.'state-list';
               $get_data_state = callAPI('GET', $APISTATEURL, false);
               $response_state = json_decode($get_data_state, true);
               $rows_state_data=$response_state['states'];
               // echo "<pre>";
               // print_r($rows_state_data);
               // echo "</pre>";
               $selectany='selected=selected';
               $o_count_state = count($state);
               $selectany='';
               if ($o_count_state == 0) {
                  $selectany='selected=selected';
               }
               ?>
               <option value="0" <?php echo $selectany; ?>>Any</option>
               <?php 
               foreach($response_state['states'] as $stateData){ 
                  // if(!in_array($stateData['stateID'],$_SESSION['state'])){
                  //    continue;
                  // }
                  ?>
                  <option  <?php if(in_array($stateData['stateID'],$state)) { echo "selected"; } ?> value="<?php echo $stateData['stateID'];?>" ><?php echo htmlspecialchars($stateData['stateName'], ENT_QUOTES); ?></option> 
               <?php 
               }
               ?>
         </select>
      </div>
      <div>&nbsp;</div>
   </div>
   <div class="bodytext">
      <div><strong>EDC / LDC / TDSP</strong></div>
      <div>
         <select name="edc[]" id="edc" multiple="multiple" size="3" class="combo_box edldc_tdsp">
             <?php 
                 $APIEDCURL=RETAIL_DASHBOARD_UAT.'cscanEdc';
                  $get_data = callAPI('GET', $APIEDCURL, false);
                  $response = json_decode($get_data, true);
                  $rows_edc_data=$response['data'];
                  // echo "<pre>";
                  // print_r($rows_edc_data);
                  // echo "</pre>";
                  // die;
                  $selectany='selected=selected';
                  $o_count = count($edc_list_array);
                  $selectany='';
                  if ($o_count == 0) {
                     $selectany='selected=selected';
                  }
                  ?>
                  <option value="0" <?php echo $selectany; ?>>Any</option>
                  <?php 
                  foreach($rows_edc_data as $edcData){ 
                     // if(!in_array($edcData['edc_id'],$_SESSION['sess_sector'])){
                     //    continue;
                     // }
                     ?>
                     <option  <?php if(in_array($edcData['edc_id'],$edc_list_array)) { echo "selected"; } ?> value="<?php echo $edcData['edc_id'];?>" ><?php echo htmlspecialchars($edcData['edc_name'], ENT_QUOTES); ?></option> 
                  <?php 
                  }
                  ?>
         </select>
      </div>
      <div>&nbsp;</div>
   </div>
</div>
<div style="width:33%;float:left;">
   <div class="bodytext">
      <div><strong>Retail Marketer</strong></div>
      <div>
         <!--<ul class="token-input-list">
            <li class="token-input-input-token">
               <input type="text" autocomplete="off" id="token-input-retailmarketer" style="outline: none;">
               <tester style="position: absolute; top: -9999px; left: -9999px; width: auto; font-size: 12px; font-family: Verdana; font-weight: 400; letter-spacing: 0px; white-space: nowrap;"></tester>
            </li>
         </ul>
         <input type="text" class="input_box" size="45" name="retailmarketer" id="retailmarketer" value="" style="display: none;">-->
          <ul class="token-input-list">
            <li class="token-input-input-token">
               <input type="text" id="retailmarketer" name="retailmarketer" autocomplete="off" class="input_box"  style="outline: none;">
               <tester style="position: absolute; top: -9999px; left: -9999px; width: auto; font-size: 12px; font-family: Verdana; font-weight: 400; letter-spacing: 0px; white-space: nowrap;"></tester>
            </li>
         </ul>
        
      </div>
      <div>&nbsp;</div>
   </div>
   <div class="bodytext">
      <div><strong>Product Type</strong></div>
      <div><label>Fixed<input type="checkbox" id="producttype_1" name="producttype[]" value="1"></label> &nbsp; <label>Promotional<input type="checkbox" id="producttype_3" name="producttype[]" value="3"></label> &nbsp; <label>Variable<input type="checkbox" id="producttype_2" name="producttype[]" value="2"></label></div>
      <div>&nbsp;</div>
   </div>
   <div class="bodytext">
      <div><strong>Offer Rate ($)</strong></div>
      <div>
         <div>
            <div style="float:left;margin-right:15px;width:20px;text-align:right;" id="offerrate-slider-range-min">0.00</div>
            <div style="float:left;width:225px;" id="offerrate-slider-range" class="ui-slider ui-slider-horizontal ui-widget ui-widget-content ui-corner-all" aria-disabled="false">
               <div class="ui-slider-range ui-widget-header ui-corner-all" style="left: 0%; width: 100%;"></div>
               <a class="ui-slider-handle ui-state-default ui-corner-all" href="#" style="left: 0%;"></a><a class="ui-slider-handle ui-state-default ui-corner-all" href="#" style="left: 100%;"></a>
            </div>
            <div style="float:left;margin-left:15px;" id="offerrate-slider-range-max">25.00</div>
            <div style="clear:left;height:1px;"></div>
         </div>
         <input type="hidden" name="offerrate" id="offerrate" value="">
      </div>
      <div>&nbsp;</div>
   </div>
</div>
<div style="width:33%;float:left;">
   <div class="bodytext">
      <div><strong>Term</strong></div>
      <div>
         <select name="term[]" id="term" multiple="multiple" size="3" class="combo_box">
            <?php 
               $APIETERMURL=RETAIL_DASHBOARD_UAT.'EtermLength';
               $get_data = callAPI('GET', $APIETERMURL, false);
               $response_term = json_decode($get_data, true);
               // echo "<pre>";
               // print_r($response_term);
               // echo "</pre>";
               // die;
               $rows_term_data=$response_term['data'];
               $selectany='selected=selected';
               $o_count = count($term_list_array);
               $selectany='';
               if ($o_count == 0) {
                  $selectany='selected=selected';
               }
               ?>
               <option value="0" <?php echo $selectany; ?>>Any</option>
               <?php 
               foreach($rows_term_data as $termData){ 
                  // if(!in_array($termData['TermLengthID'],$_SESSION['sess_sector'])){
                  //    continue;
                  // }
                  ?>
                  <option  <?php if(in_array($termData['TermLengthID'],$term_list_array)) { echo "selected"; } ?> value="<?php echo $termData['TermLengthID'];?>" ><?php echo htmlspecialchars($termData['TermLengthName'], ENT_QUOTES); ?></option> 
               <?php 
               }
               ?>
         </select>
      </div>
      <div>&nbsp;</div>
   </div>
   <div class="bodytext">
      <div><strong>% Renewable</strong></div>
      <div>
         <div>
            <div style="float:left;margin-right:15px;width:20px;text-align:right;" id="renewable-slider-range-min">0</div>
            <div style="float:left;width:225px;" id="renewable-slider-range" class="ui-slider ui-slider-horizontal ui-widget ui-widget-content ui-corner-all" aria-disabled="false">
               <div class="ui-slider-range ui-widget-header ui-corner-all" style="left: 0%; width: 100%;"></div>
               <a class="ui-slider-handle ui-state-default ui-corner-all" href="#" style="left: 0%;"></a><a class="ui-slider-handle ui-state-default ui-corner-all" href="#" style="left: 100%;"></a>
            </div>
            <div style="float:left;margin-left:15px;" id="renewable-slider-range-max">100</div>
            <div style="clear:left;height:1px;"></div>
         </div>
         <input type="hidden" name="renewable" id="renewable" value="">
      </div>
      <div>&nbsp;</div>
   </div>
   <div class="bodytext">
      <div><strong>Early Termination Fee ($)</strong></div>
      <div>
         <div>
            <div style="float:left;margin-right:15px;width:20px;text-align:right;" id="earlyterminationfee-slider-range-min">0</div>
            <div style="float:left;width:225px;" id="earlyterminationfee-slider-range" class="ui-slider ui-slider-horizontal ui-widget ui-widget-content ui-corner-all" aria-disabled="false">
               <div class="ui-slider-range ui-widget-header ui-corner-all" style="left: 0%; width: 100%;"></div>
               <a class="ui-slider-handle ui-state-default ui-corner-all" href="#" style="left: 0%;"></a><a class="ui-slider-handle ui-state-default ui-corner-all" href="#" style="left: 100%;"></a>
            </div>
            <div style="float:left;margin-left:15px;" id="earlyterminationfee-slider-range-max">500</div>
            <div style="clear:left;height:1px;"></div>
         </div>
         <input type="hidden" name="earlyterminationfee" id="earlyterminationfee" value="">
      </div>
      <div>&nbsp;</div>
   </div>
   <div class="bodytext">
      <div><strong>Monthly Fee ($)</strong></div>
      <div>
         <div>
            <div style="float:left;margin-right:15px;width:20px;text-align:right;" id="monthlyfee-slider-range-min">0</div>
            <div style="float:left;width:225px;" id="monthlyfee-slider-range" class="ui-slider ui-slider-horizontal ui-widget ui-widget-content ui-corner-all" aria-disabled="false">
               <div class="ui-slider-range ui-widget-header ui-corner-all" style="left: 0%; width: 100%;"></div>
               <a class="ui-slider-handle ui-state-default ui-corner-all" href="#" style="left: 0%;"></a><a class="ui-slider-handle ui-state-default ui-corner-all" href="#" style="left: 100%;"></a>
            </div>
            <div style="float:left;margin-left:15px;" id="monthlyfee-slider-range-max">50</div>
            <div style="clear:left;height:1px;"></div>
         </div>
         <input type="hidden" name="monthlyfee" id="monthlyfee" value="">
      </div>
      <div>&nbsp;</div>
   </div>
</div>
<div class="bodytext" style="clear:left;">
   <div>
      <strong>
         Show 
         <select name="entries" id="entries" size="1" class="combo_box" style="width:100px;">
            <option <?php echo (isset($_REQUEST['entries']) && $_REQUEST['entries'] == 10) ? 'selected' : ''; ?> value="10">10</option>
            <option <?php echo (isset($_REQUEST['entries']) && $_REQUEST['entries'] == 25) ? 'selected' : ''; ?> value="25">25</option>
            <option <?php echo (isset($_REQUEST['entries']) && $_REQUEST['entries'] == 50) ? 'selected' : ''; ?> value="50">50</option>
            <option <?php echo (isset($_REQUEST['entries']) && $_REQUEST['entries'] == 100) ? 'selected' : ''; ?> value="100">100</option>
         </select>
         Entries
      </strong>
   </div>
   <div>&nbsp;</div>
</div>
      <div>
         <input class="submitbutton" type="submit" name="search_button" id="search_button" value="Search"> &nbsp; 
         <input class="submitbutton" type="submit" name="clear_button" id="clear_button" value="Clear Search" onclick="clear_info_container(); return false;">
      </div>
      <input type="hidden" name="start_entries" id="start_entries" value=""><input type="hidden" name="sort_entries" id="sort_entries" value="">
</form>
</div>
<div><a name="info_container_top" style="visibility:hidden;">&nbsp;</a></div>
<div id="info_container222">
        <form name="resultForm" id="resultForm" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" onsubmit="return false;">
            <div style="border:solid 1px #0055E3;">
                <table width="100%" cellpadding="4" cellspacing="0" class="sortable">
                    <thead>
                        <tr>
                            <th class="toptable" nowrap="nowrap">Expand/Collapse</th>
                            <th class="toptable" nowrap="nowrap">
                                Retail Marketer
                                <img src="images/spacer.gif" border="0" style="vertical-align:bottom;" width="15" height="15">
                            </th>
                            <th class="toptable" nowrap="nowrap">
                               Product Type
                                <img src="images/spacer.gif" border="0" style="vertical-align:bottom;" width="15" height="15">
                            </th>
                            <th class="toptable" nowrap="nowrap">
                                Offer Rate ($)
                                <img src="images/spacer.gif" border="0" style="vertical-align:bottom;" width="15" height="15">
                            </th>

                            <th class="toptable" nowrap="nowrap">
                                Term
                                <img src="images/spacer.gif" border="0" style="vertical-align:bottom;" width="15" height="15">
                            </th>
                            <th class="toptable" nowrap="nowrap">
                                % Renewable
                            </th>
                            <th class="toptable" nowrap="nowrap">
                                Unit of Energy
                            </th>
                           <th class="toptable" nowrap="nowrap">
                                EDC / LDC / TDSP
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if($num_of_rows > 0) {
                        if ($response_retail_dashboard_data['total_records'] > 0) {
                            $country_name="";
                            $city_name="";
                            $state_name="";
                            $company1="";
                            $num=0;
                            foreach($response_retail_dashboard_data['Data'] as $row){
                                $product_type=$row['Product_Type'];
                                $Retail_marketer=$row['Retail_Marketer'];
                                $offer_rate=$row['Offer_Rate'];
                                $term=$row['Term'];
                                $unit_of_energy=$row['Unit_of_Energy'];
                                $edc_ldc_tdsp=$row['EDC/LDC/TDSP'];
                                $renewable=$row['Renewable'];
                                $sr_num=$row['Sr.No'];
                                $Electricity_Natural_Gas=$row['Electricity_Natural_Gas'];
                                $State=$row['State'];
                                $Product_Name=$row['Product_Name'];
                                $Early_Termination_Fee=$row['Early_Termination_Fee'];
                                $Early_Termination_Notes=$row['Early_Termination_Notes'];
                                $Monthly_Fee=$row['Monthly_Fee'];
                                $Monthly_Fee_Notes=$row['Monthly_Fee_Notes'];
                                $Notes=$row['Notes'];
                                $Source=$row['Source'];
                                $Price_to_Compare=$row['Price_to_Compare'];
                                $Date=$row['Date'];
                                $number=$num;
                                
                              $num++  
                            ?>
                        <tr>
                            <td class="bodytext" valign="top">
                                <a href="#" onclick="show_result_detail('<?php echo $number;?>'); return false;">
                                    <img name="detail_img_<?php echo $number;?>" id="detail_img_<?php echo $number;?>" src="images/plus.jpg" border="0"></a>
                            </td>
                            <td class="bodytext" valign="top"><?php echo $Retail_marketer; ?></td>
                            <td class="bodytext" valign="top"><?php if($product_type!=""){echo $product_type;}else{ echo "NA";} ?></td>
                            <td class="bodytext" valign="top"><?php echo $offer_rate;?></td>
                            <td class="bodytext" valign="top"><?php echo $term; ?></td>
                            <td class="bodytext" valign="top"><?php echo $renewable; ?></td>
                            <td class="bodytext" valign="top"><?php echo $unit_of_energy; ?></td>
                            <td class="bodytext" valign="top"><?php echo $edc_ldc_tdsp; ?></td>

                        </tr>
                        <tr style="display:none;" id="detail_<?php echo $number;?>">
                            <td colspan="9" class="bodytext" valign="top">
                                <div><b>Sr. No</b>: <?php if($sr_num!=""){echo $sr_num;}else{ echo "NA";} ?></div>
                                <div><b>Electricity - Natural Gas</b>: <?php if($country_name!=""){echo strtoupper($country_name); }else{ echo "NA";} ?> </div>
                                <div><b>State</b>: <?php if($State!=""){echo $State; }else{ echo "NA";} ?> </div>
                                <div><b>Product Name</b>: <?php if($Product_Name!=""){echo $Product_Name; }else{ echo "NA";} ?> </div>
                                <div><b>Date</b>: <?php if($Date!=""){echo $Date;}else{echo "NA";} ?></div>
                                <div><b>Early Termination Fee ($)</b>: <?php if($Early_Termination_Fee!=""){echo $Early_Termination_Fee;}else{echo "NA";} ?></div>
                                <div><b>Early Termination Notes</b>:  <?php if($Early_Termination_Notes!=""){echo $Early_Termination_Notes;}else{echo "NA";} ?></a></div>
                                <div><b>Monthly Fee ($)</b>: <?php if($Monthly_Fee!=""){echo $Monthly_Fee;}else{echo "NA";} ?></div>
                                <div><b>Monthly Fee Notes</b>: <?php if($Monthly_Fee_Notes!=""){echo $Monthly_Fee_Notes;}else{ echo "NA";} ?></div>
                                <div><b>Notes</b>: <?php if($Notes!=""){echo $Notes;}else{ echo "NA";} ?></div>
                                <div><b>Source</b>: <?php if($Source!=""){echo $Source;}else{ echo "NA";} ?></div>
                                <div><b>Price to Compare</b>: <?php if($Price_to_Compare!=""){echo $Price_to_Compare;}else{ echo "NA";} ?></div>

                            </td>
                        </tr>
                        
                    <?php }}}else { ?>
                        <tr>
                            <td colspan="8" class="bodytext" valign="top" align="center">
                                <span class="error" > No Record Found!</span>
                            </td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
                   
                <div style="float:right; font-style:italic; font-size:smaller;">
                  Duplicate products may display but were observed from unique sources
               </div>     
            </div>
                <div>
		           <div class="error" style="float:left;"></div>
                 <div class="bodytext" style="float:right;font-style:italic;font-size:smaller;"></div>
                 <?php if($num_of_rows > $pagelimit){ ?>
                 <table border="0" width="100%" cellspacing = "0"  cellpadding ="4">
                    <tr><td>&nbsp;</td></tr>
                    <?php
                    $firstlink = '[First]';
                    $prevlink = '[Prev]';
                    $nextlink = '[Next]';
                    $lastlink = '[Last]';
                    $middlelinks = '';
                    $limstart = $p;
                    $limiter = $pagelimit;
                    $rowcnt = $num_of_rows;
                    $show = 10;
                    
                    //first and previous only if not on first
                    if ($limstart > 0) {
                        if ($limstart >= $limiter)
                            $prev = $limstart - $limiter;
                        else
                            $prev = 0;
                        $firstlink = "[<a href=\"{$_SERVER['PHP_SELF']}?p=0\">First</a>]";
                        $prevlink = "<a href=\"{$_SERVER['PHP_SELF']}?p=$prev\">&laquo; Prev $limiter</a>";
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
                            $middlelinks .= "<a href=\"{$_SERVER['PHP_SELF']}?p=$startnum\">" . ($i + 1) . "</a> ";
                        } else
                            $middlelinks .= ($i + 1) . ' ';
                    }
                    //next and last if not on last
                    if ($limstart < $rowcnt && (($limstart + ($limiter * 2)) < $rowcnt || ($rowcnt - ($limstart + $limiter)) > 0)) {
                        $next = $limstart + $limiter;
                        $nextlink = "<a href=\"{$_SERVER['PHP_SELF']}?p=$next\">Next $limiter &raquo;</a>";
                        $lastlink = "[<a href=\"{$_SERVER['PHP_SELF']}?p=" . (($numbers - 1) * $limiter) . "\">Last</a>]";
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
                 <?php } ?>
                </div>
            
        </form>
    </div>
<div>&nbsp;</div>
<div>
   <form name="outputForm" id="outputForm" method="post" action="/dashboard_test.php" onsubmit="return false;">
	  <input class="submitbutton" type="submit" name="csv_button" id="csv_button" value="Export All To CSV" onclick="show_csv(); return false;"> &nbsp; 
	  <input class="submitbutton" type="submit" name="top_button1" id="top_button1" value="Top" onclick="move_page_top(); return false;">
	</form>
	<div>
   <!--<form name="buttonForm" id="buttonForm" method="post" action="/dashboard.php" onsubmit="return false;"><input class="submitbutton" type="submit" name="hide_button" id="hide_button" value="Hide Graph" onclick="hideRangeVisualization(); return false;"> &nbsp; <input class="submitbutton" type="submit" name="top_button2" id="top_button2" value="Top" onclick="move_page_top(); return false;"></form>-->
</div>
</div>	
<div id="waitdiv" style="display: none;" class="">
   <img name="wait" id="wait" src="images/searching.gif" border="0" style="display:block;margin-left:auto;margin-right:auto;margin-top:50px;">
</div>
</div>
<script type="text/javascript">
   $(function() {
      $( "#offerrate-slider-range" ).slider({
         range: true,
         step: 0.01,
         min: 0,
         max: 25,
         values: [ 0.00, 25.00 ],
         change: function( event, ui ) {
            var step_val = $( "#offerrate-slider-range" ).slider( "option", "step" );
            var val1 = ui.values[ 0 ];
            var val2 = ui.values[ 1 ];
            if(step_val<1 && typeof ui.values[ 0 ] === "number"){
               val1 = val1.toFixed(2);
               val2 = val2.toFixed(2);
            }
            $( "#offerrate-slider-range-min" ).html( val1 );
            $( "#offerrate-slider-range-max" ).html( val2 );
            $( "#offerrate" ).val( ui.values[ 0 ] + "-" + ui.values[ 1 ] );
         },
         slide: function( event, ui ) {
            var step_val = $( "#offerrate-slider-range" ).slider( "option", "step" );
            var val1 = ui.values[ 0 ];
            var val2 = ui.values[ 1 ];
            if(step_val<1 && typeof ui.values[ 0 ] === "number"){
               val1 = val1.toFixed(2);
               val2 = val2.toFixed(2);
            }
            $( "#offerrate-slider-range-min" ).html( val1 );
            $( "#offerrate-slider-range-max" ).html( val2 );
         }
      });
   });
</script>
<script type="text/javascript">
   $(function() {
      $( "#monthlyfee-slider-range" ).slider({
         range: true,
         step: 1,
         min: 0,
         max: 50,
         values: [ 0, 50 ],
         change: function( event, ui ) {
            var step_val = $( "#monthlyfee-slider-range" ).slider( "option", "step" );
            var val1 = ui.values[ 0 ];
            var val2 = ui.values[ 1 ];
            if(step_val<1 && typeof ui.values[ 0 ] === "number"){
               val1 = val1.toFixed(2);
               val2 = val2.toFixed(2);
            }
            $( "#monthlyfee-slider-range-min" ).html( val1 );
            $( "#monthlyfee-slider-range-max" ).html( val2 );
            $( "#monthlyfee" ).val( ui.values[ 0 ] + "-" + ui.values[ 1 ] );
         },
         slide: function( event, ui ) {
            var step_val = $( "#monthlyfee-slider-range" ).slider( "option", "step" );
            var val1 = ui.values[ 0 ];
            var val2 = ui.values[ 1 ];
            if(step_val<1 && typeof ui.values[ 0 ] === "number"){
               val1 = val1.toFixed(2);
               val2 = val2.toFixed(2);
            }
            $( "#monthlyfee-slider-range-min" ).html( val1 );
            $( "#monthlyfee-slider-range-max" ).html( val2 );
         }
      });
   });
</script>
<script type="text/javascript">
$(function() {
   $( "#earlyterminationfee-slider-range" ).slider({
      range: true,
      step: 1,
      min: 0,
      max: 500,
      values: [ 0, 500 ],
      change: function( event, ui ) {
         var step_val = $( "#earlyterminationfee-slider-range" ).slider( "option", "step" );
         var val1 = ui.values[ 0 ];
         var val2 = ui.values[ 1 ];
         if(step_val<1 && typeof ui.values[ 0 ] === "number"){
            val1 = val1.toFixed(2);
            val2 = val2.toFixed(2);
         }
         $( "#earlyterminationfee-slider-range-min" ).html( val1 );
         $( "#earlyterminationfee-slider-range-max" ).html( val2 );
         $( "#earlyterminationfee" ).val( ui.values[ 0 ] + "-" + ui.values[ 1 ] );
      },
      slide: function( event, ui ) {
         var step_val = $( "#earlyterminationfee-slider-range" ).slider( "option", "step" );
         var val1 = ui.values[ 0 ];
         var val2 = ui.values[ 1 ];
         if(step_val<1 && typeof ui.values[ 0 ] === "number"){
            val1 = val1.toFixed(2);
            val2 = val2.toFixed(2);
         }
         $( "#earlyterminationfee-slider-range-min" ).html( val1 );
         $( "#earlyterminationfee-slider-range-max" ).html( val2 );
      }
   });
});
</script>
<script type="text/javascript">
   $(function() {
      $( "#renewable-slider-range" ).slider({
         range: true,
         step: 1,
         min: 0,
         max: 100,
         values: [ 0, 100 ],
         change: function( event, ui ) {
            var step_val = $( "#renewable-slider-range" ).slider( "option", "step" );
            var val1 = ui.values[ 0 ];
            var val2 = ui.values[ 1 ];
            if(step_val<1 && typeof ui.values[ 0 ] === "number"){
               val1 = val1.toFixed(2);
               val2 = val2.toFixed(2);
            }
            $( "#renewable-slider-range-min" ).html( val1 );
            $( "#renewable-slider-range-max" ).html( val2 );
            $( "#renewable" ).val( ui.values[ 0 ] + "-" + ui.values[ 1 ] );
         },
         slide: function( event, ui ) {
            var step_val = $( "#renewable-slider-range" ).slider( "option", "step" );
            var val1 = ui.values[ 0 ];
            var val2 = ui.values[ 1 ];
            if(step_val<1 && typeof ui.values[ 0 ] === "number"){
               val1 = val1.toFixed(2);
               val2 = val2.toFixed(2);
            }
            $( "#renewable-slider-range-min" ).html( val1 );
            $( "#renewable-slider-range-max" ).html( val2 );
         }
      });
   });
</script>
<script>
function show_result_detail(number) {
	var element = $("#detail_"+number);
	var element2 = $("#detail_img_"+number);
	if(element && element2){
		if(element.css("display")=="none") { 
			element.css("display","table-row");
			element2.attr("src","images/minus.jpg");
		} 
		else { 
			element.css("display","none"); 
			element2.attr("src","images/plus.jpg");
		}
	}
}
</script>

<script>
$(document).ready(function(){
   $("#retailmarketer").tokenInput("ajax-get-retail-dashboard.php", {
      //theme: "facebook",
      preventDuplicates: true,
      hintText: "Search company",
      noResultsText: "No company found",
      searchingText: "Searching...",
      tokenLimit: 10

   });
});

$(".electrical_gas_natural").on("change", function() {

    var selectedValues = $(this).val();

    if (selectedValues && (selectedValues.includes("1") || selectedValues.includes("2"))) {

        $.ajax({
            url: "ajax-get-retail-dashboard.php",
            type: "POST",
            data: { energy_type: selectedValues },

            success: function(response) {

                $(".edldc_tdsp").html(response);

            },
            error: function() {
                alert("Error loading EDC data");
            }
        });

    } else {

        //$(".edldc_tdsp").html('<option value="">Any</option>');
    }

});
</script>
<?php
include 'footer_bottom.php';
?>