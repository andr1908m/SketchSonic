<?php

namespace InspireFuel;

class Idea {
  public function __construct(
      private $uid = null,
      private $type = IdeaType::Note,
      private $title = null,
      private $tags = null,
      private $createdAt = null,
      private $filename = ""
  ) {
    \invariant($filename !== null, "", $filename);
  }
  
  function uid() {
    return $this->uid;
  }
  
  function type() {
    return $this->type;
  }

  function tags() {
      return $this->tags->all();
  }
  
  function createdAt() {
    return $this->createdAt;
  }
  
  function filename() {
    return $this->filename;
  }
}

