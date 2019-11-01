<?php
declare(strict_types=1);


namespace RightThisMinute\StructureDecoder\exceptions;


use Throwable;

class UnsupportedStructure extends StructuralError
{
  public function __construct ($subject)
  {
    $got = gettype($subject);
    parent::__construct($subject,
      "Subject must be an associative array or an object. Subject is of type [$got].");
  }
}
