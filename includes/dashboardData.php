<?php 
class dashboardData {
	protected $default_map_table = 'dashboard_retail_energy_pricing';
	protected $default_maps = array(
		'srno' => array(
			'label'=>'Sr. No',
			'field'=>'sr_no',
			'pk'=>0,
			'type'=>'string',
			'search_type'=>'singleline',
		),
		'electricitynaturalgas' => array( // [Electricity, Natural Gas, (Both)]
			'label'=>'Electricity - Natural Gas',
			'field'=>'energy_type',
			'pk'=>1,
			'type'=>'list',
			'list_data'=>array('table'=>'dashboard_type_energy','name_field'=>'dashboard_type_energy_name','id_field'=>'dashboard_type_energy_id','sort_field'=>'dashboard_type_energy_name'),
			'search_type'=>'multiselect',
		),
		'state' => array(
			'label'=>'State',
			'field'=>'stateID',
			'pk'=>1,
			'type'=>'list',
			'list_data'=>array('table'=>'cscan_state','name_field'=>'stateCode','id_field'=>'stateID','sort_field'=>'stateCode'),
			'search_type'=>'multiselect',
		),
		'retailmarketer' => array(
			'label'=>'Retail Marketer',
			'field'=>'companyID',
			'pk'=>1,
			'type'=>'list',
			'list_data'=>array('table'=>'cscan_company','name_field'=>'companyName','id_field'=>'companyID','sort_field'=>'companyName'),
			'search_type'=>'lookup',
		),
		'edc' => array(
			'label'=>'EDC / LDC / TDSP',
			'field'=>'edc_id',
			'pk'=>1,
			'type'=>'list',
			'list_data'=>array('table'=>'cscan_edc','name_field'=>'edc_name','id_field'=>'edc_id','sort_field'=>'edc_name'),
			'search_type'=>'multiselect',
		),
		'producttype' => array( // [ESCO Referral, Fixed, Indexed, N/A, Variable]
			'label'=>'Product Type',
			'field'=>'product_type',
			'pk'=>1,
			'type'=>'list',
			'list_data'=>array('table'=>'dashboard_type_product','name_field'=>'dashboard_type_product_name','id_field'=>'dashboard_type_product_id','sort_field'=>'dashboard_type_product_name'),
			'search_type'=>'multicheck',
		),
		'term' => array(
			'label'=>'Term',
			'field'=>'fixed_term',
			'pk'=>1,
			'type'=>'int',
			'format'=>'0',
			'search_type'=>'multiselect',
			'search_data'=>array('table'=>'cscan_eterm_length','name_field'=>'TermLengthName','id_field'=>'TermLengthID','sort_field'=>'TermLengthSort','min_field'=>'TermLengthMin','max_field'=>'TermLengthMax'),
		),
		'renewable' => array( //[N/A]
			'label'=>'% Renewable',
			'field'=>'pct_renewable',
			'pk'=>1,
			'type'=>'float',
			'format'=>'0',
			'search_type'=>'slider',
			'search_data'=>array('min'=>0,'max'=>100,'step'=>1),
		),
		'offerrate' => array( //[0.0000�, 10% off PTC, 5% off PTC, N/A]
			'label'=>'Offer Rate ($)',
			'field'=>'offer_rate',
			'pk'=>0,
			'type'=>'float',
			'format'=>'6',
			//'search_type'=>'multiselect',
			//'search_data'=>array('table'=>'cscan_offer_price','name_field'=>'OfferPriceName','id_field'=>'OfferPriceID','sort_field'=>'OfferPriceSort','min_field'=>'OfferPriceMin','max_field'=>'OfferPriceMax'),
			'search_type'=>'slider',
			'search_data'=>array('min'=>0,'max'=>25,'step'=>0.01),
		),
		'productname' => array(
			'label'=>'Product Name',
			'field'=>'product_name',
			'pk'=>0,
			'type'=>'string',
			'search_type'=>'singleline',
		),
		'date' => array(
			'label'=>'Date',
			'field'=>'dashboard_date',
			'pk'=>0,
			'type'=>'date',
			'format'=>'m/d/y',
			'search_type'=>'date',
		),
		'unitofenergy' => array( //[kWh, therm]
			'label'=>'Unit of Energy',
			'field'=>'unit_of_energy',
			'pk'=>0,
			'type'=>'list',
			'list_data'=>array('table'=>'dashboard_type_unit','name_field'=>'dashboard_type_unit_name','id_field'=>'dashboard_type_unit_id','sort_field'=>'dashboard_type_unit_name'),
			'search_type'=>'multiselect',
		),
		'earlyterminationfee' => array( //[$100/$200, $0.00, N/A]
			'label'=>'Early Termination Fee ($)',
			'field'=>'early_termination_fee',
			'pk'=>0,
			'type'=>'float',
			'format'=>'2',
			'search_type'=>'slider',
			'search_data'=>array('min'=>0,'max'=>500,'step'=>1),
		),
		'earlyterminationnotes' => array(
			'label'=>'Early Termination Notes',
			'field'=>'early_termination_notes',
			'pk'=>0,
			'type'=>'string',
			'search_type'=>'singleline',
		),
		'monthlyfee' => array( //[N/A, Unkown, Usage Based, $0.00]
			'label'=>'Monthly Fee ($)',
			'field'=>'monthly_fee',
			'pk'=>0,
			'type'=>'float',
			'format'=>'2',
			'search_type'=>'slider',
			'search_data'=>array('min'=>0,'max'=>50,'step'=>1),
		),
		'monthlyfeenotes' => array(
			'label'=>'Monthly Fee Notes',
			'field'=>'monthly_fee_notes',
			'pk'=>0,
			'type'=>'string',
			'search_type'=>'singleline',
		),
		'notes' => array(
			'label'=>'Notes',
			'field'=>'notes',
			'pk'=>0,
			'type'=>'string',
			'search_type'=>'singleline',
		),
		'source' => array(
			'label'=>'Source',
			'field'=>'source',
			'pk'=>1,
			'type'=>'string',
			'search_type'=>'singleline',
		),
		'pricetocompare' => array(
			'label'=>'Price to Compare',
			'field'=>'price_to_compare',
			'pk'=>0,
			'type'=>'float',
			'format'=>'6',
			'search_type'=>'slider',
			'search_data'=>array('min'=>0,'max'=>20,'step'=>0.01),
		),
		'discontinueddate' => array(
			'label'=>'Discontinued Date',
			'field'=>'discontinued_date',
			'pk'=>0,
			'type'=>'date',
			'format'=>'m/d/y',
			'search_type'=>'date',
		),
	);
	public $map_table = '';
	public $maps = array();
	public $DRW = false;
	public $DRW_read = '';
	public $DRW_main = '';
	public $DRW_crm = '';
	
	function __construct($map_table='',$maps=array()){
		$this->set_DRW();
		$this->set_map_table($map_table);
		$this->set_maps($maps);
	}
	
	function guid(){
		if (function_exists('com_create_guid') === true){
			return trim(com_create_guid(), '{}');
		}
		
		return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
	}
	
	function set_DRW(){
		global $DRW,$DRW_read,$DRW_main,$DRW_crm;
		$this->DRW = $DRW;
		$this->DRW_read = $DRW_read;
		$this->DRW_main = $DRW_main;
		$this->DRW_crm = $DRW_crm;
	}
	
	function set_maps($maps=array()){
		if(count($maps)>0){
			$this->maps = $maps;
		}
		else{
			$this->maps = $this->default_maps;
		}
	}
	
	function set_map_table($map_table=''){
		if(!empty($map_table)){
			$this->map_table = $map_table;
		}
		else{
			$this->map_table = $this->default_map_table;
		}
	}
	
	function database_value($value='',$map_key=''){
		$dbvalue = false;
		if(isset($this->maps[$map_key])){
			switch($this->maps[$map_key]['type']){
				case 'float':
					$dbvalue = preg_replace('/[^0-9\\.\\-]+/','',$value);
					if(strlen($dbvalue)!=0 && strtolower($value)!='n/a'){
						$dbvalue = (float) $dbvalue;
					}
					else{
						$dbvalue = 'NULL';
					}
					break;
				case 'int':
					$dbvalue = preg_replace('/[^0-9\\-]+/','',$value);
					if(strlen($dbvalue)!=0 && strtolower($value)!='n/a'){
						$dbvalue = (int) $dbvalue;
					}
					else{
						$dbvalue = 'NULL';
					}
					break;
				case 'date':
					if(strlen($value)!=0 && strtolower($value)!='n/a'){
						$value = strtotime($value);
						$value = date('Y-m-d',$value);
					}
					else{
						$value = '0000-00-00';
					}
					$dbvalue = "'".$value."'";
					break;
				case 'list':
					if(!empty($value)){
						$dbvalue = $this->get_list($value,$map_key,'id');
					}
					else{
						$dbvalue = 0;
					}
					break;
				default: // 'string'
					$dbvalue = "'".$this->DRW->real_escape_string($value)."'";
					break;
			}
		}
		return $dbvalue;
	}
	
	function get_list($value='',$map_key='',$type='name'){
		if(isset($this->maps[$map_key]) && isset($this->maps[$map_key]['list_data'])){
			if($type=='id'){
				$sqlL = "SELECT ".$this->maps[$map_key]['list_data']['id_field']." FROM ".$this->maps[$map_key]['list_data']['table']." WHERE ".$this->maps[$map_key]['list_data']['name_field']."='".$this->DRW->real_escape_string($value)."'";
				$rsL = $this->DRW->query($sqlL,$this->DRW_read);
				$rowL = $this->DRW->fetch_row($rsL);
				return (int) $rowL[0];
			}
			else{ //name
				$sqlL = "SELECT ".$this->maps[$map_key]['list_data']['name_field']." FROM ".$this->maps[$map_key]['list_data']['table']." WHERE ".$this->maps[$map_key]['list_data']['id_field']."='".$this->DRW->real_escape_string($value)."'";
				$rsL = $this->DRW->query($sqlL,$this->DRW_read);
				$rowL = $this->DRW->fetch_row($rsL);
				return (string) $rowL[0];
			}
		}
		return false;
	}
	
	function get_list_array($map_key='',$list_key='list_data'){
		if(isset($this->maps[$map_key]) && isset($this->maps[$map_key][$list_key])){
			$outArray = array();
			$sqlL = "SELECT ".$this->maps[$map_key][$list_key]['id_field'].",".$this->maps[$map_key][$list_key]['name_field']." FROM ".$this->maps[$map_key][$list_key]['table']." WHERE ".$this->maps[$map_key][$list_key]['name_field']."<>'' ORDER BY ".$this->maps[$map_key][$list_key]['sort_field'];
			$rsL = $this->DRW->query($sqlL,$this->DRW_read);
			while($rowL = $this->DRW->fetch_row($rsL)){
				$outArray[$rowL[0]] = $rowL[1];
			}
			return $outArray;
		}
		return false;
	}
	
	function get_lookup_array($map_key='',$search=''){
		$search = trim($search);
		if(isset($this->maps[$map_key]) && isset($this->maps[$map_key]['list_data'])){
			$outArray = array();
			if($search!='') {
				$sqlL = "SELECT ".$this->maps[$map_key]['list_data']['id_field'].",".$this->maps[$map_key]['list_data']['name_field']." FROM ".$this->maps[$map_key]['list_data']['table'];
				$where = '';
				if($map_key=='retailmarketer'){
					if($where!=''){
						$where .= " AND ";
					}
					$where .= "isRetailMarketer=1";
				}
				$val = $this->mysqlLike($search);
				$regx = '';
				if(strlen($val)>2) {
					$firstpct = '%';
					if(preg_match('/^[a-zA-Z0-9]+$/',$search)){
						$regx = " AND ".$this->maps[$map_key]['list_data']['name_field']." REGEXP '[[:<:]]$search'";//[[:>:]]
					}
				}
				else {
					$firstpct = '';
				}
				if($where!=''){
					$where .= " AND ";
				}
				$where .= $this->maps[$map_key]['list_data']['name_field']." LIKE '$firstpct$val%'$regx";
				
				if($where!=''){
					$sqlL .= ' WHERE '.$where;
				}
				$sqlL .= " ORDER BY ".$this->maps[$map_key]['list_data']['sort_field'];

				$rsL = $this->DRW->query($sqlL,$this->DRW_read);
				while($rowL = $this->DRW->fetch_row($rsL)){
					$outArray[] = array('id'=>$rowL[0],'name'=>$rowL[1]);
				}
			}
			return $outArray;
		}
		return false;
	}
	
	function csvExcape($in,$delim=','){
		$out = $in;
		if(strpos($out, $delim)!==false || strpos($out, '"')!==false || strpos($out, "\r\n")!==false || strpos($out, "\n")!==false || strpos($out, "\r")!==false || preg_match('/^0+\\d+$/',$out)>0){
			$out = '"'.str_replace('"', '""', $out).'"';
		}
		return $out;
	}
	
	function csvRow($row=array(),$delim=','){
		$out = '';
		foreach($row as $r){
			if($out!=''){
				$out .= $delim;
			}
			$out .= $this->csvExcape($r,$delim);
		}
		return $out."\n";
	}
	
	function mysqlLike($string){
		$searchtext_like = $this->DRW->real_escape_string($string);
		$searchtext_like = str_replace('%','\\%',$searchtext_like);
		$searchtext_like = str_replace('_','\\_',$searchtext_like);
		return $searchtext_like;
	}
	
	function get_search_array($map_key=''){
		$values = $this->get_list_array($map_key);
		if($values===false){
			$values = $this->get_list_array($map_key,'search_data');
			if($values===false){
				return array();
			}
		}
		return $values;
	}
	
	function get_dashboard_assoc($dashboard_id='',$dashboard_date=''){
		$selectq = '';
		$history = '';
		if(!empty($dashboard_date)){
			$history = '_history';
		}
		foreach($this->maps as $match=>$map){
			if(!$this->maps[$match]['pk'] || empty($dashboard_date)){
				$selectq .= ','.$map['field'];
			}
		}
		$sql = "SELECT dashboard_id$selectq FROM ".$this->map_table.$history." WHERE dashboard_id='".$this->DRW->real_escape_string($dashboard_id)."'";
		if(!empty($dashboard_date)){
			$sql .= " AND dashboard_date=".$this->database_value($dashboard_date,'date');
		}
		$rs = $this->DRW->query($sql,$this->DRW_read);
		$row = $this->DRW->fetch_assoc($rs);
		return $row;
	}
	
	function get_dashboard_query_count($search=array(),$start=0,$limit=0,$sort_key='',$dashboard_id=''){
		$sql = $this->get_dashboard_query($search,$start,$limit,$sort_key,true,$dashboard_id);
		$rs = $this->DRW->query($sql,$this->DRW_read);
		$row = $this->DRW->fetch_row($rs);
		return (int)$row[0];
	}
	
	function do_where($search='',$map_key='',$history=''){
		$where = '';
		if($this->maps[$map_key]['type']=='list' && $this->maps[$map_key]['search_type']=='lookup'){
			$search = preg_split('/,/',$search,-1,PREG_SPLIT_NO_EMPTY);
		}
		if(!is_array($search)){
			$search = array($search);
		}
		foreach($search as $s){
			if(!empty($s)){
				if($where!=''){
					$where .= ' OR ';
				}
				if($this->maps[$map_key]['search_type']=='multiselect' && isset($this->maps[$map_key]['search_data']['min_field']) && isset($this->maps[$map_key]['search_data']['max_field'])){
					$sql = "SELECT ".$this->maps[$map_key]['search_data']['min_field'].",".$this->maps[$map_key]['search_data']['max_field']." FROM ".$this->maps[$map_key]['search_data']['table']." WHERE ".$this->maps[$map_key]['search_data']['id_field']."='".$this->DRW->real_escape_string($s)."' ORDER BY ".$this->maps[$map_key]['search_data']['sort_field'];
					$rs = $this->DRW->query($sql,$this->DRW_read);
					while($op = $this->DRW->fetch_row($rs)) {
						$where .= '('.$this->map_table.$history.".".$this->maps[$map_key]['field'].'>='.$op[0].' AND '.$this->map_table.$history.".".$this->maps[$map_key]['field'].'<='.$op[1].')';
					}
				}
				elseif($this->maps[$map_key]['type']=='date'){
					$date_where = '';
					if(preg_match('/^([^_]*)_([^_]*)$/',$s,$matches)){
						if(!empty($matches[1])){
							$date_where = $this->map_table.$history.".".$this->maps[$map_key]['field'].'>='.$this->database_value($matches[1],$map_key);
						}
						if(!empty($matches[2])){
							if($date_where!=''){
								$date_where .= ' AND ';
							}
							$date_where .= $this->map_table.$history.".".$this->maps[$map_key]['field'].'<='.$this->database_value($matches[2],$map_key);
						}
					}
					if($date_where==''){
						$date_where = $this->map_table.$history.".".$this->maps[$map_key]['field'].'='.$this->database_value($s,$map_key);
					}
					$where .= $date_where;
				}
				elseif($this->maps[$map_key]['search_type']=='slider'){
					//'search_data'=>array('min'=>0,'max'=>20),
					$savedmin = $this->maps[$map_key]['search_data']['min'];
					$savedmax = $this->maps[$map_key]['search_data']['max'];
					$tmp = explode('-',$s);
					if(!empty($tmp[0])){
						$savedmin = (float)trim($tmp[0]);
					}
					if(!empty($tmp[1])){
						$savedmax = (float)trim($tmp[1]);
					}
					if($savedmin!=$this->maps[$map_key]['search_data']['min'] || $savedmax!=$this->maps[$map_key]['search_data']['max']){
						$where .= '('.$this->map_table.$history.".".$this->maps[$map_key]['field'].'>='.$savedmin.' AND '.$this->map_table.$history.".".$this->maps[$map_key]['field'].'<='.$savedmax.')';
					}
				}
				else{ //if($this->maps[$map_key]['type']=='string' || $this->maps[$map_key]['type']=='list'){
					$where .= $this->map_table.$history.".".$this->maps[$map_key]['field']."='".$this->DRW->real_escape_string($s)."'";
				}
			}
		}
		if($where!=''){
			$where = ' AND ('.$where.')';
		}
		return $where;
	}
	
	function get_dashboard_query($search=array(),$start=0,$limit=0,$sort_key='',$do_count=false,$dashboard_id=''){
		$find_q = '';
		$history = '';
		$desc = false;
		if(strlen($sort_key)>0){
			$firstchar = substr($sort_key,0,1);
			if($firstchar=='-'){
				$sort_key = substr($sort_key,1);
				$desc = true;
			}
		}
		if(!empty($dashboard_id)){
			$history = '_history';
			$find_q .= " AND dashboard_id='".$this->DRW->real_escape_string($dashboard_id)."'";
		}
		if(isset($search['date']) && !empty($search['date'])){
			$history = '_history';
		}
		if(empty($dashboard_id) && (empty($search['edc']) || (count($search['edc'])==1 && empty($search['edc'][0])))){
			$search['edc'] = $this->get_edc_permissions($_SESSION['sess_username']);
		}
		foreach($this->maps as $match=>$map){
			if(isset($search[$match])){
				$find_q .= $this->do_where($search[$match],$match,$history);
			}
		}
		$sql = "SELECT ";
		if($do_count){
			$sql .= "COUNT(*) as total";
		}
		else{
			$sql .= "dashboard_id";
			if($history=='_history'){
				$sql .= ",dashboard_date";
			}
		}
		$sql .= " FROM ".$this->map_table.$history;
		if(isset($this->maps[$sort_key]) && $this->maps[$sort_key]['type']=='list'){
			$sql .= " LEFT JOIN ".$this->maps[$sort_key]['list_data']['table']." ON (".$this->map_table.$history.".".$this->maps[$sort_key]['field']."=".$this->maps[$sort_key]['list_data']['table'].".".$this->maps[$sort_key]['list_data']['id_field'].")";
		}
		if($find_q!=''){
			$sql .= " WHERE ".substr($find_q,5);
		}
		if(!$do_count){
			if(isset($this->maps[$sort_key])){
				if($this->maps[$sort_key]['type']=='list'){
					$orderby = $this->maps[$sort_key]['list_data']['table'].".".$this->maps[$sort_key]['list_data']['name_field'];
				}
				else{
					$orderby = $this->maps[$sort_key]['field'];
				}
				$sql .= " ORDER BY ".$orderby;
				if($desc){
					$sql .= " DESC";
				}
			}
			if($limit>0){
				$sql .= " LIMIT $start,$limit";
			}
		}
		return $sql;
	}
	
	function search_field($map_key='',$saved=''){
		$out = false;
		if(isset($this->maps[$map_key])){
			if(!empty($_REQUEST[$map_key])){
				$saved = $_REQUEST[$map_key];
			}
			$out = '';
			switch($this->maps[$map_key]['search_type']){
				case 'multiselect':
					$values = array(''=>'Any');
					if($map_key!='state'){
						foreach($this->get_search_array($map_key) as $k=>$v){
							$values[$k] = $v;
						}
					}
					if($map_key=='edc'){
						$pvalues = $this->get_edc_permissions($_SESSION['sess_username']);
						foreach($values as $k=>$v){
							if(!empty($k) && !in_array($k,$pvalues)){
								unset($values[$k]);
							}
						}
					}
					elseif($map_key=='electricitynaturalgas'){
						$pvalues = $this->get_edc_energy_types($_SESSION['sess_username']);
						foreach($values as $k=>$v){
							if(!empty($k) && !in_array($k,$pvalues)){
								unset($values[$k]);
							}
						}
					}
					$out .= '<select name="'.$map_key.'[]" id="'.$map_key.'" multiple="multiple" size="3" class="combo_box">';
					foreach($values as $k=>$v){
						$out .= '<option value="'.$k.'"';
						if((is_array($saved) && in_array($k,$saved)) || $k==$saved) {
							$out .= ' selected="selected"';
						}
						$out .= '>'.htmlspecialchars($v).'</option>';
					}
					if($map_key=='state'){
						if(is_array($saved)) {
							$tmpsaved = implode(',',$saved);
						}
						else{
							$tmpsaved = $saved;
						}
						$countries = array('US');
						if(!in_array('canada',$_SESSION['sess_search_exclude'])){
							$countries[] = 'CA';
						}
						$out .= $this->getstates($tmpsaved,false,$countries);
					}
					$out .= '</select>';
					break;
				case 'multicheck':
					$values = $this->get_search_array($map_key);
					foreach($values as $k=>$v){
						if($out!=''){
							$out .= ' &nbsp; ';
						}
						$out .= '<label>'.htmlspecialchars($v).'<input type="checkbox" id="'.$map_key.'_'.$k.'" name="'.$map_key.'[]" value="'.$k.'"';
						if((is_array($saved) && in_array($k,$saved)) || $k==$saved) {
							$out .= ' checked="checked"';
						}
						$out .= '/></label>';
					}
					break;
				case 'slider':
					$savedmin = $this->maps[$map_key]['search_data']['min'];
					$savedmax = $this->maps[$map_key]['search_data']['max'];
					if(!empty($saved)){
						$tmp = explode('-',$saved);
						if(!empty($tmp[0])){
							$savedmin = (float)trim($tmp[0]);
						}
						if(!empty($tmp[1])){
							$savedmax = (float)trim($tmp[1]);
						}
					}
					if($this->maps[$map_key]['search_data']['step']<1){
						$savedmin = number_format($savedmin, 2, '.', '');
						$savedmax = number_format($savedmax, 2, '.', '');
					}
					$out .= '<div><div style="float:left;margin-right:15px;width:20px;text-align:right;" id="'.$map_key.'-slider-range-min">'.$savedmin.'</div><div style="float:left;width:225px;" id="'.$map_key.'-slider-range"></div><div style="float:left;margin-left:15px;" id="'.$map_key.'-slider-range-max">'.$savedmax.'</div><div style="clear:left;height:1px;"></div></div><input type="hidden" name="'.$map_key.'" id="'.$map_key.'" value="'.htmlspecialchars($saved, ENT_QUOTES).'" /><script type="text/javascript">
					$(function() {
						$( "#'.$map_key.'-slider-range" ).slider({
							range: true,
							step: '.$this->maps[$map_key]['search_data']['step'].',
							min: '.$this->maps[$map_key]['search_data']['min'].',
							max: '.$this->maps[$map_key]['search_data']['max'].',
							values: [ '.$savedmin.', '.$savedmax.' ],
							change: function( event, ui ) {
								var step_val = $( "#'.$map_key.'-slider-range" ).slider( "option", "step" );
								var val1 = ui.values[ 0 ];
								var val2 = ui.values[ 1 ];
								if(step_val<1 && typeof ui.values[ 0 ] === "number"){
									val1 = val1.toFixed(2);
									val2 = val2.toFixed(2);
								}
								$( "#'.$map_key.'-slider-range-min" ).html( val1 );
								$( "#'.$map_key.'-slider-range-max" ).html( val2 );
								$( "#'.$map_key.'" ).val( ui.values[ 0 ] + "-" + ui.values[ 1 ] );
							},
							slide: function( event, ui ) {
								var step_val = $( "#'.$map_key.'-slider-range" ).slider( "option", "step" );
								var val1 = ui.values[ 0 ];
								var val2 = ui.values[ 1 ];
								if(step_val<1 && typeof ui.values[ 0 ] === "number"){
									val1 = val1.toFixed(2);
									val2 = val2.toFixed(2);
								}
								$( "#'.$map_key.'-slider-range-min" ).html( val1 );
								$( "#'.$map_key.'-slider-range-max" ).html( val2 );
							}
						});
					});
					</script>';
					break;
				case 'lookup':
					$prepop = '';
					if(!empty($saved)){
						$ids = explode(',',$saved);
						foreach($ids as $id){
							if($prepop!=''){
								$prepop .= ',';
							}
							$prepop .= '{id:'.$id.',name:"'.$this->format_field($id,$map_key,'key').'"}';
						}
					}
					if($prepop!=''){
						$prepop = 'prePopulate: ['.$prepop.'],';
					}
					$out .= '<input type="text" class="input_box" size="45" name="'.$map_key.'" id="'.$map_key.'" value="'.htmlspecialchars($saved, ENT_QUOTES).'" />';
					$out .= '<script type="text/javascript">
						$(document).ready(function () {
							$("#'.$map_key.'").tokenInput("dashboard_info.php?json=1&field='.$map_key.'", {
								'.$prepop.'
								queryParam: "look",
								minChars: 2,
								hintText: ""
							});
						});
						</script>';
					break;
				case 'singleline':
					$out .= '<input type="text" class="input_box" size="45" name="'.$map_key.'" id="'.$map_key.'" value="'.htmlspecialchars($saved, ENT_QUOTES).'" />';
					break;
			}
		}
		return $out;
	}
	function get_edc_permissions($user){
		$outArray = array();
		$query_c ="SELECT edc_id FROM cscan_edc_user WHERE userID='".$this->DRW->real_escape_string($_SESSION['sess_userID'])."'";
		$result_c = $this->DRW->query($query_c,$this->DRW_read);
		while($row_c = $this->DRW->fetch_row($result_c)){
			$outArray[] = $row_c[0];
		}
		return $outArray;
	}
	function get_edc_energy_types($user){
		$outArray = array();
		$pvalues = $this->get_edc_permissions($user);
		if(count($pvalues)>0){
			$query_c ="SELECT DISTINCT energy_type FROM dashboard_retail_energy_pricing WHERE edc_id IN (".implode(',',$pvalues).")";
			$result_c = $this->DRW->query($query_c,$this->DRW_read);
			while($row_c = $this->DRW->fetch_row($result_c)){
				$outArray[] = $row_c[0];
			}
		}
		return $outArray;
	}
	function get_edc_states($user){
		$outArray = array();
		$pvalues = $this->get_edc_permissions($user);
		if(count($pvalues)>0){
			$query_c ="SELECT DISTINCT stateID FROM dashboard_retail_energy_pricing WHERE edc_id IN (".implode(',',$pvalues).")";
			$result_c = $this->DRW->query($query_c,$this->DRW_read);
			while($row_c = $this->DRW->fetch_row($result_c)){
				$outArray[] = $row_c[0];
			}
		}
		return $outArray;
	}
	function getstates($stateID = 0,$usecode=false,$countries=array()){
		$out = '';
		$out .= $this->getstatesCountry($stateID,$usecode);
		
		$sqlc = "SELECT DISTINCT countryCode,country FROM cscan_state JOIN ISO31661_alpha2code ON (code=countryCode) WHERE countryCode<>'US' ORDER BY country";
		$rsc = $this->DRW->query( $sqlc, $this->DRW_read );
		while($rowc = $this->DRW->fetch_row($rsc) ) {
			if(count($countries)==0 || in_array($rowc[0],$countries)){
				$tmp_out = $this->getstatesCountry($stateID,$usecode,$rowc[0]);
				if($tmp_out!=''){
					$out .= '<optgroup>'.$tmp_out.'</optgroup>';
				}
			}
		}
		return $out;
	}
	function getstatesCountry($stateID = 0,$usecode=false,$country='US'){
		$out = '';
		$sql = "SELECT DISTINCT cscan_state.stateID,stateName,stateCode FROM cscan_state 
			JOIN dashboard_retail_energy_pricing ON (cscan_state.stateID=dashboard_retail_energy_pricing.stateID)
			WHERE countryCode='".$country."'";
		$svalues = $this->get_edc_states($_SESSION['sess_username']);
		if(count($svalues)>0){
			$sql .= "AND cscan_state.stateID IN (".implode(',',$svalues).")";
		}
		$sql .= " ORDER BY stateName";
		$result = $this->DRW->query( $sql, $this->DRW_read );
		if(!empty($stateID)) {
			$stateID = explode(',',$stateID);
		}
		else {
			$stateID = array();
		}
		while( $row = $this->DRW->fetch_array( $result ) ){
			if($usecode) {
				$code = $row['stateCode'];
			}
			else {
				$code = $row['stateID'];
			}
			$out .= '<option value="'.$code.'"';
			if(in_array($code, $stateID)) {
				$out .= ' selected="selected"';
			}
			$out .= ">".htmlspecialchars($row['stateName'])."</option>";
		}
		return $out;
	}
	function format_field($value='',$key='',$type='key'){
		$value = (string) $value;
		$map_key = '';
		if($type=='field'){
			foreach($this->maps as $match=>$map){
				if($map['field']==$key){
					$map_key = $match;
					break;
				}
			}
		}
		elseif(isset($this->maps[$key])){ // key
			$map_key = $key;
		}
		if(isset($this->maps[$map_key])){
			if(isset($this->maps[$map_key]['format'])){
				$format = $this->maps[$map_key]['format'];
			}
			else{
				$format = '';
			}
			switch($this->maps[$map_key]['type']){
				case 'float':
				case 'int':
					if(strlen($value)!=0){
						$value = number_format($value,$format);
					}
					else{
						$value = 'N/A';
					}
					break;
				case 'date':
					if(strlen($value)!=0 && $value!='0000-00-00'){
						$value = strtotime($value);
						$value = date($format,$value);
					}
					else{
						$value = 'N/A';
					}
					break;
				case 'list':
					if(!empty($value)){
						$value = $this->get_list($value,$map_key,'name');
					}
					else{
						$value = 'N/A';
					}
					break;
			}
		}
		
		return $value;
	}
	
	function get_label($key='',$type='key'){
		if($type=='field'){
			foreach($this->maps as $match=>$map){
				if($map['field']==$key){
					return $this->maps[$match]['label'];
				}
			}
		}
		elseif(isset($this->maps[$key])){ // key
			return $this->maps[$key]['label'];
		}
		return '';
	}
	
	function add_dashboard_data($data=array(),$fid=0){
		$replace_q1 = '';
		$replace_q2 = '';
		$replace_qh1 = '';
		$replace_qh2 = '';
		$find_q = '';
		foreach($this->maps as $match=>$map){
			if(!isset($data[$match]) && $this->maps[$match]['pk']){
				$data[$match] = '';
			}
			$dbvalue = $this->database_value($data[$match],$match);
			$replace_q1 .= ','.$map['field'];
			$replace_q2 .= ','.$dbvalue;
			if($map['pk']){
				if($dbvalue==='NULL'){
					$find_q .= ' AND '.$map['field'].' IS '.$dbvalue;
				}
				else{
					$find_q .= ' AND '.$map['field'].'='.$dbvalue;
				}
			}
			else{
				$replace_qh1 .= ','.$map['field'];
				$replace_qh2 .= ','.$dbvalue;
			}
		}
		$dashboard_id = '';
		$last_dashboard_date = '';
		$new_dashboard_date = date('Y-m-d',strtotime($data['date']));
		if($find_q!=''){
			$sqlL = "SELECT dashboard_id,dashboard_date FROM ".$this->map_table." WHERE ".substr($find_q,5);
			$rsL = $this->DRW->query($sqlL,$this->DRW_read);
			$rowL = $this->DRW->fetch_row($rsL);
			if(!empty($rowL[0])){
				$dashboard_id = $rowL[0];
				$last_dashboard_date = $rowL[1];
			}
		}
		if(empty($dashboard_id)){
			$dashboard_id = $this->guid();
		}
		if(empty($last_dashboard_date) || $last_dashboard_date<$new_dashboard_date){
			$sql = "REPLACE INTO ".$this->map_table." (dashboard_id$replace_q1) VALUES ('$dashboard_id'$replace_q2)";
			$this->DRW->query($sql,$this->DRW_main);
		}
		$sql = "REPLACE INTO ".$this->map_table."_history (import_file_id,dashboard_id$replace_qh1) VALUES ($fid,'$dashboard_id'$replace_qh2)";
		$this->DRW->query($sql,$this->DRW_main);
	}
        function remove_dashboard_data($dashboard_id='',$fid=0){
            $selects = array();
            $emailData = [];
            foreach($this->maps as $match=>$map){
                if(!$map['pk']){
                    $selects[] = $map['field'];
                }
            }
            $sqlL2 = "SELECT COUNT(DISTINCT import_file_id) FROM ".$this->map_table."_history WHERE dashboard_id='".$this->DRW->real_escape_string($dashboard_id)."'";
            $rsL2 = $this->DRW->query($sqlL2,$this->DRW_read);
            $rowL2 = $this->DRW->fetch_row($rsL2);
            $count = $rowL2[0];
            if($count==1 || empty($fid)){                
                $sql = "DELETE FROM ".$this->map_table." WHERE dashboard_id='".$this->DRW->real_escape_string($dashboard_id)."'";
                if($this->DRW->query($sql,$this->DRW_main)){
                    $data = [
                        'auth_id' => $GLOBALS['AUTH_DATA']['userID'],
                        'auth_name' => authUserName($GLOBALS['AUTH_DATA']['userID']),
                        'deleted_id' => $dashboard_id,
                        'sql_query' => $sql,
                        'ip_address' => ipAddress(),
                        'user_agent' => $_SERVER['HTTP_USER_AGENT'],
                        'delete_type' => 'Dashboard',
                        'is_mobile' => isMobile(),
                        'insert_date' => date("Y-m-d H:i:s")
                    ];
                    trackDelete($data);
                    $emailData[] = $data;
                }                    
            }else{
                $sqlL2 = "SELECT ".implode(',',$selects)." FROM ".$this->map_table."_history WHERE dashboard_id='".$this->DRW->real_escape_string($dashboard_id)."' AND import_file_id<>".$fid." ORDER BY dashboard_date DESC LIMIT 1";
                $rsL2 = $this->DRW->query($sqlL2,$this->DRW_read);
                $rowL2 = $this->DRW->fetch_assoc($rsL2);
                $update = '';
                foreach($selects as $select){
                        if($update!=''){
                                $update .= ',';
                        }
                        $update .= $select."='".$this->DRW->real_escape_string($rowL2[$select])."'";
                }
                $sql = "UPDATE ".$this->map_table." SET ".$update." WHERE dashboard_id='".$this->DRW->real_escape_string($dashboard_id)."'";
                $this->DRW->query($sql,$this->DRW_main);
            }
            $sql = "DELETE FROM ".$this->map_table."_history WHERE dashboard_id='".$this->DRW->real_escape_string($dashboard_id)."'";
            if(!empty($fid)){
                $sql .= " AND import_file_id=".$fid;
            }
            if($this->DRW->query($sql,$this->DRW_main)){
                $data = [
                    'auth_id' => $GLOBALS['AUTH_DATA']['userID'],
                    'auth_name' => authUserName($GLOBALS['AUTH_DATA']['userID']),
                    'deleted_id' => $dashboard_id,
                    'sql_query' => $sql,
                    'ip_address' => ipAddress(),
                    'user_agent' => $_SERVER['HTTP_USER_AGENT'],
                    'delete_type' => 'Dashboard',
                    'is_mobile' => isMobile(),
                    'insert_date' => date("Y-m-d H:i:s")
                ];
                trackDelete($data);
                $emailData[] = $data;
            }
            
            return $emailData;
	}
	
	function remove_dashboard_import($fid=0){
            $emailData = [];
            $sqlL = "SELECT DISTINCT dashboard_id FROM ".$this->map_table."_history WHERE import_file_id=".$fid;
            $rsL = $this->DRW->query($sqlL,$this->DRW_read);
            while($rowL = $this->DRW->fetch_row($rsL)){
                $data = $this->remove_dashboard_data($rowL[0],$fid);
                $emailData = array_merge($emailData,$data);
            }
            $html = '';
            if(count($emailData)>0){
                $html = '<table width="100%" border="1">';
                $html .= '<tr><td>Auth ID</td><td>User Name</td><td>Deleted ID</td><td>SQL Query</td><td>IP Address</td><td>User Agent</td><td>Module</td><td>Is Mobile</td><td>Deleted Date (CST)</td></tr>';

                foreach($emailData as $tr){
                    if(is_array($tr) && count($tr)>0){
                       $html .= '<tr>';
                       foreach($tr as $td){
                           $html .= '<td>'.$td.'</td>'; 
                       }
                       $html .= '</tr>';
                    }
                }     
                $emailData = [];
                $html .= '</table>';                
            }
            if(!empty($html)){
                //echo '<pre>';print_r($html);//die;
                //sendDevAlert('Caution! Data Deleted From Dashboard',$html);
            }
	}
}
?>