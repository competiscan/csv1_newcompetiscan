<?php
require_once('includes/globalSession.php');
require_once('includes/checklogin.php');
require_once('includes/thumb.php');
require_once 'HTTP/Download.php';
require_once('includes/functions_latest2.php');  //latest function

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
		list($sql) = doQuery_latest2(0, false, '', false, $bid);
		$sql .= $orderby;
		$sql .= " Limit 0,100";
		$rs = $DRW->query($sql,$DRW_read);
		while($row = $DRW->fetch_assoc($rs)) {
			if(in_array($row['theproductID'],$checked_pids) || (is_array($pages) && count($pages)==0)){
				$productIDArray[] = $row['theproductID'];
			}
		}
	}
}
if(!empty($productID)){
	$productIDArray[] = $productID;
}

set_include_path(get_include_path() . PATH_SEPARATOR . './php_powerpoint/Classes_70298/');
include_once 'PHPPowerPoint.php';
include_once 'PHPPowerPoint/IOFactory.php';

if ((is_array($productIDArray) && count($productIDArray) > 0) || isset($imageDataArray)) {
	$objPHPPowerPoint = new PHPPowerPoint();
	$objPHPPowerPoint->getProperties()->setCreator("Competiscan");
	$objPHPPowerPoint->getProperties()->setLastModifiedBy("Competiscan");
	$objPHPPowerPoint->getProperties()->setTitle("Office 2007 PPTX Document");
	$objPHPPowerPoint->getProperties()->setSubject("Competiscan Office 2007 PPTX Document");
	$objPHPPowerPoint->getProperties()->setDescription("Competiscan Document (c) ".date('Y'));
	$objPHPPowerPoint->getProperties()->setKeywords("office 2007 openxml php");
	$objPHPPowerPoint->getProperties()->setCategory("Competiscan Document");
	$i = 0;
	
	foreach($productIDArray as $productID){
		$productQuery = "SELECT productHeadline,entryID FROM cscan_product_detail WHERE productID=$productID";
		$productQuery = $DRW->query($productQuery,$DRW_read);
		$productRs = $DRW->fetch_array($productQuery);
		
		if(empty($productRs['entryID'])){
			continue;
		}
		if($i>0){
			$objPHPPowerPoint->createSlide();
			$objPHPPowerPoint->setActiveSlideIndex($i);
		}
		$currentSlide = $objPHPPowerPoint->getActiveSlide();
		
		$text_offset = 20;
		$text_size = 20;
		$shape = $currentSlide->createRichTextShape();
		$shape->setWidth(320);
		$shape->setOffsetX(40);
		$shape->setOffsetY($text_offset);
		$shape->getActiveParagraph()->getAlignment()->setHorizontal( PHPPowerPoint_Style_Alignment::HORIZONTAL_JUSTIFY );
		$textRun = $shape->createTextRun($productRs['entryID']);
		$textRun->getHyperlink()->setUrl('http://'.$_SERVER['HTTP_HOST'].'/index.php?product='.$productID);
		$textRun->getFont()->setName('Calibri');
		$textRun->getFont()->setSize($text_size); //Calibri 24 Align Right Gray
		$textRun->getFont()->setColor( new PHPPowerPoint_Style_Color( 'FF808080' ) ); //FFA9A9A9 FF2F4F4F FF696969 FFD3D3D3 FF778899 FF708090 FFC0C0C0
		
		$text_offset = 70;
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
		$shape->getActiveParagraph()->getAlignment()->setHorizontal( PHPPowerPoint_Style_Alignment::HORIZONTAL_CENTER );//VERTICAL_BOTTOM
		$textRun = $shape->createTextRun(utf8_encode('�').date('Y').' Confidential & Proprietary. All Rights Reserved'); //�2011 Confidential & Proprietary. All Rights Reserved
		$textRun->getFont()->setName('Arial');
		$textRun->getFont()->setSize(7);
		$textRun->getFont()->setColor( new PHPPowerPoint_Style_Color(PHPPowerPoint_Style_Color::COLOR_WHITE) );
		
		$image_number = 0;
		$query2 = "SELECT img_document_sort,img_document_filename,img_document_content_type,img_document_size_byte,UNIX_TIMESTAMP(img_document_createddate),document_id,img_document_path 
			FROM cscan_img_document WHERE productID=$productID AND document_id=$document_id ORDER BY img_document_sort";
		$query_result2 = $DRW->query($query2,$DRW_read);
		while ($data2 = $DRW->fetch_row($query_result2)) {
			$img_document_sort = (int)$data2[0];
			$img_document_filename = $data2[1];
			$img_document_content_type = $data2[2];
			$img_document_size_byte = $data2[3];
			$img_document_createddate = $data2[4];
			$document_id = $data2[5];
			$img_document_path = $data2[6];
			
			if(is_array($pages) && count($pages)>0 && !in_array($productID.'_'.$img_document_sort,$pages)){
				continue;
			}
			
			$src = dirname(__FILE__)."$img_document_path$img_document_filename";
			$src = FULL_sample_img($productID,$document_id,$img_document_path,$img_document_filename);
			if(!is_file($src)){
				continue;
			}
			//$img_size = getimagesize($src);
			
			//if($image_number==0){
			//	$objPHPPowerPoint->createSlide();
			//	$objPHPPowerPoint->setActiveSlideIndex($i);
			//	$currentSlide = $objPHPPowerPoint->getActiveSlide();
			//}
			
			$shape = $currentSlide->createDrawingShape();
			$shape->setName('Competiscan Page '.$img_document_sort);
			$shape->setDescription('Competiscan Page '.$img_document_sort);
			$shape->setPath($src);
			$shape->getHyperlink()->setUrl('https://www.competiscan.com/productDocuments.php?id='.$productID.'#page='.$img_document_sort);
			//$shape->setWidth($img_size[0]);
			//$shape->setHeight($img_size[1]);
			$shape->setOffsetX(10+(30*$image_number));
			$shape->setOffsetY(20+$text_offset+$text_size+(30*$image_number));
			//$shape->setRotation(25);
			$shape->getShadow()->setVisible(true);
			//$shape->getShadow()->setDirection(45);
			$shape->getShadow()->setDistance(0);
			$shape->getShadow()->setAlignment(PHPPowerPoint_Shape_Shadow::SHADOW_CENTER);
			$shape->getShadow()->setBlurRadius(25);
			
			$image_number++;
		}
		$i++;
	}
	if(isset($imageDataArray)){
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
		
		$text_offset = 70;
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
		$shape->getActiveParagraph()->getAlignment()->setHorizontal( PHPPowerPoint_Style_Alignment::HORIZONTAL_CENTER );//VERTICAL_BOTTOM
		$textRun = $shape->createTextRun(utf8_encode('�').date('Y').' Confidential & Proprietary. All Rights Reserved'); //�2011 Confidential & Proprietary. All Rights Reserved
		$textRun->getFont()->setName('Arial');
		$textRun->getFont()->setSize(7);
		$textRun->getFont()->setColor( new PHPPowerPoint_Style_Color(PHPPowerPoint_Style_Color::COLOR_WHITE) );
		
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
				$shape->getShadow()->setAlignment(PHPPowerPoint_Shape_Shadow::SHADOW_CENTER);
				$shape->getShadow()->setBlurRadius(25);
			}
		}
		$i++;
	}
	
	$objPHPPowerPoint->createSlide();
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
	$shape->getActiveParagraph()->getAlignment()->setHorizontal( PHPPowerPoint_Style_Alignment::HORIZONTAL_JUSTIFY );
	$textRun = $shape->createTextRun('Competiscan LLC  CONFIDENTIAL   ALL RIGHTS RESERVED');
	$textRun->getFont()->setName('Arial');
	$textRun->getFont()->setSize(12); //Arial 12 Justified Gray 70%
	$textRun->getFont()->setColor( new PHPPowerPoint_Style_Color( 'FF808080' ) ); //FFA9A9A9 FF2F4F4F FF696969 FFD3D3D3 FF778899 FF708090 FFC0C0C0
	$textRun->getFont()->setUnderline( PHPPowerPoint_Style_Font::UNDERLINE_SINGLE );
	
	$shape = $currentSlide->createRichTextShape();
	$shape->setWidth(940);
	$shape->setOffsetX(10);
	$shape->setOffsetY(520);
	$shape->getActiveParagraph()->getAlignment()->setHorizontal( PHPPowerPoint_Style_Alignment::HORIZONTAL_JUSTIFY );
	$textRun = $shape->createTextRun('
The ideas, concepts and information contained in this document, and the manner in which this information is presented, are proprietary trade secrets owned by Competiscan LLC and may not be used or duplicated without authorization.  The reading of this document constitutes an agreement with the foregoing and an understanding to be bound by its terms and conditions.  Reproduction or disclosure of these materials in whole or in part without the prior written approval of Competiscan LLC is expressly prohibited by law.');
	$textRun->getFont()->setName('Arial');
	$textRun->getFont()->setSize(12); //Arial 12 Justified Gray 70%
	$textRun->getFont()->setColor( new PHPPowerPoint_Style_Color( 'FF808080' ) ); //FFA9A9A9 FF2F4F4F FF696969 FFD3D3D3 FF778899 FF708090 FFC0C0C0
	
	$shape = $currentSlide->createRichTextShape();
	$shape->setWidth(960);
	$shape->setOffsetY(693);
	$shape->getActiveParagraph()->getAlignment()->setHorizontal( PHPPowerPoint_Style_Alignment::HORIZONTAL_CENTER );//VERTICAL_BOTTOM
	$textRun = $shape->createTextRun(utf8_encode('�').date('Y').' Confidential & Proprietary. All Rights Reserved'); //�2011 Confidential & Proprietary. All Rights Reserved
	$textRun->getFont()->setName('Arial');
	$textRun->getFont()->setSize(7);
	$textRun->getFont()->setColor( new PHPPowerPoint_Style_Color(PHPPowerPoint_Style_Color::COLOR_WHITE) );
	
	$outfile = '/tmp/exportPowerPoint_'.$_SESSION['sess_userID'].'.pptx';
	if(file_exists($outfile)) {
		unlink($outfile);
	}
	$objWriter = PHPPowerPoint_IOFactory::createWriter($objPHPPowerPoint, 'PowerPoint2007');
	$objWriter->save($outfile);
	foreach($imageDataArray as $k=>$ida){
		if(file_exists($ida)) {
			unlink($ida);
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
		$dl->setContentDisposition(HTTP_DOWNLOAD_INLINE, "Competiscan_PowerPoint_".date('Y-m-d').".pptx");
		$dl->send();
	}
	else{
		echo ' No File ';
	}
}
else{
	echo ' Invalid Record ';
}
?>
