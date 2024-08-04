<?php

namespace InspireFuel;

trait Contractual {
  function __construct($message, $value) {
    parent::__construct($this->formatMessage($message, $value),code:0);
  }
  
  private function formatMessage($message, $value) {
    $serialized = $value === null? "" : ": ".$this->varDump($value);
    $result = $message.$serialized;
    return $result;
  }
  
  private function varDump($value) {
    ob_start();
    var_dump($value);
    $output = ob_get_clean();
    return $output;
  }
}

class PreconditionViolation extends \Exception {
  use Contractual;
}

class PostconditionViolation extends \Exception {
  use Contractual;
}

class InvariantViolation extends \Exception {
  use Contractual;
}

/**
 * @param bool $condition
 * @param callable():\Exception $callback
 */
function verify($condition, $callback) {
  if(!$condition)
    throw $callback();
}

function requires($condition, $message = "", $value = null) {
  check(PreconditionViolation::class, $condition, $message, $value);
}

function ensures($condition, $message = "", $value = null) {
  check(PostconditionViolation::class, $condition, $message, $value);
}

function invariant($condition, $message = "", $value = null) {
  check(InvariantViolation::class,$condition, $message, $value);
}

function check($class, $condition, $message, $value) {
  if(!$condition)
    throw new $class($message,$value);
}

function checkAll($class, $conditions) {
  foreach($conditions as $condition) {
    check($class,...$condition);
  }
}
