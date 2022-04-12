<?php
declare(strict_types=1);


namespace RightThisMinute\StructureDecoder;


use JetBrains\PhpStorm\Immutable;
use RightThisMinute\StructureDecoder\exceptions\StructureDecoderError;


/**
 * A value that has been decoded, successfully or not. `$error` will be set if
 * there was an error decoding the value.
 */
#[Immutable]
final class Value
{
  /**
   * @param string                     $name
   *   Name of the decoded field as it was found on the structure it was
   *   decoded from.
   * @param mixed                      $value
   *   Decoded value.
   * @param StructureDecoderError|null $error
   *   `null` if field was decoded successfully (or was empty/missing but not
   *    required). If not `null`, then `$value` will be `null` and there was
   *    a problem decoding the field.
   */
  public function __construct
    ( public readonly string $name
    , public readonly mixed $value
    , public readonly ?StructureDecoderError $error)
  {}
}
