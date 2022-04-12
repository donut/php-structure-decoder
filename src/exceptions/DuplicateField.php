<?php

namespace RightThisMinute\StructureDecoder\exceptions;


use function RightThisMinute\StructureDecoder\field_group;


/**
 * When decoding a group of fields at once, this is thrown when there are
 * multiple Field definitions passed for the same field.
 *
 * @see field_group()
 */
class DuplicateField extends StructuralError
{
  /**
   * @param array<string,mixed>|object $subject
   *
   * @param string       $field_name
   */
  public function __construct (array|object $subject, string $field_name)
  {
    parent::__construct
    ( $subject
    , "The field [$field_name] has already been decoded on this subject."
    , $field_name );
  }
}
