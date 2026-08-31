#!/usr/bin/php
<?php 
error_reporting(E_ALL);
ini_set('display_errors',1);
require_once('includes/dbcon.php');
$DRW->databaseReadWrite_die = 1;
require_once('includes/functions.php');

$name = 'Humana2';
$entryIDArray = array('2012-10-31-736','2012-10-30-607','2012-10-30-598','2012-10-29-844','2012-10-29-661','2012-10-29-538','2012-10-25-954','2012-10-24-463','2012-10-23-816','2012-10-23-714','2012-10-22-646','2012-10-22-209','2012-10-20-474','2012-10-20-421','2012-10-18-788','2012-10-18-549','2012-10-17-856','2012-10-17-790','2012-10-17-787','2012-10-16-778','2012-10-16-777','2012-10-16-712','2012-10-15-588','2012-10-15-547','2012-10-15-546','2012-10-11-766','2012-10-11-765','2012-10-10-864','2012-10-10-795','2012-10-08-704','2012-10-07-133','2012-10-04-679','2012-10-04-653','2012-10-03-733','2012-10-02-509','2012-10-02-508','2012-10-01-786','2012-10-01-785');
$productIDs = array();
$valid_entryIDArray = array();
$entryIDfiles = array();
foreach($entryIDArray as $eid){
	$query_p = "select productID from cscan_product_detail where entryID='$eid'";
	$query_result_p = $DRW->query($query_p,$DRW_read);
	$data = $DRW->fetch_row($query_result_p);
	if(!empty($data[0])){
		$productIDs[] = $data[0];
		$valid_entryIDArray[] = $eid;
	}
}
$document_id = '1';
$orig = false;
if(count($productIDs)>0){
	$document_ids = explode(',',$document_id);
	
	$document_content_type = 'application/pdf';
	$files = array();
	foreach($productIDs as $k=>$p){
		$p = (int)$p;
		if(isset($document_id[$k])){
			$d = (int)$document_id[$k];
		}
		elseif(!isset($d)){
			$d = 1;
		}
		if($orig && $is_admin){
			$table = 'cscan_document_orig';
		}
		else{
			$table = 'cscan_document';
			track_document($_SESSION['sess_userID'], $p, $d);
		}
		$query2 = "SELECT document_id,document_filename,document_content_type,document_size_byte,UNIX_TIMESTAMP(document_createddate),document_path FROM $table WHERE productID=$p AND document_id=$d";
		
		$query_result2 = $DRW->query($query2,$DRW_read);
		$data2 = $DRW->fetch_row($query_result2);
		$document_id = (float)$data2[0];
		$document_filename = $data2[1];
		$document_content_type = $data2[2];
		$document_size_byte = $data2[3];
		$document_createddate = $data2[4];
		$document_path = $data2[5];
		$DRW->free_result($query_result2);
		
		$pdf_src = dirname(__FILE__)."$document_path$document_filename";
		if($document_filename!='' && is_file($pdf_src)){
			$entryid_file = '/tmp/'.$valid_entryIDArray[$k].'.pdf';
			$s = 'convert -font helvetica -pointsize 40 label:'.escapeshellarg($valid_entryIDArray[$k]).' '.escapeshellarg($entryid_file);
			$ex = shell_exec($s);
			if(is_file($entryid_file)){
				$files[] = escapeshellarg($entryid_file);
				$entryIDfiles[] = $entryid_file;
			}
			$files[] = escapeshellarg($pdf_src);
		}
	}
	
	if(count($files)>0){
		$merged_file = dirname(__FILE__)."/MergedPDF".date('YmdHis').".pdf";//tempnam("/tmp", "PDFMerge");
		$merged_file = '/opt/home/competiscan/downloads/Merged_'.$name.'.pdf';
		$s = "gs -q -dNOPAUSE -dBATCH -sDEVICE=pdfwrite -sOutputFile=".escapeshellarg($merged_file)." ".implode(' ',$files);
		$ex = shell_exec($s);
		
		foreach($entryIDfiles as $f){
			if(!empty($f) && is_file($f)){
				unlink($f);
			}
		}
		if(is_file($merged_file)){
			echo 'Merged File: '.$merged_file."\n";
			exit;
		}
	}
}
echo "Merged File failed\n";
?>