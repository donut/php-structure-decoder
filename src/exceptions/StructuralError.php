<?php


namespace RightThisMinute\StructureDecoder\exceptions;


use Throwable;

abstract class StructuralError extends StructureDecoderError
{
  /** @var mixed */
  private $subject;

  /** @var string */
  private $reason;

  /** @var string|null */
  private $field_name;

  /** @var mixed|null */
  private $value;

  public function __construct
    ($subject, string $reason, ?string $field_name=null, $value=null)
  {
    $message = isset($field_name)
      ? "Failed decoding field [$field_name]: $reason"
      : "Failed decoding structure: $reason";

    parent::__construct($message);
    $this->subject = $subject;
    $this->reason = $reason;
    $this->field_name = $field_name;
    $this->value = $value;
  }

  /** @return mixed */
  public function getSubject () { return $this->subject; }

  /** @return string */
  public function getReason () : string { return $this->reason; }

  /** @return string|null */
  public function getFieldName () : ?string { return $this->field_name; }

  /** @return mixed|null */
  public function getValue () { return $this->value; }
}
