<?php
require_once __DIR__ . '/paginator.php';

class Paginator_html extends Paginator
{
	#helper to safely build the ssid/sort/bid query-string suffix used by
	#several link-building methods below. Values are cast to int, so they're
	#already safe to interpolate, but this keeps the logic in one place.
	private function getRequestSuffix()
	{
		$ssid = isset($_REQUEST['ssid']) ? '&amp;ssid=' . (int)$_REQUEST['ssid'] : '';
		$sort = isset($_REQUEST['sort']) ? '&amp;sort=' . (int)$_REQUEST['sort'] : '';
		$bid  = isset($_REQUEST['bid'])  ? '&amp;bid='  . (int)$_REQUEST['bid']  : '';
		return $ssid . $sort . $bid;
	}

	#outputs a link set like this 1 of 4 of 25 First | Prev | Next | Last |
	function firstLast()
	{
		if ($this->getCurrent() == 1) {
			$first = "First | ";
		} else {
			$first = "<a href=\"" . $this->getPageName() . "?page=" . $this->getFirst() . "\">First</a> |";
		}

		if ($this->getPrevious()) {
			$prev = "<a href=\"" . $this->getPageName() . "?page=" . $this->getPrevious() . "\">Prev</a> | ";
		} else {
			$prev = "Prev | ";
		}

		if ($this->getNext()) {
			$next = "<a href=\"" . $this->getPageName() . "?page=" . $this->getNext() . "\">Next</a> | ";
		} else {
			$next = "Next | ";
		}

		if ($this->getLast()) {
			$last = "<a href=\"" . $this->getPageName() . "?page=" . $this->getLast() . "\">Last</a> | ";
		} else {
			$last = "Last | ";
		}

		echo $this->getFirstOf() . " of " . $this->getSecondOf() . " of " . $this->getTotalItems() . " ";
		echo $first . " " . $prev . " " . $next . " " . $last;
	}

	//outputs a link set like this Previous 1 2 3 4 5 6 Next
	function previousNext()
	{
		$suffix = $this->getRequestSuffix();

		if ($this->getPrevious()) {
			echo "<a href=\"" . $this->getPageName() . "?page=" . $this->getPrevious() . "$suffix\" class=\"HyperLink\">Previous</a> ";
		}
		$links = $this->getLinkArr();
		foreach ($links as $link) {
			if ($link == $this->getCurrent()) {
				echo "<span style=\"color:#B5364B;\"> $link </span>";
			} else {
				echo "<a href=\"" . $this->getPageName() . "?page=$link$suffix\" class=\"HyperLink\">" . $link . "</a> ";
			}
		}
		if ($this->getNext()) {
			echo "<a href=\"" . $this->getPageName() . "?page=" . $this->getNext() . "$suffix\" class=\"HyperLink\">Next</a> ";
		}
	}

	function searchPreviousNext()
	{
		if ($this->getPrevious()) {
			echo "<a onClick=\"setAction(" . $this->getPrevious() . ")\" style='cursor:pointer;' class=\"HyperLink\">Previous</a> ";
		}
		$links = $this->getLinkArr();
		foreach ($links as $link) {
			if ($link == $this->getCurrent()) {
				echo "<span style=\"color:#B5364B;\"> $link </span>";
			} else {
				echo "<a onClick=\"setAction(" . $link . ")\" style='cursor:pointer;' class=\"HyperLink\">" . $link . "</a> ";
			}
		}
		if ($this->getNext()) {
			echo "<a onClick=\"setAction(" . $this->getNext() . ")\" style='cursor:pointer;' class=\"HyperLink\">Next</a> ";
		}
	}

	function firstPreviousNextLast()
	{
		$suffix = $this->getRequestSuffix();

		if ($this->getCurrent() == 1) {
			echo '';
		} else {
			echo "<a href=\"" . $this->getPageName() . "?page=" . $this->getFirst() . "$suffix\" class=\"HyperLink\">First</a> | ";
		}

		if ($this->getPrevious()) {
			echo "<a href=\"" . $this->getPageName() . "?page=" . $this->getPrevious() . "$suffix\" class=\"HyperLink\">Previous</a> ";
		}

		$links = $this->getLinkArr();
		foreach ($links as $link) {
			if ($link == $this->getCurrent()) {
				echo "<span style=\"color:#B5364B;\"> $link </span>";
			} else {
				echo "<a href=\"" . $this->getPageName() . "?page=$link$suffix\" class=\"HyperLink\">" . $link . "</a> ";
			}
		}

		if ($this->getNext()) {
			echo "<a href=\"" . $this->getPageName() . "?page=" . $this->getNext() . "$suffix\" class=\"HyperLink\">Next</a> | ";
		}

		if ($this->getLast()) {
			echo "<a href=\"" . $this->getPageName() . "?page=" . $this->getLast() . "$suffix\" class=\"HyperLink\">Last</a> ";
		}
	}
} //ends class