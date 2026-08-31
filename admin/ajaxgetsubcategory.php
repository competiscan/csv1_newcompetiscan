<?php
require_once("../auth_auth.php");
require_once '../includes/functions.php';
ini_set('memory_limit', '-1');
set_time_limit(0);
$html='';
if(isset($_REQUEST['ajaxfor']) && $_REQUEST['ajaxfor']=='getsubcat' && $_REQUEST['id']!='') {
    $id=$_REQUEST['id'];
    $m_subcategoryID=$_REQUEST['scid'];    
    $subcategory =getSubCategory($id, false);
   
    $html .='<select class="combo_box" name="m_subcategoryID"><option value="0">All</option>';
    if($subcategory){   
        foreach( $subcategory as $scid=>$scname ) {
                   $html .="<option value=\"$scid\"";
                    if($scid==$m_subcategoryID) $html .=' selected="selected"';
                    $html .=">".htmlspecialchars($scname)."</option>";
                }
    }    
    $html .='</select><input type="hidden" name="om_subcategoryID" value="'.htmlspecialchars($m_subcategoryID, ENT_QUOTES).'" />';        
            
	
    }
    
echo $html; die;
?>
