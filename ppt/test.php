<?php
error_reporting(E_ALL);
require_once 'src/PhpPresentation/Autoloader.php';
\PhpOffice\PhpPresentation\Autoloader::register();
require_once 'Common/src/Common/Autoloader.php';
\PhpOffice\Common\Autoloader::register();

use PhpOffice\PhpPresentation\PhpPresentation;
use PhpOffice\PhpPresentation\IOFactory;
use PhpOffice\PhpPresentation\Style\Color;
use PhpOffice\PhpPresentation\Style\Alignment;
use PhpOffice\PhpPresentation\Shape\Drawing\Base64;
//echo "dshdsjhs"; die;
$objPHPPowerPoint = new PhpPresentation();
///print_r($objPHPPowerPoint); die;

// Create slide
$currentSlide = $objPHPPowerPoint->getActiveSlide();

// Create a shape (drawing)
$shape = $currentSlide->createDrawingShape();
$shape->setName('PHPPresentation logo')
      ->setDescription('PHPPresentation logo')
      ->setPath('./docs/images/phppowerpoint_logo.gif')
      ->setHeight(36)
      ->setOffsetX(10)
      ->setOffsetY(10);
$shape->getShadow()->setVisible(true)
                   ->setDirection(45)
                   ->setDistance(10);
//echo "shdhsdhshd"; die;
// Create a shape (text)
$shape = $currentSlide->createRichTextShape()
      ->setHeight(300)
      ->setWidth(600)
      ->setOffsetX(170)
      ->setOffsetY(180);
$shape->getActiveParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER );
$textRun = $shape->createTextRun('Thank you for using PHPPresentation!');
$textRun->getFont()->setBold(true)
                   ->setSize(60)
                   ->setColor( new Color( 'FFE06B20' ) );


 $imgpath='https://csbucket007.s3.amazonaws.com/PDF/2019/06/2322383/23223830.jpg';
 $imageData = "data:image/jpeg;base64,".base64_encode(file_get_contents($imgpath));
$shape = new Base64();
$shape->setName('PHPPresentation logo')
    ->setDescription('PHPPresentation logo')
    ->setData($imageData)
    ->setResizeProportional(false)
    ->setHeight(299)
    ->setWidth(325)
    ->setOffsetX(10)
    ->setOffsetY(200);
$currentSlide->addShape($shape);

//echo "<pre>";
//print_r($shape); die;
	/*$outfile = 'tmp/exportPowerPoint.pptx';
		if(file_exists($outfile)) {
			unlink($outfile);
		}

		$objWriter = IOFactory::createWriter($objPHPPowerPoint, 'PowerPoint2007');
		//echo "okkk"; die;
		echo $objWriter->save($outfile); die;
		echo "<pre>";
		print_r($objWriter); die;

		if (file_exists($outfile)) {
			echo "dsdsdsd"; die;
			//@ob_end_clean();
			//header("Location: ".$outfile);
			$dl = new HTTP_Download();
			$dl->setFile($outfile);
			//$dl->setLastModified(time());
			$dl->setContentType('application/vnd.openxmlformats-officedocument.presentationml.presentation');//application/vnd.ms-powerpoint
			$dl->setCacheControl('public');
			$dl->setCache(true);
			$dl->setContentDisposition(HTTP_DOWNLOAD_INLINE, "Competiscan_".date('Y-m-d').".pptx");
			$dl->send();
		}*/

$oWriterPPTX = IOFactory::createWriter($objPHPPowerPoint, 'PowerPoint2007');
 //echo __DIR__ . "/sample.pptx"; die;
$t=time();

$oWriterPPTX->save(__DIR__ . "/tmp/sample_".$t.".pptx");

//$oWriterPPTX = IOFactory::createWriter($objPHPPowerPoint, 'PowerPoint2007');
//echo "<pre>";
//print_r($oWriterPPTX); die;
//echo __DIR__ . "/sample_test.pptx";
//$oWriterPPTX->save(__DIR__ . "/sample_test.pptx");

//echo "dsgdgsdggsdsttttt"; die;
//$oWriterODP = IOFactory::createWriter($objPHPPowerPoint, 'ODPresentation');
//$oWriterODP->save(__DIR__ . "/sample.odp");

?>
