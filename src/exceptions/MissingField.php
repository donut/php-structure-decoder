<?php
declare(strict_types=1);


namespace RightThisMinute\StructureDecoder\exceptions;


use Throwable;

class MissingField extends StructuralError
{
  public function __construct ($subject, string $field_name)
  {
    parent::__construct
      ($subject, "Missing field [$field_name].", $field_name);
  }
}
