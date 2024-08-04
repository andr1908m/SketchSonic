<?php

namespace InspireFuel;

enum IdeaType {
  case Note;
  case Riff;
}

class InvalidIdeaType extends \Exception {
  function __construct($value) {
    parent::__construct("must be 'note' or 'riff', was '$value'");
  }
}