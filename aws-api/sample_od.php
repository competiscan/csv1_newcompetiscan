<?php ini_set('display_errors', 1);
      ini_set('display_startup_errors', 1);
      error_reporting(E_ALL);		
   require_once("../includes/dbcon.php");
       
    $checkS = "SELECT productID,img_document_filename,img_document_path,digital_text,updated_date FROM cscan_digital_od_ads_text WHERE is_uploaded_s3=1 AND conversion_status=1 order by updated_date desc";
    $checkS = $DRW->query($checkS, $DRW_read2);
    $countS = $DRW->num_rows($checkS);
    ?>
<html>
    <head>
        
    </head>
    <body>
        <table border="1" width="100%">
            <tr>
                <th colspan="5" width="100%">
                    <strong><h2>Amazon Rekognition API</h2></strong>
                </th>
                
            </tr>
            <tr>
                <th width="5%">
                    S.No.
                </th>
                <th width="8%">
                    Product Id
                </th>
                <th width="32%">
                    Online Display
                </th>
                <th width="45%">
                    Online Display Text
                </th>
                <th width="10%">
                   Date
                </th>
            </tr>
            <?php      
            if($countS>0){
                $p=1;
                while ($row_doc = $DRW->fetch_array($checkS)) {
                       $productID=$row_doc['productID'];
                       $img_document_filename=$row_doc['img_document_filename'];
                       $img_document_path=$row_doc['img_document_path'];                       
                       $digital_text=$row_doc['digital_text'];                       
                       $updated_date=$row_doc['updated_date'];
                       $img_link='..'.$img_document_path.$img_document_filename; 
                       ?>
                    <tr>
                        <td align="center">
                            <?php echo $p; ?>
                        </td>
                        <td align="center">
                            <a target="_blank" href="http://demo.competiscan.com/admin/addproduct-digital.php?id=<?php echo $productID;?>&add=1"><?php echo $productID;?></a>
                        </td>
                        <td align="center">
                            <iframe src="<?php echo $img_link;?>"></iframe> 
                        </td>
                        <td align="center">
                            <?php echo $digital_text;?>
                        </td>
                         <td align="center">
                            <?php echo date('Y-m-d h:i:s',strtotime($updated_date));?>
                        </td>
                    </tr>
                    <?php $p++;
                }       
            }else{
                echo '<tr><td colspan="5">There is no record exist.</td></tr>';
            }            
    ?>
      </table>
    </body>
</html>