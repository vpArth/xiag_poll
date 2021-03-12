<?php

namespace Xiag\Poll\Util;

use function str_replace;
use function strpos;
use function strtolower;
use function strtoupper;
use function substr;
use function ucwords;

class Request implements RequestInterface
{
  private $headers;

  public function getHeaders(): array
  {
    return $this->headers ?: $this->headers = self::parseHeaders();
  }
  public function get($key, $default = null)
  {
    return $_POST[$key] ?? $_GET[$key] ?? $default;
  }
  private static function parseHeaders(): array
  {
    $result = [];
    foreach ($_SERVER as $key => $value) {
      if (strpos($key, 'HTTP_') === 0) {
        $name = substr($key, 5);
        $name = ucwords(strtolower(str_replace('_', ' ', $name)));
        $name = str_replace(' ', '-', $name);

        $result[$name] = $value;
      }
    }

    return $result;
  }
  public function getPath(): string
  {
    return $_SERVER['REDIRECT_PATH_INFO'] ?? $_SERVER['PATH_INFO'] ?? '/';
  }
  public function getMethod(): string
  {
    return strtoupper($_SERVER['REQUEST_METHOD']);
  }
}
