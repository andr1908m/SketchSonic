<?php

require_once "contracts.php";

use function Kahlan\describe;
use function Kahlan\expect;
use function Kahlan\it;

class Ideas {
  
  public function __construct(private $persister, private $timestamper = null) {
    $this->timestamper ??= fn() => time();
  }
  
  public function save($idea) {
    $this->persister->save($idea);
  }
  
  function make($type = IdeaType::Note, $name = "", $tags = []) {
    $timestamper = $this->timestamper;
    return new Idea($type, $name, $tags, $timestamper());
  }
}

class Persister {
  public $idea;
  
  function save($name) {
    $this->idea = $name;
  }
  
  function hasRecord($name) {
    return $this->idea !== null;
  }
}

enum IdeaType {
  case Note;
  case Riff;
}

class Idea {
  public function __construct(
      private $type = IdeaType::Note,
      private $name = "", 
      private $tags = [],
      private $createdAt = 0,
      private $filename = ""
  ) {
    requires($this->tagsAreStrings($tags), "all tags are strings", $tags);
    requires($this->noEmptyTags($tags), "no empty tags", $tags);
    requires($filename !== null, value:$filename);
  }
  
  function type() {
    return $this->type;
  }

  function tags() {
      return new ArrayIterator($this->tags);
  }
  
  function createdAt() {
    return $this->createdAt;
  }
  
  function filename() {
    return $this->filename;
  }

  private function tagsAreStrings($tags) {
    $a = $this->filterWithIndex($tags, fn($e) => !is_string($e));
    return iterator_count($a) === 0;
  }
  
  private function filterWithIndex($tags, $condition) {
    $i = 0;
    foreach($tags as $tag) {
      if($condition($tag))
        yield [$tag,$i];
      $i++;
    }
  } 
  
  private function noEmptyTags($tags) {
    $i = array_search("",$tags);
    return $i === false; 
  }
}

class IdeaFactory {
  
  function __construct(private $timestamper) {
    $this->timestamper ??= fn() => time();
  }
  
  function make($type = IdeaType::Note, $name = "", $tags = []) {
    $timestamper = $this->timestamper;
    return new Idea($type, $name, $tags, $timestamper());
  }
}

describe("Ideas", function() {
  beforeEach(function() {
    $this->persister = new Persister();
    $this->ideas = new Ideas($this->persister);
  });
  
  it("returns false when idea not found", function() {
    expect($this->persister->idea)->toBeNull();
  });
  
  it("saves a idea", function() {
    $this->ideas->save(new Idea(name:"Name"));
    expect($this->persister->idea)->not->toBeNull();
  });
  
  it("saves a default type of 'Note'", function() {
    $this->ideas->save(new Idea(name:"Name"));
    expect($this->persister->idea->type())->toBe(IdeaType::Note);
  });
  
  it("can save a type of Note or Riff", function() {
    $expectWhenAdded = Closure::fromCallable('expectWhenSaved')->bindTo($this);
    $expectWhenAdded(new Idea(type:IdeaType::Note, name:"Name"), IdeaType::Note);
    $expectWhenAdded(new Idea(type:IdeaType::Riff, name:"Name"), IdeaType::Riff);
  });
  
  function expectWhenSaved($idea, $typeSavedWas) {
    $this->ideas->save($idea);
    expect($this->persister->idea->type())->toBe($typeSavedWas);
  }
 
  it("tags must not be empty strings or null", function() {
    $e = captureException(fn() => $this->ideas->save(
        new Idea(IdeaType::Note, "Name", tags:[null])
    ));
    expect($e->getMessage())->toMatch('/^all tags are strings/');
    
    $e = captureException(fn() => $this->ideas->save(
        new Idea(IdeaType::Note, "Name", tags:["valid",""])
    ));
    expect($e->getMessage())->toMatch("/^no empty tags/");
  });
  
  function captureException(callable $callback) {
    try {
      $callback();
    } catch (Exception $e) {
      return $e;
    }
    return null;
  }
  
  it("can save a idea with tags", function() {
    $this->ideas->save(new Idea(IdeaType::Note, "Name", tags:["tag1","tag2"]));
    expect((array)($this->persister->idea->tags()))->toBe(["tag1","tag2"]);
  });
  
  it("can create an timestamped idea via factory", function() {
    $this->ideas = new Ideas($this->persister, fn () => 0);
    $idea = $this->ideas->make();
    expect($idea->createdAt())->toBe(0);
  });
  
  it("can create an idea with a filename, and it must not be null", function() {
    expect(fn() => new Idea(filename:null))->toThrow();
    $idea = new Idea(filename:"mymagicfile.mp3");
    expect($idea->filename())->toBe("mymagicfile.mp3");
  });
  
});