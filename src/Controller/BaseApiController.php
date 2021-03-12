<?php

namespace Xiag\Poll\Controller;

use function headers_sent;

class BaseApiController
{
  protected function json($data): void
  {
    headers_sent() || header('Content-Type: application/json');
    echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
  }
}
