<?php

namespace InspireFuel;

class Tags {
  function __construct(private $tags) {
    \invariants(
        [$this->tagsAreStrings(), "all tags are strings", $tags],
        [$this->noEmptyTags(), "no empty tags", $tags],
        [$this->tagsAreSingleWords(),"tags must contain single words",$tags],
    );
  }
  
  function all() {
    return new \ArrayIterator($this->tags);
  }
  
  private function tagsAreStrings() {
    $isNotAString = fn($e) => !is_string($e);
    return $this->noneExist($isNotAString);
  }
  
  private function noEmptyTags() {
    $i = array_search("",$this->tags);
    return $i === false;
  }
  
  private function tagsAreSingleWords() {
    $containsSpace = fn($t) => $t === null || preg_match("/\s/", $t);
    return $this->noneExist($containsSpace);
  }
  
  private function noneExist($condition) {
    $a = $this->filterWithIndex($condition);
    return iterator_count($a) === 0;
  }
  
  private function filterWithIndex($condition) {
    $i = 0;
    foreach($this->tags as $tag) {
      if($condition($tag))
        yield [$tag,$i];
        $i++;
    }
  }
}
