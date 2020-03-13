<?php
declare(strict_types=1);


namespace RightThisMinute\StructureDecoder\types;


use RightThisMinute\StructureDecoder\exceptions\DecodeError;
use RightThisMinute\StructureDecoder\exceptions\WrongType;
use function Functional\map;

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


function object () : callable
{
  return function ($value) : object
  {
    if (!is_object($value))
      throw new WrongType($value, 'object');

    return $value;
  };
}
