<?php
class Paginator
{
	// properties (visibility modernized from old `var`)
	public $previous;
	public $current;
	public $next;
	public $page;
	public $total_pages;
	public $link_arr;
	public $range1;
	public $range2;
	public $num_rows;
	public $first;
	public $last;
	public $first_of;
	public $second_of;
	public $limit;
	public $prev_next;
	public $base_page_num;
	public $extra_page_num;
	public $total_items;
	public $pagename;

	#Constructor for Paginator.
	#Takes the current page and the number of items in the source data and sets the current page ($this->page) and the total items in the source ($this->total_items).
	function __construct($page, $num_rows)
	{
		// validate/sanitize $page: must be a positive integer, default to 1
		$page = (int)$page;
		if ($page < 1) {
			$this->page = 1;
		} else {
			$this->page = $page;
		}

		// validate $num_rows: must be a non-negative integer
		$num_rows = (int)$num_rows;
		if ($num_rows < 0) {
			$num_rows = 0;
		}

		$this->num_rows = $num_rows;
		$this->total_items = $this->num_rows;

		// sensible defaults so calling getRange1()/getRange2()/getLinkArr()
		// before set_Limit()/set_Links() doesn't emit warnings or misbehave
		$this->limit = 5;
		$this->prev_next = 5;
		$this->setBasePage();
		$this->setExtraPage();
	}

	#Takes  $limit and sets $this->limit.
	#Calls private methods
	#setBasePage() and setExtraPage() which use $this->limit.
	function set_Limit($limit = 5)
	{
		$limit = (int)$limit;
		// guard against divide-by-zero / nonsensical limits
		if ($limit < 1) {
			$limit = 5;
		}
		$this->limit = $limit;
		$this->setBasePage();
		$this->setExtraPage();
	}

	#This method creates a number that setExtraPage() uses to if there are
	#and extra pages after limit has divided the total number of pages.
	function setBasePage()
	{
		if (!$this->limit) {
			$this->limit = 5;
		}
		$div = $this->num_rows / $this->limit;
		$this->base_page_num = floor($div);
	}

	function setExtraPage()
	{
		$this->extra_page_num = $this->num_rows - ($this->base_page_num * $this->limit);
	}

	#Used in making numbered links.  Sets the number of links behind and
	#ahead of the current page.  For example if there were a possiblity of
	#20 numbered links and this was set to 5 and the current link was 10
	#the result would be this 5 6 7 8 9 10 11 12 13 14 15.
	function set_Links($prev_next = 5)
	{
		$prev_next = (int)$prev_next;
		if ($prev_next < 0) {
			$prev_next = 5;
		}
		$this->prev_next = $prev_next;
	}

	#method to get the total items.
	function getTotalItems()
	{
		$this->total_items = $this->num_rows;
		return $this->total_items;
	}

	#method to get the base number to use in queries and such.
	function getRange1()
	{
		$this->range1 = ($this->limit * $this->page) - $this->limit;
		return $this->range1;
	}

	#method to get the offset.
	function getRange2()
	{
		if ($this->page == $this->base_page_num + 1) {
			$this->range2 = $this->extra_page_num;
		} else {
			$this->range2 = $this->limit;
		}
		return $this->range2;
	}

	#method to get the first of number as in 5 of .
	function getFirstOf()
	{
		$this->first_of = $this->range1 + 1;
		return $this->first_of;
	}

	#method to get the second number in a series as in 5 of 8.
	function getSecondOf()
	{
		if ($this->page == $this->base_page_num + 1) {
			$this->second_of = $this->range1 + $this->extra_page_num;
		} else {
			$this->second_of = $this->range1 + $this->limit;
		}
		return $this->second_of;
	}

	#method to get the total number of pages.
	function getTotalPages()
	{
		if ($this->extra_page_num) {
			$this->total_pages = $this->base_page_num + 1;
		} else {
			$this->total_pages = $this->base_page_num;
		}
		return $this->total_pages;
	}

	#method to get the first link number.
	function getFirst()
	{
		$this->first = 1;
		return $this->first;
	}

	#method to get the last link number.
	function getLast()
	{
		if ($this->page == $this->total_pages) {
			$this->last = 0;
		} else {
			$this->last = $this->total_pages;
		}
		return $this->last;
	}

	function getPrevious()
	{
		if ($this->page > 1) {
			$this->previous = $this->page - 1;
		} else {
			$this->previous = 0;
		}
		return $this->previous;
	}

	#method to get the number of the link previous to the current link.
	function getCurrent()
	{
		$this->current = $this->page;
		return $this->current;
	}

	#method to get the current page name. Is mostly used in links to the next page.
	#Escaped with htmlspecialchars() since PHP_SELF is a known XSS vector when
	#reflected back into HTML attributes unescaped.
	function getPageName()
	{
		$this->pagename = htmlspecialchars($_SERVER['PHP_SELF'] ?? '', ENT_QUOTES, 'UTF-8');
		return $this->pagename;
	}

	#method to get the number of the link after the current link.
	function getNext()
	{
		$this->getTotalPages();
		if ($this->total_pages != $this->page) {
			$this->next = $this->page + 1;
		} else {
			$this->next = 0;
		}
		return $this->next;
	}

	#method that returns an array of the numbered links that should be displayed.
	function getLinkArr()
	{
		#gets the top range
		$top = $this->getTotalPages() - $this->getCurrent();
		if ($top <= $this->prev_next) {
			$top_range = $this->getCurrent() + $top;
		} else {
			$top = $this->prev_next;
			$top_range = $this->getCurrent() + $top;
		}

		//gets the bottom range
		$bottom = $this->getCurrent() - 1;
		if ($bottom <= $this->prev_next) {
			$bottom_range = $this->getCurrent() - $bottom;
		} else {
			$bottom = $this->prev_next;
			$bottom_range = $this->getCurrent() - $bottom;
		}

		// guard: clamp to at least page 1, and make sure bottom_range never
		// exceeds top_range (which used to make range() count downward and
		// return a reversed/garbage link array on edge cases, e.g. very few
		// total pages or an out-of-bounds current page)
		if ($bottom_range < 1) {
			$bottom_range = 1;
		}
		if ($top_range < $bottom_range) {
			$top_range = $bottom_range;
		}

		$this->link_arr = array();
		$j = 0;
		foreach (range($bottom_range, $top_range) as $i) {
			$this->link_arr[$j] = $i;
			$j++;
		}
		return $this->link_arr;
	}
} //ends Paginator class