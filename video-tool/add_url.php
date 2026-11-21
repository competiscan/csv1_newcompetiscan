<?php die;
require_once 'config.php';
if(isset($_POST['url']) && $_POST['url']!='') 
{
    $urlName = trim($_POST['url']);
    if(!empty($urlName)){
        if(strstr($urlName,'http') && strstr(strtolower($urlName),'youtube.com')){
            $sql = "SELECT id FROM cscan_youtube_video where youtube_url='".$urlName."'";
            $checkS = $DRW->query($sql, $DRW_read);
            $countS = $DRW->num_rows($checkS);        
            if ($countS > 0) {
                
                $msg =  'This Url is already exist. Please enter another YouTube Url.';                
                
            }else{
                $ins_query="Insert into cscan_youtube_video (youtube_url) values('".$urlName."')";
                $checkS = $DRW->query($ins_query, $DRW_main);
                $msg =  'YouTube url has been added successfully!';
                header('Location: index.php?msg='.$msg);
            }
        }else{
            $msg =  'Invalid url. Please enter valid YouTube url.';
        }
    }else{
        $msg =  'YouTube url should not be blank.';        
    }
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Add Youtube Url</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
    </head>
    <body>
		
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
            <table style="border:1px solid #ccc" cellspacing=0 width=45%>
                <tr>
                    <td align="right" colspan="2" style="padding-top: 10px;margin-right: 10px;">
                     <a href="index.php">Back</a> &nbsp;&nbsp;&nbsp;
                    </td>
                </tr>
                <tr>
                    <td align="center" colspan="2">
                     <h2>Add YouTube Url</h2>
                    </td>
                </tr>
                <?php
                if(!empty($msg)){
                    echo '<tr>
                    <td colspan="2" align="center">
                     <p><font color="red">'.$msg.'</font></p>
                     </td>
                     </tr>';
                }
                ?>
                <tr>
                    <td align="center">YouTube Url:</td>
                    <td>
                      <input type="text" size="40" name="url" maxlength="500"><br>
                    </td>
                </tr>
                <tr>
                    <td>
                    &nbsp;
                    </td>
                </tr>
                <tr>
                    <td align="center">&nbsp;&nbsp;&nbsp;</td>
                    <td align="left" style="padding-bottom: 10px;">
                     <input type="submit" name="submit" value="Submit">
                    </td>
                </tr>
            </table>       
            
        </form>
    </body>
</html>
