<?php

namespace Xiag\Poll\Data;

use PDO;
use PDOStatement;

class PdoDB implements SqlDbInterface
{
  /** @var PDO */
  protected $pdo;
  public function __construct(PDO $pdo)
  {
    $this->pdo = $pdo;
  }

  public function exec(string $sql, array $data = [])
  {
    $stmt = $this->pdo->prepare($sql);

    $stmt !== false || $this->error();
    $stmt->execute($data) || $this->error($stmt);

    return $stmt;
  }
  public function cell(string $sql, array $data = [], $column = 0)
  {
    $stmt = $this->exec($sql, $data);
    $row  = $stmt->fetch(PDO::FETCH_NUM);

    $row !== false || $this->error($stmt);

    return $row[$column] ?? null;
  }
  public function col(string $sql, array $data = [], $column = 0): array
  {
    $stmt = $this->exec($sql, $data);
    $res  = $stmt->fetchAll(PDO::FETCH_COLUMN, $column);

    $res !== false || $this->error($stmt);

    return $res;
  }
  public function row(string $sql, array $data = []): array
  {
    $stmt = $this->exec($sql, $data);
    $res  = $stmt->fetch(PDO::FETCH_ASSOC);

    $res !== false || $this->error($stmt);

    return $res;
  }

  public function rows(string $sql, array $data = []): array
  {
    $stmt = $this->exec($sql, $data);
    $res  = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $res !== false || $this->error($stmt);

    return $res;
  }

  public function last_insert_id(): string
  {
    return $this->pdo->lastInsertId();
  }

  public function inTransaction(): bool
  {
    return $this->pdo->inTransaction();
  }
  public function beginTransaction(): bool
  {
    return $this->pdo->beginTransaction();
  }
  public function rollBack(): bool
  {
    return $this->pdo->rollBack();
  }
  public function commit(): bool
  {
    return $this->pdo->commit();
  }

  protected function error(PDOStatement $stmt = null): void
  {
    $errorInfo = $stmt ? $stmt->errorInfo() : $this->pdo->errorInfo();
    $errorCode = $stmt ? $stmt->errorCode() : $this->pdo->errorCode();

    if (($errorInfo[0] ?? '00000') === '00000' || !$errorCode) {
      return;
    }

    $error = $errorInfo[2] ?? $errorInfo;
    throw new DBException("[{$errorCode}] {$error}");
  }
}
