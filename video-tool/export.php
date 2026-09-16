<?php require_once 'config.php';
include "simple_html_dom.php";
date_default_timezone_set("America/Chicago");
$message = (!empty($_GET['msg'])) ? trim(($_GET['msg'])) : '';
if (isset($_GET['page_no']) && $_GET['page_no']!="") {
    $page_no = $_GET['page_no'];
} else {
    $page_no = 1;
}
$site_url = 'http://'.$_SERVER['HTTP_HOST'];

$sql = "SELECT * FROM cscan_youtube_video order by id desc ";
$checkV = $DRW->query($sql, $DRW_read);
$countV = $DRW->num_rows($checkV);

$table='<table border=1 cellspacing=0 width=95% align="center">  
    <tr>
        <th>S.No.</th>
        <th>YouTube Url</th>
        <th>Status</th>
        <th>Logo</th>
        <th>Logo Matched Time</th>
        <th>Keywords</th>
        <th>Keywords Matched Time in Video Screen</th>
        <th>Keywords Matched Time in Voice</th>
        <th>Sentiment Score</th>
        <th>Sentiment Score (Positive)</th>
        <th>Sentiment Score (Negative)</th>
        <th>Sentiment Score (Neutral)</th>
        <th>Sentiment Score (Mixed)</th>
    </tr>
    ';

$sn=1;
if ($countV> 0) {   
    $status = '';
    while ($row = $DRW->fetch_array($checkV)) {
        $line=1;
        if ($row['status'] == 3 && $row['audio_text_status']>0) {
           $status = 'Processed';
        } else {
           $status = 'Unprocessed';
        }
        $sentiment='';
        $audio_text_status = $row['audio_text_status'];
        $sql_sentiment = "SELECT id,sentiment,positive,negative,neutral,mixed FROM cscan_youtube_sentiment where video_id='".$row['id']."'";
        $checkS = $DRW->query($sql_sentiment, $DRW_read);
        $countS = $DRW->num_rows($checkS);
        if ($countS > 0) {
            $row_sentiment = $DRW->fetch_array($checkS);
            $sentiment = $row_sentiment['sentiment'];
            $positive = $row_sentiment['positive']*100;
            $negative = $row_sentiment['negative']*100;
            $neutral = $row_sentiment['neutral']*100;
            $mixed = $row_sentiment['mixed']*100;
        }

        $table .='<tr>
            <td>'.$sn.'</td> 
            <td><a href="' . $row['youtube_url'] . '" target="_blank">' . $row['youtube_url'] . '</a></td>
            <td align="center">' . $status . '</td>';            
             
               
        $sql_match_logo = "SELECT l.id,l.logo_name,l.logo_path,lm.logo_match_time,lm.created_date,l.created_date as logo_date FROM cscan_youtube_search_logos as l join cscan_youtube_logos_match as lm on((lm.logo_id=l.id) AND (lm.video_id='" .$row['id']. "') AND lm.logo_match_time IS NOT NULL) order by l.id desc ";
        $checkLM = $DRW->query($sql_match_logo, $DRW_read);
        $countLM = $DRW->num_rows($checkLM);
        $p = 1;
        $keywordsMatch=array();
        $logoMatch=array();
        $sql_match = "SELECT k.id,k.keyword,km.keyword_match_time,km.audio_match_time,km.created_date,k.created_date as keyword_date FROM cscan_youtube_search_keywords as k join cscan_youtube_keywords_match as km on((km.keyword_id=k.id) AND (km.video_id='" . $row['id'] . "') AND (km.keyword_match_time IS NOT NULL OR km.audio_match_time IS NOT NULL)) order by k.id desc ";
        $checkVM = $DRW->query($sql_match, $DRW_read);
        $countVM = $DRW->num_rows($checkVM);
        //$dataVM = $DRW->fetch_row($checkVM);
        if($countLM>0){
            while ($row_match = $DRW->fetch_array($checkLM)){
                $logoMatch[]=$row_match;
            }
        }
        if($countVM>0){
            while ($row_match = $DRW->fetch_array($checkVM)){
                $keywordsMatch[]=$row_match;
            }
        }
        
        if(count($keywordsMatch)>=count($logoMatch)){
            $array_loop=$keywordsMatch;
        }else{
            $array_loop=$logoMatch;
        }
        
        
       // die;
        if($line==1){
            if(empty($logoMatch)){
                $match_time = '';
                $logo_url='Not Matched';
            }else{
                $match_time = $logoMatch[0]['logo_match_time'];
                $logo_url=$site_url.'/video-tool/'.$logoMatch[0]['logo_path'] . '/' . $logoMatch[0]['logo_name'];
            }
           $table .= '<td>'.$logo_url.'</td>';
           $table .= '<td>'.$match_time.'</td>';
           if(empty($keywordsMatch)){
                $keywords='Not Matched';
                $match_time_screen = '';
                $match_time_voice = 'Not Matched';
                
            }else{
                $keywords=$keywordsMatch[0]['keyword'];
                $match_time_screen = $keywordsMatch[0]['keyword_match_time'];
                $match_time_voice = $keywordsMatch[0]['audio_match_time'];               
            }
            $table .= '<td>'.$keywords.'</td>';
            $table .= '<td>'.$match_time_screen.'</td>';            
            $table .= '<td>'.$match_time_voice.'</td>'; 
            $table .= '<td>'.$sentiment.'</td>';
            $table .= '<td>'.$positive.'</td>';
            $table .= '<td>'.$negative.'</td>';
            $table .= '<td>'.$neutral.'</td>';
            $table .= '<td>'.$mixed.'</td>';
            $table .= '</tr>';            
        }
        if(count($array_loop)>1){
            for($i=1;$i<count($array_loop);$i++){
                 $table .= '<tr><td> </td><td> </td><td> </td>';
                 if(!empty($logoMatch[$i]['logo_path']) && $logoMatch[$i]['logo_path']!=''){
                     $table .= '<td>'.$logoMatch[$i]['logo_path'] . '/' . $logoMatch[$i]['logo_name'].'</td>';
                 }else{
                     $table .= '<td> Not Matched</td>';
                 }
                 if(!empty($logoMatch[$i]['logo_match_time']) && $logoMatch[$i]['logo_match_time']!=''){
                     $table .= '<td>'.$logoMatch[$i]['logo_match_time'].'</td>';
                 }else{
                     $table .= '<td> </td>';
                 }
                 if(!empty($keywordsMatch[$i]['keyword']) && $keywordsMatch[$i]['keyword']!=''){
                     $table .= '<td>'.$keywordsMatch[$i]['keyword'].'</td>';
                 }else{
                     $table .= '<td> </td>';
                 }
                 if(!empty($keywordsMatch[$i]['keyword_match_time']) && $keywordsMatch[$i]['keyword_match_time']!=''){
                     $table .= '<td>'.$keywordsMatch[$i]['keyword_match_time'].'</td>';
                 }else{
                     $table .= '<td>Not Matched</td>';
                 }
                 if(!empty($keywordsMatch[$i]['audio_match_time']) && $keywordsMatch[$i]['audio_match_time']!=''){
                     $table .= '<td>'.$keywordsMatch[$i]['audio_match_time'].'</td>';
                 }else{
                     $table .= '<td>Not Matched</td>';
                 }
                  $table .= '<td></td><td></td><td></td><td></td>';
                  $table .= '</tr>';                
            }
        }
        $table .= '<tr>                    
        <td> </td>
        <td> </td>
        <td> </td>
        <td> </td>
        <td> </td>
        <td> </td>
        <td> </td>
        <td> </td> 
        <td> </td>
        <td> </td>
        <td> </td> 
        <td> </td>
        <td> </td> 
        </tr>'; 
        $sn++;
    }
   
}
  $table .= '</table>';
$filename = 'video-tool.csv';
 $html = str_get_html($table);
header('Content-type: application/ms-excel');
header('Content-Disposition: attachment; filename='.$filename);
$fp = fopen("php://output", "w");
foreach($html->find('tr') as $element)
{
    $td = array();
    foreach( $element->find('th') as $row)  
    {
        $td [] = $row->plaintext;
    }
    if(!empty($td))
        fputcsv($fp, $td);

    $td = array();
    foreach( $element->find('td') as $row)  
    {
        $td [] = $row->plaintext;
    }
    if(!empty($td))
        fputcsv($fp, $td);
}
fclose($fp);
?>