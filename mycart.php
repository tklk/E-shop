<?php
require_once("session.php");
class myCart {
	var $total = 0;
	var $itemcount = 0;
	var $deliverfee = 0;
	var $grandtotal = 0;
	var $items = array();
	var $itemprices = array();
	var $itemqtys = array();
	var $iteminfo = array();


	function cart() {} // constructor function

	function get_contents()
	{
		$items = array();
		foreach($this->items as $tmp_item)
		{
			$item = FALSE;

			$item['id'] = $tmp_item;
			$item['qty'] = $this->itemqtys[$tmp_item];
			$item['price'] = $this->itemprices[$tmp_item];
			$item['info'] = $this->iteminfo[$tmp_item];
			$item['subtotal'] = $item['qty'] * $item['price'];
			$items[] = $item;
		}
		return $items;
	}


	function add_item($itemid,$qty=1,$price = FALSE, $info = FALSE)
	{
		if(isset($this->itemqtys[$itemid])&&($this->itemqtys[$itemid]>0))
		{ // item already exist in cart
			$this->itemqtys[$itemid] = $qty + $this->itemqtys[$itemid];
			$this->_update_total();
		} else {
			$this->items[]=$itemid;
			$this->itemqtys[$itemid] = $qty;
			$this->itemprices[$itemid] = $price;
			$this->iteminfo[$itemid] = $info;
		}
		$this->_update_total();
	}

	// changes items quantity
	function edit_item($itemid,$qty)
	{
		if($qty < 1) {
			$this->del_item($itemid);
		} else {
			$this->itemqtys[$itemid] = $qty;
		}
		$this->_update_total();
	}

	// removes specific item from cart
	function del_item($itemid)
	{ 
		$temp = array();
		$this->itemqtys[$itemid] = 0;
		foreach($this->items as $item)
		{
			if($item != $itemid)
			{
				$temp[] = $item;
			}
		}
		$this->items = $temp;
		$this->_update_total();
	}


	function empty_cart()
	{
		$this->total = 0;
		$this->itemcount = 0;
		$this->deliverfee = 0;
		$this->grandtotal = 0;
		$this->items = array();
		$this->itemprices = array();
		$this->itemqtys = array();
		$this->iteminfo = array();
	}


	function _update_total()
	{
		$this->itemcount = 0;
		$this->total = 0;
		if(sizeof($this->items > 0))
		{
			foreach($this->items as $item) {
			$this->total = $this->total + ($this->itemprices[$item] * $this->itemqtys[$item]);
			$this->itemcount++;
			}
		}

		// deleiver fee
		if($this->total >= 50000){
			$this->deliverfee = 0;			
		}else{
			$this->deliverfee = 500;						
		}
		$this->grandtotal = $this->total+$this->deliverfee;		
	}
}
?>
