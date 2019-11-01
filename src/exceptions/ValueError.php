<?php
declare(strict_types=1);


namespace RightThisMinute\StructureDecoder\exceptions;


use Throwable;

abstract class ValueError extends StructureDecoderError
{

  /** @var mixed */
  private $value;

  /** @var string */
  private $reason;

  public function __construct ($value, string $reason)
  {
    $message = "Failed decoding value: $reason";
    parent::__construct($message);
    $this->value = $value;
    $this->reason = $reason;
  }

  /** @return mixed */
  public function getValue () { return $this->value; }

  /** @return string */
  public function getReason () : string { return $this->reason; }
}
