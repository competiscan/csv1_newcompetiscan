<?php
require_once('includes/globalSession.php');
require_once ("includes/dbcon.php");
require_once('includes/rpv-dashboard-function.php');

// echo "<pre>";
// print_r($_REQUEST['formData']);
// echo "</pre>";
// die;
$user_id=$_SESSION['sess_userID'];
$sess_api_searchID=$_REQUEST['csv1_search_id'];
$check_search_sql = "SELECT search_competi_id FROM cscan_search where userID={$_SESSION['sess_userID']} AND ID='".$sess_api_searchID."'";
$resultCheck = $DRW->query($check_search_sql,$DRW_read);
if($DRW->num_rows($resultCheck) > 0){
    $dataSearchCheck = $DRW->fetch_array($resultCheck);
    if($dataSearchCheck['search_competi_id']!=''){
        $search_competi_id=$dataSearchCheck['search_competi_id'];
        }
    }

$dataArray= $_REQUEST['formData'];
$myexcelDataArray=array();
$myexcelDataArray['user_id']=(int)$user_id; 
$myexcelDataArray['status_unique_id']=$_REQUEST['status_unique_id'];
$field_name='';
$myexcelDataArray['search_keyword']='';
$myexcelDataArray['file_type']='csv';
foreach ($dataArray as $item) {
    $key="";
    $value ="";
    $key = $item["name"];
    $value = $item["value"];
    if($key=='ssid'){
        $myexcelDataArray['sid']=(int)$value; 
    }
    if($key=='bid'){
        $bid=(int)$value;
        if($bid>0){
            $sqlbasket = "SELECT productID FROM cscan_product_basket WHERE basket_id=$bid AND userID={$_SESSION['sess_userID']}";
            $resultsbasket = $DRW->query($sqlbasket,$DRW_read);
            if($DRW->num_rows($resultsbasket) > 0){
                $bktProductIDArray=array();
                while($row_basket_data = $DRW->fetch_row($resultsbasket)){
                    $bktProductIDArray[]=$row_basket_data[0];
                }
               $myexcelDataArray['product_id'][]=implode(',',$bktProductIDArray);
            }
        }
    }
    if($key=='page'){
        
        $page_no=(int)$value; 
    }
    if($key=='btn_value'){
        if($value=='Generate File for Current Page'){
            
            $myexcelDataArray['page']=$page_no;
            $myexcelDataArray['report_type']='current_page_record';
        }
        if($value=='Generate File for all Records'){
            $myexcelDataArray['page']='0';
            $myexcelDataArray['report_type']='all_record';
        }
        if($value=='Comprehensive Banking Report'){
            $myexcelDataArray['page']='0';
            $myexcelDataArray['report_type']='record_banking';
        }
        if($value=='Comprehensive Credit Card Report'){
            $myexcelDataArray['page']='0';
            $myexcelDataArray['report_type']='record_creditcard';
        }
        if($value=='Panelist Report (Panelist ID)'){
            $myexcelDataArray['page']='0';
            $myexcelDataArray['fields'][]='competi_id';
            $myexcelDataArray['fields'][]='additional_ppdate';
            $myexcelDataArray['report_type']='record_by_panelistID';
            if($search_competi_id!=''){
            $myexcelDataArray['search_competi_id'][]=$search_competi_id;
            }
            
        }
        if($value=='Panelist Report (Entry ID)'){
            $myexcelDataArray['page']='0';
            $myexcelDataArray['fields'][]='competi_id';
            $myexcelDataArray['report_type']='record_by_entryID';
        }
    }
    
    if($key=='field[]'){
        $myexcelDataArray['fields'][]=$value;
    }
   
    if($key=='file_choice' AND ($value==1 || $value==3)){
        if($sess_api_searchID>0) {
            list($displayKeywords) = getKeywords($sess_api_searchID);
            $displayKeywords = preg_replace('/(.)(<strong>)/', '$1| $2', trim($displayKeywords));
            $searchtitle = html_entity_decode(strip_tags($displayKeywords));
            $myexcelDataArray['search_keyword']=$searchtitle;
            $myexcelDataArray['file_type']='xlsx';
            unset($displayKeywords);

        }else{
            if($bid>0){
                $bq= "SELECT basket_name FROM cscan_basket Where basket_id='".$bid."' AND userID={$_SESSION['sess_userID']}";
                    $rsb = $DRW->query($bq,$DRW_read);
                    if($DRW->num_rows($rsb) > 0){
                        $basketname='';
                        $datab = $DRW->fetch_row($rsb);
                        $basketname= $datab[0]; 
                        $myexcelDataArray['search_keyword']=$basketname;
                        $myexcelDataArray['file_type']='xlsx';
                    }
                }
        }
    }
}
//  echo "<pre>";
//  print_r($myexcelDataArray);
//  echo "</pre>";
// echo json_encode($myexcelDataArray);
// die;
if($_REQUEST['action']=='download_myexcel'){
    $API_DOWNLOADURL = DOWNLOAD_MYEXCEL_UAT;
    $posted_jsondata=$myexcelDataArray; 
    $posted_jsondata=json_encode($myexcelDataArray);
    if(!empty($posted_jsondata)){
    $ch_download = curl_init($API_DOWNLOADURL); 
    curl_setopt($ch_download, CURLOPT_POST, 1);
    curl_setopt($ch_download, CURLOPT_POSTFIELDS, $posted_jsondata);
    curl_setopt($ch_download, CURLOPT_RETURNTRANSFER, 1); 
    curl_setopt($ch_download, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    $result_download = curl_exec($ch_download);
    //echo $result_download;exit;
    if(!empty($result_download)){
            $data=json_decode($result_download);
            $filelink=$data->filelink;
            echo trim($filelink);exit;

    }
    }else{
        echo json_last_error_msg();exit;
    } 
}
?>