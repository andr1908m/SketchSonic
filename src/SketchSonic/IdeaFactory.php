<?php

namespace SketchSonic;

use DateTime;
class IdeaFactory {
  private $timestamper;
  private $idGenerator;
  function __construct(?callable $timestamper = null) {
    $this->timestamper = $timestamper ??  fn() => time();
    $this->idGenerator = new IdeaNumberGenerator();
  }
  
  function make($type = "note", $title = "", $tags = [], $filename = "") {
    $timestamper = $this->timestamper;
    $generator = $this->idGenerator;
    return new Idea(
      type: $this->from($type), 
      ideaNumber: new IdeaNumber($generator()), 
      title: new Title($title), 
      tags: new HashSet(TagName::class, $tags),
      createdAt: new DateTime("@{$timestamper()}"), 
      filename: $filename
    );
 }

 private function from($type) {
    if($type === "note")
      return IdeaType::Note;
    elseif($type === "riff")
      return IdeaType::Riff;
    else
      throw new InvalidType($type);
  }
}

class IdeaNumberGenerator {
  function __invoke() {
    return "idea_".hash("sha256","".rand(1,1_000_000));
  }
}
