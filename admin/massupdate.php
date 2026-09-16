<?php
$fromtemp = false;
$updID = false;
ini_set("memory_limit","-1");
set_time_limit(0);
require_once '../auth_auth.php';
require_once '../includes/functions.php';
require_once '../includes/Product.php';
require_once '../includes/Company.php';
require_once '../includes/AdminLog.php';
require_once '../includes/AdminPermission.php';
require_once 'addProductPersistenceAndLogic.php';

$permChecker = new AdminPermission();

/**
 * Extract sector/category ids from a string
 *
 * @param string $sector_category_underscored sector and category ids chained into A_B_C_D format
 * @return array $collated_sector_category_ids
 */
function get_sector_category_ids($sector_category_underscored) {
    $sector_category_array = explode('_', $sector_category_underscored);
    $collated_sector_category_ids = array('sector' => $sector_category_array[0],
        'category' => $sector_category_array[1],
        'subcategory' => $sector_category_array[2],
        'subsubcategory' => $sector_category_array[3],
        );

    return $collated_sector_category_ids;
}

if ($permChecker->userCanMassUpdate() && isset($_POST['massupdate_ids']) && trim($_POST['massupdate_ids']) != '') {
    global $DRW, $DRW_main;

    $ids_to_edit = trim($_POST['massupdate_ids']);
    $product_ids = explode(',', $ids_to_edit);
    $company_ids = trim($_POST['cmp_ids']);
    $company_ids_array = explode(',', $company_ids);
    $product_name = $_POST['productName'];
    $sector_cat_id[] = $_POST['combo_sid'];
    $sector_cat_id[] = $_POST['combo_cid'];
    $sector_cat_id[] = $_POST['combo_scid'];
    $sector_cat_id[] = $_POST['combo_sscid'];
    
    /* Added By Pradeep */    
    if(isset($_POST['is_citi']) and $_POST['is_citi']!=''){
	   $is_citi= $_POST['is_citi'];    
	}else{
	    $is_citi='';
	}
    if(isset($_POST['is_junk']) and $_POST['is_junk']!=''and $_POST['is_junk']=='1'){
	   $is_junk= $_POST['is_junk'];    
	}else{
	    $is_junk='0';
	}


	    $final_sector_check='';
	    $productstatus='';
	    $combo_sid='';	
    
	/* End Added By Pradeep */	
          
    $sector_category_combo = implode('_', $sector_cat_id); // Grabbing all to represent as A_B_C_D
    $sector_category_combo_multiple = $_POST['scsc_comboIDs'];
    $admin_log = new AdminLog($DRW, $DRW_main);
    $product = new Product($DRW, $DRW_main, $admin_log);
    $company = new Company($DRW, $DRW_main);
    $all_companies = $company->get_all();

    // Check if any sector/category information needs to be updated
    if (substr($sector_category_combo, 0, 1) != '0') {
        $sector_category_ids[] = get_sector_category_ids($sector_category_combo);
    }

    // Check if any multi-selected sector/category information needs to be updated
    if (substr($sector_category_combo_multiple, 0, 1) != '0') {
        $sector_category_combo_sets = explode('|', $sector_category_combo_multiple);
	$checksector		    = $sector_category_combo_sets[0];
        $checksector_array	    = explode('_',$checksector);
        $final_sector_check	    = $checksector_array[0];

        for ($i = 0, $n = count($sector_category_combo_sets); $i < $n; $i++) {
            $sector_category_ids[] = get_sector_category_ids($sector_category_combo_sets[$i]);
        }
    }


    if (isset($company_ids_array[0]) && $company_ids_array[0] != 0) {
        $company_image = $company->get_image($company_ids_array[0]);
    }
	/* Added By Pradeep */	
    
	$combo_sid=$_POST['combo_sid'];
        
        $pstats='';
  $cstats='';
   if(isset($_POST['pstat']) AND $_POST['pstat']!=''){
     $pstats=(int)$_POST['pstat'];  
  } 
  if(isset($_POST['cstat']) AND $_POST['cstat']!=''){
     $cstats=(int)$_POST['cstat'];
  }
   
    
        
 $productstatus=CheckProductStatus($is_citi,$combo_sid,$final_sector_check,$pstats,$cstats);
        
 //$productstatus=CheckProductStatus($is_citi,$combo_sid,$final_sector_check);
	 if($is_junk=='1'){
	     $productstatus='10';
	 }
     
	/* End Added By Pradeep */		
    $sqlarray   =   array();
    $emailData  =   array();
    for ($i = 0, $n = count($product_ids); $i < $n; $i++) {
        $product_detail = $product->get_product($product_ids[$i]);

        if (trim($product_name) != '') {
            $product_detail->productName = $product_name;
        }
	/* Added By Pradeep */	
   
	if (trim($productstatus)!= '' and $is_junk!='1') { 
            $product_detail->productStatus = $productstatus;
            $product_detail->is_citi=0;            
        }else if($is_junk=='1'){
            $sql = "SELECT document_filename,document_path FROM cscan_document WHERE productID=$product_ids[$i]";
            $query = $DRW->query($sql,$DRW_read);
            if($DRW->num_rows($query)>0){
                $rs = $DRW->fetch_assoc($query);
                $root = dirname(__FILE__);
                if (strpos($root, '/admin') !== false) {
                    $root = substr($root, 0, strpos($root, '/admin'));
                }
                $u = $root.$rs['document_path'].$rs['document_filename'];
                $DRW->free_result($query);
                
                $x = (!empty($u))?explode("/", $u):"";
                $y = explode(".",end($x));
                $z = explode("_",current($y));
                $pdfId = (int)end($z);
                //echo $pdfId.'<=>'.$product_ids[$i];
                if($pdfId){
                    $sectors = explode(",",$product_detail->sectorID);
                    
                    if(in_array($product_detail->productStatus,[2,4]) && (!in_array(6, $sectors) && !in_array(87, $sectors) && !in_array(90, $sectors)) && $product_detail->mTypeID != 3 && ($pdfId == $product_ids[$i])){
                        //if productStatus is Unapproved or Problem, then copy pdf to dmapproved folder also
                        // Exculde sector Banking, Credit Cards, Mortgage & Loan
                        copydmApprovedPdf($product_ids[$i]);
                        //daApprovedCsv($product_ids[$i]);
                    }
                }
            }
            $product_detail->productStatus ='10';
            if($is_citi!=''){
            $product_detail->is_citi=$is_citi;
            }
        }
    
	/* End Added By Pradeep */	


        if ($company_ids != '' && count($company_ids_array) > 0) {
            $product->update_companies($company_ids_array, $all_companies);
        }

        if (isset($sector_category_ids)) {
            $product->update_sectors_and_categories($sector_category_ids);
        }

        if (isset($company_image) && $company_image != '') {
            $product->copy_image($company_image, true);
        }
        
        $product->save();
        
        if(count($product_ids)>=10){
            $sqlarraydata=$product->trackmassupdate();
            $data = [
                    'auth_id' => $GLOBALS['AUTH_DATA']['userID'],
                    'auth_name'=>authUserName($GLOBALS['AUTH_DATA']['userID']),
                    'deleted_id' => (int) $product_ids[$i],
                    'sql_query' => $sqlarraydata,
                    'ip_address' => ipAddress(),
                    'user_agent' => $_SERVER['HTTP_USER_AGENT'],
                    'delete_type' => 'Mass Update Product',
                    'is_mobile' => isMobile(),
                    'insert_date' => date("Y-m-d H:i:s")
                ];
            trackDelete($data);
           // $emailData[] = $data;
        
       }
    }
    
//     if (count($emailData) > 0) {
//                    $html = '<table width="100%" border="1">';
//                    $html .= '<tr><td>Auth ID</td><td>User Name</td><td>Updated ID</td><td>SQL Query</td><td>IP Address</td><td>User Agent</td><td>Module</td><td>Is Mobile</td><td>Updated Date (CST)</td></tr>';
//                    foreach ($emailData as $tr) {
//                        if (is_array($tr) && count($tr) > 0) {
//                            $html .= '<tr>';
//                            foreach ($tr as $td) {
//                                $html .= '<td>' . $td . '</td>';
//                            }
//                            $html .= '</tr>';
//                        }
//                    }
//                    $html .= '</table>';
//
//                    sendDevAlert('Caution! Data Updated From Manage Product', $html);
//                }
  
}

if (isset($_POST['cstat']) && isset($_POST['pstat'])) {
    header("Location: /admin/manageproduct.php?pstat=".(int)$_POST['pstat']."&cstat=".(int)$_POST['cstat']);
} else {
    header("Location: /admin/");
}
