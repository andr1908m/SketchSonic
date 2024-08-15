<?php

namespace SketchSonic;

use Exception;

class Title {
  function __construct(private $title) {
    verify(!is_null($title), fn() => new Exception("title cannot be null"));
    verify(strlen($title) != 0, fn() => new CannotBeEmpty("title", $title));
    verify(strlen($title) < 256, fn() => new TooBig("title", 256));
  }

  function value() {
    return $this->title;
  }
}

class CannotBeEmpty extends Exception {
  function __construct($name ,$value) {
    parent::__construct("$name is required, was: $value");
  }
}

class TooBig extends \Exception {
  function __construct($name ,$max) {
    parent::__construct("$name is too long, should be max $max chars");
  }
}