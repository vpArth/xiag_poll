<?php

namespace Xiag\Poll\Data;

use function array_keys;
use function array_values;
use function count;
use function implode;
use function str_repeat;

class CrudDb implements CrudDbInterface
{
  /**
   * @var SqlDbInterface
   */
  protected $db;

  public function __construct(SqlDbInterface $db)
  {
    $this->db = $db;
  }

  public function find(string $table, array $cond, string $fetchMode = 'row')
  {
    $params   = [];
    $whereStr = self::buildWhere($cond, $params);

    return $this->db->$fetchMode("SELECT * FROM {$table}{$whereStr}", $params);
  }
  /**
   * @inheritDoc
   */
  public function insert(string $table, array $data)
  {
    $fields = array_keys($data);
    $params = array_values($data);

    $fieldsStr = implode(', ', $fields);
    $valuesStr = str_repeat('?, ', count($fields) - 1) . '?';

    $this->db->exec("INSERT INTO {$table} ($fieldsStr) VALUES ($valuesStr)", $params);

    return $this->db->last_insert_id();
  }
  public function update(string $table, array $data, array $cond): void
  {
    $params = [];

    $fieldsStr = self::buildSetClause($data, $params);
    $whereStr  = self::buildWhere($cond, $params);

    $this->db->exec("UPDATE {$table} SET {$fieldsStr}{$whereStr}", $params);
  }

  protected static function buildWhere(array $cond, array &$params): string
  {
    $where = [];
    foreach ($cond as $field => $value) {
      if ($value === null) {
        $where[] = "$field IS NULL";
      } else {
        $where[]  = "{$field} = ?";
        $params[] = $value;
      }
    }
    return $where ? ' WHERE ' . implode(' AND ', $where) : '';
  }

  protected static function buildSetClause(array $data, array &$params): string
  {
    $fields = [];
    foreach ($data as $field => $value) {
      $fields[] = "{$field} = ?";
      $params[] = $value;
    }

    return implode(', ', $fields);
  }
}
