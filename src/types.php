<?php
declare(strict_types=1);


namespace RightThisMinute\StructureDecoder\types;


use RightThisMinute\StructureDecoder\exceptions\DecodeError;
use RightThisMinute\StructureDecoder\exceptions\WrongType;
use function Functional\map;
use function Functional\none;


function array_of (callable $decoder) : callable
{
  return function ($value) use ($decoder) : array
  {
    $array = array_of_mixed()($value);

    return map($array, function($value, $key)use($decoder){
      try {
        return $decoder($value);
      }
      catch (\Throwable $exn) {
        throw new DecodeError($value, $exn, (string)$key);
      }
    });
  };
}


function array_of_mixed () : callable
{
  return function ($value) : array
  {
    if (!is_array($value))
      throw new WrongType($value, 'array');

    return $value;
  };
}


function bool () : callable
{
  return function ($value) : bool
  {
    if (!is_bool($value))
      throw new WrongType($value, 'boolean');

    return $value;
  };
}


/**
 * Returns a value decoder that takes a mix of types that may have boolean
 * values like 1/0 or yes/no and returns the actual boolean equivalent.
 *
 * A numeric 1 (int, float, string) evaluates to `true`, and a numeric 0
 * evaluates to `false`. The following strings evaluate to true: true, t, on,
 * yes. And these strings evaluate to false: false, f, off, no. Any other
 * values will throw a `WrongType` exception.
 *
 * @return callable
 */
function bool_of_mixed () : callable
{
  return function ($value) : bool
  {
    if (is_bool($value))
      return $value;

    if ($value === 1 || $value === 1.0 || $value === '1')
      return true;

    if ($value === 0 || $value === 0.0 || $value === '0')
      return false;

   if (is_string($value)) {
     $value = strtolower($value);

     if (in_array($value, ['true', 't', 'on', 'yes'], true))
       return true;

     if (in_array($value, ['false', 'f', 'off', 'no'], true))
       return true;
   }

   throw new WrongType($value, 'boolean-ish', true);
  };
}


function dict_of (callable $decoder) : callable
{
  return function ($value) use ($decoder) : array
  {
    $dict = dict_of_mixed()($value);
    return map($dict, function($value, $key)use($decoder){
      try {
        return $decoder($value);
      }
      catch (\Throwable $exn) {
        throw new DecodeError($value, $exn, (string)$key);
      }
    });
  };
}


function dict_of_mixed () : callable
{
  return function ($value) : array
  {
    if (is_object($value))
      $value = get_object_vars($value);
    else if (!is_array($value))
      throw new WrongType($value, 'object or associative array');

    $is_associative_array =
      none($value, function ($_, $key){ return !is_string($key); });
    if (!$is_associative_array)
      throw new WrongType
      ($value, 'associative array with string keys');

    return $value;
  };
}


function first_of (callable ...$decoders) : callable
{
  return function ($value) use ($decoders)
  {
    foreach ($decoders as $decoder) {
      try { return $decoder($value); }
      catch (\Throwable $exn) {}
    }

    throw $exn;
  };
}


function int () : callable
{
  return function ($value) : int {
    if (!is_int($value))
      throw new WrongType($value, 'int');

    return $value;
  };
}


/**
 * Returned decoder expects a string with an integer value. White space and
 * left padded zeroes are stripped when testing and converting to int.
 *
 * @return callable
 */
function int_of_string () : callable
{
  return function ($value) : int
  {
    if (!is_string($value))
      throw new WrongType($value, 'int as string');

    if (!is_numeric($value))
      throw new WrongType
        ($value, 'int as string', true);

    $value = trim($value);
    $value = ltrim($value, '0');
    $int = (int)$value;

    if ("$int" !== $value)
      throw new WrongType
        ($value, 'int as string', true);

    return $int;
  };
}


function mixed () : callable
{
  return function ($value)
  {
    if (!isset($value))
      throw new WrongType($value, 'not null');

    return $value;
  };
}


function object () : callable
{
  return function ($value) : object
  {
    if (!is_object($value))
      throw new WrongType($value, 'object');

    return $value;
  };
}


function string () : callable
{
  return function ($value) : string
  {
    if (!is_string($value))
      throw new WrongType($value, 'string');

    return $value;
  };
}
