<?php

namespace Xiag\Poll\Data;

interface CrudDbInterface
{
  public function find(string $table, array $cond, string $fetchMode = 'row'): ?array;
  public function insert(string $table, array $data): int;
  public function update(string $table, array $data, array $cond): void;
  public function save(string $table, array $data, array $cond, string $pkField = null): array;
}
