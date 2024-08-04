<?php

namespace InspireFuel;

use ArrayIterator;

class HashSet {
  function __construct($class, private $elements) {
    $toType = fn ($type) => new $class($type);
    $this->elements = $this->uniquify($elements, $toType); 
  }

  private function uniquify($values,$toType) {
    return array_map($toType, array_unique($values));
  }

  function all() {
    return new ArrayIterator($this->elements);
  }
}

