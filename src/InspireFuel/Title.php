<?php

namespace InspireFuel;

class Title {
  function __construct(private $title) {
    \invariants(
        [strlen($title) !== 0, "title is required", $title],
        [strlen($title) < 256, "title is too long, should be max 256 chars", $title]
    );
  }
}