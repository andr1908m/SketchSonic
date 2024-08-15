<?php

namespace SketchSonic;

class InvalidType extends \Exception {
  function __construct($value) {
    parent::__construct("must be 'note' or 'riff', was '$value'");
  }
}