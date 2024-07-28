<?php


function requires($condition, $message = "", $value = null) {
  $serialized = $value === null? "" : ": ".varDump($value);
  if(!$condition) 
     throw new PreconditionViolation($message.$serialized);
}

class PreconditionViolation extends Exception {}

function varDump($value) {
  ob_start();
  var_dump($value);
  $output = ob_get_clean();
  return $output;
}