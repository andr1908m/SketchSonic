<?php

// hi
class Greeting {

  public function hello() {
    $this->hi();
  }

  private function hi() {
    $this->hey();
  }

  private function hey() {
    echo "hell";
  }

}

$h = new Greeting();

$h->hello();

