<?php die;
require_once 'config.php';
date_default_timezone_set("America/Chicago");
$message = (!empty($_GET['msg'])) ? trim(($_GET['msg'])) : '';
if (isset($_GET['page_no']) && $_GET['page_no']!="") {
    $page_no = $_GET['page_no'];
    } else {
    $page_no = 1;
    }
$total_records_per_page = 10;
$offset = ($page_no-1) * $total_records_per_page;
$previous_page = $page_no - 1;
$next_page = $page_no + 1;
$adjacents = "2";
$search_status='';
session_start();
if(!isset($_SESSION['search_status']) ){
    
    $_SESSION['search_status']='';
}
if(isset($_REQUEST['search_status'])){     
   $search_status=$_POST['search_status'];
   $_SESSION['search_status']= $search_status;  
}
$search_status=$_SESSION['search_status'];
$processed='';
$unprocessed='';
if($search_status==3){
   $processed=' selected="selected" ';
}elseif($search_status=='0'){   
   $unprocessed=' selected="selected" ';
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>YouTube Url List</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">       
         <link href="../css/bootstrap2.min.css" rel="stylesheet">
        
    </head>
    <body>
        <?php
        $where='';
        if($search_status==3){
            $where=' where status='.$search_status.' AND audio_text_status>0 ';
        }elseif($search_status=='0'){
             $where=' where status<3';            
        }
        
        $sql = "SELECT * FROM cscan_youtube_video ".$where." order by id desc ";       
        
        $numquery = $DRW->query($sql,$DRW_read);       
        $numrows=$DRW->num_rows($numquery);        
        $total_records = $numrows;        
        
        $total_no_of_pages = ceil($total_records / $total_records_per_page);

        $second_last = $total_no_of_pages - 1; // total pages minus 1
        
        $sql .= "LIMIT $offset, $total_records_per_page"; 
        $rs = $DRW->query($sql,$DRW_read);
        $checkS = $DRW->query($sql, $DRW_read);
        $countS = $DRW->num_rows($checkS);
        
        
            echo '<table border=1 cellspacing=0 width=95% align="center">
                <tr>
                    <th colspan="6" align="center" style="background-color: #1400ff36;"><h2>YouTube Urls</h2></th>
                </tr>
                <tr>
                    <th colspan="6"><span style="float:left; font-weight:bold; padding-top:10px;padding-bottom: 10px;">&nbsp;&nbsp;&nbsp;<a href="keywords.php"> Manage Search Keywords </a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="logos.php"> Manage Search logos </a></span><span style="float:right;padding-top:10px;padding-bottom: 10px;"><a href="add_url.php"> Add New Url </a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="export.php"><input type="button" id="export_record" value="Export All Records"> &nbsp;&nbsp; </a></span></th>
                </tr>
                <tr>
                    <td colspan="6">
                    <span style="float:left; margin:10px 10px 10px 30px;">
                    Search by Status: 
                    </span>
                    <span style="float:left; margin:10px 30px 10px 0px;">                     
                    <form method="post" action="" name="search_form">                     
                    <select name="search_status" onchange="this.form.submit()">
                        <option value="">-- Any --</option>
                        <option value="3" '.$processed.'> Processed </option>
                        <option value="0" '.$unprocessed.'> Unprocessed </option>
                    </select>                      
                      </form>
                      </span>
                    </td>
                </tr>
                ';
            if (!empty($message)) {
                echo '<tr>
                            <th colspan="6" align="center"><font color="#46a049">&nbsp;&nbsp;' . $message . '</font></th>
                    </tr>';
            }
         
            echo '
                <tr>
                    <th width="3%" style="height:33px">S.N.</th>
                    <th width="25%">Video</th>
                    <th width="25%">Url</th>
                    <th width="10%">Status</th>
                    <th width="15%">Action</th>
                    <th width="15%">Date</th>
                </tr>';
            // output data of each row
            $i = 1;
            if($page_no>1){
                $i=(($page_no-1)*$total_records_per_page)+1;
            }
            $status = '';
        if ($numrows > 0) {       
            while ($row = $DRW->fetch_array($checkS)) {
                if ($row['status'] == 3 && $row['audio_text_status']>0) {
                    $status = 'Processed';
                } else {
                    $status = 'Unprocessed';
                }

                preg_match('/[\\?\\&]v=([^\\?\\&]+)/', $row['youtube_url'], $matches);
                if (count($matches) > 0)
                    $videoid = $matches[1];
                else
                    $videoid = '';

                $width = '400px';
                $height = '200px';

                echo '<tr>
                    <td align="center">&nbsp;' . $i . '</td>
                     <td>&nbsp;
                        <iframe style="margin-top:5px;margin-bottom:5px;" id="ytplayer" type="text/html" width="' . $width . '" height="' . $height . '"
                           src="https://www.youtube.com/embed/' . $videoid . '?rel=0&showinfo=0&color=white&iv_load_policy=3"
                           frameborder="0" allowfullscreen>
                        </iframe>
                        &nbsp;
                     </td>
                    <td>&nbsp;<a href="' . $row['youtube_url'] . '" target="_blank">' . $row['youtube_url'] . '</a></td>
                    <td align="center">&nbsp;' . $status . '</td>
                    <td align="center">&nbsp;<a href="' . $row['youtube_url'] . '" target="_blank"> View </a>&nbsp;&nbsp;&nbsp;&nbsp; <a  onclick="return confirm(' . "'Are you sure, you want to delete it?'" . ')' . '"  href="del_url.php?uid=' . $row['id'] . '"> Delete </a> &nbsp;&nbsp;<a  href="video-detail.php?vid=' . $row['id'] . '"> Detail </a></td>
                    <td align="center">&nbsp;' . date("m/d/Y h:i:s", strtotime($row['created_date'])) . '</td>
                </tr>';
                $i++;
                //del_url.php?id='.$row['id'].'
            }
            
        }else{
            echo '<tr><th colspan="6"> There are no records exist.</th></tr>';
            
        }
        echo '</table>';
        ?>
        <div class="text-center">&nbsp;
    <?php  if($total_records >0 && $total_records >$total_records_per_page){ ?>
    <ul class="pagination "> 
            <?php /*if($page_no > 1){
            echo "<li><a href='?page_no=1'>First Page</a></li>";
            } */?>

            <li <?php if($page_no <= 1){ echo "class='disabled'"; } ?>>
            <a <?php if($page_no > 1){
            echo "href='?page_no=$previous_page'";
            } ?>>Previous</a>
            </li>
            <?php 
            if ($total_no_of_pages <= 10){  	 
                    for ($counter = 1; $counter <= $total_no_of_pages; $counter++){
                    if ($counter == $page_no) {
                    echo "<li class='active'><a>$counter</a></li>";	
                            }else{
                    echo "<li><a href='?page_no=$counter'>$counter</a></li>";
                            }
                    }
            }elseif ($total_no_of_pages > 10){
            if($page_no <= 4) {			
             for ($counter = 1; $counter < 8; $counter++){		 
                    if ($counter == $page_no) {
                       echo "<li class='active'><a>$counter</a></li>";	
                            }else{
                       echo "<li><a href='?page_no=$counter'>$counter</a></li>";
                            }
            }
            echo "<li><a>...</a></li>";
            echo "<li><a href='?page_no=$second_last'>$second_last</a></li>";
            echo "<li><a href='?page_no=$total_no_of_pages'>$total_no_of_pages</a></li>";
            }elseif($page_no > 4 && $page_no < $total_no_of_pages - 4) { 
            echo "<li><a href='?page_no=1'>1</a></li>";
            echo "<li><a href='?page_no=2'>2</a></li>";
            echo "<li><a>...</a></li>";
            for (
                 $counter = $page_no - $adjacents;
                 $counter <= $page_no + $adjacents;
                 $counter++
                 ) { 
                 if ($counter == $page_no) {
             echo "<li class='active'><a>$counter</a></li>"; 
             }else{
                    echo "<li><a href='?page_no=$counter'>$counter</a></li>";
                      }                  
                   }
            echo "<li><a>...</a></li>";
            echo "<li><a href='?page_no=$second_last'>$second_last</a></li>";
            echo "<li><a href='?page_no=$total_no_of_pages'>$total_no_of_pages</a></li>";
            }else {
            echo "<li><a href='?page_no=1'>1</a></li>";
            echo "<li><a href='?page_no=2'>2</a></li>";
            echo "<li><a>...</a></li>";
            for (
                 $counter = $total_no_of_pages - 6;
                 $counter <= $total_no_of_pages;
                 $counter++
                 ) {
                 if ($counter == $page_no) {
             echo "<li class='active'><a>$counter</a></li>"; 
             }else{
                    echo "<li><a href='?page_no=$counter'>$counter</a></li>";
             }                   
                 }
            }
            }

            ?> 
            <li <?php if($page_no >= $total_no_of_pages){
            echo "class='disabled'";
            } ?>>
            <a <?php if($page_no < $total_no_of_pages) {
            echo "href='?page_no=$next_page'";
            } ?>>Next</a>
            </li>

            <?php if($page_no < $total_no_of_pages){
            echo "<li><a href='?page_no=$total_no_of_pages'>Last &rsaquo;&rsaquo;</a></li>";
            } 

            ?>
        </ul>
    <?php } ?>
</div>
        
        
        
    </body>
</html>
