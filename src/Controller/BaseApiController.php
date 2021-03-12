<?php

namespace Xiag\Poll\Controller;

class BaseApiController
{
  protected function json($data): void
  {
    header('Content-Type: application/json');
    echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
  }
}
