<?php

namespace InspireFuel;

use function Kahlan\context;
use function Kahlan\describe;
use function Kahlan\expect;
use function Kahlan\it;

describe("IdeaFactory", function() {
  beforeEach(function() {
    $this->ideaFactory = new IdeaFactory();
  });
  
  context("title", function() {
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
  });
  

  context("type", function() {
    it("creates an idea with a default type of 'Note'", function() {
      $idea = $this->ideaFactory->make(title:"title");
      expect($idea->type())->toBe(IdeaType::Note);
    });
    
    it("creates an idea with a type of Note or Riff", function() {
      $f = $this->ideaFactory;
      
      expect($f->make("note", "title")->type())->toBe(IdeaType::Note);
      expect($f->make("riff", "title")->type())->toBe(IdeaType::Riff);
  
      $e = captureException(fn() => $f->make("invalid",title:"title"));
      expect($e->getMessage())->toMatch("/^must be 'note' or 'riff'/");
    });
  });
 
  context("tags", function() {
    it("can create an idea with tags", function() {
      $f = $this->ideaFactory;
      $idea = $f->make(title:"title", tags:["tag1","tag2"]);
      expect((array)($idea->tags()))->toBe(["tag1","tag2"]);
    });

    it("cannot create tags with empty strings or nulls", function() {
      $f = $this->ideaFactory;
      
      $e = captureException(fn() => 
          $f->make(title:"title", tags:[null])
      );
      expect($e->getMessage())->toMatch('/^all tags are strings/');
      
      $e = captureException(fn() => 
          $f->make(title:"title", tags:[""])
      );
      expect($e->getMessage())->toMatch("/^no empty tags/");
    });

    it("cannot create multi word tags", function() {
      $f = $this->ideaFactory;
      $e = captureException(fn() =>
          $f->make(title:"title", tags:["first second", "third fourth"])
      );
      expect($e->getMessage())->toMatch("/^tags must contain single words/");
    });
  });
  
  function captureException(callable $callback) {
    try {
      $callback();
    } catch (\Exception $e) {
      return $e;
    }
    return null;
  }
  
  context("createdAt", function() {
    it("can create a timestamped idea", function() {
      $this->ideaFactory = new IdeaFactory(fn () => 0);
      $idea = $this->ideaFactory->make(title:"title");
      expect($idea->createdAt()->getTimestamp())->toBe(0);
    });
  });
  
  it("can create an idea with a filename, but it must not be null", function() {
    expect(fn() => $this->ideaFactory->make(title:"title",filename:null))
      ->toThrow();
    
    $idea = $this->ideaFactory->make(title:"title",filename:"file.txt");
    expect($idea->filename())->toBe("file.txt");
  });
  
  it("can create an idea with a valid uid", function() {
    $idea = $this->ideaFactory->make(title: "title");
    
    $uid = $idea->uid();
    expect($uid->prefix())->toBe("idea_");
    // we use sha256
    expect($uid->value())->toMatch('/[a-f0-9]{64}/'); 
  });
});