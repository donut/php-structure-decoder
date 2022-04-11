<?php
declare(strict_types=1);


namespace RightThisMinute\StructureDecoder;


use RightThisMinute\StructureDecoder\exceptions\DecodeError;
use RightThisMinute\StructureDecoder\exceptions\EmptyValue;
use RightThisMinute\StructureDecoder\exceptions\MissingField;
use RightThisMinute\StructureDecoder\exceptions\UnsupportedStructure;
use Throwable;


/**
 * Checks for key or property of $name on $subject and decodes its value with
 * $decoder.
 *
 * @param array<string,mixed>|object $subject
 * @param string $name
 *   Property/key on to look for on $subject.
 * @param callable $decoder
 *   Function that takes a single argument of mixed type and returns the value
 *   in the desired type and format.
 *
 * @return mixed
 *   This will be whatever $decoder returns.
 * @throws DecodeError
 *   When $subject doesn't contain $name or its value is null or if $decoder
 *   throws an error.
 */
function field ($subject, string $name, callable $decoder)
{
  if (!is_object($subject) && !is_array($subject))
    throw new DecodeError($subject, new UnsupportedStructure($subject));

  if (is_object($subject) && !isset($subject->$name)
      || is_array($subject) && !isset($subject[$name]))
    throw new DecodeError($subject, new MissingField($subject, $name));

  $value = is_object($subject) ? $subject->$name : $subject[$name];

  try {
    return $decoder($value);
  }
  catch (Throwable $e) {
    throw new DecodeError($subject, $e, $name);
  }
}


/**
 * Checks for key or property of $name on $subject and decodes its value with
 * $decoder. If $name is missing from $subject, returns null.
 *
 * @param object|array<string,mixed> $subject
 * @param string       $name
 *   Property/key on to look for on $subject.
 * @param callable     $decoder
 *   Function that takes a single argument of mixed type and returns the value
 *   in the desired type and format.
 *
 * @return mixed
 *   Returns $default if there is no field of $name on $subject or $decoder
 *   throws EmptyValue. Otherwise, returns the result of $decoder.
 * @throws DecodeError
 *   If $decoder throws an error.
 */
function optional_field
  ( object|array $subject
  , string $name
  , callable $decoder
  , $default=null )
  : mixed
{
  try {
    return field($subject, $name, $decoder);
  }
  catch (DecodeError $e) {
    $prev = $e->getPrevious();
    if ($prev instanceof MissingField || $prev instanceof EmptyValue)
      return $default ?? null;

    throw $e;
  }
}
