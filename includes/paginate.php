<?php
/**
 * @author Rashaud Teague
 * @copyright 2011
 */
$GLOBALS['DRW'] = $DRW;
$GLOBALS['DRW_read'] = $DRW_read;
$GLOBALS['DRW_main'] = $DRW_main;
$GLOBALS['DRW_crm'] = $DRW_crm;

class Paginate {
	protected $_total_pages;
	protected $_total_rows;
	protected $_data;
	protected $_page;
	protected $_search_field = '';
	
	public function __construct() {
		if (!isset($_GET['page'])) {
			$_GET['page'] = 1;
		} else {
			if ($_GET['page'] == '') $_GET['page'] = 1;
		}
		
		$this->_page = $_GET['page'];
	}
	
	public function set_search_field($search_field) {
		$this->_search_field = $search_field;
	}
	
	public function get_data() {
		return $this->_data;
	}
	
	public function paginate($sql, $limit) {
		global $DRW,$DRW_read,$DRW_main,$DRW_crm;
		$result = $DRW->query($sql,$DRW_read);
		$total_rows = $DRW->num_rows($result);
		
		unset($result);
		
		if ($limit == 0) $limit = 0;
		
		$start = 0;
		
		$pages_possible = ceil($total_rows / $limit);
		
		if ($this->_page > 1) {
			if ($this->_page > $pages_possible) $this->_page = $pages_possible;
			
			$start = ($limit * $this->_page) - $limit;
		}
		
		$sql .= " LIMIT ".$start.", ".$limit;
		//print $sql .'<br />';
		$result = $DRW->query($sql,$DRW_read);
		
		$data = array();
		
		while ($row = $DRW->fetch_array($result)) $data[] = $row;
		
		$this->_total_rows = $total_rows;
		$this->_total_pages = $pages_possible;
		$this->_data = $data;
	}
	
	public function print_page_links() {
		$first_page = 1;
		$last_page = $this->_total_pages;
		$next_page = ($this->_page < $last_page) ? ($this->_page + 1) : $last_page;
		$prev_page = ($this->_page > 1) ? ($this->_page - 1) : $first_page;
		
		if ($this->_search_field != '') {
			if ($_GET[$this->_search_field] != '') {
				$search_query = '&'.$this->_search_field.'='.$_GET[$this->_search_field];
			}
		} else {
			$search_query = '';
		}
		
		print '
		<a href = "'.$_SERVER['PHP_SELF'].'?page=1'.$search_query.'">First</a> | 
		<a href = "'.$_SERVER['PHP_SELF'].'?page='.$prev_page.$search_query.'">Previous</a> | 
		<a href = "'.$_SERVER['PHP_SELF'].'?page='.$next_page.$search_query.'">Next</a> | 
		<a href = "'.$_SERVER['PHP_SELF'].'?page='.$last_page.$search_query.'">Last</a>
		';
		
		print ' / Page: '.$this->_page.' of '. $this->_total_pages .' / '.$this->_total_rows.' total records.';
	}
}
?>