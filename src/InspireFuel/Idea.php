<?php

namespace InspireFuel;

class Idea {
  public function __construct(
    private IdeaNumber $ideaNumber,
    private IdeaType $type,
    private Title $title,
    private HashSet $tags,
    private \DateTime $createdAt,
    private string $filename,
  ) {
    invariant($filename !== null, "", $filename);
  }
  
  function ideaNumber() {
    return $this->ideaNumber;
  }
  
  function type() {
    return $this->type;
  }

  function title() {
    return $this->title->value();
  }

  function tags() {
    $tags = $this->tags->all();
    foreach ($tags as $tag) {
      yield $tag->value();
    }
  }
  
  function createdAt() {
    return $this->createdAt;
  }
  
  function filename() {
    return $this->filename;
  }
}

