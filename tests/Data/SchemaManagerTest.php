<?php

namespace Tests\Data;

use PHPUnit\Framework\TestCase;
use Xiag\Poll\Data\SchemaManager;
use Xiag\Poll\Data\SqlDbInterface;
use function sprintf;

class SchemaManagerTest extends TestCase
{
  /**
   * @var \PHPUnit\Framework\MockObject\MockObject|SqlDbInterface
   */
  private $db;
  /**
   * @var SchemaManager
   */
  private $subject;

  protected function setUp(): void
  {
    $this->db      = $this->createMock(SqlDbInterface::class);
    $this->subject = new SchemaManager($this->db);
  }

  public function testUp(): void
  {
    $this->db->expects(self::exactly(3))
        ->method('exec')
        ->withConsecutive(...[
            [sprintf(SchemaManager::SQL_CREATE_POLL_TABLE, SchemaManager::TABLE_POLL)],
            [sprintf(SchemaManager::SQL_CREATE_ANSWER_TABLE, SchemaManager::TABLE_ANSWER, SchemaManager::TABLE_POLL)],
            [sprintf(SchemaManager::SQL_CREATE_VOTE_TABLE, SchemaManager::TABLE_VOTE, SchemaManager::TABLE_ANSWER)],
        ]);

    $this->subject->up();
  }
  public function testDown(): void
  {
    $this->db->expects(self::exactly(3))
        ->method('exec')
        ->withConsecutive(...[
            [sprintf(SchemaManager::SQL_DROP_TABLE, SchemaManager::TABLE_VOTE)],
            [sprintf(SchemaManager::SQL_DROP_TABLE, SchemaManager::TABLE_ANSWER)],
            [sprintf(SchemaManager::SQL_DROP_TABLE, SchemaManager::TABLE_POLL)],
        ]);

    $this->subject->down();
  }
}
