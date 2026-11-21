<?php
$fromtemp = false;
$updID = false;
$disabled = false;
$url = false;

include_once 'addProductPersistenceAndLogic.php';
include_once 'addProductFormBuilder.php';
?>
<script>
/**
 * Check if any products are selected, open up popup if so
 */
function massUpdate()
{
    var items_to_edit = [];

    for (var i=0; i < document.delForm.elements.length; i++) {
        if (document.delForm.elements[i].name == 'delID[]' && document.delForm.elements[i].checked == true) {
            items_to_edit.push(document.delForm.elements[i].value);
        }
    }

    if (items_to_edit.length > 0) {
        document.getElementById('massupdater').massupdate_ids.value = items_to_edit;
        document.getElementById('showbox_massupdate').style.display = 'block';
        massUpdateSearchFormNameFocus();
    }
}

/**
 * Check if any field needs changing, otherwise warn nothing has been edited
 */
function massUpdateCheck()
{
    var something_changed = false;

    if (document.getElementById('cmp_ids').value != '' || document.getElementById('productName').value != '' || document.getElementById('scsc_comboIDs').value != ''
        || document.getElementById('combo_sid').value != '0' || document.getElementById('massupdate_image').value != '') {
        something_changed = true;
    }

    if (!something_changed) {
        alert('No edits have been made');
    }

    return something_changed;
}

function massUpdateCancel()
{
    document.getElementById('showbox_massupdate').style.display = 'none';
    massUpdateSearchFormNameFocus();
}

/**
 * Search lookup helper can only work if there is one prodName form on page. Need to switch back and forth
 * the name of form used by search to allow edit form to make use of the search lookup helper.
 */
function massUpdateSearchFormNameFocus()
{
    var manager_form = document.getElementById('manage_product');
    manager_form.name = (manager_form.name == 'prodForm') ? 'prodForm_original' : 'prodForm';
}
</script>
<div id="showbox_massupdate" class="popup bodytext hide">
<h3>Edit Selected Entries</h3>
<?php if(isset($_REQUEST['cstat']) && $_REQUEST['cstat']=='11' && isset($_REQUEST['pstat']) && $_REQUEST['pstat']=='6' ){
    $showciti=1;
}else if(isset($_SESSION['pstat']) && $_SESSION['pstat']=='6' && isset($_SESSION['cstat']) && $_SESSION['cstat']=='11'){
    $showciti=1;
}else{
    $showciti=0;
}?>
<?php if(isset($_REQUEST['cstat']) && $_REQUEST['cstat']=='13'){
    $showjunk=0;
}else if(isset($_SESSION['cstat']) && $_SESSION['cstat']=='13'){
    $showjunk=0;
}else{
    $showjunk=1;
}?>

<form name="prodForm" id="massupdater" action="massupdate.php" method="post" enctype="multipart/form-data" onsubmit="return massUpdateCheck();">
<input type="hidden" id="massupdate_ids" name="massupdate_ids" value="">
<input type="hidden" id="pstat" name="pstat" value="<?php echo (isset($_SESSION['pstat'])) ? (int)$_SESSION['pstat'] : 0 ?>">
<input type="hidden" id="cstat" name="cstat" value="<?php echo (isset($_SESSION['cstat'])) ? (int)$_SESSION['cstat'] : 0 ?>">
<input type="hidden" name="co_states" value=""><input type="hidden" name="incentive_ongoing" value=""><span id="so_incentive" style="display:none;"></span>
<div class="popup-label">Company - <span onclick="showDiv_outer('showbox_cmps_outer', 'addcmplink'); document.forms.cmp_selform.cmp_id.focus(); return false;" id="addcmplink">Add company</span></div>
<div id="cmps" class="popup-collection"></div>
<input type="hidden" name="cmp_ids" value=""><input type="hidden" name="old_cmp_ids" value=""><input type="hidden" name="is_insuranceexchange" value="">
<div class="popup-control">
<div class="popup-label pull-right" onclick="add_SCSC(); return false;" id="add_SCSC_link">Add</div>
<div id="sectorID_div">
    <label for="combo_sid">Sector</label>
    <select name="combo_sid" id="combo_sid" class="combo_box" onchange="clearSCSC();"><option value="0">&nbsp;</option>
<?php
foreach ($sector as $id => $name) {
    if (checkSector($id)) {
        echo '<option value="'.$id.'">'.htmlspecialchars($name).'</option>';
    }
}
?>
    </select>
</div>
<div class="popup-collection">
    <div id="categoryID_div" class="popup-level">
        <label for="combo_cid">Category</label>
        <select name="combo_cid" id="combo_cid" class="combo_box hide" onchange="do_SCSC(document.prodForm.combo_cid,'cid',document.prodForm.combo_scid,true);"><option value="0">&nbsp;</option></select>
    </div>
    <div id="subCategoryID_div" class="popup-level">
        <label for="combo_scid">Sub Category</label>
        <select name="combo_scid" id="combo_scid" class="combo_box hide" onchange="do_SCSC(document.prodForm.combo_scid,'scid',document.prodForm.combo_sscid,true);"><option value="0">&nbsp;</option></select>
    </div>
    <div id="subSubCategoryID_div" class="popup-level">
        <label for="combo_sscid">Sub Sub Category</label>
        <select name="combo_sscid" id="combo_sscid" class="combo_box hide"><option value="0">&nbsp;</option></select>
    </div>
    <div id="scsc_combos"></div>
</div>
<label for="productName">Product name</label>
    <div class="popup-collection">
    <div id="showbox_cpns_outer" class="hide">
        <label for="co_productName">Lookup</label>
        <input type="text" id="co_productName" name="co_productName" class="input_box" size="30" maxlength="200" autocomplete="off" onkeyup="startTimer('showCPNs()');">
        <div id="showbox_cpns" class="popup-lookup"></div>
    </div>
    <div class="popup-control">
        <input type="text" id="productName" name="productName" size="42" maxlength="255" class="popup-level" value="">
    </div>
 <?php 
 if($showciti==1){?>
         <div class="popup-control">
        CITI <label><input name="is_citi" value="1" checked="checked" type="radio">Yes</label> &nbsp; <label><input name="is_citi" value="0" type="radio">No</label>
         </div>
        <?php }?>
        <?php if($showjunk==1){?> 
        <div class="popup-control">
        Glacier <label><input name="is_junk" value="1"  type="radio">Yes</label> &nbsp; <label><input name="is_junk" value="0" checked="checked" type="radio">No</label>
         </div>
        <?php }
        
       
        ?>
    </div>
<input type="hidden" name="scsc_comboIDs" id="scsc_comboIDs" value=""><input type="hidden" name="co_comboIDs" value=""><input type="hidden" name="scsc_combo_edit" value="">
</div>
<input type="submit" value="Update Selected Records" class="button">
<span class="popup-label pull-right" onclick="massUpdateCancel();">Cancel</span>
</form>
</div>
<?php
include_once 'addProductJSandPopups.php';
