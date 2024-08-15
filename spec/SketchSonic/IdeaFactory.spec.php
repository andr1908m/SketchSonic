<?php

namespace SketchSonic;

use function Kahlan\context;
use function Kahlan\describe;
use function Kahlan\expect;
use function Kahlan\it;
use function Kahlan\beforeEach;

describe("IdeaFactory", function() {
  beforeEach(function() {
    $this->ideaFactory = new IdeaFactory();
  });
  
  context("title", function() {
    it("cannot create an idea without a title", function() {
      expectExceptionMessage(fn() => $this->ideaFactory->make())
        ->toMatch("/^title is required/");
    });

    it("cannot create an idea with null title", function() {
      expectExceptionMessage(fn() => $this->ideaFactory->make(title:null))
        ->toMatch("/^title cannot be null/");
    });

    it("cannot create an idea with a title too long", function() {
      $f = $this->ideaFactory;
      
      $length = 256;
      expectExceptionMessage(fn() => $f->make(title:str_repeat("a", $length)))
        ->toMatch("/^title is too long, should be max 256 chars/");
      
      $length = 255;
      expect(fn() => $f->make(title:str_repeat("a", $length)))
        ->not->toThrow();
    });

    it("can obtain value with ->value()", function() {
      $f = $this->ideaFactory;
      
      $idea = $f->make(title:"valid title");

      expect($idea->title())->toBe("valid title");
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
  
      expectExceptionMessage(fn() => $f->make("invalid", title:"title"))
        ->toMatch("/^must be 'note' or 'riff', was 'invalid'/");
    });
  });
 
  context("tags", function() {
    it("can create an idea with tags", function() {
      $f = $this->ideaFactory;
      $idea = $f->make(title:"title", tags:["tag1","tag2"]);
      expect(iterator_to_array($idea->tags()))->toBe(["tag1","tag2"]);
    });

    it("does not include duplicate tags", function() {
      $f = $this->ideaFactory;
      $idea = $f->make(title:"title", tags:["tag1","tag1"]);
      expect(iterator_to_array($idea->tags()))->toBe(["tag1"]);
    });

    it("cannot create tags with empty strings or nulls", function() {
      $f = $this->ideaFactory;
      
      expectExceptionMessage(fn() => $f->make(title:"title", tags:[null]))
        ->toMatch('/^all tags are strings/');
      
      expectExceptionMessage(fn() => $f->make(title:"title", tags:[""]))
        ->toMatch("/^no empty tag/");
    });

    it("cannot create multi word tags", function() {
      $f = $this->ideaFactory;

      expectExceptionMessage(fn() =>
        $f->make(title:"title", tags:["first second", "third fourth"]))
        ->toMatch("/^tag must contain single words/");
    });
  });
  
  function expectExceptionMessage(callable $throwingCB) {    
    return expect(captureException($throwingCB)->getMessage());
  }

  function captureException(callable $throwingCB) {
    try {
      $throwingCB();
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

  context("filename", function() {
    it("can create an idea with a filename", function() {
      $idea = $this->ideaFactory->make(title:"title",filename:"file.txt");
      expect($idea->filename())->toBe("file.txt");
    });

    it("cannot create an idea with a null filename", function() {
      expect(fn() => $this->ideaFactory->make(title:"title",filename:null))
        ->toThrow();
    });
  });
  
  it("can create an idea with a valid idea number", function() {
    $idea = $this->ideaFactory->make(title: "title");
    
    $uid = $idea->ideaNumber();
    expect($uid->prefix())->toBe("idea_");
    // we use sha256
    expect($uid->value())->toMatch('/[a-f0-9]{64}/'); 
  });
});