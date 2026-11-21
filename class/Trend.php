<?php
/**
* addTrend()
*
* return boolean
*/   
   function addTrend($post){
       //echo "<pre>";
       //print_r($post); die;
      global $DRW, $DRW_read, $DRW_main, $DRW_crm;
        if(isset($post['trendname'],$post['audience_id'],$post['scsc_comboIDs']) and !empty($post['trendname']) and !empty($post['audience_id']) and !empty($post['scsc_comboIDs'])){
        if(isset($_FILES)){
        $fileArray = $_FILES;
        }else{
         $fileArray = array();
        }
        if (isset($post['scsc_comboIDs'])){
            $comboIDs = trim($post['scsc_comboIDs']);
        } else {
            $comboIDs = '';
        }
        $trendname = $DRW->real_escape_string(trim($_POST['trendname']));
        $trendlink = "";
        $trendaudience_id = $DRW->real_escape_string(@implode(',',$_POST['audience_id']));
        $trendcountry = $DRW->real_escape_string(trim($_POST['country']));
        $trend_date = $_POST['trenddate_y'].'-'.$_POST['trenddate_m'].'-'.$_POST['trenddate_d'];
        for ($x = 0; $x < 10; $x++) {
            $rndtrend_id=generateRandomNumeric(); //genrate random 10 digit number
            $sqlcheck= "SELECT trend_id FROM cscan_trend_report where rndtrend_id='".$rndtrend_id."'";
            $querycheck = $DRW->query($sqlcheck,$DRW_read);
            if($DRW->num_rows($querycheck) < 1) { 
               break; 
            }
        }
        
            $sql = "INSERT INTO cscan_trend_report SET rndtrend_id='".$rndtrend_id."',trend_name='$trendname', trend_link='$trendlink',audience_id='$trendaudience_id', country_id ='$trendcountry', trend_date='$trend_date'";
            $actMsg = 'added';
            $DRW->query($sql,$DRW_main);
            $trend_id = $DRW->insert_id($DRW_main);
            $upload_document = uploadTrendPDF($trend_id, $fileArray);
            $c1 = @explode('|',$comboIDs);
            foreach($c1 as $c){
                $c2 = @explode('_',$c);
                $sql_category = "INSERT INTO cscan_trends_category SET trend_id='$trend_id', sector_id='$c2[0]',category_id='$c2[1]', subcategory_id ='$c2[2]', subtosubcategory_id='$c2[3]'";
                    $DRW->query($sql_category,$DRW_main);
            }
        if($post['submit'] == 'Save & Add More'){
                ob_end_clean();
                header("Location: addTrend.php?a=1");
                exit;
        }
        else{
                ob_end_clean();
                header("Location: manageTrends.php");
                exit;
        }
        return 5;
    
        //}
        
        }else{
        return 2; //required parameter missing
    }
} 

/**
* updateTrend()
*
* return boolean
*/

 function updateTrend($post){
    global $DRW, $DRW_read, $DRW_main, $DRW_crm;
        if(isset($post['trendname'],$post['audience_id'],$post['scsc_comboIDs']) and !empty($post['trendname']) and !empty($post['audience_id']) and !empty($post['scsc_comboIDs'])){
        if(isset($_FILES)){
        $fileArray = $_FILES;
        }else{
         $fileArray = array();
        }
        if (isset($post['scsc_comboIDs'])){
            $comboIDs = trim($post['scsc_comboIDs']);
        } else {
            $comboIDs = '';
        }
        $trend_id = $DRW->real_escape_string(trim($_POST['id']));
        $trendname = $DRW->real_escape_string(trim($_POST['trendname']));
        $trendlink = "";
        $trendaudience_id = $DRW->real_escape_string(@implode(',',$_POST['audience_id']));
        $trendcountry = $DRW->real_escape_string(trim($_POST['country']));
        $trend_date = $_POST['trenddate_y'].'-'.$_POST['trenddate_m'].'-'.$_POST['trenddate_d'];
                $sql = "UPDATE cscan_trend_report SET trend_name='$trendname' , trend_link='$trendlink', audience_id='$trendaudience_id', country_id ='$trendcountry', trend_date='$trend_date' WHERE trend_id='$trend_id'";
                $actMsg = 'updated';
                $DRW->query($sql,$DRW_main);
                $upload_document = uploadTrendPDF($trend_id, $fileArray);
                $sql_delete = "Delete from cscan_trends_category Where trend_id='$trend_id'";
                $DRW->query($sql_delete,$DRW_main);
                $c1 = explode('|',$comboIDs);
                foreach($c1 as $c){
                    $c2 = explode('_',$c);
                    $sql_category = "INSERT INTO cscan_trends_category SET trend_id='$trend_id', sector_id='$c2[0]',category_id='$c2[1]', subcategory_id ='$c2[2]', subtosubcategory_id='$c2[3]'";
                    $DRW->query($sql_category,$DRW_main);
                }
            return 5;
    
    }else{
        return 2; //required parameter missing
    }
}

/**
 * Upload Document and convert pdf to text
 *
 * return boolean
 */
   function uploadTrendPDF($trend_id, $fileArray, $filekey = 'trend_document') {
       global $DRW, $DRW_read, $DRW_main, $DRW_crm;
        $AUTH_DATA = $GLOBALS['AUTH_DATA'];
        global $s3, $bucket_name;
        //echo $trend_id;
        //print_r($fileArray); die;
        if (isset($fileArray[$filekey])) {
            $pdfNameArr = preg_replace('!\s+!', ' ', trim($fileArray[$filekey]['name']));
            $pdfNameArr = preg_replace('/[^a-zA-Z0-9_\\.\\-]/', '_', $pdfNameArr);
            $pdfTypeArr = $fileArray[$filekey]['type'];
            $pdfTempNameArr = $fileArray[$filekey]['tmp_name'];
            $pdfSizeArr = $fileArray[$filekey]['size'];
        } else {
            $pdfNameArr = '';
            $pdfTypeArr = '';
            $pdfTempNameArr = '';
            $pdfSizeArr = 0;
        }
        $message = 0;
        $yearpath = date('Y/');
        $monthpath = date('m/');
        $datepath = $yearpath . $monthpath;

        $root = dirname(__FILE__);
        $root = substr($root, 0, strpos($root, '/class'));
        $pdfpart = $root . '/trend_document/';
        $pdfPath = "$pdfpart$datepath$trend_id/";
        $pdfs3newPath = "trend_document/$datepath$trend_id/";

        if ($pdfNameArr != '') {
            if ($filekey == 'trend_document') {
                $valid_types = array("pdf", "ppt", "pptx","csv","docx","doc","xls","xlsx");
            }
            $name_arr = explode(".", $pdfNameArr);
            $ext_name = strtolower(end($name_arr));
            $ext_type = $ext_name;
            if (in_array($ext_type, $valid_types) || in_array($ext_name, $valid_types)) {
                if (is_uploaded_file($pdfTempNameArr)) {
                    if (!is_dir($pdfpart . $yearpath)) {
                        @mkdir($pdfpart . $yearpath, 02755);
                    }
                    if (!is_dir($pdfpart . $datepath)) {
                        @mkdir($pdfpart . $datepath, 02755);
                    }
                    if (!is_dir($pdfPath)) {
                        @mkdir($pdfPath, 02755);
                    }
                    $pdfName = substr($pdfNameArr, 0, -4);
                    $pdfName = $pdfName . "_" . $trend_id . "." . $ext_name;
                    $file = $pdfPath . $pdfName;
                    if ($ext_name == 'pdf') {
                        $content_type = "application/pdf";
                    } else {
                        if ($ext_name == "ppt") {
                            $content_type = "application/vnd.ms-powerpoint";
                        } elseif ($ext_name == "pptx") {
                            $content_type = "application/vnd.openxmlformats-officedocument.presentationml.presentation";
                        }elseif ($ext_name == "doc") {
                            $content_type = "application/msword";
                        }elseif ($ext_name == "docx") {
                            $content_type = "application/vnd.openxmlformats-officedocument.wordprocessingml.document";
                        }elseif ($ext_name == "csv") {
                            $content_type = "text/csv";
                        }elseif ($ext_name == "xls") {
                            $content_type = "application/vnd.ms-excel";
                        }elseif ($ext_name == "xlsx") {
                            $content_type = "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet";
                        }
                        
                    }
                    $result = $s3->putObject([
                        'Bucket' => $bucket_name,
                        'Key' => $pdfs3newPath . $pdfName,
                        'SourceFile' => $pdfTempNameArr,
                        'ACL' => 'public-read',
                        'ContentType' => $content_type,
                        'Metadata' => array(
                            'string' => 'string'
                        )
                    ]);
                   // $pdfContent = '';
                    if (move_uploaded_file($pdfTempNameArr, $pdfPath . $pdfName)) {
                       $sqlUpdate = "UPDATE cscan_trend_report SET file_path='$pdfs3newPath' , file_name='$pdfName' WHERE trend_id='$trend_id'";
                       $DRW->query($sqlUpdate, $DRW_main);
                        $pdfContent = '';
                        if ($content_type == "application/pdf") {
                            //echo "fsfdjfdhfhdfhd"; die;
                            // $ppt_text = pptx_to_text($file); die;
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
                        /********************* DOCX TO TEXT ********************/
                        else if($content_type == "application/msword" || $content_type =="application/vnd.openxmlformats-officedocument.wordprocessingml.document"){
                                $full_path= $pdfPath . $pdfName;
                              $pdfContent = docx_to_text($full_path);
                            saveTrendDocumentText($trend_id, $pdfContent);
                                if (is_file($file)) {
                                    @unlink($file);
                                }
                            }
                        /*********************CSV TO TEXT  ********************/
                        else if($content_type == "text/csv"){
                            $full_path= $pdfPath . $pdfName;
                            $pdfContent = file_get_contents($full_path);
                            //$pdfContent = csv_to_text($pdfName);
                            saveTrendDocumentText($trend_id, $pdfContent);
                                if (is_file($file)) {
                                    @unlink($file);
                                }
                            }
                        /*********************XLSX TO TEXT  ********************/
                        else if($content_type == "application/vnd.ms-excel" || $content_type =="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"){
                            $full_path= $pdfPath . $pdfName;
                            $pdfContent = xlsx_to_text($full_path);
                            saveTrendDocumentText($trend_id, $pdfContent);
                                if (is_file($file)) {
                                    @unlink($file);
                                }
                            }
                        /********************* PPTX TO TEXT ********************/
                       else if($content_type == "application/vnd.ms-powerpoint" || $content_type =="application/vnd.openxmlformats-officedocument.presentationml.presentation"){
                           $full_path= $pdfPath . $pdfName; 
                           $pdfContent = pptx_to_text($full_path);
                            saveTrendDocumentText($trend_id, $pdfContent);
                                if (is_file($file)) {
                                    @unlink($file);
                                }
                            }
                            
                    }
                }
            }
        }
    }
    
    /**
     * Save text into db
     *
     * @return boolean 
     */
    function saveTrendDocumentText($trend_id, $pdfContent) {
       global $DRW, $DRW_read, $DRW_main, $DRW_crm;
        $query2 = "SELECT SQL_NO_CACHE COUNT(*) FROM cscan_trend_document_text WHERE trend_id=$trend_id";
        $query_result2 = $DRW->query($query2, $DRW_read);
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
             $sql = "INSERT INTO cscan_trend_document_text (trend_id,document_text) values ($trend_id,'" . $DRW->real_escape_string($pdfContent) . "')";
             $DRW->query($sql, $DRW_main);
        }
    }
    
    /**
     * Save docx_to_text
     *
     * @return value 
     */
    function docx_to_text($pdfName){
       //echo $pdfName; die;
        $striped_content = '';
        $content = '';
         $zip = new ZipArchive;
         $zip = zip_open($pdfName);
        if (!$zip || is_numeric($zip)) return false;
        while ($zip_entry = zip_read($zip)) {
                //echo "dsdshdhshdhsd"; die;
                if (zip_entry_open($zip, $zip_entry) == FALSE) continue;

                if (zip_entry_name($zip_entry) != "word/document.xml") continue;

                $content .= zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));

                zip_entry_close($zip_entry);
        }// end while
        zip_close($zip);
        $content = str_replace('</w:r></w:p></w:tc><w:tc>', " ", $content);
        $content = str_replace('</w:r></w:p>', "\r\n", $content);
        $striped_content = strip_tags($content);
        return $striped_content;
    }
    /**
     * Save xlsx_to_text
     *
     * @return value 
     */
    function xlsx_to_text($pdfName) {
        $xml_filename = "xl/sharedStrings.xml"; //content file name
        $zip_handle = new ZipArchive;
        $output_text = "";
        if (true === $zip_handle->open($pdfName)) {
            if (($xml_index = $zip_handle->locateName($xml_filename)) !== false) {
                $xml_datas = $zip_handle->getFromIndex($xml_index);
                $xml_handle = DOMDocument::loadXML($xml_datas, LIBXML_NOENT | LIBXML_XINCLUDE | LIBXML_NOERROR | LIBXML_NOWARNING);
                $output_text = strip_tags($xml_handle->saveXML());
            } else {
                $output_text .= "";
            }
            $zip_handle->close();
        } else {
            $output_text .= "";
        }
        return $output_text;
    }
    /**
     * Save pptx_to_text
     *
     * @return value 
     */
    function pptx_to_text($pdfName) {
        $input_file = $pdfName;
        $zip_handle = new ZipArchive;
        $output_text = "";
        if ($zip_handle->open($input_file)) {
            //echo 'success';
        } else {
            //echo 'failed';
        } 
        if (true === $zip_handle->open($input_file)) {
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
            //echo "sdsdsds"; die;
            $output_text .= "";
        }
        return $output_text;
    } 
    
    function read_doc($pdfName) {
            $fileHandle = fopen($pdfName, "r");
            $line = @fread($fileHandle, filesize($pdfName));			   
            $lines = explode(chr(0x0D),$line);
            $outtext = "";

            foreach($lines as $thisline)
              {
                    $pos = strpos($thisline, chr(0x00));

                    if (($pos != FALSE) || (strlen($thisline)==0))
                      { //$outtext .= $thisline." ";
                      } else {
                            $outtext .= $thisline." ";
                      }
              }
              //echo $outtext; die;
             $outtext = preg_replace("/[^a-zA-Z0-9\s\,\.\-\n\r\t@\/\_\(\)]/","",$outtext);
            return $outtext;
    }
    /**
     * Get the list according id
     *
     * @return array
     */
    function getTrendById($trend_id) {
        global $DRW, $DRW_read, $DRW_main, $DRW_crm;
        if(!defined('ENV')){
        define('ENV',getenv('SERVER_NAME'));
        }
        $selectSqlcolumn='';
        //if(ENV == 'localhost' || ENV == 'demo.competiscan.com'){
        $selectSqlcolumn ='rndtrend_id,';
        //}
        $trendlist = array();
        $query = "SELECT SQL_NO_CACHE trend_id,$selectSqlcolumn trend_name,trend_link, file_path,file_name,audience_id,country_id,trend_date
            FROM cscan_trend_report
            WHERE trend_id='$trend_id'";
        $result = $DRW->query($query, $DRW_read);
        $trendlist=$DRW->fetch_assoc($result);
        return $trendlist;
    }
    
    /**
     * Get the list according id
     *
     * @return array
     */
    function getAllCategoryByTrendId($trend_id) {
        global $DRW, $DRW_read, $DRW_main, $DRW_crm;
        $comboIDsA = array();
        $sql= "SELECT SQL_NO_CACHE sector_id,category_id, subcategory_id,subtosubcategory_id
            FROM cscan_trends_category WHERE trend_id='$trend_id'";
         $result = $DRW->query($sql, $DRW_read);
            $resultCount = $DRW->num_rows($result);
            if($resultCount >0){
            while ($rowCatData = $DRW->fetch_row($result)) {
                    $comboIDsA[] = implode('_', $rowCatData);
            }
            $comboIDs = implode('|', $comboIDsA);
          //print_r($comboIDs); die;
           return $comboIDs;
        }
       }
    /**
     * Get the getCategoryMulti()
     *
     * @return array
     */  
    function getCategoryMulti($sectorID) {
        global $DRW, $DRW_read, $DRW_main, $DRW_crm;
        $arr = array();
        $sql = "select sectorID, sectorName  from cscan_sector where parentID IN($sectorID)  ORDER BY sectorName";
        $result = $DRW->query($sql, $DRW_read);
        if ($DRW->num_rows($result) > 0) {
            while ($row = $DRW->fetch_array($result)) {
                $sectorID = $row['sectorID'];
                $sectorName = $row['sectorName'];
                $arr[$sectorID] = $sectorName;
            }
            return $arr;
        } else {
            return 0;
        }
    }
    /**
     * Get the getCategoryMulti()
     *
     * @return array
     */ 

    function getSubCategoryMulti($categoryID, $withcat = true) {
        global $DRW, $DRW_read, $DRW_main, $DRW_crm;
        $categoryQuery = "select sectorID,sectorName from cscan_sector where sectorID IN($categoryID)";
        $categoryQuery = $DRW->query($categoryQuery, $DRW_read);
        $result1 = $DRW->fetch_array($categoryQuery);
        $categoryName = $result1['sectorName'];
        //$categoryID = $result1[sectorID];
        //echo $categoryID;
        $arr = array();
        $sql = "select sectorID, sectorName from cscan_sector where parentID IN($categoryID) ORDER BY sectorName"; //'$sectorID'
        //echo $sql;
        $result = $DRW->query($sql, $DRW_read);
        $count = $DRW->num_rows($result);
        if ($count > 0) {
            while ($row = $DRW->fetch_array($result)) {
                $sectorID = $row['sectorID'];
                $sectorName = $row['sectorName'];
                if ($withcat) {
                    $sectorName = $categoryName . "-" . $sectorName;
                }
                $arr[$sectorID] = $sectorName;
            }
            return $arr;
        } else {
            return 0;
        }
    }
    /*
     * Random gerate 10 digits numbers
     */
    function generateRandomNumeric() {
    $alphabet = '0123456789';
    $pass = array(); //remember to declare $pass as an array
    $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
    for ($i = 0; $i < 10; $i++) {
        $n = rand(0, $alphaLength);
        $pass[] = $alphabet[$n];
    }
    return implode($pass); //turn the array into a string
    }
//echo docx_to_text('/var/www/html/competiscan.com/trend_document/2020/04/1596/docx_text._1596.docx'); die;