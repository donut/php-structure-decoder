<?php
declare(strict_types=1);


namespace RightThisMinute\StructureDecoder\exceptions;


use Throwable;

class WrongType extends ValueError
{
  /**
   * WrongType constructor.
   *
   * @param $value
   *   The value that is of the wrong type.
   * @param string $expected_type
   *   The expected type of the value.
   * @param bool $include_value
   *   Whether or not to include the value in the message. This can be helpful
   *   in situations like converting a string of a numeric value to an int
   *   or float.
   */
  public function __construct
    ($value, string $expected_type, bool $include_value=false)
  {
    $got = gettype($value);

    if ($include_value)
      $got = "$got: $value";

    parent::__construct($value,
      "expected type [$expected_type] but got [$got]");
  }
}
