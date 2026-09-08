<?php die;
require_once 'config.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
date_default_timezone_set("America/Chicago");
$message = (!empty($_GET['msg']))?trim(($_GET['msg'])):'';
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Video Detail</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="../css/bootstrap2.min.css" rel="stylesheet">
    </head>
    <body>		
        <?php
        if($_GET['vid'] && $_GET['vid']!=''){
            $id=$_GET['vid'];      
            $sql = "SELECT id,youtube_url,audio_text_status FROM cscan_youtube_video where id='".$id."'";        
            $checkV = $DRW->query($sql, $DRW_read);
            $countV = $DRW->num_rows($checkV);        
            if($countV>0){
                $sentiment='';
                $row = $DRW->fetch_array($checkV);
                $audio_text_status=$row['audio_text_status'];
                preg_match('/[\\?\\&]v=([^\\?\\&]+)/', $row['youtube_url'], $matches);
                if(count($matches)>0)
                    $videoid = $matches[1];
                else
                    $videoid = '';					
                $width = '1000px';
                $height = '400px';
                $sql_sentiment = "SELECT id,sentiment,positive,negative,neutral,mixed FROM cscan_youtube_sentiment where video_id='".$id."'";        
                $checkS = $DRW->query($sql_sentiment, $DRW_read);
                $countS = $DRW->num_rows($checkS);        
                if($countS>0){
                    $row_sentiment = $DRW->fetch_array($checkS);
                    $sentiment=$row_sentiment['sentiment'];
                    $positive = $row_sentiment['positive']*100;
                    $negative = $row_sentiment['negative']*100;
                    $neutral = $row_sentiment['neutral']*100;
                    $mixed = $row_sentiment['mixed']*100;
                }		       

                echo '<table border=1 cellspacing=0 width=100%>
                    <tr>
                            <th colspan="5" align="center" style="background-color: #1400ff36;"><h2>YouTube Video</h2></th>
                    </tr>
                    <tr>    
                            <th colspan="5"><span style="float:left; font-weight:bold; padding-top:10px;padding-bottom: 10px;">&nbsp;&nbsp;&nbsp;<a href="keywords.php"> Manage Search Keywords </a>&nbsp;&nbsp;&nbsp;<a href="logos.php"> Manage Search Logos </a></span><span style="float:right;padding-top:10px;padding-bottom: 10px;margin-right:35px;"><a href="index.php"> Back </a>&nbsp;&nbsp;</span></th>
                    </tr>
                    <tr>    
                            <th align="center"  colspan="5"><iframe style="margin-left:20px;margin-top:10px;margin-bottom:10px;" id="ytplayer" type="text/html" width="'.$width.'" height="'.$height.'"
                            src="https://www.youtube.com/embed/'.$videoid.'?rel=0&showinfo=0&color=white&iv_load_policy=3"
                            frameborder="0" allowfullscreen></iframe></th>
                    </tr>
                    <tr>    
                    <td colspan="5"align="left" style="margin-top:10px;margin-bottom:10px; padding-top:10px;padding-bottom: 10px;">&nbsp;&nbsp;<strong>YouTube Url:</strong> <a target="_blank" href="'.$row['youtube_url'].'"> '.$row['youtube_url'].'</a>';            
                    if($sentiment!=''){
                        echo '&nbsp;&nbsp;<span style="text-align:right; margin-left:60px;"><strong>Sentiment:</strong>&nbsp;&nbsp;'.ucfirst(strtolower($sentiment)).'&nbsp;&nbsp;<strong>Sentiment Score(Positive):</strong>&nbsp;&nbsp;'.$positive.'&nbsp;&nbsp;<strong>Sentiment Score(Negative):</strong>&nbsp;&nbsp;'.$negative.'&nbsp;&nbsp;<strong>Sentiment Score(Neutral):</strong>&nbsp;&nbsp;'.$neutral.'&nbsp;&nbsp;<strong>Sentiment Score(Mixed):</strong>&nbsp;&nbsp;'.$mixed.'</span>';
                    }
                    echo '</td>
                    </tr>
                    </table>
                    <table border=1 cellspacing=0 width=100%>
                        <tr>
                            <td valign="top" width="39%"><table width="100%" border="1">                               
                        <tr>
                            <th width="5%" style="height:33px">S.N.</th>
                            <th width="40%">Logo</th>
                            <th width="35%">Matched Time in Video Screen/Status</th>                                       					
                            <th width="25%">Date</th>
                        </tr>';
                        $sql_match_logo = "SELECT l.id,l.logo_name,l.logo_path,lm.logo_match_time,lm.created_date,l.created_date as logo_date FROM cscan_youtube_search_logos as l left join cscan_youtube_logos_match as lm on((lm.logo_id=l.id) AND (lm.video_id='".$id."')) order by l.id desc ";        
                        $checkLM = $DRW->query($sql_match_logo, $DRW_read);
                        $countLM = $DRW->num_rows($checkLM);
                        $p=1;
                        if($countLM>0){					
                            while ($row_match = $DRW->fetch_array($checkLM)) {
                                if($row_match['logo_match_time']){

                                      $match_time=$row_match['logo_match_time'];							
                                  }else{
                                      $match_time='Not Matched';	
                                  }
                                 if(!$row_match['created_date']){
                                      $row_match['created_date']=$row_match['logo_date'];
                                  }
                                 echo '<tr>
                                        <td align="center">&nbsp;'.$p.'</td>
                                         <td>&nbsp;
                                         <img style="max-width:200px;max-height:200px;padding-top:3px;padding-bottom:3px;" src="'.$row_match['logo_path'].'/'.$row_match['logo_name'].'"/>
                                        &nbsp;						
                                        </td>
                                        <td>&nbsp;'.$match_time.'</td>                                                
                                        <td align="center">&nbsp;'.date("m/d/Y h:i:s",strtotime($row_match['created_date'])).'</td>
                                </tr>';
                                $p++;   
                            }
                        }else{
                            echo '
                            <tr>    
                                <th colspan="4"align="center">&nbsp;&nbsp; There are no record exist.</th>
                            </tr>';
                        }
                        echo '</table></td>                                 
                        <td width="1%"></td>    
                        <td valign="top"width="60%"><table border="1">
                        ';                

                        echo '
                        <tr>
                            <th width="3%" style="height:33px" align="center">S.N.</th>
                            <th width="25%">Keywords</th>
                            <th width="25%">Matched Time in Video Screen/Status</th>
                            <th width="25%">Matched Time in Voice/Status</th>						
                            <th width="15%">Date</th>
                        </tr>';
                // output data of each row
                $i = 1;
                $status='';
                $sql_match = "SELECT k.id,k.keyword,km.keyword_match_time,km.audio_match_time,km.created_date,k.created_date as keyword_date FROM cscan_youtube_search_keywords as k left join cscan_youtube_keywords_match as km on((km.keyword_id=k.id) AND (km.video_id='".$id."')) order by k.id desc ";        
                $checkVM = $DRW->query($sql_match, $DRW_read);
                $countVM = $DRW->num_rows($checkVM);        
                if($countVM>0){					
                    while ($row_match = $DRW->fetch_array($checkVM)) {
                        if($row_match['keyword_match_time']){
                            $match_time=$row_match['keyword_match_time'];							
                        }else{
                            $match_time='Not Matched';	
                        }
                        if($row_match['audio_match_time']){							
                            $audio_match_time=$row_match['audio_match_time'];							
                        }else if($audio_text_status==2){
                            $audio_match_time='Transcript option is not available in this video on Youtube';	
                        }else{
                            $audio_match_time='Not Matched';	
                        }

                        if(!$row_match['created_date']){
                            $row_match['created_date']=$row_match['keyword_date'];
                        }			
                        echo '<tr>
                                <td align="center">&nbsp;'.$i.'</td>
                                 <td>&nbsp;
                                 '.$row_match['keyword'].'
                                &nbsp;						
                                </td>
                                <td>&nbsp;'.$match_time.'</td>
                                <td>&nbsp;'.$audio_match_time.'</td>
                                <td>&nbsp;'.date("m/d/Y h:i:s",strtotime($row_match['created_date'])).'</td>
                        </tr>';
                        $i++;

                    }
                }else{
                    echo '
                    <tr>    
                        <th colspan="5"align="center">&nbsp;&nbsp; There are no record exist.</th>
                    </tr>';  
                }
                echo '</table></td></tr></table></br></br></br>';
            }else{
                    $msg =  'Invalid id provided!';
                    header('Location: index.php?msg='.$msg);
            }
        } 
        ?>
    </body>
</html>
