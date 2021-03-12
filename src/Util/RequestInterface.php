<?php

namespace Xiag\Poll\Util;

interface RequestInterface
{
  public function get(string $key, $default = null);
  public function getHeaders(): array;

  public function getMethod(): string;
  public function getPath(): string;
}
