<?php
/**
* XHTML link class
* Creates or prints an XHTML valid link
* @author Nick Korbel <lqqkout13@users.sourceforge.net>
* @version 01-20-05
* @package Link
*
* Copyright (C) 2003 - 2005 phpScheduleIt
* License: GPL, see LICENSE
*/

class Link {
	var $url;
	var $text;
	var $_class;
	var $style;
	var $text_on_over;
	var $img_src;
	var $img_alt;

	/**
	* Link Constructor
	* Creates a new XHTML valid link
	* @param string $url url to link to
	* @param string $text text of link
	* @param string $class link class
	* @param string $style inline style of link (overrides class)
	* @param string $text_on_over text to display in status bar onmouseover
	* @param string $on_over javascript to call onmouseover
	*/
	public function __construct($url=null, $text=null, $class=null, $style=null, $text_on_over=null) {
		$this->url = $url;
		$this->text = $text;
		$this->_class = $class;
		$this->style = $style;
		$this->text_on_over = addslashes($text_on_over);
	}
	
	/**
	* Set the url of the link
	* @param string $url url to link to
	*/
	public function setUrl($url) {
		$this->url = $url;
	}
	
	/**
	* Set the text of the link
	* @param string $text text of link
	*/
	public function setText($text) {
		$this->text = $text;
	}
	
	/**
	* Set the class of the link
	* @param string $class link class
	*/
	public function setClass($class) {
		$this->_class = $class;
	}
	
	/**
	* Set the inline style of the link
	* @param string $style inline style of link (overrides class)
	*/
	public function setStyle($style) {
		$this->style = $style;
	}
	
	/**
	* Set the text onmouseover
	* @param string $text_on_over text to display in status bar onmouseover
	*/
	public function setTextOnOver($text_on_over) {
		$this->text_on_over = addslashes($text_on_over);
	}
	
	/**
	* Set the image source "src" property
	* @param string $img_src image source property
	*/
	public function setImgSrc($img_src) {
		$this->img_src = $img_src;
	}
	
	/**
	* Set the image alt property
	* @param string $img_alt image alt property
	*/
	public function setImgAlt($img_alt) {
		$this->img_alt = $img_alt;
	}

	/**
	* Return the url of the link
	* @return string $url url to link to
	*/
	public function getUrl() {
		return $this->url;
	}
	
	/**
	* Return the text of the link
	* @return string $text text of link
	*/
	public function getText() {
		return $this->text;
	}
	
	/**
	* Return the class of the link
	* @return string $class link class
	*/
	public function getClass() {
		return $this->_class;
	}
	
	/**
	* Return the inline style of the link
	* @return string $style inline style of link (overrides class)
	*/
	public function getStyle() {
		return $this->style;
	}
	
	/**
	* Return the text onmouseover
	* @return string $text_on_over text to display in status bar onmouseover
	*/
	public function getTextOnOver() {
		return stripslashes($this->text_on_over);
	}
	
	/**
	* Return the image source "src" property
	* @return string
	*/
	public function getImgSrc() {
		return $this->img_src;
	}
	
	/**
	* Return the image alt property
	* @return string
	*/
	public function getImgAlt() {
		return $this->img_alt;
	}
	
	/**
	* Print out a link without creating a new Link object
	* @param string $url url to link to
	* @param string $text text of link
	* @param string $class link class
	* @param string $style inline style of link (overrides class)
	* @param string $text_on_over text to display in status bar onmouseover
	*/
	public static function doLink($url=null, $text=null, $class=null, $style=null, $text_on_over=null) {
		echo Link::getLink($url, $text, $class, $style, $text_on_over);
	}
	
	/**
	* Print out an image link without creating a new Link obejct
	* @param string $url url to link to
	* @param string $img_src image source "src" property
	* @param string $alt image alt property
	* @param string $text_on_over text to display in status bar onmouseover
	*/
	function doImageLink($url = null, $img_src = null, $alt = null, $text_on_over = null) {
		echo $this->getImageLink($url, $img_src, $alt, $text_on_over);
	}
	
	/**
	* Prints out the link using the class values
	* @param none
	* @see doLink()
	*/
	public function printLink() {
		$this->doLink($this->url, $this->text, $this->_class, $this->style, $this->text_on_over);		
	}
	
	/**
	* Returns the HTML for the link with given parameters
	* @param string $url url to link to
	* @param string $text text of link
	* @param string $class link class
	* @param string $style inline style of link (overrides class)
	* @param string $text_on_over text to display in status bar onmouseover
	* @return string of HTML for link
	*/
	public static function getLink($url=null, $text=null, $class=null, $style=null, $text_on_over=null) {
		$text_on_over = (!is_null($text_on_over)) ? $text_on_over : $text;	// Use passed in text on mouse over, else just use link text
		return "<a href=\"$url\" class=\"$class\" style=\"$style\" onmouseover=\"javascript: window.status='" . addslashes($text_on_over) . "'; return true;\" onmouseout=\"javascript: window.status=''; return true;\">$text</a>\n";
	}
	
	/**
	* Returns the HTML for the link with given parameters
	* @param string $url url to link to
	* @param string $img_src image source "src" property
	* @param string $alt image alt property
	* @param string $text_on_over text to display in status bar onmouseover
	*/
	public function getImageLink($url = null, $img_src = null, $alt = null, $text_on_over = null) {
		$text_on_over = (!is_null($text_on_over)) ? $text_on_over : $alt;	// Use passed in text on mouse over, else just use link text
		return "<a href=\"$url\" onmouseover=\"javascript: window.status='" . addslashes($text_on_over) . "'; return true;\" onmouseout=\"javascript: window.status=''; return true;\"><img src=\"$img_src\" alt=\"$alt\" title=\"$alt\" border=\"0\"/></a>\n";
	}
}
