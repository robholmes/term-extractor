<?php

namespace TermExtractor\Filters;

use TermExtractor\Filters\FilterInterface;

class PermissiveFilter implements FilterInterface {
    
	public function accept($word, $occur, $strength) {
		return true;
	}
}
