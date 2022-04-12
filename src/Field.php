<?php
declare(strict_types=1);


namespace RightThisMinute\StructureDecoder;


use Closure;
use JetBrains\PhpStorm\Immutable;
use JetBrains\PhpStorm\Pure;
use RightThisMinute\StructureDecoder\exceptions\EmptyValue;
use RightThisMinute\StructureDecoder\exceptions\MissingField;
use RightThisMinute\StructureDecoder\exceptions\StructureDecoderError;


/**
 * Defines the expectations for a field and how to decode its value. Meant to
 * be used with functions like `field_group()`.
 *
 * @see field_group()
 */
#[Immutable]
final class Field
{
  /**
   * @var Closure
   *
   * A function that accepts a mixed value and decodes it. On error, it
   * should throw a `StructureDecoderError`.
   *
   * @see StructureDecoderError
   */
  public readonly Closure $decoder;

  /**
   * @var bool
   *
   * Whether the field is required or not. If this is `false` and the field
   * is missing or `$this->decoder` throws an `EmptyValue` exception, the
   * resulting value will be `$this->default`.
   *
   * @see MissingField
   * @see EmptyValue
   */
  public readonly bool $required;

  /**
   * @var mixed
   *
   * Default value to use if a field is considered missing or empty and
   * `$this->$required` is `false`.
   */
  public readonly mixed $default;

  /**
   * @param string     $name
   *   Name of the field to be decoded as it appears on the target structure
   *   (array key, object property).
   * @param callable   $decoder
   *   A function that accepts a mixed value and decodes it. On error, it
   *   should throw a `StructureDecoderError`.
   * @param bool       $required
   *   Whether the field is required or not. If this is `false` and the field
   *   is missing or `$decoder` throws an `EmptyValue` exception, the resulting
   *   value will be `$default`.
   * @param mixed|null $default
   *   Default value to use if a field is considered missing or empty and
   *   `$required` is `false`.
   */
  #[Pure]
  public function __construct
    ( public readonly string $name
    , callable $decoder
    , bool $required = true
    , mixed $default = null )
  {
    $this->decoder
      = $decoder instanceof Closure ? $decoder : $decoder(...);
    $this->required = $required;
    $this->default = $default;
  }
}
