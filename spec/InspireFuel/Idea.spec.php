<?php

namespace InspireFuel;

require_once __DIR__."/../../vendor/autoload.php";


use function Kahlan\describe;
use function Kahlan\expect;
use function Kahlan\it;

class IdeaRepository {
  public $idea;
  
  function save($title) {
    $this->idea = $title;
  }
  
  function hasRecord($title) {
    return $this->idea !== null;
  }
}

class IdeaFactory {
  function __construct(private $timestamper = null) {
    $this->timestamper ??= fn() => time();
  }
  
  function make($type = IdeaType::Note, $title = "", $tags = [], $filename = "") {
    $timestamper = $this->timestamper;
    return new Idea(
        new UID(uniqid("idea_",more_entropy: true)), 
        $type, 
        new Title($title), 
        new Tags($tags), 
        new \DateTime("@{$timestamper()}"), 
        $filename
    );
 }
}

describe("Idea", function() {
  beforeEach(function() {
    $this->repo = new IdeaRepository();
    $this->ideaFactory = new IdeaFactory();
  });
  
  it("cannot create an idea without a title", function() {
    $e = captureException(fn() => $this->ideaFactory->make());
    
    expect($e->getMessage())
      ->toMatch("/^title is required/");
  });
  
  it("cannot create an idea with a title too long", function() {
    $f = $this->ideaFactory;
    
    $title = str_repeat("a", 256);    
    expect(captureException(fn() => $f->make(title:$title))->getMessage())
      ->toMatch("/^title is too long, should be max 256 chars/");
    
    $title = str_repeat("a", 255);
    expect(fn() => $f->make(title:$title))
      ->not->toThrow();
  });
  
  it("creates an idea a default type of 'Note'", function() {
    $idea = $this->ideaFactory->make(title:"title");
    expect($idea->type())->toBe(IdeaType::Note);
  });
  
  it("creates an idea with a type of Note or Riff", function() {
    $f = $this->ideaFactory;
    
    expect($f->make(IdeaType::Note, "title")->type())->toBe(IdeaType::Note);
    expect($f->make(IdeaType::Riff, "title")->type())->toBe(IdeaType::Riff);
  });
 
  it("cannot create tags with empty strings or nulls", function() {
    $f = $this->ideaFactory;
    
    $e = captureException(fn() => 
        $f->make(IdeaType::Note, "title", tags:[null])
    );
    expect($e->getMessage())->toMatch('/^all tags are strings/');
    
    $e = captureException(fn() => 
        $f->make(IdeaType::Note, "title", tags:[""])
    );
    expect($e->getMessage())->toMatch("/^no empty tags/");
  });
  
  function captureException(callable $callback) {
    try {
      $callback();
    } catch (\Exception $e) {
      return $e;
    }
    return null;
  }
  
  it("cannot create multi word tags", function() {
    $f = $this->ideaFactory;
    $e = captureException(fn() =>
        $f->make(title:"title", tags:["first second", "third fourth"])
    );
    expect($e->getMessage())->toMatch("/^tags must contain single words/");
  });
  
  it("can create an idea with tags", function() {
    $f = $this->ideaFactory;
    $idea = $f->make(IdeaType::Note, "title", tags:["tag1","tag2"]);
    expect((array)($idea->tags()))->toBe(["tag1","tag2"]);
  });
  
  it("can create an timestamped idea", function() {
    $this->ideaFactory = new IdeaFactory(fn () => 0);
    $idea = $this->ideaFactory->make(title:"title");
    expect($idea->createdAt()->getTimestamp())->toBe(0);
  });
  
  it("can create an idea with a filename, but it must not be null", function() {
    expect(fn() => $this->ideaFactory->make(title:"title",filename:null))
      ->toThrow();
    
    $idea = $this->ideaFactory->make(title:"title",filename:"mymagicfile.mp3");
    expect($idea->filename())->toBe("mymagicfile.mp3");
  });
  
  it("can create an idea with a valid uid", function() {
    $idea = $this->ideaFactory->make(title: "title");
    
    $uid = $idea->uid();
    expect($uid->prefix())->toBe("idea_");
    // this is how uniqid in php works from what i can see
    expect($uid->value())->toMatch('/^[0-9a-f]{14}\.[0-9a-f]{8}$/i'); 
  });
  
  
});