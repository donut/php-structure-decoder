<?php

namespace RightThisMinute\StructureDecoder\exceptions;


use JetBrains\PhpStorm\Pure;


/**
 * For values that could be considered "empty" like a string of 0 characters
 * or an array without elements.
 */
class EmptyValue extends ValueError
{
  #[Pure]
  public function __construct (mixed $value, string $reason)
  {
    parent::__construct($value, $reason);
  }
}
