<?php

namespace Tests\Data;

use PDO;
use PHPUnit\Framework\TestCase;
use Xiag\Poll\Data\DBException;
use Xiag\Poll\Data\PdoDB;
use function array_column;

class PdoDBFunctionalTest extends TestCase
{
  /**
   * @var PdoDB
   */
  private $db;

  protected function setUp(): void
  {
    $pdo = new PDO('sqlite::memory:');

    $this->db = new PdoDB($pdo);
  }

  public function testRows(): void
  {
    $rows = $this->db->rows('SELECT a, b FROM (SELECT ? a, ? b UNION ALL SELECT ?, ? UNION ALL SELECT ?, ?)', [1, 2, 4, 5, 4, 7]);
    self::assertCount(3, $rows);
    self::assertEquals([2, 5, 7], array_column($rows, 'b'));

    $rows = $this->db->rows('SELECT a, b FROM (SELECT ? a, ? b UNION ALL SELECT ?, ? UNION ALL SELECT ?, ?) WHERE a = ?', [1, 2, 4, 5, 4, 7, 4]);

    self::assertCount(2, $rows);
    self::assertEquals([5, 7], array_column($rows, 'b'));
  }

  public function testRow(): void
  {
    $row = $this->db->row('SELECT a, b FROM (SELECT ? a, ? b UNION ALL SELECT ?, ? UNION ALL SELECT ?, ?) WHERE a = ?', [1, 2, 4, 5, 4, 7, 4]);

    self::assertEquals(['a' => 4, 'b' => 5], $row);
  }

  public function testCell(): void
  {
    $cell = $this->db->cell('SELECT b FROM (SELECT ? a, ? b UNION ALL SELECT ?, ? UNION ALL SELECT ?, ?) WHERE a = ?', [1, 2, 4, 5, 4, 7, 4]);

    self::assertEquals(5, $cell);
  }

  public function testCol(): void
  {
    $col = $this->db->col('SELECT a, b FROM (SELECT ? a, ? b UNION ALL SELECT ?, ? UNION ALL SELECT ?, ?) WHERE a = ?', [1, 2, 4, 5, 4, 7, 4], 1);

    self::assertEquals([5, 7], $col);
  }

  public function testFailSql(): void
  {
    $this->expectException(DBException::class);
    $this->expectExceptionMessage('[HY000] no such column: something');
    $this->db->row('SELECT something wrong');
  }
}
