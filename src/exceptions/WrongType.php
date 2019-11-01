<?php
declare(strict_types=1);


namespace RightThisMinute\StructureDecoder\exceptions;


use Throwable;

class WrongType extends ValueError
{
  public function __construct ($value, string $expected_type)
  {
    $got = gettype($value);
    parent::__construct($value,
      "Expected type [$expected_type] but got [$got].");
  }
}
