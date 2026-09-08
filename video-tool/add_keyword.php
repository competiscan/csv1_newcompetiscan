<?php die;
require_once 'config.php';
if(isset($_POST['keyword']) && $_POST['keyword']!='') 
{
	$keywordName = trim($_POST['keyword']);
    if(!empty($keywordName)){
		$sql = "SELECT id FROM cscan_youtube_search_keywords where keyword='".$keywordName."'";
		$checkK = $DRW->query($sql, $DRW_read);
		$countK = $DRW->num_rows($checkK);        
		if ($countK > 0) {
			$msg =  'This keyword already exist so please add another keyword.';					
		}else{
			$ins_query="Insert into cscan_youtube_search_keywords (keyword) values('".$keywordName."')";
			$checkS = $DRW->query($ins_query, $DRW_main);
			$msg =  'Search Keyword has been added successfully!';
			header('Location: keywords.php?msg='.$msg);
			
		}
	}else{
		$msg =  'Search Keyword should not be blank.';
		
	}
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Add Search Keyword</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
    </head>
    <body>
		
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
        <table style="border:1px solid #ccc" cellspacing=0 align="left" width=50%>
            <tr>
				<td align="right" colspan="2" style="padding-top: 10px; margin-right:10px;">
				 <a href="keywords.php">Back</a> &nbsp;&nbsp;&nbsp;
				</td>
			</tr>
			<tr>
				<td align="center" colspan="2">
				 <h2>&nbsp;&nbsp;Add Search Keywords</h2>
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
			<tr>
                            <td align="center"> Search keyword:</td>
                            <td>  <input type="text" size="40" name="keyword" maxlength="500"><br>
				</td>
			</tr>
			<tr>
				<td>
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
