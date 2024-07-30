<?php

namespace InspireFuel;

enum IdeaType {
  case Note;
  case Riff;

  static function from($value) {
    \requires(
      [in_array($value, ["note", "riff"]), "must be 'note' or 'riff'", $value]
    );
    return match ($value) {
      "note" => self::Note,
      "riff" => self::Riff,
    };
  }
}