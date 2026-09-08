<?php
$time = time();
include_once 'includes/functions.php';
require_once("includes/dbcon.php");
$DRW->databaseReadWrite_die = 1;
ini_set("memory_limit","-1");
set_time_limit(0);
$maxbytes = 500000;
$query2 = "SELECT dts_val,s.productID,dts_id FROM cscan_document_text_search s 
join cscan_document d  on d.productID=s.productID WHERE bucket_name = 'csv2-productcontent' and s.report_modify_date >= ( NOW() - INTERVAL 5 Minute)";
    $query_result2 = $DRW->query($query2, $DRW_read);
     while($data2 = $DRW->fetch_array($query_result2)){       
        $pdfContent=$data2['dts_val'];
        $productID=$data2['productID'];
        $pdfContent = clean_pdfContent($pdfContent);
        $sql = "Update cscan_document_text_search set dts_val ='".$DRW->real_escape_string($pdfContent)."' where productID='".$productID."'";
        $DRW->query($sql, $DRW_main);
       
        
    }
    
/*$queryProduct = "SELECT productHeadline,pd.productID FROM cscan_product_detail pd 
join cscan_document d  on d.productID=pd.productID WHERE bucket_name = 'csv2-productcontent'";*/
/*$queryProduct = "SELECT productHeadline,pd.productID FROM cscan_product_detail pd 
join cscan_document d  on d.productID=pd.productID WHERE bucket_name = 'csv2-productcontent' and pd.report_modify_date >= ( NOW() - INTERVAL 5 Minute)";
    $query_result_product = $DRW->query($queryProduct, $DRW_read);
     while($dataProduct = $DRW->fetch_array($query_result_product)){       
        $productHeadline=$dataProduct['productHeadline'];
        $productID=$dataProduct['productID'];
        $productHeadline = cleanContentHeadline($productHeadline);
        $sql = "Update cscan_product_detail set productHeadline ='".$DRW->real_escape_string($productHeadline)."' where productID='".$productID."'";
        $DRW->query($sql, $DRW_main);
        
    }
   
function cleanContentHeadline($productHeadline) {
        //trim(preg_replace('/\\s+/', ' ', $_POST['productHeadline']));
    //$productHeadline = preg_replace('/[^a-zA-Z0-9_\[\]]+/', ' ', $productHeadline); // \'
    $productHeadline = trim(preg_replace('/\\s+/', ' ', $productHeadline));
    return $productHeadline;
}*/
echo "done";
?>
