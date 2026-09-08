<?php 
$start_time = microtime(true);
require_once('includes/globalSession.php');
//require_once('includes/checklogin.php');
require_once('includes/functions.php');  //latest function
$sql= "SELECT trend_id,trend_link,file_name,file_path FROM cscan_trend_report where trend_link IS NOT NULL AND file_name='' and file_path=''";
$query = $DRW->query($sql,$DRW_read);
    while($rowData = $DRW->fetch_assoc($query)) {
        //print_r($rowData); die;
        $trend_id = $rowData['trend_id'];
        $trend_link = $rowData['trend_link'];
        $file_name = $rowData['file_name'];
        $file_path = $rowData['file_path'];
        //echo $filename = basename($trend_link); die;
        $exp_array= explode('/',$trend_link);
        //echo "<pre>";
       //print_r($exp_array); die;
        if(count($exp_array)=='5'){
           $foldername=$exp_array[3];
           $exp_file_name =$exp_array[4]; 
        }elseif(count($exp_array)=='4'){
            $foldername=$exp_array[2]; 
            $exp_file_name =$exp_array[3]; 
        }
        if($foldername=='downloads' ||$foldername=='fileuploads'){
            $foldername=$foldername;
        }else{
            $chk_folder= explode('.',$foldername);
            $foldername=$chk_folder[0];
        }
       // echo $exp_file_name; die;
        //$chk_ext= explode('.',$exp_file_name);
       //echo clean($exp_file_name);
       //echo $exp_file_name."<br/>"; 
        //$exp_file_name='https://files.competiscan.com/downloads/PDPNovember2008.ppt#1';
        $exp_file_name=urldecode($exp_file_name);
        if(strstr($exp_file_name,'#')){
           $exp_file_name= strstr($exp_file_name,'#',true);
        }
        //echo $exp_file_name; die;
        $ext_name = pathinfo($exp_file_name, PATHINFO_EXTENSION);
        if($ext_name==""){
            $ext_name="pdf";
            $exp_file_name=$exp_file_name.'.'.$ext_name;
        }
       //echo $ext_name=$chk_ext[1]; die;
        if($file_name=="" && $file_path==""){
            $data = downloadTrendPDF($trend_id,$exp_file_name,$ext_name,$foldername);
        }

    } 
    echo "File downloaded successfully!"; exit;
 /*function clean($string) {
   //$string = str_replace(' ', '20', $string); // Replaces all spaces with hyphens.
   return preg_replace('/[^A-Za-z0-9\-.]/', ' ', $string); // Removes special chars.
}*/
function downloadTrendPDF($trend_id,$exp_file_name,$ext_name,$foldername) {
        global $s3, $bucket_name,$DRW_main,$DRW;
       // echo $exp_file_name; die;
        $yearpath = date('Y/');
        $monthpath = date('m/');
        $datepath = $yearpath . $monthpath;
        $tendPath= $yearpath . $monthpath.$trend_id;
        $root = dirname(__FILE__);
        $pdfpart = $root . '/trend_document/';
        $pdfPath = "$pdfpart$datepath$trend_id/";
        $pdfs3newPath = "trend_document/$datepath$trend_id/";
        $valid_types = array("pdf","ppt","PPT", "pptx","xls","xlsx","mp4");
        if(in_array($ext_name, $valid_types)) {
           if (!is_dir($pdfpart . $yearpath)){
               @mkdir($pdfpart . $yearpath, 02755);
           }
           if (!is_dir($pdfpart . $datepath)) {
               @mkdir($pdfpart . $datepath, 02755);
           }
           if (!is_dir($pdfPath)) {
               @mkdir($pdfPath, 02755);
           } 
          $pdfName = $exp_file_name;
          $file = $pdfPath . $pdfName;
           if ($ext_name == 'pdf') {
               $content_type = "application/pdf";
           } else {
               if ($ext_name == "PPT" || $ext_name == "ppt") {
                   $content_type = "application/vnd.ms-powerpoint";
               } elseif ($ext_name == "pptx") {
                   $content_type = "application/vnd.openxmlformats-officedocument.presentationml.presentation";
               }elseif ($ext_name == "doc") {
                   $content_type = "application/msword";
               }elseif ($ext_name == "docx") {
                   $content_type = "application/msword";
               }elseif ($ext_name == "ppt") {
                   $content_type = "application/vnd.openxmlformats-officedocument.presentationml.presentation";
               }elseif ($ext_name == "xls") {
                   $content_type = "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet";
               }elseif ($ext_name == "xlsx") {
                   $content_type = "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet";
               }elseif ($ext_name == "video/mp4") {
                   $content_type = "video/mp4";
               }elseif ($ext_name == "") {
                   $content_type = "";
               }

           }
           //echo $foldername.'/'.$pdfName; die;
           $s3checkfile = $s3->doesObjectExist($bucket_name, $foldername.'/'.$pdfName);
           //echo "tsdttdtsdt".$s3checkfile; die;
           if ($s3checkfile){
            try {
                    // Get the object.
                    $result = $s3->getObject([
                        'Bucket' => $bucket_name,
                        'Key'    => $foldername.'/'.$pdfName,
                        'SaveAs' => $file,

                    ]);
                  // echo "<pre>";
                   //print_r($result); 
                   //echo "<pre>";die;
                } catch (S3Exception $e) {
                    echo $e->getMessage() . PHP_EOL;
                }
                
               $result_data = $s3->putObject([
                        'Bucket' => $bucket_name,
                        'Key' => $pdfs3newPath . $pdfName,
                        'SourceFile' => $pdfPath.$pdfName,
                        'ACL' => 'public-read',
                        'ContentType' => $content_type,
                        'Metadata' => array(
                        'string' => 'string'
                        )
                    ]);
                
            }
              // $pdfContent = '';
            if(file_exists($pdfs3newPath.$pdfName)){
            $sqlUpdate = "UPDATE cscan_trend_report SET file_path='$pdfs3newPath' , file_name='$pdfName' WHERE trend_id='$trend_id'";
              $query = $DRW->query($sqlUpdate,$DRW_main);
                $pdfContent = '';
                if ($content_type == "application/pdf") {
                    if ($pdfContent == '') {
                        $pdfContent_tmp = shell_exec('/usr/bin/pdftotext -q ' . escapeshellarg($file) . ' -');
                        if ($pdfContent_tmp != '') {
                            $pdfContent = $pdfContent_tmp;
                        }
                    }
                    saveTrendDocumentText($trend_id, $pdfContent);
                    if (is_file($file)) {
                        @unlink($file);
                    }
                }
                /********************* PPT TO TEXT ********************/
               /*else if($content_type == "application/vnd.ms-powerpoint" || $content_type =="application/vnd.openxmlformats-officedocument.presentationml.presentation"){
                    $pdfContent = pptx_to_text($pdfName);
                     $saveData= saveTrendDocumentText($trend_id, $pdfContent);
                      if($saveData!=''){   
                        if (is_file($file)) {
                               @unlink($file);
                            }
                        }
                    }*/
                    /********************* DOC TO TEXT ********************/
                    /*else if($content_type == "application/msword"){
                   //echo  $pdfContent = $this->read_docx($pdfName); 
                    saveTrendDocumentText($trend_id, $pdfContent);
                        if (is_file($file)) {
                            @unlink($file);
                        }
                    }*/
            } 

       }
    }
    
    function saveTrendDocumentText($trend_id, $pdfContent) {
         global $DRW_main,$DRW,$DRW_read;
        $query2 = "SELECT SQL_NO_CACHE COUNT(*) FROM cscan_trend_document_text WHERE trend_id=$trend_id";
        $query_result2 = $DRW->query($query2,$DRW_read);
        $data2 = $DRW->fetch_row($query_result2);
        $count = (int) $data2[0];
        if ($count > 0) {
            //deleteSphinx(array($productID), array($document_id));
            $sql = "DELETE FROM cscan_trend_document_text WHERE trend_id=$trend_id";
            $DRW->query($sql, $DRW_main);
        }
        $pdfContent = clean_pdfContent($pdfContent);
        $wrap = wordwrap($pdfContent,500000, "\n");
        $stringArray = preg_split('/\\n/', $wrap, -1, PREG_SPLIT_NO_EMPTY);
        foreach ($stringArray as $val) {
             $sql = "INSERT INTO cscan_trend_document_text (trend_id,document_text) values ($trend_id,'" .$DRW->real_escape_string($pdfContent) . "')";
             $DRW->query($sql, $DRW_main);
        }
    }
    
   function pptx_to_text($pdfName) {
        $input_file = $pdfName;
        $zip_handle = new ZipArchive;
        $output_text = "";
        if (true === $zip_handle->open($input_file)) {
            //echo "dsdgsdgsgd"; die;
            $slide_number = 1; //loop through slide files
            while (($xml_index = $zip_handle->locateName("ppt/slides/slide" . $slide_number . ".xml")) !== false) {
                //print_r($xml_index); die;
                $xml_datas = $zip_handle->getFromIndex($xml_index);
                $xml_handle = DOMDocument::loadXML($xml_datas, LIBXML_NOENT | LIBXML_XINCLUDE | LIBXML_NOERROR | LIBXML_NOWARNING);
                $output_text .= strip_tags($xml_handle->saveXML());
                $slide_number++;
            }
            if ($slide_number == 1) {
                $output_text .= "";
            }
            $zip_handle->close();
        } else {
            $output_text .= "";
        }
        return $output_text;
    } 
?>