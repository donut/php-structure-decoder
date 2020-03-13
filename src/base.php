<?php
declare(strict_types=1);


namespace RightThisMinute\StructureDecoder;


use RightThisMinute\StructureDecoder\exceptions\DecodeError;
use RightThisMinute\StructureDecoder\exceptions\MissingField;
use RightThisMinute\StructureDecoder\exceptions\UnsupportedStructure;


/**
 * @param $subject
 * @param string $name
 * @param callable $decoder
 *
 * @return mixed
 * @throws DecodeError
 */
function field ($subject, string $name, callable $decoder)
{
  if (!is_object($subject) and !is_array($subject))
    throw new DecodeError($subject, new UnsupportedStructure($subject));

  if (is_object($subject) and !isset($subject->$name)
      or is_array($subject) and !isset($subject[$name]))
    throw new DecodeError($subject, new MissingField($subject, $name));

  $value = is_object($subject) ? $subject->$name : $subject[$name];

  try {
    return $decoder($value);
  }
  catch (\Throwable $e) {
    throw new DecodeError($subject, $e, $name);
  }
}


/**
 * @param $subject
 * @param string $name
 * @param callable $decoder
 *
 * @return mixed|null
 * @throws DecodeError
 */
function optional_field
  ($subject, string $name, callable $decoder, $default=null)
{
  try {
    return field($subject, $name, $decoder);
  }
  catch (DecodeError $e) {
    if ($e->getPrevious() instanceof MissingField)
      return $default ?? null;

    throw $e;
  }
}
