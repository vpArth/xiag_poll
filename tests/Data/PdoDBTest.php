<?php

namespace Tests\Data;

use Generator;
use PDO;
use PDOStatement;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Xiag\Poll\Data\DBException;
use Xiag\Poll\Data\PdoDB;
use function uniqid;

class PdoDBTest extends TestCase
{
  /**
   * @var PdoDB
   */
  private $subject;

  /**
   * @var PDO|MockObject
   */
  private $pdo;

  /**
   * @var PDOStatement|MockObject
   */
  private $stmt;

  protected function setUp(): void
  {
    $this->pdo     = $this->createMock(PDO::class);
    $this->subject = new PdoDB($this->pdo);

    $this->stmt = $this->createMock(PDOStatement::class);
  }

  public function testExec(): void
  {
    $sql  = uniqid('sql-', false);
    $data = [1, 2, 'x'];

    $this->assertExecCall($sql, $data);

    $result = $this->subject->exec($sql, $data);

    self::assertSame($this->stmt, $result);
  }

  public function testCell(): void
  {
    $expected = uniqid('res-', false);
    $sql      = uniqid('sql-', false);
    $data     = [1, 2, 'x'];
    $column   = 42;

    $this->assertExecCall($sql, $data);
    $this->stmt->expects(self::once())
        ->method('fetch')
        ->with(PDO::FETCH_NUM)
        ->willReturn([$column => $expected]);

    $result = $this->subject->cell($sql, $data, $column);

    self::assertEquals($expected, $result);
  }
  public function testCellFail(): void
  {
    $sql    = uniqid('sql-', false);
    $data   = [1, 2, 'x'];
    $column = 42;

    $this->assertExecCall($sql, $data);
    $this->stmt->expects(self::once())
        ->method('fetch')
        ->with(PDO::FETCH_NUM)
        ->willReturn(false);

    $this->assertErrorCall(['HY00', null, 'Database error'], 'HY00');

    $this->expectException(DBException::class);
    $this->expectExceptionMessage('[HY00] Database error');

    $this->subject->cell($sql, $data, $column);
  }
  public function testCellFalse(): void
  {
    $sql    = uniqid('sql-', false);
    $data   = [1, 2, 'x'];
    $column = 42;

    $this->assertExecCall($sql, $data);
    $this->stmt->expects(self::once())
        ->method('fetch')
        ->with(PDO::FETCH_NUM)
        ->willReturn(false);

    $this->assertErrorCall(['00000', null, 'Database error'], 'HY00');

    $result = $this->subject->cell($sql, $data, $column);

    self::assertNull($result);
  }

  public function testCol(): void
  {
    $sql    = uniqid('sql-', false);
    $data   = [1, 2, 'x'];
    $column = 42;

    $expected = [13, 14, 15];

    $this->assertExecCall($sql, $data);
    $this->stmt->expects(self::once())
        ->method('fetchAll')
        ->with(PDO::FETCH_COLUMN, $column)
        ->willReturn($expected);

    $result = $this->subject->col($sql, $data, $column);

    self::assertEquals($expected, $result);
  }
  public function testRow(): void
  {
    $sql  = uniqid('sql-', false);
    $data = [1, 2, 'x'];

    $expected = ['a' => 42];

    $this->assertExecCall($sql, $data);
    $this->stmt->expects(self::once())
        ->method('fetch')
        ->with(PDO::FETCH_ASSOC)
        ->willReturn($expected);

    $result = $this->subject->row($sql, $data);

    self::assertEquals($expected, $result);
  }
  public function testRows(): void
  {
    $sql  = uniqid('sql-', false);
    $data = [1, 2, 'x'];

    $expected = [['a' => 3], ['a' => 5], ['a' => 8]];

    $this->assertExecCall($sql, $data);
    $this->stmt->expects(self::once())
        ->method('fetchAll')
        ->with(PDO::FETCH_ASSOC)
        ->willReturn($expected);

    $result = $this->subject->rows($sql, $data);

    self::assertEquals($expected, $result);
  }

  public function testLast_insert_id(): void
  {
    $expected = uniqid('id-', false);
    $this->pdo->expects(self::once())
        ->method('lastInsertId')
        ->willReturn($expected);

    $actual = $this->subject->last_insert_id();

    self::assertEquals($expected, $actual);
  }

  protected function assertExecCall(string $sql, array $data): void
  {
    $this->pdo->expects(self::once())
        ->method('prepare')
        ->with($sql)
        ->willReturn($this->stmt);

    $this->stmt->expects(self::once())
        ->method('execute')
        ->with($data)
        ->willReturn(true);
  }
  protected function assertErrorCall($info, $code, $useStmt = true): void
  {
    $obj = $useStmt ? $this->stmt : $this->pdo;

    $obj->expects(self::once())
        ->method('errorInfo')
        ->willReturn($info);

    $obj->expects(self::once())
        ->method('errorCode')
        ->willReturn($code);
  }

  /**
   * @dataProvider pdoProxyData
   */
  public function testPdoBoolProxies($method): void
  {
    $this->pdo->method($method)->willReturnOnConsecutiveCalls(false, true);
    self::assertFalse($this->subject->{$method}());
    self::assertTrue($this->subject->{$method}());
  }
  public function pdoProxyData(): ?Generator
  {
    yield ['inTransaction'];
    yield ['beginTransaction'];
    yield ['rollBack'];
    yield ['commit'];
  }
}
