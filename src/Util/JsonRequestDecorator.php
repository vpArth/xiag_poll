<?php

namespace Xiag\Poll\Util;

use function array_key_exists;
use function file_get_contents;
use function json_decode;
use function json_last_error;
use function json_last_error_msg;
use const JSON_ERROR_NONE;

class JsonRequestDecorator implements RequestInterface
{
  /** @var RequestInterface */
  protected $wrapped;

  private $jsonData;

  public function __construct(RequestInterface $wrapped)
  {
    $this->wrapped = $wrapped;
  }

  public function getHeaders(): array
  {
    return $this->wrapped->getHeaders();
  }
  public function get($key, $default = null)
  {
    $data = $this->getJsonData();
    if (array_key_exists($key, $data)) {
      return $data[$key];
    }

    return $this->wrapped->get($key, $default);
  }
  public function getPath(): string
  {
    return $this->wrapped->getPath();
  }
  public function getMethod(): string
  {
    return $this->wrapped->getMethod();
  }

  private function getJsonData(): array
  {
    return $this->jsonData ?? ($this->jsonData = $this->parseJsonBody());
  }

  private function parseJsonBody(): array
  {
    $body = $this->getInputStream();

    $result = json_decode($body, true);
    if (null === $result && json_last_error() !== JSON_ERROR_NONE) {
      return [
          '__json_parse_error' => json_last_error_msg(),
      ];
    }

    return $result;
  }

  public function getInputStream(): string
  {
    return file_get_contents('php://input') ?: '';
  }
}
