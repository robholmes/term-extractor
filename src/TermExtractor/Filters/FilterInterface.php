<?php

namespace TermExtractor\Filters;

interface FilterInterface {
    
    public function accept($word, $occur, $strength);

}