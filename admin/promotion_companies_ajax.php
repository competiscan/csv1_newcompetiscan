<?php
//$ALLOW_GROUPS = array(6);
require_once("../auth_auth.php");
require_once '../includes/functions.php';
if(isset($GLOBALS['AUTH_DATA']['userID'])){
    $sess_admin_userId=$GLOBALS['AUTH_DATA']['userID'];
    $sessionuniqueid=session_id();
}else{
    $sess_admin_userId="";
    $sessionuniqueid='';
}

$html_company='';
$maxCompnayCount=10;
$holiday_id='';
$sale_type_id='';
$isallreadysaved="0";
$tempid='0';
$istemp='0';
$countnum='0';
$istemp_value='0';
if(isset($_REQUEST['companyid']) && $_REQUEST['action']=='getprocompany') { 
   $lastSubCatIdArray=$_REQUEST['lastSubCatArray'];
   //print_r($lastSubCatArray); die;
   $company_id=$_REQUEST['companyid'];
   $formtemp=$_REQUEST['formtemp'];
   $disable=$_REQUEST['disable'];
   //$disable='';
   $link_disable='';
   if($disable!=''){
       $link_disable='style="pointer-events: none;"';
   }
   $styleAdd='';
   if($formtemp==1){
       $styleAdd="bluelink";
   }
   //###########Start Edit promotion#######
   if(isset($_REQUEST['pid']) && $_REQUEST['pid']!=''){
    $productID= $_REQUEST['pid'];  
   }else{
     $productID='0';
     
   } 
   $table=" cscan_promotions";
   $table_other="cscan_promotion_other_fields";
   $sqlQueryWhere=" productID='".$productID."' ";
   $sqlOtherQueryWhere=" productID='".$productID."'";
   if($formtemp==1 && $productID!=0 && $productID!=''){
       $table="cscan_promotions_temp";
       $table_other="cscan_promotion_other_fields_temp";
       //$sqlQueryWhere=" muid='".$productID."' AND formtemp='1' AND userID='" . $sess_admin_userId . "' AND session_id='" . $sessionuniqueid . "'";
       $sqlQueryWhere=" muid='".$productID."' AND formtemp='1'";
       $sqlOtherQueryWhere=" muid='".$productID."'";
   }
   if (!empty($company_id) && $company_id!='' && !empty($lastSubCatIdArray) && $lastSubCatIdArray!='') {
        $strcompany_id = implode(',', $company_id);
        $last_sub_cat_id = implode(',', $lastSubCatIdArray);
        if($productID!='' && $productID!='0'){
            $sqlpromotionOther = "SELECT holiday_id,sale_type_id FROM $table_other WHERE $sqlOtherQueryWhere";
            $resultpromotionOther = $DRW->query($sqlpromotionOther,$DRW_read);
            $rowDataOther = $DRW->fetch_array($resultpromotionOther);
            $holiday_id=$rowDataOther['holiday_id'];
            $sale_type_id=$rowDataOther['sale_type_id'];
            $sqlpromotion = "SELECT count(*) as cmpcount,companyID,categoryID FROM $table WHERE $sqlQueryWhere and companyID IN ($strcompany_id) AND categoryID IN ($last_sub_cat_id) GROUP by companyID,categoryID";
            $resultpromotion = $DRW->query($sqlpromotion,$DRW_read);
            $html_company='<div style="padding-left:160px;" id="display_prompotion_company">';
            $dispalystr=array();
             while($rowdataComp = $DRW->fetch_array($resultpromotion)){
                 $sqlComp = "SELECT companyName FROM cscan_company WHERE companyID ='".$rowdataComp['companyID']."'";
                 $result_query = $DRW->query($sqlComp,$DRW_read);
                 $row_data = $DRW->fetch_array($result_query);
                 $tempCompanyName=$row_data['companyName'];
                 $sql_product_type = "SELECT sectorID, sectorName FROM cscan_sector WHERE sectorID ='".$rowdataComp['categoryID']."'";
                 $query_product_type = $DRW->query($sql_product_type,$DRW_read);
                 $row_data_product_type = $DRW->fetch_array($query_product_type);
                 $tempSectorName=$row_data_product_type['sectorName'];
                 $dispalystr[]='<a '.$link_disable.' class="'.$styleAdd.'" href="javascript:void(0);" onclick="displayPromotionselectdCompany('.$rowdataComp['companyID'].','.$rowdataComp['categoryID'].','.$productID.','.$formtemp.');">'.$tempCompanyName.' / '.$tempSectorName.' ('.$rowdataComp['cmpcount'].')</a>';
                 }
                 $displaycompCount=implode(', ', $dispalystr);
                 $html_company.=$displaycompCount;
       }
       $html_company.='</div>';
       //###########End Edit promotion##########
            $defaultvalue='0';
            $sql = "SELECT companyID, companyName FROM cscan_company WHERE companyID IN ($strcompany_id) ORDER BY companyName";
            $result = $DRW->query($sql,$DRW_read);
            if ($DRW->num_rows($result) > 0) {
                $html_company.='<div id="div_promotion_266_select" style="overflow:hidden;"><table border="0" width="100%" cellpadding="5" cellspacing="0"><tbody><tr><td class="bodytext" align="right" valign="top" width="30%"> Promotion Company:</td><td class="bodytext" valign="top" width="70%"><select '.$disable.' onchange="addcompanypromotionfield('.$defaultvalue.','.$tempid.','.$countnum.','.$productID.','.$istemp.','.$formtemp.');" id="selected_promotion_company" class="combo_box selected_promotion_company" name="selected_promotion_company"><option value="0" selected="selected">--Select One--</option>';
                while ($row = $DRW->fetch_array($result)) {
                    $html_company.='<option value="'.$row['companyID'].'"'; 
                    if($company_id == $row['companyID']) {
                      $html_company.= " selected=\"selected\"";
                    }
                    $html_company.='>'.htmlspecialchars($row['companyName']).'</option>'; 
                    
                }
            }       

        $html_company.='</select></td></tr></tbody></table></div>';
        
        
        $sql_subcat = "SELECT sectorID, sectorName FROM cscan_sector WHERE sectorID IN ($last_sub_cat_id) ORDER BY sectorName";
            $result_subcat = $DRW->query($sql_subcat,$DRW_read);
            if ($DRW->num_rows($result_subcat) > 0) {
                $html_company.='<div id="div_promotion_product_type" style="overflow:hidden;"><table border="0" width="100%" cellpadding="5" cellspacing="0"><tbody><tr><td class="bodytext" align="right" valign="top" width="30%"> Product Type:</td><td class="bodytext" valign="top" width="70%"><select '.$disable.' onchange="addcompanypromotionfield(this.value,'.$tempid.','.$countnum.','.$productID.','.$istemp.','.$formtemp.');" id="selected_product_type" class="combo_box selected_product_type" name="selected_product_type"><option value="0" selected="selected">--Select One--</option>';
                while ($rowsubcat = $DRW->fetch_array($result_subcat)) {
                    $html_company.='<option value="'.$rowsubcat['sectorID'].'"'; 
                    /*if($company_id == $row['sectorID']) {
                      $html_company.= " selected=\"selected\"";
                    }*/
                    $html_company.='>'.htmlspecialchars($rowsubcat['sectorName']).'</option>'; 
                    
                }
            }       

        $html_company.='</select></td></tr></tbody></table></div>';
        $html_company.='<div id="div_promotion_266">';

        $html_company.='<div id="div_promotion_append"></div><div id="div_promotion_show_alert"></div></div>';
        $html_company.='<div id="div_promotion_holiday_266" style="overflow:hidden;"><table border="0" width="100%" cellpadding="5" cellspacing="0"><tbody><tr><td class="bodytext" align="right" valign="top" width="30%"> Holiday:</td><td class="bodytext" valign="top" width="70%"><select '.$disable.' class="combo_box" name="holiday"><option value="0"> -- Select One -- </option>';
        $holidays = getPromotionHolidays();
        foreach($holidays as $id=>$name ) {
           $html_company.='<option value="'.$id.'"'; 
            if($holiday_id == $id) {
              $html_company.= " selected=\"selected\"";
            }
            $html_company.='>'.htmlspecialchars($name).'</option>';  
        }
        $html_company.='</select></td></tr></tbody></table></div>';
        $html_company.='<div id="div_promotion_sale_type_266" style="overflow:hidden;"><table border="0" width="100%" cellpadding="5" cellspacing="0"><tbody><tr><td class="bodytext" align="right" valign="top" width="30%"> Sale Type:</td><td class="bodytext" valign="top" width="70%"><select '.$disable.' class="combo_box" name="sale_type"><option value="0"> -- Select One -- </option>';
        $saleType = getPromotionSaleType();
        foreach($saleType as $id=>$name ) {
            $html_company.='<option value="'.$id.'"'; 
            if($sale_type_id == $id) {
              $html_company.= " selected=\"selected\"";
            }
            $html_company.='>'.htmlspecialchars($name).'</option>';   
        }
        $html_company.='</select></td></tr></tbody></table><table border="0" width="100%" cellpadding="5" cellspacing="0"><tbody><tr><td colspan="2">&nbsp;</td></tr></tbody></table></div>';
        echo $html_company;exit;
    } 
}
$html='';
if(isset($_REQUEST['companyid']) && !empty($_REQUEST['lstsubcatid']) && $_REQUEST['action']=='getaddpromotionfield') { 
    $company_id=$_REQUEST['companyid'];
    $lstsubcatid=$_REQUEST['lstsubcatid'];
    $tempid=$_REQUEST['tempid'];
    if($tempid==''){
      $tempid='0';  
    }
    $countnum=$_REQUEST['countnum'];
    if($_REQUEST['isallreadysaved']!=''){
     $isallreadysaved=$_REQUEST['isallreadysaved'];
    }
    $istemp_value=$_REQUEST['istemp'];
    if($istemp_value==''){
        $istemp_value='0';
    }
    $remove_id=$_REQUEST['remove_id'];
    $formtemp=$_REQUEST['formtemp'];
    $styleAdd='';
    if($formtemp==1){
        $styleAdd="bluelink";
    }
    $sqlQueryWhere=" AND muid='0' AND formtemp='0' AND userID='".$sess_admin_userId."' AND session_id='".$sessionuniqueid."'";
    if($formtemp==1){
        $sqlQueryWhere=" AND muid='0' AND formtemp='1' AND userID='".$sess_admin_userId."' AND session_id='".$sessionuniqueid."'";
    }/*elseif($formtemp==1 && $isallreadysaved!='0' && $isallreadysaved!=''){
        $sqlQueryWhere=" AND formtemp='1' AND muid='".$isallreadysaved."' ";
    }*/
    
    $html= '<table><tr><td><div style="float:left;padding-left: 158px;" id="div_promotion_show_company">';
    $sqltemp = "SELECT id,categoryID,companyID FROM cscan_promotions_temp WHERE categoryID='".$lstsubcatid."' AND companyID='".$company_id."'$sqlQueryWhere "; 
    $resulttemp = $DRW->query($sqltemp,$DRW_read);
    $tempArray=array();
    $mainArray=array();
    if ($DRW->num_rows($resulttemp) > 0) {
        
       while ($rowTempData = $DRW->fetch_assoc($resulttemp)) {
          $tempArray[]=$rowTempData; 
       }  
    }
    $remove_temp_promo_query='';
    if($isallreadysaved!='0' && $isallreadysaved!='' && $formtemp==0){
        
        if($remove_id!='' && $remove_id!='0'){
            $remove_temp_promo_query=" AND id NOT IN ($remove_id)";
        }
        $sql_main = "SELECT id,categoryID,companyID,productID FROM cscan_promotions WHERE productID='".$isallreadysaved."' AND categoryID='".$lstsubcatid."' And companyID='".$company_id."' $remove_temp_promo_query "; 
        $result_main = $DRW->query($sql_main,$DRW_read);
        if ($DRW->num_rows($result_main) > 0) {

           while ($rowMainData = $DRW->fetch_assoc($result_main)) {
              $mainArray[]=$rowMainData; 
           }  
        }
    }
    if($isallreadysaved!='0' && $isallreadysaved!='' && $formtemp==1){
        
        if($remove_id!='' && $remove_id!='0'){
            $remove_temp_promo_query=" AND id NOT IN ($remove_id)";
        }
        $sql_main = "SELECT id,categoryID,companyID,muid FROM cscan_promotions_temp WHERE muid='".$isallreadysaved."' AND formtemp='1' AND categoryID='".$lstsubcatid."' And companyID='".$company_id."' $remove_temp_promo_query ";
        $result_main = $DRW->query($sql_main,$DRW_read);
        if ($DRW->num_rows($result_main) > 0) {

           while ($rowMainData = $DRW->fetch_assoc($result_main)) {
              $mainArray[]=$rowMainData; 
           }  
        }
    }
    
    
    $allmerge_array=array_merge($mainArray,$tempArray);
    //echo "<pre>";
    //print_r($allmerge_array);
    //echo "<pre>";
    $str=array();
    $num=1;
      
    for($cnt=0;$cnt<count($allmerge_array); $cnt++){
       //echo $num;
       $id=$allmerge_array[$cnt]['id'];
       $companyID=$allmerge_array[$cnt]['companyID'];
       $productID='0';
       $istemp=0;
       if((isset($allmerge_array[$cnt]['productID']) && $allmerge_array[$cnt]['productID']!='') || (isset($allmerge_array[$cnt]['muid']) && $allmerge_array[$cnt]['muid']!='')){
        //$productID=$allmerge_array[$cnt]['productID'];
        $istemp=1;
       }
       $commastr='';
       if($num > 1){
         $commastr='<span id="remove_comma_'.$id.'">,</span> ';  
       }
       $str[]=$commastr.'<a class="'.$styleAdd.'" href="javascript:void(0);" id="remove_'.$id.'" onclick="addcompanypromotionfield('.$lstsubcatid.','.$id.','.$num.','.$isallreadysaved.','.$istemp.','.$formtemp.'); return false;"><b>Promotion #'.$num.'</b></a>';
    $num++;
    }
    $str1=implode('', $str);
    $html.=trim($str1);
    $html.= '</div></td></tr></table>';
    $uniantable1='';
    if($isallreadysaved!='0' && $isallreadysaved!='' && $formtemp==0){
     $uniantable1 = " UNION SELECT id FROM cscan_promotions WHERE productID='".$isallreadysaved."' AND categoryID='".$lstsubcatid."' And companyID='".$company_id."' $remove_temp_promo_query ";   
    }
    if($isallreadysaved!='0' && $isallreadysaved!='' && $formtemp==1){
     $uniantable1 = " UNION SELECT id FROM cscan_promotions_temp WHERE muid='".$isallreadysaved."' AND formtemp='1' AND categoryID='".$lstsubcatid."' And companyID='".$company_id."' $remove_temp_promo_query ";   
    }
    $sqltempCount = "SELECT id FROM cscan_promotions_temp WHERE categoryID='".$lstsubcatid."' AND companyID='".$company_id."'$sqlQueryWhere $uniantable1"; 
    $resulttempCount = $DRW->query($sqltempCount,$DRW_read);
    $rowdatacount=$DRW->num_rows($resulttempCount);
    $rowcompcount=$rowdatacount+1;
    $promotion_type_id='';
    $coupan_discount_value='';
    $ad_price='';
    $regular_price='';
    $shipping_detail_id='';
    $online_in_store_id='';
    $qualifier_id='';
    $qualifier_min_purchase_value='';
    $code_required='';
    $bogo='';
    $bogo_buy_value='';
    $bogo_get_value='';
    $sqltemp_edit='';
    $remove_link="display:none;";
    if($tempid!="" && $tempid!='0'){
      $remove_link="display:block;";
      $rowcompcount= $countnum; 
      if($isallreadysaved!='0' && $formtemp==0){
       $sqltemp_edit = "SELECT * FROM cscan_promotions WHERE productID='".$isallreadysaved."' AND categoryID='".$lstsubcatid."' And companyID='".$company_id."' AND id ='".$tempid."'";
      }
      elseif($isallreadysaved!='0' && $formtemp==1){
       $sqltemp_edit = "SELECT * FROM cscan_promotions_temp WHERE muid='".$isallreadysaved."' AND formtemp='1' AND categoryID='".$lstsubcatid."' And companyID='".$company_id."' AND id ='".$tempid."'";
      }elseif($formtemp==1 && ($isallreadysaved==0 || $isallreadysaved=='')){
         $sqltemp_edit = "SELECT * FROM cscan_promotions_temp WHERE categoryID='".$lstsubcatid."' AND companyID='".$company_id."' AND id ='".$tempid."'$sqlQueryWhere"; 
      }
      //echo $sqltemp_edit;
       $result_temp = $DRW->query($sqltemp_edit,$DRW_read);
       
       $checktemp=$DRW->num_rows($result_temp);
       if($checktemp >0){
           //echo "okkkk";
           $rowshow_data = $DRW->fetch_array($result_temp); 
       }else{
         $sqlt_main_table = "SELECT * FROM cscan_promotions_temp WHERE  categoryID='".$lstsubcatid."' AND companyID='".$company_id."' AND id ='".$tempid."'$sqlQueryWhere";  
         $result_main_table = $DRW->query($sqlt_main_table,$DRW_read);
         $rowshow_data = $DRW->fetch_array($result_main_table);
         
       }
    
      $promotion_type_id=$rowshow_data['promotion_type_id'];
      $coupan_discount_value=$rowshow_data['coupan_discount_value'];
      $ad_price=$rowshow_data['ad_price'];
      $regular_price=$rowshow_data['regular_price'];
      $shipping_detail_id=$rowshow_data['shipping_detail_id'];
      $online_in_store_id=$rowshow_data['online_in_store_id'];
      $qualifier_id=$rowshow_data['qualifier_id'];
      $qualifier_min_purchase_value=$rowshow_data['qualifier_min_purchase_value'];
      $code_required=$rowshow_data['code_required'];
      $bogo=$rowshow_data['bogo'];
      $bogo_buy_value=$rowshow_data['bogo_buy_value'];
      $bogo_get_value=$rowshow_data['bogo_get_value'];
      //echo "<pre>";
     // print_r($rowshow_data);
      //echo "<pre>"; die;
      
    }
    if($rowcompcount<=$maxCompnayCount){
        $sqlComp = "SELECT companyID,companyName FROM cscan_company WHERE companyID ='".$company_id."' ORDER BY companyName"; 
        $result_query = $DRW->query($sqlComp,$DRW_read);
        $row_data = $DRW->fetch_array($result_query);
        $companyName=$row_data['companyName'];
        $companyID=$row_data['companyID'];
        $sqlsector = "SELECT sectorID,sectorName FROM cscan_sector WHERE sectorID='".$lstsubcatid."' ORDER BY sectorName";
        $result_query_sector = $DRW->query($sqlsector,$DRW_read);
        $row_data_sector = $DRW->fetch_array($result_query_sector);
        $sectorName=$row_data_sector['sectorName'];
        
        if ($DRW->num_rows($result_query) > 0) {
            $html.='<div class="div_promotion_hide" style=""><input type="hidden" id ="count_promotion_no" name="count_promotion_no" value="'.$rowcompcount.'"/>';   
            $html .= '<table border="0" width="100%" cellpadding="5" cellspacing="0">'
               . '<tbody>'
               . '<tr>'
               . '<td class="bodytext" align="right" valign="top" width="30%"></td>'
               . '<td class="bodytext" style="" align="right" valign="top" width="70%"><a onclick="RemovePromotion('.$tempid.','.$isallreadysaved.','.$istemp_value.','.$formtemp.');" class="'.$styleAdd.'" style="margin-right: 200px;'.$remove_link.'" href="javascript:void(0);"><b>Remove Poromotion</b></a></td>'
               . '</tr>'
               . '<tr>'
               . '<td class="bodytext" align="right" valign="top" width="30%"><b>Promotion #'.$rowcompcount.':</b></td>'
               . '<td class="bodytext" valign="top" width="70%"><b>'.$companyName.'</b></td>'
               . '</tr>'
               . '<tr>'
               . '<td class="bodytext" align="right" valign="top" width="30%"><b>Product Type:</b></td>'
               . '<td class="bodytext" valign="top" width="70%"><b>'.$sectorName.'</b></td>'
               . '</tr>'
               . '<tr>'
               . '<td class="bodytext" align="right" valign="top" width="30%"> Promotion Type:</td>'
               . '<td class="bodytext" valign="top" width="70%"><select id="promotion_type" class="combo_box" name="promotion_type"><option value="0">--Select One--</option>';
                $promotionType = getPromotionType();
                foreach($promotionType as $id=>$name ) {
                    $html.='<option value="'.$id.'"'; 
                    if($promotion_type_id == $id) {
                      $html.= " selected=\"selected\"";
                    }
                    $html.='>'.htmlspecialchars($name).'</option>';   
                }
            $html.='</select></td></tr>'
               . '<tr>'
               . '<td class="bodytext" align="right" valign="top" width="30%"> Coupon/Discount Value:</td>'
               . '<td class="bodytext" valign="top" width="70%"><div style="clear:left;"><input type="text" id="coupon_discount_value" name="coupon_discount_value" size="10" maxlength="100" class="input_box" value="'.$coupan_discount_value.'" /></div></td>'
               . '</tr>'
               . '<tr>'
               . '<td class="bodytext" align="right" valign="top" width="30%"> Ad Price($):</td>'
               . '<td class="bodytext" valign="top" width="70%"><div style="clear:left;"><input type="text" id="add_price" name="add_price" size="10" maxlength="100" class="input_box" value="'.$ad_price.'" /></div></td>'
               . '</tr>'
               . '<tr>'
               . '<td class="bodytext" align="right" valign="top" width="30%"> Regular Price ($):</td>'
               . '<td class="bodytext" valign="top" width="70%"><div style="clear:left;"><div style="clear:left;"><input type="text" id="regular_price" name="regular_price" size="10" maxlength="100" class="input_box" value="'.$regular_price.'" /></div></td>'
               . '</tr>'
               . '<tr>'
               . '<td class="bodytext" align="right" valign="top" width="30%"> Shipping Detail:</td>'
               . '<td class="bodytext" valign="top" width="70%"><div style="clear:left;"><select class="combo_box" id="shipping_detail" name="shipping_detail"><option value="o">--Select One--</option>';
               $shippingDetails = getPromotionShippingDetail();
                foreach($shippingDetails as $id=>$name ) {
                    $html.='<option value="'.$id.'"'; 
                    if($shipping_detail_id == $id) {
                      $html.= " selected=\"selected\"";
                    }
                    $html.='>'.htmlspecialchars($name).'</option>'; 
                }
            $html.='</select></td></tr>'
               . '<tr>'
               . '<td class="bodytext" align="right" valign="top" width="30%"> Online/In-Store:</td>'
               . '<td class="bodytext" valign="top" width="70%"><div style="clear:left;"><select class="combo_box" id="online_in_store" name="online_in_store"><option value="0">--Select One--</option>';
                $onlineStore = getPromotionOnlineIStore();
                foreach($onlineStore as $id=>$name ) {
                    $html.='<option value="'.$id.'"'; 
                    if($online_in_store_id == $id) {
                      $html.= " selected=\"selected\"";
                    }
                    $html.='>'.htmlspecialchars($name).'</option>';  
                }
            $html.='</select></td></tr>'
               . '<tr>'
               . '<td class="bodytext" align="right" valign="top" width="30%"> Qualifier:</td>'
               . '<td class="bodytext" valign="top" width="70%"><div style="clear:left;"><select class="combo_box" id="qualifier" name="qualifier"><option>--Select One--</option>';
               $qualifiers = getPromotionQualifiers();
                foreach($qualifiers as $id=>$name ) {
                    $html.='<option value="'.$id.'"'; 
                    if($qualifier_id == $id) {
                      $html.= " selected=\"selected\"";
                    }
                    $html.='>'.htmlspecialchars($name).'</option>';  
                }
            $html.='</select></td></tr>'
               . '<tr>'
               . '<td class="bodytext" align="right" valign="top" width="30%"> Qualifier (Minimum Purchase Value $):</td>'
               . '<td class="bodytext" valign="top" width="70%"><div style="clear:left;"><div style="clear:left;"><input type="text" id="qualifier_minimum_purchase_value" name="qualifier_minimum_purchase_value" size="10" maxlength="100" class="input_box" value="'.$qualifier_min_purchase_value.'" /></div></td>'
               . '</tr>'
               . '<tr>'
               . '<td class="bodytext" align="right" valign="top" width="30%"> Code Required:</td>'
               . '<td class="bodytext" valign="top" width="70%"><div style="clear:left;"><select class="combo_box" id="code_required" name="code_required"><option value="0"> --Select One-- </option>';
            $html.='<option value="1"';
                if($code_required!='' && $code_required=='1'){
                    $html.= " selected=\"selected\"";
                }
            $html.='>Yes</option>';
            $html.='<option value="2"';
            if($code_required!='' && $code_required=='2'){
                $html.= " selected=\"selected\"";
            }
            $html.='>No</option>';
            $html.='</select></td>'
               . '</tr>'
               . '<tr>'
               . '<td class="bodytext" align="right" valign="top" width="30%"> BOGO:</td>'
               . '<td class="bodytext" valign="top" width="70%"><div style="clear:left;"><select onchange="checkPromotionBogo(this.value);"class="combo_box" id="bogo" name="bogo"><option value="0"> --Select One-- </option>';
            $html.='<option value="1"';
            $bogo_style="display:none;";
            
            if($bogo!='' && $bogo=='1'){
                $bogo_style="block;";
                $html.= " selected=\"selected\"";
            }
            $html.='>Yes</option>';
            $html.='<option value="2"';
                if($bogo!='' && $bogo=='2'){
                    $html.= " selected=\"selected\"";
                }
            $html.='>No</option>';
            $html.='</select></td>'
               . '</tr>'
               . '<tr class="buy_bogo" style="'.$bogo_style.'">'
               . '<td class="bodytext" align="right" valign="top" width="30%"> Buy [X]:</td>'
               . '<td class="bodytext" valign="top" width="70%"><div style="clear:left;"><input type="text" id="buy_x" name="buy_x" size="10" maxlength="100" class="input_box" value="'.$bogo_buy_value.'" /></div></td>'
               . '</tr>'
               . '<tr class="get_bogo" style="'.$bogo_style.'">'
               . '<td class="bodytext" align="right" valign="top" width="30%"> Get [X]:</td>'
               . '<td class="bodytext" valign="top" width="70%"><div style="clear:left;"><input type="text" id="get_x" name="get_x" size="10" maxlength="100" class="input_box" value="'.$bogo_get_value.'" /></div></td>'
               . '</tr>'
               . '</tbody>'
               . '</table>'
               . '</div>';
            $html.= '<table border="0" width="100%" cellpadding="5" cellspacing="0">'
                    . '<tbody>'
                    . '<tr>'
                    . '<td class="bodytext" align="right" valign="top" width="30%">&nbsp;</td>'
                    . '<td class="bodytext" valign="top" width="70%"><div class="save_promotion_hide" style="clear:left;"><a class="'.$styleAdd.'" href="javascript:void(0);" onclick="SavePromotionField('.$lstsubcatid.','.$tempid.','.$isallreadysaved.','.$istemp_value.','.$formtemp.'); return false;" class="">Save Promotion</a></div><div class="add_more_promotion_hide" style="display:none;clear:right;"><a class="'.$styleAdd.'" href="javascript:void(0);" onclick="addcompanypromotionfield('.$lstsubcatid.',0,'.$rowcompcount.','.$isallreadysaved.','.$istemp_value.','.$formtemp.'); return false;" class="add_more_promotion">Add More Promotion</a></div></td>'
                    . '</tr><tr><td colspan="2">&nbsp;</td></tr>'
                    . '</tbody>'
                    . '</table>';
       echo $html; exit;
        }
    }else{
        echo $html; exit;
    }
}


if(isset($_REQUEST['compid']) && $_REQUEST['compid']!="" && $_REQUEST['action']=='add_promotion') {
    $formtemp=$_REQUEST['formtemp'];
    $styleAdd='';
    if($formtemp==1){
        $styleAdd="bluelink";
    }
    if($_REQUEST['isallreadysaved']!=''){
     $isallreadysaved=$_REQUEST['isallreadysaved'];  
    }else{
        $isallreadysaved='0';
    }
    $istemp=$_REQUEST['istemp'];
    $companyID=$_REQUEST['compid'];
    $lstsubcatid=$_REQUEST['lstsubcatid'];
    $count_promotion_no=$_REQUEST['count_promotion_no'];
    $promotion_type=$_REQUEST['promotion_type'];
    $coupon_discount_value=$_REQUEST['coupon_discount_value'];
    $ad_price=$_REQUEST['add_price'];
    $regular_price=$_REQUEST['regular_price'];
    $shipping_detail_id=$_REQUEST['shipping_detail'];
    $online_in_store_id=$_REQUEST['online_in_store'];
    $qualifier_id=$_REQUEST['qualifier'];
    $qualifier_min_purchase_value=$_REQUEST['qualifier_minimum_purchase_value'];
    $code_required=$_REQUEST['code_required'];
    $bogo=$_REQUEST['bogo'];
    $bogo_buy_value=$_REQUEST['buy_x'];
    $bogo_get_value=$_REQUEST['get_x'];
    $updateid=$_REQUEST['updateid'];
    
    if($isallreadysaved!='' && $updateid!='0' && $istemp=='1' && $formtemp==0){
         $sql = "UPDATE cscan_promotions SET categoryID='".$lstsubcatid."',companyID='".$companyID."',promotionNo='".$count_promotion_no."',promotion_type_id='".$promotion_type."',coupan_discount_value='".$coupon_discount_value."',ad_price='".$ad_price."',regular_price='".$regular_price."',shipping_detail_id='".$shipping_detail_id."',online_in_store_id='".$online_in_store_id."',qualifier_id='".$qualifier_id."',qualifier_min_purchase_value='".$qualifier_min_purchase_value."',code_required='".$code_required."',bogo='".$bogo."',bogo_buy_value='".$bogo_buy_value."',bogo_get_value='".$bogo_get_value."' where id='".$updateid."'";
    }elseif($isallreadysaved!='' && $updateid!='0' && $istemp=='1' && $formtemp==1){
         $sql = "UPDATE cscan_promotions_temp SET categoryID='".$lstsubcatid."',companyID='".$companyID."',promotionNo='".$count_promotion_no."',promotion_type_id='".$promotion_type."',coupan_discount_value='".$coupon_discount_value."',ad_price='".$ad_price."',regular_price='".$regular_price."',shipping_detail_id='".$shipping_detail_id."',online_in_store_id='".$online_in_store_id."',qualifier_id='".$qualifier_id."',qualifier_min_purchase_value='".$qualifier_min_purchase_value."',code_required='".$code_required."',bogo='".$bogo."',bogo_buy_value='".$bogo_buy_value."',bogo_get_value='".$bogo_get_value."' where id='".$updateid."'";
    }elseif($isallreadysaved!='' && $updateid!='0' && $istemp=='0' && $formtemp==1){
         $sql = "UPDATE cscan_promotions_temp SET categoryID='".$lstsubcatid."',companyID='".$companyID."',promotionNo='".$count_promotion_no."',promotion_type_id='".$promotion_type."',coupan_discount_value='".$coupon_discount_value."',ad_price='".$ad_price."',regular_price='".$regular_price."',shipping_detail_id='".$shipping_detail_id."',online_in_store_id='".$online_in_store_id."',qualifier_id='".$qualifier_id."',qualifier_min_purchase_value='".$qualifier_min_purchase_value."',code_required='".$code_required."',bogo='".$bogo."',bogo_buy_value='".$bogo_buy_value."',bogo_get_value='".$bogo_get_value."' where id='".$updateid."'";
    }elseif($isallreadysaved!='' && $updateid!='0' && $istemp=='0' && $formtemp==0){
         $sql = "UPDATE cscan_promotions_temp SET categoryID='".$lstsubcatid."',companyID='".$companyID."',promotionNo='".$count_promotion_no."',promotion_type_id='".$promotion_type."',coupan_discount_value='".$coupon_discount_value."',ad_price='".$ad_price."',regular_price='".$regular_price."',shipping_detail_id='".$shipping_detail_id."',online_in_store_id='".$online_in_store_id."',qualifier_id='".$qualifier_id."',qualifier_min_purchase_value='".$qualifier_min_purchase_value."',code_required='".$code_required."',bogo='".$bogo."',bogo_buy_value='".$bogo_buy_value."',bogo_get_value='".$bogo_get_value."' where id='".$updateid."'";
    }
    else{
        $sql = "INSERT INTO  cscan_promotions_temp SET formtemp='".$formtemp."',session_id='".$sessionuniqueid."', userID='".$sess_admin_userId."',categoryID='".$lstsubcatid."',companyID='".$companyID."',promotionNo='".$count_promotion_no."',promotion_type_id='".$promotion_type."',coupan_discount_value='".$coupon_discount_value."',ad_price='".$ad_price."',regular_price='".$regular_price."',shipping_detail_id='".$shipping_detail_id."',online_in_store_id='".$online_in_store_id."',qualifier_id='".$qualifier_id."',qualifier_min_purchase_value='".$qualifier_min_purchase_value."',code_required='".$code_required."',bogo='".$bogo."',bogo_buy_value='".$bogo_buy_value."',bogo_get_value='".$bogo_get_value."'";
    }
    //echo $sql; die;
   $save_data=$DRW->query($sql,$DRW_main);
   if($save_data){
       $last_id = $DRW->insert_id();
       if($updateid!='' && $updateid!='0'){
           $last_id=$updateid;
       }
       
       $sqlComp = "SELECT companyName FROM cscan_company WHERE companyID ='".$companyID."'";
        $result_query = $DRW->query($sqlComp,$DRW_read);
        $row_data = $DRW->fetch_array($result_query);
        $tempCompanyName=$row_data['companyName'];
        $commastr='';
        if($count_promotion_no>1){
           $commastr='<span id="remove_comma_'.$last_id.'">,</span>';
        }
        $html_tmp=trim($commastr).'<a class="'.$styleAdd.'" href="javascript:void(0);" onclick="addcompanypromotionfield('.$lstsubcatid.','.$last_id.','.$count_promotion_no.','.$isallreadysaved.','.$istemp.','.$formtemp.'); return false;"><b> Promotion #'.$count_promotion_no.'</b></a>';
   echo $html_tmp; exit;
   }else{
    echo "3";exit;   
   }
}

if(isset($_REQUEST['tempid']) && $_REQUEST['tempid']!="" && $_REQUEST['action']=='removepromotion') {
    $tempid=$_REQUEST['tempid'];
    $formtemp=$_REQUEST['formtemp'];
    $delWhereQuery='';
    if($formtemp==1){
      $delWhereQuery=" AND formtemp=1 AND muid=0";  
    }
    if($tempid!=''){
        $sqlDelete = "Delete from cscan_promotions_temp  where id='".$tempid."' AND session_id='".$sessionuniqueid."'$delWhereQuery";
        $DRW->query($sqlDelete,$DRW_main);
    }
    echo "5"; exit;
}

?>