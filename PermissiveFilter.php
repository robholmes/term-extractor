<?php 
class PermissiveFilter {
	public function __construct() {	}
	public function accept($word, $occur, $strength) {
		return true;
	}
}