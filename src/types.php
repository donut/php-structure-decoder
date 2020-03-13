<?php
declare(strict_types=1);


namespace RightThisMinute\StructureDecoder\types;


use RightThisMinute\StructureDecoder\exceptions\DecodeError;
use RightThisMinute\StructureDecoder\exceptions\WrongType;
use function Functional\map;
use function Functional\none;

function string () : callable
{
  return function ($value) : string
  {
    if (!is_string($value))
      throw new WrongType($value, 'string');

    return $value;
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


function array_of_mixed () : callable
{
  return function ($value) : array
  {
    if (!is_array($value))
      throw new WrongType($value, 'array');

    return $value;
  };
}



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


function object () : callable
{
  return function ($value) : object
  {
    if (!is_object($value))
      throw new WrongType($value, 'object');

    return $value;
  };
}
