<?php

namespace Xiag\Poll\Data;

interface CrudDbInterface
{
  public function find(string $table, array $cond, string $fetchMode = 'row');
  /**
   * @return string|int last inserted id
   * @throws DBException if foreign keys check fails for example
   */
  public function insert(string $table, array $data);
  public function update(string $table, array $data, array $cond): void;
}
