<?php 
class Paginator_html extends Paginator 
{ 
	#outputs a link set like this 1 of 4 of 25 First | Prev | Next | Last |              
	function firstLast()
	{				
		if($this->getCurrent()==1)
		{
			$first = "First | ";
		} 
		else 
		{ 
			$first="<a href=\"" .  $this->getPageName() . "?page=" . $this->getFirst() . "\">First</a> |"; 
		}  
		if($this->getPrevious())
		{
			$prev = "<a href=\"" .  $this->getPageName() . "?page=" . $this->getPrevious() . "\">Prev</a> | ";
		} 
		else 
		{ 
			$prev="Prev | "; 
		}

		if($this->getNext())
		{
			$next = "<a href=\"" . $this->getPageName() . "?page=" . $this->getNext() . "\">Next</a> | ";
		} 
		else 
		{ 
			$next="Next | "; 
		}

		if($this->getLast())
		{
			$last = "<a href=\"" . $this->getPageName() . "?page=" . $this->getLast() . "\">Last</a> | ";
		} 
		else 
		{ 
			$last="Last | "; 
		}
		echo $this->getFirstOf() . " of " .$this->getSecondOf() . " of " . $this->getTotalItems() . " ";
		echo $first . " " . $prev . " " . $next . " " . $last;
	}
	//outputs a link set like this Previous 1 2 3 4 5 6 Next   
	function previousNext()
	{
		if(isset($_REQUEST['ssid'])) $ssid = "&amp;ssid=".(int)$_REQUEST['ssid'];
		else $ssid = '';
		if(isset($_REQUEST['sort'])) $sort = "&amp;sort=".(int)$_REQUEST['sort'];
		else $sort = '';
		if(isset($_REQUEST['bid'])) $bid = "&amp;bid=".(int)$_REQUEST['bid'];
		else $bid = '';
		if($this->getPrevious())
		{
			echo "<a href=\"" . $this->getPageName() . "?page=" . $this->getPrevious() . "$ssid$sort$bid\" class=\"HyperLink\">Previous</a> ";
		}
		$links = $this->getLinkArr();
		foreach($links as $link)
		{
			if($link == $this->getCurrent())
			{
				echo "<span style=\"color:#B5364B;\"> $link </span>";
			} 
			else 
			{	
				echo "<a href=\"" . $this->getPageName() . "?page=$link$ssid$sort$bid\" class=\"HyperLink\">" . $link . "</a> ";
			}
		} 
		if($this->getNext())
		{
			echo "<a href=\"" . $this->getPageName() . "?page=" . $this->getNext() . "$ssid$sort$bid\" class=\"HyperLink\">Next</a> ";
		}
	}
	function searchPreviousNext()
	{
		if($this->getPrevious())
		{
			echo "<a onClick = setAction(". $this->getPrevious(). ") style='cursor:pointer;' class=\"HyperLink\">Previous</a> ";
		}
		$links = $this->getLinkArr();
		foreach($links as $link)
		{
			if($link == $this->getCurrent())
			{
				echo "<span style=\"color:#B5364B;\"> $link </span>";
			} 
			else 
			{	
				echo "<a onClick = setAction(". $link ."); style='cursor:pointer;' class=\"HyperLink\">" . $link . "</a> ";
			}
		} 
		if($this->getNext())
		{
			echo "<a onClick = setAction(".$this->getNext().")  style='cursor:pointer;' class=\"HyperLink\">Next</a> ";
		}
	}
        function firstPreviousNextLast()
	{
		if(isset($_REQUEST['ssid'])) $ssid = "&amp;ssid=".(int)$_REQUEST['ssid'];
		else $ssid = '';
		if(isset($_REQUEST['sort'])) $sort = "&amp;sort=".(int)$_REQUEST['sort'];
		else $sort = '';
		if(isset($_REQUEST['bid'])) $bid = "&amp;bid=".(int)$_REQUEST['bid'];
		else $bid = '';
                if($this->getCurrent()==1)
		{
                    echo '';
		} 
		else 
		{ 
			echo "<a href=\"" .  $this->getPageName() . "?page=" . $this->getFirst() . "$ssid$sort$bid\" class=\"HyperLink\">First</a> | "; 
		} 
		if($this->getPrevious())
		{
			echo "<a href=\"" . $this->getPageName() . "?page=" . $this->getPrevious() . "$ssid$sort$bid\" class=\"HyperLink\">Previous</a> ";
		}
		$links = $this->getLinkArr();
		foreach($links as $link)
		{
			if($link == $this->getCurrent())
			{
				echo "<span style=\"color:#B5364B;\"> $link </span>";
			} 
			else 
			{	
				echo "<a href=\"" . $this->getPageName() . "?page=$link$ssid$sort$bid\" class=\"HyperLink\">" . $link . "</a> ";
			}
		} 
		if($this->getNext())
		{
			echo "<a href=\"" . $this->getPageName() . "?page=" . $this->getNext() . "$ssid$sort$bid\" class=\"HyperLink\">Next</a> | ";
		}
                
                if($this->getLast())
		{
			echo "<a href=\"" . $this->getPageName() . "?page=" . $this->getLast() . "$ssid$sort$bid\" class=\"HyperLink\">Last</a> ";
		} 
		
	}
}//ends class
?>