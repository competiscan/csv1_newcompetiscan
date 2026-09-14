<?php
require_once('includes/globalSession.php');
require_once('includes/checklogin.php');
require_once('includes/thumb.php');
require_once 'HTTP/Download.php';
//require_once('includes/sphinx_function2.php');  //sphinx functions.
require_once('includes/functions_latest2.php');  //latest function
$copyright= html_entity_decode('&copy;&nbsp;',ENT_QUOTES,'UTF-8');
##################PPTImages Isses##########################
require_once 'ppt/src/PhpPresentation/Autoloader.php';
\PhpOffice\PhpPresentation\Autoloader::register();
require_once 'ppt/Common/src/Common/Autoloader.php';
\PhpOffice\Common\Autoloader::register();
use PhpOffice\PhpPresentation\PhpPresentation;
use PhpOffice\PhpPresentation\DocumentLayout;
use PhpOffice\PhpPresentation\IOFactory;
use PhpOffice\PhpPresentation\Style\Shadow;
use PhpOffice\PhpPresentation\Style\Color;
use PhpOffice\PhpPresentation\Style\Border;
use PhpOffice\PhpPresentation\Style\Alignment;
use PhpOffice\PhpPresentation\Shape\Drawing\Base64; 
##################PPTImages Isses##########################
$newdocumentpath='';
$img_content_type='';
$source='';
$img_companyID='';
if(!defined('ENV')){
    define('ENV',getenv('SERVER_NAME'));
}
/*############START CONVERT EMAIL IMAGE############*/
if(isset($_SESSION['sess_userID'])){
    $sess_userID=$_SESSION['sess_userID'];
} else{
   $sess_userID=''; 
}
/*############END CONVERT EMAIL IMAGE############*/
//error_reporting( E_ALL ^ E_DEPRECATED );//// 
//ini_set('display_errors',1);
$headlinesarray =   array();
if(!empty($_REQUEST['headlines'])){
    $headlinesarray =   $_REQUEST['headlines'];
}
$isheadline =   false;
if(isset($_REQUEST['id'])) {
	$productID = (int)$_REQUEST['id'];
}
else {
	$productID = 0;
}
if(isset($_REQUEST['did'])) {
	$document_id = (int)$_REQUEST['did'];
}
else {
	$document_id = 1;
}
$pages = array();
if(isset($_REQUEST['pages'])) {
	$pages = $_REQUEST['pages'];
}
if(!is_array($pages)){
	$pages = array($pages);
}
if(isset($_REQUEST['sort'])) {
	$sort = (int)$_REQUEST['sort'];
}
else {
	$sort = -3;
}
$ptext = '';
if(!empty($_REQUEST['pp'])) {
	$pp = (int)$_REQUEST['pp'];
	$ptext = 'PowerPoint';
}
else {
	$pp = 0;
}
if(!empty($_REQUEST['pdf'])) {
	$pdf = (int)$_REQUEST['pdf'];
	$ptext = 'PDF';
	$pp = 0;
}
else {
	$pdf = 0;
}

if (ENV == 'localhost') {
    $site_urls = 'http://localhost/competiscan.com/';
} elseif (ENV == 'demo.competiscan.com') {
    $site_urls = 'http://demo.competiscan.com/';
} else {
    $site_urls = 'https://competiscan.com/';
}

$basket_name = '';
$productIDArray = array();

if(isset($_REQUEST['bid'])) {
	$bid = (int)$_REQUEST['bid'];
	if($bid>=0) {
		$checked_pids = array();
		foreach($pages as $p_p){
			list($p_id,$p) = explode('_',$p_p);
			if(!in_array($p_id,$checked_pids)){
				$checked_pids[] = $p_id;
			}
		}
		list($orderby,$dorelev,$doexpans) = doQuerySort($sort);
		//list($sql) = doQuery(0, false, '', false, $bid);
                //list($sql) = doQuerytestsphinx(0, false, '', false, $bid);
                list($sql) = doQuery_latest2(0, false, '', false, $bid);
                
		$sql .= $orderby;
		$sql .= " Limit 0,100";
		$rs = $DRW->query($sql,$DRW_read);
		while($row = $DRW->fetch_assoc($rs)) {
			if(in_array($row['theproductID'],$checked_pids) || count($pages)==0){
				$productIDArray[] = $row['theproductID'];
			}
		}
	}
	
	if($bid==0){
		$basket_name = 'Default Basket';
	}
	else{
		$sql = "SELECT basket_name FROM cscan_basket WHERE basket_id=$bid AND userID={$_SESSION['sess_userID']}";
		$result = $DRW->query($sql,$DRW_read); 
		$rs = $DRW->fetch_array($result);
		$basket_name = $rs['basket_name'];
	}
}

if(!empty($productID)){
	$productIDArray[] = $productID;
}

if($pp){
	//set_include_path(get_include_path() . PATH_SEPARATOR . './php_powerpoint/Classes_70298/');
	//include_once 'PHPPowerPoint.php';
       // include_once 'PHPPowerPointDrowing.php';
	//include_once 'PHPPowerPoint/IOFactory.php';
}
elseif($pdf){
	//require_once('File/PDF.php');
        require('fpdf/fpdf.php');
}

if (count($productIDArray) > 0 || isset($imageDataArray) && false) {
	if($pp){
                
                $objPHPPowerPoint = new PhpPresentation();
                
		//$objPHPPowerPoint = new PHPPowerPoint();
                //Start new implemention ppt export
                $objPHPPowerPoint->getLayout()->setDocumentLayout(['cx' => 1280, 'cy' => 800], true)
                ->setCX(1280, DocumentLayout::UNIT_PIXEL)
                ->setCY(800, DocumentLayout::UNIT_PIXEL);
                //End new implemention ppt export
		$objPHPPowerPoint->getProperties()->setCreator("Competiscan");
		$objPHPPowerPoint->getProperties()->setLastModifiedBy("Competiscan");
		$objPHPPowerPoint->getProperties()->setTitle("Office 2007 PPTX Document");
		$objPHPPowerPoint->getProperties()->setSubject("Competiscan Office 2007 PPTX Document");
		$objPHPPowerPoint->getProperties()->setDescription("Competiscan Document (c) ".date('Y'));
		$objPHPPowerPoint->getProperties()->setKeywords("office 2007 openxml php");
		$objPHPPowerPoint->getProperties()->setCategory("Competiscan Document");
                 
	}
	elseif($pdf){
		/* 
		class My_File_PDF extends File_PDF {
			function header() {
				
				// Select Arial bold 15
				$this->setFont('Arial', 'B', 15);
				// Move to the right
				$this->cell(80);
				// Framed title
				$this->cell(30, 10, 'Title', 1, 0, 'C');
				// Line break
				$this->newLine(20);
				$this->setFillColor('rgb', .93, .93, 1);
			}
			function footer() {
				// Go to 1.5 cm from bottom
				$this->setY(-15);
				// Select Arial italic 8
				$this->setFont('Arial', 'I', 8);
				// Print centered page number
				$this->cell(0, 10, 'Page ' . $this->getPageNo().' of {nb}', 0, 0, 'C');
				$this->setFillColor('rgb', .93, .93, 1);
			}
		}
		*/
		
		//$file_pdf = @File_PDF::factory(array('orientation' => 'P', 'unit'=>'mm', 'format'=>'A4'));
               // $file_pdf->open();
               ############### Start S3 Implementation PDF ##################
                $file_pdf = new FPDF();
		$file_pdf->addPage();
		$page_width = $file_pdf->getPageWidth();
		$page_height = $file_pdf->getPageHeight();
		$fonts = 14;
                $file_pdf->setFont('Arial','B',$fonts);
                $file_pdf->setTextColor(255,255,255);
		$file_pdf->setFillColor(0,0,255);
		//$file_pdf->cell(0, $fonts/2, $basket_name, 0, 1, 'b',1);
                $file_pdf->Cell(0,$fonts/2,$basket_name,0,1,'R',true);
		$file_pdf->cell(0, 1, '', 0, 1, 'L',0);
//		Cell(float w [, float h [, string txt [, mixed border [, int ln [, string align [, boolean fill [, mixed link]]]]]]])	
		//$file_pdf->image(dirname(__FILE__).'/images/competiscan_pdf.jpg',$file_pdf->getX(), $file_pdf->getY(), $page_width, 0, 'JPEG', 'https://www.competiscan.com/');//$width,$height
                $pdf_logo=dirname(__FILE__).'/images/pdf_logo.jpg';
                //echo $page_width; exit;
                if(file_exists($pdf_logo)) { 
                $file_pdf->image($pdf_logo,$file_pdf->getX(), $file_pdf->getY(), ($page_width-20), 0, 'JPEG', $site_urls);//$width,$height
                }
                $file_pdf->cell(0, 28, '', 0, 1, 'L',0);
		$file_pdf->cell(0, 2, '', 0, 1, 'L',1);
		$file_pdf->setFillColor(0,0,255);
                ############### END S3 Implementation PDF ##################
                
	}
	$i = 0;
	foreach($productIDArray as $productID){
                $img_companyID='';
                $source='';
                $newdocumentpath='';
		$productQuery = "SELECT productHeadline,entryID,mChannelID FROM cscan_product_detail WHERE productID=$productID";
		$productQuery = $DRW->query($productQuery,$DRW_read);
		$productRs = $DRW->fetch_array($productQuery);
		$mChannelID=$productRs['mChannelID'];
		if(empty($productRs['entryID'])){
			continue;
		}
		if($pp){
                       
			if($i>0){ 
				$objPHPPowerPoint->createSlide();
				$objPHPPowerPoint->setActiveSlideIndex($i);
			} 
                        //die('Test');
			$currentSlide = $objPHPPowerPoint->getActiveSlide();
                        //print_r($currentSlide); die;
                        //Start new implemention ppt export
                        $text_offset = 20;
			$text_size = 10;
			$shape = $currentSlide->createRichTextShape();
			$shape->setWidth(320);
			$shape->setOffsetX(40);
                        $shape->setOffsetY(675);
			$shape->getActiveParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_JUSTIFY );
			$textRun = $shape->createTextRun($productRs['entryID']);
			//$textRun->getHyperlink()->setUrl('http://'.$_SERVER['HTTP_HOST'].'/index.php?product='.$productID);
			$textRun->getHyperlink()->setUrl('https://cp.competiscan.com/productdetail?id='.$productID);
			$textRun->getFont()->setName('Arial');
			$textRun->getFont()->setSize($text_size); //Calibri 24 Align Right Gray
			$textRun->getFont()->setColor( new Color( 'FF808080' ) ); //FFA9A9A9 FF2F4F4F FF696969 FFD3D3D3 FF778899 FF708090 FFC0C0C0
                        //End new implemention ppt export
                        
			/*$text_offset = 20;
			$text_size = 20;
			$shape = $currentSlide->createRichTextShape();
			$shape->setWidth(320);
			$shape->setOffsetX(40);
			$shape->setOffsetY($text_offset); 
                        $shape->getActiveParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_JUSTIFY );
			//$shape->getActiveParagraph()->getAlignment()->setHorizontal( PHPPowerPoint_Style_Alignment::HORIZONTAL_JUSTIFY );
			$textRun = $shape->createTextRun($productRs['entryID']);
			$textRun->getHyperlink()->setUrl('http://'.$_SERVER['HTTP_HOST'].'/index.php?product='.$productID);
			$textRun->getFont()->setName('Calibri');
			$textRun->getFont()->setSize($text_size); //Calibri 24 Align Right Gray
			//$textRun->getFont()->setColor( new PHPPowerPoint_Style_Color( 'FF808080' ) ); //FFA9A9A9 FF2F4F4F FF696969 FFD3D3D3 FF778899 FF708090 FFC0C0C0
			$textRun->getFont()->setColor( new Color( 'FF808080' ) ); //FFA9A9A9 FF2F4F4F FF696969 FFD3D3D3 FF778899 FF708090 FFC0C0C0
                        
                        
			$text_offset = 70;
			$shape = $currentSlide->createDrawingShape();
			$shape->setName('Competiscan upper');
			$shape->setDescription('Competiscan upper');
                        //echo dirname(__FILE__).'/php_powerpoint/ppt-Upper-Blue-Bar.jpg'; die;
			$shape->setPath(dirname(__FILE__).'/php_powerpoint/ppt-Upper-Blue-Bar.jpg');
			$shape->setWidth(960);
			$shape->setOffsetY($text_offset);
			$shape = $currentSlide->createDrawingShape();
			$shape->setName('Competiscan footer '.($i + 1));
			$shape->setDescription('Competiscan footer '.($i + 1));
			$shape->setPath(dirname(__FILE__).'/php_powerpoint/ppt-footer-blue.jpg');
			$shape->setWidth(960);
			$shape->setOffsetY(670);
			$shape = $currentSlide->createDrawingShape();
			$shape->setName('Competiscan logo '.($i + 1));
			$shape->setDescription('Competiscan logo '.($i + 1));
			$shape->setPath(dirname(__FILE__).'/php_powerpoint/ppt-logo.png');
			$shape->setHeight(43);
			$shape->setOffsetX(753);
			$shape->setOffsetY(680);
			//die("sdhhsdtt");
			$shape = $currentSlide->createRichTextShape();
			$shape->setWidth(960);
			$shape->setOffsetY(693);
			//$shape->getActiveParagraph()->getAlignment()->setHorizontal( PHPPowerPoint_Style_Alignment::HORIZONTAL_CENTER );//VERTICAL_BOTTOM
			$shape->getActiveParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER );
                        $textRun = $shape->createTextRun($copyright.date('Y').' Confidential & Proprietary. All Rights Reserved'); //�2011 Confidential & Proprietary. All Rights Reserved
			$textRun->getFont()->setName('Arial');
			$textRun->getFont()->setSize(7);
			//$textRun->getFont()->setColor( new PHPPowerPoint_Style_Color(PHPPowerPoint_Style_Color::COLOR_WHITE) );
                        $textRun->getFont()->setColor( new Color(Color::COLOR_WHITE ) ); */
                        
                        
                        
		}
		elseif($pdf){
			$is_border_image=true;
			$query2 = "SELECT img_id,img_filename,img_content_type,img_size_byte,UNIX_TIMESTAMP(img_createddate),img_path,img_companyID FROM cscan_img WHERE productID=$productID AND img_id=1";
                        $query_result2 = $DRW->query($query2,$DRW_read);
                        if($DRW->num_rows($query_result2) > 0){   			
                            $data2 = $DRW->fetch_row($query_result2);
                            $img_id = (float)$data2[0];
                            $img_filename = $data2[1];
                            $img_content_type = strtolower($data2[2]);
                            $img_size_byte = $data2[3];
                            $img_createddate = $data2[4];
                            $img_path = $data2[5]; 
                            $img_companyID = $data2[6];
                            $img_filepath = dirname(__FILE__)."$img_path$img_filename";

                            #########################START S3 IMPLEMENTATION#####################
                            $newdocumentpath = $img_path.$img_filename; 
                            if($newdocumentpath!=''){ 
                                $source = $displays3URL.substr($newdocumentpath,1);
                                //Creating a presigned URL 
                                $exp_data=explode(".",$img_filename);
                                if($exp_data[1]=='gif'){
								//if(empty($exp_data[1])){	
                                    //$source=dirname(__FILE__).'/images/competiscan_logo.jpg';
									//$source=dirname(__FILE__).'/images/competiscan-logo2.png';
									$source=dirname(__FILE__).'/competiscan-logo2.png';
									$is_border_image=false;
                                }else{ 
                                    $cmd = $s3->getCommand('GetObject', [
                                            'Bucket' => $bucket_name,
                                            'Key' => substr($newdocumentpath,1)
                                    ]);
                                    $request = $s3->createPresignedRequest($cmd, '+20 minutes');
                                    // Get the actual presigned-url
                                    $presignedUrl = (string)$request->getUri(); 
                                    $source=$presignedUrl;
                                }
                            }

                        #########################END S3 IMPLEMENTATION#####################
                       }
			if($img_companyID!=0){  
				$query2 = "SELECT img_co_content_type,img_co_size_byte,UNIX_TIMESTAMP(img_co_createddate),img_co_path,img_co_filename FROM cscan_img_company WHERE companyID=$img_companyID";
				$query_result2 = $DRW->query($query2,$DRW_read);
				if($DRW->num_rows($query_result2) > 0){   			
                                    $data2 = $DRW->fetch_row($query_result2);
                                    $img_content_type = strtolower($data2[0]);
                                    $img_size_byte = $data2[1];
                                    $img_createddate = $data2[2];
                                    $img_path = $data2[3];
                                    $img_filename = $data2[4];
                                    $img_filepath = dirname(__FILE__)."$img_path$img_filename";
                                    #########################START S3 IMPLEMENTATION#####################
                                    $newdocumentpath = $img_path.$img_filename; 
                                    if($newdocumentpath!='') {
                                        $source = $displays3URL.substr($newdocumentpath,1);   
                                        $exp_data=explode(".",$img_filename);
                                        if($exp_data[1]=='gif'){
										//if(empty($exp_data[1])){		
                                            //$source=dirname(__FILE__).'/images/competiscan_logo.jpg';;
											//$source=dirname(__FILE__).'/images/competiscan-logo2.png';
											$source=dirname(__FILE__).'/competiscan-logo2.png';
											$is_border_image=false;
                                        } else{                       
                                            //Creating a presigned URL 
                                            $cmd = $s3->getCommand('GetObject', [
                                                    'Bucket' => $bucket_name,
                                                    'Key' => substr($newdocumentpath,1)
                                            ]);
                                            $request = $s3->createPresignedRequest($cmd, '+20 minutes');
                                            // Get the actual presigned-url
                                            $presignedUrl = (string)$request->getUri(); 
                                            $source=$presignedUrl;
                                        }
                                    }
                                    #########################END S3 IMPLEMENTATION#####################

                                }
                           }
			
			$file_pdf->cell(0, 4, '', 0, 1, 'L',0);
			
			//image/jpeg,image/JPG,image/gif,image/png
			//image/jpeg, image/pjpeg, image/gif , image/png,image/x-png, image/bmp
			$itype = '';
			if(strpos($img_content_type,'png')!==false){
				$itype = 'PNG';
			}
			elseif(strpos($img_content_type,'jpeg')!==false || strpos($img_content_type,'jpg')!==false){
				$itype = 'JPEG';
			}
                         	if($file_pdf->getY()+22>$page_height){
					$file_pdf->addPage();
				}
                               #########################START S3 IMPLEMENTATION#####################  
                                $info='';
                                if($newdocumentpath!=''){
                                $info = $s3->doesObjectExist($bucket_name,substr($newdocumentpath,1));
                               
                                if($info){
									//echo $source; die;
				  					//$file_pdf->image($source,$file_pdf->getX(), $file_pdf->getY(), 0, 20, $itype, $site_urls.'index.php?product='.$productID);
									$file_pdf->image($source,$file_pdf->getX(), $file_pdf->getY(), 0, 20, $itype,'https://cp.competiscan.com/productdetail?id='.$productID);
                                } 
                               } 
                               #########################END S3 IMPLEMENTATION#####################
                               if($is_border_image) {
									$file_pdf->cell(30, 20, '', 'LTRB', 1, 'L',0);
							   }else{
									$file_pdf->cell(30, 20, '', '', 1, 'L',0);
							   }
							   //$file_pdf->cell(30, 20, '', 'LTRB', 1, 'L',0);
				$file_pdf->cell(0, 2, '', 0, 1, 'L',0);
			//}
			
			$bq = "SELECT basket_note,DATE_FORMAT(basket_date,'%m/%d/%Y') FROM cscan_product_basket WHERE basket_id=$bid AND userID={$_SESSION['sess_userID']} AND productID=$productID";
			$rsb = $DRW->query($bq,$DRW_read);
			$datab = $DRW->fetch_row($rsb);
			$basket_note = $datab[0];
			$basket_date = $datab[1];
			
			$fonts = 10;
			$file_pdf->setTextColor(0, 0, 0);
			if(!empty($basket_note)){
				$file_pdf->setFont('Arial','B',$fonts);
				$file_pdf->multiCell(0, $fonts/2, $basket_note, 0, 'L',0);
				$file_pdf->cell(0, 2, '', 0, 1, 'L',0);
			}
			$file_pdf->setFont('Arial','',$fonts);
			$file_pdf->multiCell(0, $fonts/2, iconv("UTF-8", "CP1250//TRANSLIT",$productRs['productHeadline']), 0, 'L',0);
			$file_pdf->cell(0, 2, '', 0, 1, 'L',0);
			//$file_pdf->setTextColor('rgb',0, 0, 1, 0);
                        $file_pdf->setTextColor(0,0,255);
			$file_pdf->setFont('Arial','U',$fonts);
                        //adit for the pdf download properly
			//$file_pdf->cell(0, $fonts/2, 'Read More: '.$productRs['entryID'], 0, 1, 'L',0,'https://www.competiscan.com/productDocuments.php?did=1&id='.$productID);
                        $file_pdf->cell(0, $fonts/2, 'Read More: '.$productRs['entryID'], 0, 1, 'L',0,$site_urls.'productPdf.php?did='.$document_id.'&id='.$productID);
			$file_pdf->cell(0, 2, '', 0, 1, 'L',0);
                        
		}
		 
		if(count($pages)>0){ 
			$image_number = 0;
                        $condQuery='';
                        if($mChannelID=='5'){
                           $condQuery='';
                        } else {
                           $condQuery ='AND document_id='.$document_id;  
                        }
                        /*############START CONVERT EMAIL IMAGE############*/
                        $tablename=' cscan_img_document';
                        $pptSql1 = "SELECT ppt_status FROM cscan_user_single_image_ppt WHERE productID='".$productID."' AND userID='".$sess_userID."'";
                        $pptQuery1 = $DRW->query($pptSql1,$DRW_read);
                        if($DRW->num_rows($pptQuery1) > 0)
                        {
                            $pptResult1 = $DRW->fetch_array($pptQuery1);
                            $pppImageStatus1=$pptResult1['ppt_status'];
                            if($pppImageStatus1==1 || $pppImageStatus1==2){
                                $tablename=' cscan_img_document_ppt';
                             }
                        }
                /*############END CONVERT EMAIL IMAGE############*/
			$query2 = "SELECT img_document_sort,img_document_filename,img_document_content_type,img_document_size_byte,UNIX_TIMESTAMP(img_document_createddate),document_id,img_document_path,sample_img_status,bucket_name  
				FROM {$tablename} WHERE productID=$productID $condQuery ORDER BY img_document_sort";
			$query_result2 = $DRW->query($query2,$DRW_read);
			while ($data2 = $DRW->fetch_row($query_result2)) {
				$img_document_sort = (int)$data2[0];
				$img_document_filename = $data2[1];
				$img_document_content_type = $data2[2];
				$img_document_size_byte = $data2[3];
				$img_document_createddate = $data2[4];
				$document_id = $data2[5];
				$img_document_path = $data2[6];
                                /*######## added for s3 AWS Lambda services ######## */
                                $resample_img_status = $data2[7];
                                $check_bucket_name= $data2[8];
                                $newdocumentpath='';
                                $imgpath='';
                                ######################## for the video section ##################
                                if(ENV == 'localhost' || ENV == 'demo.competiscan.com'){
                                    if($img_document_content_type=='video/mp4' || empty($img_document_path) ||  empty($img_document_filename)){
                                        // $query_video            =   "SELECT img_id,img_filename,img_content_type,img_size_byte,UNIX_TIMESTAMP(img_createddate),img_path,img_companyID FROM cscan_img WHERE productID=$productID";
                                       $query_video= "SELECT img_co_content_type,img_co_size_byte,UNIX_TIMESTAMP(img_co_createddate),img_co_path,img_co_filename FROM cscan_img_company WHERE companyID=(SELECT img_companyID FROM cscan_img WHERE productID=$productID)";
                                       $query_result_video     =   $DRW->query($query_video,$DRW_read);
                                        $data_video             =   $DRW->fetch_row($query_result_video);
                                        /*$img_document_filename  =   $data_video['1'] ;
                                        $img_document_path      =   $data_video['5'] ; */
                                        $img_content_type = $data_video[0];
                                        $img_size_byte = $data_video[1];
                                        $img_createddate = $data_video[2];
                                        $img_document_path = $data_video[3];
                                        $img_document_filename = $data_video[4];

                                    }
                                }
				######################## for the video section ##################
				if(!in_array($productID.'_'.$img_document_sort,$pages)){
					continue;
				} 
                                    #########################START S3 IMPLEMENTATION#####################
                                    if(ENV == 'localhost' || ENV == 'demo.competiscan.com'){        
                                        if(strpos($img_document_path,'/')=='0'){
                                            $img_document_path  = substr($img_document_path,1);
                                        }
                                    }
                                        $newdocumentpath = $img_document_path.$img_document_filename;
                                        $info='';
                                        if($newdocumentpath!=''){
                                            if(ENV == 'localhost' || ENV == 'demo.competiscan.com'){ 
                                                $info = $s3->doesObjectExist($bucket_name,$newdocumentpath);
                                            }else{
                                                $info = $s3->doesObjectExist($bucket_name,substr($newdocumentpath,1));
                                            }
                                           
                                        }
                                        
                                        
                                        if($info){
                                            /*######## Start for added s3 AWS Lambda services ######## */
                                            if(ENV == 'localhost' || ENV == 'demo.competiscan.com'){                                                
                                            
                                                $query_sample_img       =   "SELECT default_img_width,default_img_height,sample3_img_width,sample3_img_height FROM cscan_pdf_img_sample WHERE status=1 order by id DESC limit 1";
                                                $result_sample_img      =   $DRW->query($query_sample_img,$DRW_read);
                                                $data_sample_img        =   $DRW->fetch_row($result_sample_img);
                                                $default_img_width      =   $data_sample_img['0'] ;
                                                $default_img_height     =   $data_sample_img['1'] ;
                                                $sample3_img_width      =   $data_sample_img['2'] ;
                                                $sample3_img_height     =   $data_sample_img['3'] ;
                                                if($resample_img_status==3){
                                                    $sample_imgwidth='?width='.$sample3_img_width;
                                                    if($sample3_img_height>0){
                                                         $sample_imgwidth .='&height='.$sample3_img_height;
                                                    }                     

                                                }else{
                                                    $sample_imgwidth='?width='.$default_img_width;
                                                    if($default_img_height>0){
                                                         $sample_imgwidth .='&height='.$default_img_height;
                                                    }

                                                }

                                                    $imgpath=$s3FileUrl.$img_document_path.$img_document_filename.$sample_imgwidth;

                                                /*######## End for added s3 AWS Lambda services ######## */
                                            }else{
                                                
                                                $imgpath = $displays3URL.substr($newdocumentpath,1);
                                            }
                                         
                                                                              
                                        } else{
                                            //$imgpath = dirname(__FILE__).'/images/competiscan_logo.jpg';
						$imgpath = dirname(__FILE__).'/images/competiscan-logo.png';
                                                //ADD CSV2 CDN URL
                                                if(!empty($check_bucket_name) AND $check_bucket_name!=''){
                                                    $imgpath = $displays3CSV2URL.$newdocumentpath; 
                                                    //echo $imgpath = $displays3CSV2URL.substr($newdocumentpath,0); die;
                                                }
                                        }
                                      
                                        $imageData = "data:image/jpeg;base64,".base64_encode(file_get_contents($imgpath));
                                     #########################END S3 IMPLEMENTATION#####################
                                    
                                
                                 //$src='https://csbucket007.s3.amazonaws.com/PDF/2019/06/2322553/23225530.jpg';
				//echo $src = dirname(__FILE__)."$img_document_path$img_document_filename";  die;
				######################## for the video section ##################
                               // $src = FULL_sample_img($productID,$document_id,$img_document_path,$img_document_filename); 
                                // if(ENV == 'localhost' || ENV == 'demo.competiscan.com'){
                                   /* if($img_document_content_type=='video/mp4'){
                                        $src    =   video_humb($productID,$document_id,$img_document_path,$img_document_filename);
                                    }*/
                                // }
                               //echo $src; exit;
                                ######################## for the video section ##################
                                    
//                                if(!is_file($src)){
//					continue;
//				}
				//$img_size = getimagesize($src);
				//echo $src;die;
				if($pp){
                                    
                                    ############ for add headlines ############################
                                    //Start new implemention ppt export
                                    if($image_number==0){   
                                        $isheadline=false;
                                       if(!empty($headlinesarray) && in_array($productID, $headlinesarray)){ 
                                        $shape = $currentSlide->createRichTextShape();
                                        $shape->setWidth(420);
                                        $shape->setOffsetX(20);
                                        $shape->setOffsetY(110);
                                        $shape->getActiveParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_LEFT );
                                        $textRun = $shape->createTextRun($productRs['productHeadline']);
                                        $textRun->getFont()->setName('Arial');
                                        $textRun->getFont()->setSize(11);
                                        $isheadline=true;
                                       }
                                     }
                                     //End new implemention ppt export
                                    /*if($image_number==0){   
                                        $isheadline=false;
                                       if(!empty($headlinesarray) && in_array($productID, $headlinesarray)){ 
                                        $shape = $currentSlide->createRichTextShape();
                                        $shape->setWidth(350);
                                        $shape->setOffsetX(20);
                                        $shape->setOffsetY(110);
                                        //$shape->setOffsetY($text_offset);
                                        //$shape->getActiveParagraph()->getAlignment()->setHorizontal( PHPPowerPoint_Style_Alignment::HORIZONTAL_LEFT );
                                       $shape->getActiveParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_LEFT );
                                        $textRun = $shape->createTextRun($productRs['productHeadline']);
                                        $isheadline=true;
                                       }
                                     }*/
                                    ############ end for add headlines ############################   
                                    
                                     ######################## for the video section ##################
                                    //if(ENV == 'localhost' || ENV == 'demo.competiscan.com'){ 
                                    //$fixedwidth     =   '410';
                                    //$fixedheight    =   '380'; 
                                    $fixedwidth     =   '345';
                                    $fixedheight    =   '500'; 
                                    //$offesetx       =   30;
                                    //Start new implemention ppt export
                                    $offesetx       =   300;
                                    //End new implemention ppt export
                                    if($isheadline){
                                        $fixedwidth  =  '400';
                                        $fixedheight =  '500';
                                        //$offesetx    =   390;
                                        //Start new implemention ppt export
                                        $offesetx    =   590;
                                        //End new implemention ppt export
                                    }
                                      
                                       //$imageData='https://files.v2.competiscan.com/CSV2_PRODUCT/current/9188963/productImages/1.jpg';
                                      //$imageData='/var/www/html/uat3.competiscan.com/PDF/2019/06/2322553/23225530.jpg';
                                      $shape = new Base64();
                                      //$shape = $currentSlide->createDrawingShape();
                                        $img_size       =   getimagesize($imageData);
                                        
                                        if(!empty($img_size)){
                                            $imgwidth   =   ($img_size[0]+100);
                                            $imgheight  =   ($img_size[1]+100);
                                            
                                            if($imgwidth<$fixedwidth && $imgheight<$fixedheight){
                                                $imgwidth   =   $fixedwidth;
                                                $imgheight  =   $fixedheight;
                                            }else if($imgwidth > $fixedwidth && $imgheight > $fixedheight){
                                                $imgwidth   =   $fixedwidth;
                                                $imgheight  =   $fixedheight;
                                            }else if($imgwidth < $fixedwidth && $imgheight > $fixedheight){
                                                $imgwidth   =    $img_size[0];
                                                $imgheight  =   $fixedheight;
                                            }else if($imgwidth > $fixedwidth && $imgheight < $fixedheight){
                                                $imgwidth   =    $fixedwidth;
                                                $imgheight  =    $img_size[1];
                                            }
                                        }
                                       // echo $imgwidth."hegt".$imgheight; exit;
                                   //}
                                    ######################## for the video section ##################
                                        //if($image_number==0){
					//	$objPHPPowerPoint->createSlide();
					//	$objPHPPowerPoint->setActiveSlideIndex($i);
					//	$currentSlide = $objPHPPowerPoint->getActiveSlide();
					//}
                                       // $shape = $currentSlide->createDrawingShape();
					$shape->setName('Competiscan Page '.$img_document_sort);
					$shape->setDescription('Competiscan Page '.$img_document_sort);
                                        //echo $src;exit;
					$shape->setData($imageData);
                                        $shape->setResizeProportional(false);
					//$shape->getHyperlink()->setUrl('https://www.competiscan.com/productDocuments.php?did=1&id='.$productID.'#page='.$img_document_sort);
                                        $shape->getHyperlink()->setUrl($site_urls.'productPdf.php?did=1&id='.$productID);
					$shape->setWidth($img_size[0]);
					$shape->setHeight($img_size[1]);
                                        ######################## for the video section ##################
                                        // if(ENV == 'localhost' || ENV == 'demo.competiscan.com'){
                                           // $shape->setWidth($imgwidth);
                                            //$shape->setHeight($imgheight);
                                        // }
                                        if($isheadline){
                                            // $shape->setWidth($imgwidth);
                                             //$shape->setHeight($imgheight);
                                        } 
                                        
                                        ######################## for the video section ##################
                                         //$shape->setOffsetX(200);
					// $shape->setOffsetY(200);
					 /*$shape->setOffsetX($offesetx+(30*$image_number));
					 $shape->setOffsetY(20+$text_offset+$text_size+(30*$image_number));
					//$shape->setRotation(25);
					$shape->getShadow()->setVisible(true);
					//$shape->getShadow()->setDirection(45);
					 $shape->getShadow()->setDistance(0);
					 $shape->getShadow()->setAlignment(Shadow::SHADOW_TOP_RIGHT);
					 $shape->getShadow()->setBlurRadius(25);
                                         $currentSlide->addShape($shape);*/
                                         //Start new implemention ppt export
                                         $shape->setOffsetX($offesetx+(30*$image_number));
					 $shape->setOffsetY(20+$text_offset+$text_size+(30*$image_number));
                                         $shape->getShadow()->setVisible(true);
					 $shape->getShadow()->setDistance(0);
					 $shape->getShadow()->setAlignment(Shadow::SHADOW_TOP_RIGHT);
					 $shape->getShadow()->setBlurRadius(25);
                                         $shape->getBorder()
                                        ->setLineStyle(Border::LINE_SINGLE)
                                        ->setDashStyle(Border::DASH_SOLID)
                                        ->setLineWidth(3)
                                        ->getColor()->setARGB('#808080');
                                         $currentSlide->addShape($shape);
                                         //End new implemention ppt export
				}
				elseif($pdf){
                                   
					//$file_pdf->image($src,$file_pdf->getX(), $file_pdf->getY(), $page_width/4, 0, 'JPEG', 'https://www.competiscan.com/productDocuments.php?did=1&id='.$productID.'#page='.$img_document_sort);
					//$file_pdf->cell(0, 20, '', 0, 1, 'L',0);
				}
				$image_number++;
			}
		}
		$i++;
                
	}
     
        if(isset($imageDataArray)){
		if($pp){ 
			$currentSlide = $objPHPPowerPoint->getActiveSlide();
			
			$text_offset = 20;
			$text_size = 20;
			/*$shape = $currentSlide->createRichTextShape();
			$shape->setWidth(320);
			$shape->setOffsetX(40);
			$shape->setOffsetY($text_offset);
			$shape->getActiveParagraph()->getAlignment()->setHorizontal( PHPPowerPoint_Style_Alignment::HORIZONTAL_JUSTIFY );
			$textRun = $shape->createTextRun('');
			$textRun->getFont()->setName('Calibri');
			$textRun->getFont()->setSize($text_size); //Calibri 24 Align Right Gray
			$textRun->getFont()->setColor( new PHPPowerPoint_Style_Color( 'FF808080' ) ); //FFA9A9A9 FF2F4F4F FF696969 FFD3D3D3 FF778899 FF708090 FFC0C0C0*/
			
			/*$text_offset = 70;
			$shape = $currentSlide->createDrawingShape();
			$shape->setName('Competiscan upper');
			$shape->setDescription('Competiscan upper');
			$shape->setPath(dirname(__FILE__).'/php_powerpoint/ppt-Upper-Blue-Bar.jpg');
			$shape->setWidth(960);
			$shape->setOffsetY($text_offset);
			
			$shape = $currentSlide->createDrawingShape();
			$shape->setName('Competiscan footer '.($i + 1));
			$shape->setDescription('Competiscan footer '.($i + 1));
			$shape->setPath(dirname(__FILE__).'/php_powerpoint/ppt-footer-blue.jpg');
			$shape->setWidth(960);
			$shape->setOffsetY(670);
			
			$shape = $currentSlide->createDrawingShape();
			$shape->setName('Competiscan logo '.($i + 1));
			$shape->setDescription('Competiscan logo '.($i + 1));
			$shape->setPath(dirname(__FILE__).'/php_powerpoint/ppt-logo.png');
			$shape->setHeight(43);
			$shape->setOffsetX(753);
			$shape->setOffsetY(680);
			
			$shape = $currentSlide->createRichTextShape();
			$shape->setWidth(960);
			$shape->setOffsetY(693);
			//$shape->getActiveParagraph()->getAlignment()->setHorizontal( PHPPowerPoint_Style_Alignment::HORIZONTAL_CENTER );//VERTICAL_BOTTOM
			$shape->getActiveParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER );
                        $textRun = $shape->createTextRun($copyright.date('Y').' Confidential & Proprietary. All Rights Reserved'); //�2011 Confidential & Proprietary. All Rights Reserved
			$textRun->getFont()->setName('Arial');
			$textRun->getFont()->setSize(7);
			//$textRun->getFont()->setColor( new PHPPowerPoint_Style_Color(PHPPowerPoint_Style_Color::COLOR_WHITE) );
			$textRun->getFont()->setColor( new Color( Color::COLOR_WHITE ) );*/
			foreach($imageDataArray as $k=>$ida){
                            if(file_exists($ida)) {
                                $shape = $currentSlide->createDrawingShape();
                                $shape->setName('Competiscan Chart '.$k);
                                $shape->setDescription('Competiscan Chart '.$k);
                                $shape->setPath($ida);
                                $shape->setOffsetX(10+(30*$k));
                                $shape->setOffsetY(20+$text_offset+$text_size+(30*$k));
                                $shape->getShadow()->setVisible(true);
                                $shape->getShadow()->setDistance(0);
                                //$shape->getShadow()->setAlignment(PHPPowerPoint_Shape_Shadow::SHADOW_CENTER);
                                $shape->getShadow()->setAlignment(Shadow::SHADOW_CENTER);
                                $shape->getShadow()->setBlurRadius(25);
                            }
			}
		}
		$i++;
	}
	
	if($pp){
                //comment last slide code from ppt
		/*$objPHPPowerPoint->createSlide();
		$objPHPPowerPoint->setActiveSlideIndex($i);
		$currentSlide = $objPHPPowerPoint->getActiveSlide();
		
		$shape = $currentSlide->createDrawingShape();
                
		$shape->setName('Competiscan footer');
		$shape->setDescription('Competiscan footer');
		$shape->setPath(dirname(__FILE__).'/php_powerpoint/ppt-footer-blue.jpg');
		$shape->setWidth(960);
		$shape->setOffsetY(670);
		
		$shape = $currentSlide->createDrawingShape();
		$shape->setName('Competiscan logo');
		$shape->setDescription('Competiscan logo');
		$shape->setPath(dirname(__FILE__).'/php_powerpoint/ppt-logo.png');
		$shape->setHeight(43);
		$shape->setOffsetX(753);
		$shape->setOffsetY(680);
		
		$shape = $currentSlide->createRichTextShape();
		$shape->setWidth(940);
		$shape->setOffsetX(10);
		$shape->setOffsetY(500);
		//$shape->getActiveParagraph()->getAlignment()->setHorizontal( PHPPowerPoint_Style_Alignment::HORIZONTAL_JUSTIFY );
		$shape->getActiveParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_JUSTIFY );
                $textRun = $shape->createTextRun('Competiscan LLC  CONFIDENTIAL   ALL RIGHTS RESERVED');
		$textRun->getFont()->setName('Arial');
		$textRun->getFont()->setSize(12); //Arial 12 Justified Gray 70%
		//$textRun->getFont()->setColor( new PHPPowerPoint_Style_Color( 'FF808080' ) ); //FFA9A9A9 FF2F4F4F FF696969 FFD3D3D3 FF778899 FF708090 FFC0C0C0
		$textRun->getFont()->setColor( new Color( 'FF808080' ) );
               // $textRun->getFont()->setUnderline( PHPPowerPoint_Style_Font::UNDERLINE_SINGLE );
		
		$shape = $currentSlide->createRichTextShape();
		$shape->setWidth(940);
		$shape->setOffsetX(10);
		$shape->setOffsetY(520);
		//$shape->getActiveParagraph()->getAlignment()->setHorizontal( PHPPowerPoint_Style_Alignment::HORIZONTAL_JUSTIFY );
		$shape->getActiveParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_JUSTIFY );
                $textRun = $shape->createTextRun('
	The ideas, concepts and information contained in this document, and the manner in which this information is presented, are proprietary trade secrets owned by Competiscan LLC and may not be used or duplicated without authorization.  The reading of this document constitutes an agreement with the foregoing and an understanding to be bound by its terms and conditions.  Reproduction or disclosure of these materials in whole or in part without the prior written approval of Competiscan LLC is expressly prohibited by law.');
		$textRun->getFont()->setName('Arial');
		$textRun->getFont()->setSize(12); //Arial 12 Justified Gray 70%
		//$textRun->getFont()->setColor( new PHPPowerPoint_Style_Color( 'FF808080' ) ); //FFA9A9A9 FF2F4F4F FF696969 FFD3D3D3 FF778899 FF708090 FFC0C0C0
		$textRun->getFont()->setColor( new Color( 'FF808080' ) );
		$shape = $currentSlide->createRichTextShape();
		$shape->setWidth(960);
		$shape->setOffsetY(693);
		//$shape->getActiveParagraph()->getAlignment()->setHorizontal( PHPPowerPoint_Style_Alignment::HORIZONTAL_CENTER );//VERTICAL_BOTTOM
		$shape->getActiveParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER );
                
                $textRun = $shape->createTextRun($copyright.date('Y').' Confidential & Proprietary. All Rights Reserved'); //�2011 Confidential & Proprietary. All Rights Reserved
		$textRun->getFont()->setName('Arial');
		$textRun->getFont()->setSize(7);
		//$textRun->getFont()->setColor( new PHPPowerPoint_Style_Color(PHPPowerPoint_Style_Color::COLOR_WHITE) );
		$textRun->getFont()->setColor( new Color(Color::COLOR_WHITE) );*/
		$outfile = '/tmp/exportPowerPoint_'.$_SESSION['sess_userID'].'.pptx';
		if(file_exists($outfile)) {
			unlink($outfile);
		}
		//$objWriter = PHPPowerPoint_IOFactory::createWriter($objPHPPowerPoint, 'PowerPoint2007');
		$objWriter = IOFactory::createWriter($objPHPPowerPoint, 'PowerPoint2007');
                $objWriter->save($outfile);
		if(isset($imageDataArray)){
			foreach($imageDataArray as $k=>$ida){
				if(file_exists($ida)) {
					unlink($ida);
				}
			}
		}
               if (file_exists($outfile)) {
			//@ob_end_clean();
			//header("Location: ".$outfile);
			$dl = new HTTP_Download();
			$dl->setFile($outfile);
			//$dl->setLastModified(time());
			$dl->setContentType('application/vnd.openxmlformats-officedocument.presentationml.presentation');//application/vnd.ms-powerpoint
			$dl->setCacheControl('public');
			$dl->setCache(true);
			$dl->setContentDisposition(HTTP_DOWNLOAD_INLINE, "Competiscan_".$ptext."_".date('Y-m-d').".pptx");
			$dl->send();
		}
		else{
			echo ' No File ';
		}
	}
	elseif($pdf){
		$inline = false;
                 
                ############### Start S3 Implementation PDF ##################
                // Clean output buffers to ensure clean PDF output
                while (ob_get_level()) {
                    ob_end_clean();
                }
                $file_pdf->Output("Competiscan_".$ptext."_".date('Y-m-d').".pdf",'D');
                exit;
		############### Start S3 Implementation PDF ##################
                //$pdf->Output(F,'directory/filename.pdf'); 
//$file_pdf->output("Competiscan_".$ptext."_".date('Y-m-d').".pdf", $inline);
		//$file_pdf->close();
                //@unlink();
	}
	else{
		echo ' No File ';
	}
}
else{
	echo ' Invalid Record ';
}
?>