<?php

namespace Xiag\Poll\Data;

interface SqlDbInterface
{
  public function exec(string $sql, array $data = []);
  public function cell(string $sql, array $data = [], $column = 0);
  public function row(string $sql, array $data = []): array;
  public function rows(string $sql, array $data = []): array;

  public function last_insert_id(): string;
}
