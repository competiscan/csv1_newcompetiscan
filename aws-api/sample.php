<?php ini_set('display_errors', 1);
      ini_set('display_startup_errors', 1);
      error_reporting(E_ALL);		
   require_once("../includes/dbcon.php");
   $page=50;
   $startpage=0;
    if(!empty($_REQUEST['p'])){
        $startpage=($page*$_REQUEST['p']);
    }   
    $checkS = "SELECT productID,img_document_filename,img_document_path,digital_text,transcription_job_name,updated_date FROM cscan_digital_video_ads_text WHERE conversion_status=2 order by id desc limit $startpage,$page";
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
                    <strong><h2>Amazon Transcribe API</h2></strong>
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
                    Online Video
                </th>
                <th width="45%">
                    Online Video Text
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
                       $transcription_job_name=$row_doc['transcription_job_name'];
                       $digital_text=$row_doc['digital_text'];
                       $transcription_job_name=$row_doc['transcription_job_name'];
                       $updated_date=$row_doc['updated_date'];
                      // $vid_link='..'.$img_document_path.$img_document_filename; 
                       //https://nmgtesttranscribe.s3.amazonaws.com/2017/07/2807844/2807844.mp4
                       //$bucket_name='nmgtesttranscribe'; 
                        $vid_link=$displays3URL.substr($img_document_path,1).$img_document_filename;            
                       ?>
                    <tr>
                        <td align="center">
                            <?php echo $p; ?>
                        </td>
                        <td align="center">
                            <a target="_blank" href="http://competiscan.com/admin/addproduct-digital.php?id=<?php echo $productID;?>&add=3"><?php echo $productID;?></a>
                        </td>
                        <td align="center">
                            <video width="300" height="180" controls>
                            <source src="<?php echo $vid_link;?>" type="video/mp4">Your browser does not support the video tag.
                            </video>
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