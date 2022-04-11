<?php
declare(strict_types=1);


namespace RightThisMinute\StructureDecoder\exceptions;


use JetBrains\PhpStorm\Pure;


/**
 * A problem with a value of a field as apposed to the field itself. For
 * example, a field could be misspelled, but that doesn't say anything about
 * its value.
 */
abstract class ValueError extends StructureDecoderError
{

  private mixed $value;

  private string $reason;

  #[Pure]
  public function __construct (mixed $value, string $reason)
  {
    $message = "Failed decoding value: $reason.";
    parent::__construct($message);
    $this->value = $value;
    $this->reason = $reason;
  }

  public function getValue () : mixed { return $this->value; }

  public function getReason () : string { return $this->reason; }
}
