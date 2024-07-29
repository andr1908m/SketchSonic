<?php

namespace InspireFuel;

class UID {
  function __construct(private $value) {}
  
  function prefix() {
    return "idea_";
  }
  
  function value() {
    return substr($this->value, strlen("idea_"));
  }
}