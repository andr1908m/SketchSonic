<?php

namespace InspireFuel;

class TagName {
  function __construct(private $value) {
    invariant(is_string($value), "all tags are strings", $value);
    invariant($value !== "", "no empty tag", $value);
    invariant($this->isWord(),"tag must contain single words",$value);
  }

  function value() {
    return $this->value;
  }

  private function isWord() {
    return $this->value !== null && !preg_match("/\s/", $this->value);
  }
}