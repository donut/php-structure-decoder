<?php
declare(strict_types=1);


namespace RightThisMinute\StructureDecoder\exceptions;


use Throwable;

class DecodeError extends StructureDecoderError
{
  /** @var string[] */
  private $path;

  /** @var \Throwable */
  private $final;

  /** @var mixed */
  private $subject;

  /** @var string|null */
  private $field_name;

  public function __construct
    ($subject, Throwable $error, ?string $field_name=null)
  {
    $this->subject = $subject;
    $this->field_name = $field_name;
    $this->path = isset($field_name) ? [$field_name] : [];
    $this->final = $error;

    if ($error instanceof self) {
      $this->path = array_merge($this->path, $error->getPath());
      $this->final = $error->getFinal();
    }
    else if ($error instanceof StructuralError) {
      $sub_field = $error->getFieldName();
      if (isset($sub_field))
        $this->path[] =  $sub_field;
    }

    if (count($this->path) > 0) {
      $path = implode('->', $this->path);
      $message = "At [$path], " . lcfirst($this->final->getMessage());
    }
    else
      $message = $this->final->getMessage();

    parent::__construct($message, 0, $error);
  }

  /**
   * @return \Throwable
   */
  public function getFinal () : \Throwable { return $this->final; }

  /** @return string[] */
  public function getPath () : array { return $this->path; }

  /** @return mixed */
  public function getSubject () { return $this->subject; }
}
