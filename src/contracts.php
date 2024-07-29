<?php

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

class PreconditionViolation extends Exception {
  use Contractual;
}

class PostconditionViolation extends Exception {
  use Contractual;
}

class InvariantViolation extends Exception {
  use Contractual;
}

function verify($condition, $message = "", $value = null) {
  check(PreconditionViolation::class, $condition, $message, $value);
}

function ensure($condition, $message = "", $value = null) {
  check(PostconditionViolation::class, $condition, $message, $value);
}

function invariant($condition, $message = "", $value = null) {
  check(InvariantViolation::class,$condition, $message, $value);
}

function invariants(...$conditions) {
  checkAll(InvariantViolation::class,$conditions);
}

function requires(...$conditions) {
  checkAll(PreconditionViolation::class,$conditions);
}

function ensures(...$conditions) {
  checkAll(PostconditionViolation::class,$conditions);
}

function check($class,$condition, $message, $value) {
  if(!$condition)
    throw new $class($message,$value);
}

function checkAll($class,$conditions) {
  foreach($conditions as $condition) {
    check($class,...$condition);
  }
}
