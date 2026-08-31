<?php die;
require_once 'config.php';
$root = dirname(__FILE__);
$folder='search-logo';
$dir = $root.'/'.$folder;
if (!is_dir($dir)) {
    mkdir($dir, 0777);
}

if(isset($_FILES['logo']) && $_POST['submit']){
    $errors= array();      
    $file_name =rand(10,1000).str_replace(' ','',trim(strtolower($_FILES['logo']['name'])));
    $file_size =$_FILES['logo']['size'];
    $file_tmp =$_FILES['logo']['tmp_name'];
    $file_type=$_FILES['logo']['type'];

    $value = explode(".", $file_name);
    $file_ext = strtolower(array_pop($value)); 
    //$file_ext=strtolower(end(explode('.',$file_name)));

    $extensions= array("jpeg","jpg","png");

    if(in_array($file_ext,$extensions)=== false){
       $errors[]="extension not allowed, please choose a JPEG or PNG file.";
    }

    if($file_size > 2097152){
       $errors[]='File size must be less than 2 MB';
    }
      
    if(empty($errors)==true){
        if(move_uploaded_file($file_tmp,$folder."/".$file_name)){
           $ins_query="Insert into cscan_youtube_search_logos (logo_name,logo_path) values('".$file_name."','".$folder."')";
           $checkS = $DRW->query($ins_query, $DRW_main);
           $msg =  'Search logo has been added successfully!';
           header('Location: logos.php?msg='.$msg);

        }else{
            $msg =  'something went wrong. Please try again!';
        }        
    }else{
        $msg=implode(',',$errors);
    }
}

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Add Search Logo</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
    </head>
    <body>
		
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data">
        <table style="border:1px solid #ccc" cellspacing=0 width=40%>
            <tr>
				<td align="right" colspan="2" style="padding-top: 10px;">
				 <a href="logos.php">Back</a> &nbsp;&nbsp;&nbsp;
				</td>
			</tr>
			<tr>
				<td align="left" colspan="2">
				 <h2>&nbsp;&nbsp;Add Search Logo</h2>
				</td>
			</tr>
			<?php
			if(!empty($msg)){
				echo '<tr>
				<td align="center" colspan="2">
				 <p><font color="#46a049">'.$msg.'</font></p>
				 </td>
				 </tr>';
			}
			?>
			<tr>    <td align="center">Search Logo:</td>
                                <td> <input type="file" size="40" name="logo" accept="image/*"><br><br>
				</td>
				
			</tr>
			<tr>
				<td colspan="2">
				&nbsp;
				</td>
			</tr>
			<tr>
                                <td align="left" >&nbsp;&nbsp;&nbsp;</td>
                                 <td align="left" style="padding-bottom: 10px;">
                                    <input type="submit" name="submit" value="Submit">
				</td>                                
				
			</tr>
			</table>       
            
        </form>
    </body>
</html>
