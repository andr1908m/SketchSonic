<?php

namespace InspireFuel;

class IdeaFactory {
  function __construct(private $timestamper = null) {
    $this->timestamper ??= fn() => time();
  }
  
  function make($type = "note", $title = "", $tags = [], $filename = "") {
    $timestamper = $this->timestamper;
    return new Idea(
        new UID("idea_".hash("sha256",rand(1,1_000_000))), 
        IdeaType::from($type), 
        new Title($title), 
        new Tags($tags), 
        new \DateTime("@{$timestamper()}"), 
        $filename
    );
 }
}
