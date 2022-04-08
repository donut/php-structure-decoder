<?php
declare(strict_types=1);


namespace RightThisMinute\StructureDecoder\exceptions;


use JetBrains\PhpStorm\Pure;

abstract class ValueError extends StructureDecoderError
{

  private mixed $value;

  private string $reason;

  #[Pure]
  public function __construct (mixed $value, string $reason)
  {
    $message = "Failed decoding value: $reason";
    parent::__construct($message);
    $this->value = $value;
    $this->reason = $reason;
  }

  public function getValue () : mixed { return $this->value; }

  public function getReason () : string { return $this->reason; }
}
