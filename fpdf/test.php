<?php

//require('fpdf.php');
/*
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial','B',16);
$pdf->Cell(40,10,'Hello World!');
$pdf->WriteHTML('<div>Hello World</div>');

$pdf->Image('https://csbucket007.s3.amazonaws.com/productImages/2019/05/2322383/thumb2322383.jpeg');
$pdf->Output();  
*/
   /* $the_file       = "/temp/your-app-test.html";
    $myfile         = fopen($the_file, "r") or die("Unable to open file!!!!<br><br><br>");
    $homepage     = file_get_contents($the_file);
    fclose($myfile);
    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial','B',9);
    $pdf->Cell(40,10, $homepage);

    $pdf->Output();*/

$ip=shell_exec('hostname -i');
echo $ip;die;
?>
