<?php
function createthumb($name,$filename,$new_w=150,$new_h=100) {
	$src_img = false;
	
	if(preg_match('/(jpg|jpeg)$/i',$name)) {
		$src_img = imagecreatefromjpeg($name);
	}
	elseif(preg_match('/png$/i',$name)) {
		$src_img = imagecreatefrompng($name);
	}
	elseif(preg_match('/gif$/i',$name)) {
		$src_img = imagecreatefromgif($name);
	}
	
	if($src_img){
		$old_x = imageSX($src_img);
		$old_y = imageSY($src_img);
		$dst_img = imagecreatetruecolor($new_w,$new_h);
		imagecopyresampled($dst_img,$src_img,0,0,0,0,$new_w,$new_h,$old_x,$old_y);
		
		if(preg_match('/(jpg|jpeg)$/i',$name)) {
			imagejpeg($dst_img,$filename,100);
		}
		elseif(preg_match('/png$/i',$name)) {
			imagepng($dst_img,$filename,0,PNG_NO_FILTER);
		}
		elseif(preg_match('/gif$/i',$name)) {
			imagegif($dst_img,$filename);
		}
		
		imagedestroy($dst_img); 
		imagedestroy($src_img);
		
		return true;
	}
	
	return false;
}
function createPreviewJPG($path,$name,$productID,$back=false,$dops=false,$document_id=1){
	$dops = intval($dops);
	$s = '';
	$pos = strpos(__FILE__,'/includes/');
	if($pos!==false){
		$s .= 'cd '.substr(__FILE__,0,$pos).'; ';
	}
	$s .= '/usr/bin/php convert_back.php '.escapeshellarg($path).' '.escapeshellarg($name).' '.escapeshellarg($productID).' '.$dops.' '.$document_id;
	
	if($back){
		$s .= ' > /dev/null 2>&1 &';
	}
	$ex = exec($s);
}
// for using social media image conversion to old process
function createPreviewJPG1($path,$name,$productID,$back=false,$dops=false,$document_id=1){
	$dops = intval($dops);
	$s = '';
	$pos = strpos(__FILE__,'/includes/');
	if($pos!==false){
		$s .= 'cd '.substr(__FILE__,0,$pos).'; ';
	}
	$s .= '/usr/bin/php convert_back_1.php '.escapeshellarg($path).' '.escapeshellarg($name).' '.escapeshellarg($productID).' '.$dops.' '.$document_id;
	
	if($back){
		$s .= ' > /dev/null 2>&1 &';
	}
	$ex = exec($s);
}
###########START ADD PRODUCT PDF OPTMIZIZE#################
function createPreviewJPGWithPythonCode($path,$name,$productID,$back=false,$dops=false,$document_id=1){
	$dops = intval($dops);
        $root = dirname(__FILE__);
        if (strpos($root, '/includes') !== false) {
            $root = substr($root, 0, strpos($root, '/includes'));
        }
        $output_image = shell_exec("python3 ".$root."/convert_back_pdftoimage.py $path $name $productID $document_id $dops 2>&1");
        //echo $output_image; die;
        $output_image = str_replace(array( "[", "]","'" ), '', $output_image); 
        $output_image_array = explode(",", $output_image);
        /*echo "<pre>";
        print_r($output_image_array);
        echo count($output_image_array);die; */  
        if($document_id!=0){
           savePDFToImage($productID,$document_id,$path,$output_image_array);
          }
	
}
###########END ADD PRODUCT PDF OPTMIZIZE#################
function rotateImage($path){
	$rotate = 'convert -rotate 90 '.escapeshellarg($path).' '.escapeshellarg($path);
	exec($rotate);
}
function FULL_sample_img($productID,$document_id,$img_document_path,$img_document_filename){
	global $DRW,$DRW_read,$DRW_main,$DRW_crm;
	$root = dirname(__FILE__);
	$pos = strpos($root,'/includes');
	if($pos!==false){
		$root = substr($root,0,$pos);
	}
	$src = FULL_sample_img_path($img_document_path,$img_document_filename);
	if(!is_file($src)){
		$query2 = "SELECT document_filename,document_path FROM cscan_document WHERE productID=$productID AND document_id=$document_id";
		$query_result2 = $DRW->query($query2,$DRW_read);
		$data2 = $DRW->fetch_row($query_result2);
		$document_filename = $data2[0];
		$document_path = $root.$data2[1];
		$pdf_src = "$document_path$document_filename";
		if($document_filename!='' && is_file($pdf_src)){
			createPreviewJPG($document_path,$document_filename,'FULL_'.$productID,false,3,0);
		}
	}
	return $src;
}
function FULL_sample_img_path($img_document_path,$img_document_filename){
	$root = dirname(__FILE__);
	$pos = strpos($root,'/includes');
	if($pos!==false){
		$root = substr($root,0,$pos);
	}
	$src = $root.$img_document_path.'FULL_'.$img_document_filename;
	return $src;
}

function video_humb($productID,$document_id,$img_document_path,$img_document_filename){
	global $DRW,$DRW_read,$DRW_main,$DRW_crm;
	$root = dirname(__FILE__);
	$pos = strpos($root,'/includes');
	if($pos!==false){
		$root = substr($root,0,$pos);
	}
	//$src = FULL_sample_img_path($img_document_path,$img_document_filename);
        $src = $root.$img_document_path.$img_document_filename;
	return $src;
}

/* ########### Image save under db created by AWS Lambda service    ############*/

function saveImagefromPdf($path,$name,$productID){
    $AUTH_DATA = $GLOBALS['AUTH_DATA'];
    global $DRW, $DRW_read, $DRW_main, $DRW_crm;
    global $s3,$bucket_name;
   /* $path='PDF/2019/08/2322658/';
    $name='test_2322658.pdf';
    $productID=2322658;    
    */       
    //$info = $s3->doesObjectExist([$bucket_name, $path.$name.'/index.json']);
    /* $info = $s3->getObjectInfo([$bucket_name,'PDF/2019/08/2322706/index.json',
    ]);
     */
    
    $response =false;
    while($response==false){
        $response = $s3->doesObjectExist($bucket_name, $path.$name.'/index.json');
    }   
    $results = $s3->getObject([
          'Bucket' => $bucket_name,
          'Key' => $path.$name.'/index.json'
      ]);
     
    $bodyAsString = $results['Body']->__toString();
    $data=(array) (json_decode($bodyAsString));
    
    if(!empty($data['pages'])){
        $checkV = "SELECT img_document_path,img_document_filename FROM cscan_img_document WHERE productID=$productID AND document_id=1";
        $checkV = $DRW->query($checkV, $DRW_read);
        while ($dataV = $DRW->fetch_row($checkV)) {            
            $info = $s3->doesObjectExist($bucket_name,$dataV[0] . $dataV[1]);            
            if($info){
                $result = $s3->deleteObject([
                    'Bucket' => $bucket_name,
                    'Key' => $dataV[0] . $dataV[1],
                ]);
            }
        }
        $sql = "DELETE FROM cscan_img_document WHERE productID=$productID AND document_id=1";
        $DRW->query($sql, $DRW_main);
        $img_document_sort = 0; 
        $imgcount=count($data['pages']);
        $percentimg=(int)$imgcount*0.1;
        if($percentimg<1)
            $percentimg=1;
        
        $p=array('percentimg' => 1,'check_img_name' => '', 'check_img_path'=>'');
        foreach($data['pages'] as $result){       
            $imgpath= $result->backgroundImageURI;
            if(!empty($imgpath)){
                $imgarr =explode('/',$imgpath);
                $img_document_path=$path.$name.'/';
                $img_document_filename=end($imgarr);
                $img_document_content_type='image/png';
                if($img_document_sort==0){
                    $img_document_default = 1;
                }else{
                    $img_document_default = 0;
                }
                $img_size_byte = '16276';
                if($p['percentimg']==$percentimg){
                    $p['check_img_name']=$img_document_filename;
                    $p['check_img_path']=$img_document_path;
                }
                $img_document_sort_ins = $img_document_sort + 1;
                $sql = "REPLACE INTO cscan_img_document (productID,document_id,img_document_sort,img_document_filename,img_document_createddate,img_document_content_type,img_document_size_byte,img_document_createdby,img_document_default,img_document_path)
                                VALUES ($productID,1,$img_document_sort_ins,'" . $DRW->real_escape_string($img_document_filename) . "',NOW(),'image/png',$img_size_byte,{$GLOBALS['AUTH_DATA']['userID']},$img_document_default,'" . $DRW->real_escape_string($img_document_path) . "')";
                $DRW->query($sql, $DRW_main);
                $img_document_sort++;       
                $img_document_default = 0;
            }
            $p['percentimg']++;
        }
        if(!empty($p['check_img_name'])){
            $response =false;
            while($response==false){
                $response = $s3->doesObjectExist($bucket_name, $p['check_img_path'].$p['check_img_name']);
            }  
            
        }
    }     
    
}
###########START ADD PRODUCT PDF OPTMIZIZE#################
function savePDFToImage($productID, $document_id, $pdfPath,$output_image_array,$maxbytes = 500000) {
    global $DRW, $DRW_read, $DRW_main, $DRW_crm;
    global $s3,$bucket_name,$serverbaseurl;
    $root = dirname(__FILE__);
    if (strpos($root, '/includes') !== false) {
        $root = substr($root, 0, strpos($root, '/includes'));
    }
    $checkV = "SELECT img_document_path,img_document_filename FROM cscan_img_document WHERE productID=$productID AND document_id=$document_id";
    $checkV = $DRW->query($checkV, $DRW_read);
    while ($dataV = $DRW->fetch_row($checkV)) {
        $path = $root . $dataV[0] . $dataV[1];
        if(strpos($dataV[0],'/')=='0'){
         $dataV[0]  = substr($dataV[0],1);
        } 
        $info = $s3->doesObjectExist($bucket_name,$dataV[0] . $dataV[1]);
        if($info){
                $result = $s3->deleteObject([
                    'Bucket' => $bucket_name,
                    'Key' => $dataV[0] . $dataV[1],
                ]);
        }
        
        
        if (is_file($path) && $path != $pdfPath . $dataV[1]) {
           // @unlink($path);
        }
    }
    $sql = "DELETE FROM cscan_img_document WHERE productID=$productID AND document_id=$document_id";
    $DRW->query($sql, $DRW_main);
    $img_document_default = 1;
    $img_document_sort = 0;
    $img_document_path = strstr($pdfPath,'/PDF');
    $length_image = count($output_image_array);
    for($img=0; $img<$length_image; $img++){
        $image_name=trim($output_image_array[$img]);
        $image = $pdfPath.$image_name;
        $img_size_byte = filesize($image);
        $img_document_sort_ins = $img_document_sort + 1;
        $sql = "REPLACE INTO cscan_img_document (productID,document_id,img_document_sort,img_document_filename,img_document_createddate,img_document_content_type,img_document_size_byte,img_document_createdby,img_document_default,img_document_path)
			VALUES ($productID,$document_id,$img_document_sort_ins,'" . $DRW->real_escape_string($image_name) . "',NOW(),'image/jpeg',$img_size_byte,{$GLOBALS['AUTH_DATA']['userID']},$img_document_default,'" . $DRW->real_escape_string($img_document_path) . "')";
        $DRW->query($sql, $DRW_main);
        sendthumbimageons3($img_document_path , $DRW->real_escape_string($image_name) );
        $img_document_sort++;
        $img_document_default = 0;
    }
}
###########END ADD PRODUCT PDF OPTMIZIZE#################
?>