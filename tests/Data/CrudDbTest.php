<?php

namespace Tests\Data;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Xiag\Poll\Data\CrudDb;
use Xiag\Poll\Data\SqlDbInterface;
use function uniqid;

class CrudDbTest extends TestCase
{
  /**
   * @var MockObject|SqlDbInterface
   */
  private $db;
  /**
   * @var CrudDb
   */
  private $subject;

  protected function setUp(): void
  {
    $this->db      = $this->createMock(SqlDbInterface::class);
    $this->subject = new CrudDb($this->db);
  }

  public function testFind(): void
  {
    $table     = uniqid('table_', false);
    $cond      = ['field' => uniqid('value', false)];
    $fetchMode = 'cell';
    $expected  = uniqid('res-', false);

    $this->db->expects(self::once())
        ->method($fetchMode)
        ->with("SELECT * FROM {$table} WHERE field = ?", [$cond['field']])
        ->willReturn($expected);

    $actual = $this->subject->find($table, $cond, $fetchMode);

    self::assertEquals($expected, $actual);

    $this->db->expects(self::once())
        ->method('row')
        ->with("SELECT * FROM {$table} WHERE field = ?", [$cond['field']])
        ->willReturn(['res' => $expected]);

    $actual = $this->subject->find($table, $cond);

    self::assertEquals(['res' => $expected], $actual);
  }
  public function testInsert(): void
  {
    $table = uniqid('table_', false);
    $data  = ['field' => uniqid('value', false)];

    $id = uniqid('id-', false);

    $this->db->expects(self::once())
        ->method('last_insert_id')
        ->willReturn($id);

    $this->db->expects(self::once())
        ->method('exec')
        ->with("INSERT INTO {$table} (field) VALUES (?)", [$data['field']]);

    $actual = $this->subject->insert($table, $data);

    self::assertEquals($id, $actual);
  }

  public function testUpdate(): void
  {
    $table = uniqid('table_', false);
    $data  = ['field' => uniqid('value', false)];
    $cond  = ['x' => uniqid('value', false), 'n' => null];

    $this->db->expects(self::once())
        ->method('exec')
        ->with("UPDATE {$table} SET field = ? WHERE x = ? AND n IS NULL", [$data['field'], $cond['x']]);

    $this->subject->update($table, $data, $cond);
  }

}
