<?php
declare(strict_types=1);


namespace RightThisMinute\StructureDecoder;


use RightThisMinute\StructureDecoder\exceptions\DecodeError;
use RightThisMinute\StructureDecoder\exceptions\DuplicateField;
use RightThisMinute\StructureDecoder\exceptions\EmptyValue;
use RightThisMinute\StructureDecoder\exceptions\MissingField;
use RightThisMinute\StructureDecoder\exceptions\UnsupportedStructure;
use Throwable;

use function Functional\reduce_left;


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


/**
 * Checks each field in subject as defined in $fields, not stopping if one
 * or more checks returns/throws an error.
 *
 * When using `field()` and `field_optional()`, if one field throws an error,
 * none of the fields after that one are decoded. This is good when consuming
 * values from a source with a defined structure like an API or database, but
 * is less helpful when getting values from users like form submissions. When
 * a user submits a form, you want to let them know of all problems at once
 * instead of one at a time.
 *
 * This function is meant for situations where all errors should be reported
 * instead of one-at-a-time.
 *
 * @param array<string,mixed>|object $subject
 * @param Field                      ...$fields
 *   List of fields to check for and decode on $subject.
 *
 * @return array<string,Value>
 *   Keys are the names of fields as defined in their respective Field
 *   definition in $fields. The value of each entry is a Value instance. Be
 *   sure to check the Value::error property before trusting that the value is
 *   what was expected.
 *
 * @throws DuplicateField
 *   When $fields contains multiple definitions with the same Field::name
 *   value.
 */
function field_group (array|object $subject, Field ...$fields) : array
{
  return reduce_left
    ( $fields
    , function(Field $field, $_, $__, $values) use ($subject){
      if (isset($values[$field->name]))
        throw new DuplicateField($subject, $field->name);

      try {
        $value =
          $field->required
          ? field($subject, $field->name, $field->decoder)
          : optional_field
              ($subject, $field->name, $field->decoder, $field->default);
      }
      catch (DecodeError $exn) {
        $error = $exn;
      }

      $values[$field->name] =
        new Value($field->name, $value ?? null, $error ?? null);

      return $values;
    }
    , initial: [] );
}
