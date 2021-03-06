<?php

namespace Xiag\Poll\Data;

use function sprintf;

class SchemaManager
{
  public const TABLE_ANSWER = 'Answer';
  public const TABLE_VOTE   = 'Vote';
  public const TABLE_POLL   = 'Poll';

  public const SQL_CREATE_POLL_TABLE   = <<<'SQL'
CREATE TABLE IF NOT EXISTS %s (
  id integer not null primary key,
  uuid varchar(36) not null unique,
  question varchar(255) not null 
)
SQL;
  public const SQL_CREATE_ANSWER_TABLE = <<<'SQL'
CREATE TABLE IF NOT EXISTS %s (
    id integer not null primary key,
    id_poll integer not null references %s(id) ON DELETE CASCADE ON UPDATE CASCADE,
    title varchar(16) not null
)
SQL;

  public const SQL_CREATE_VOTE_TABLE = <<<'SQL'
CREATE TABLE IF NOT EXISTS %s (
    id integer not null primary key,
    id_answer integer not null references %s(id) ON DELETE CASCADE ON UPDATE CASCADE,
    username varchar(255) not null
)
SQL;

  public const SQL_DROP_TABLE = 'DROP TABLE IF EXISTS %s';

  /** @var SqlDbInterface */
  protected $db;

  public function __construct(SqlDbInterface $db)
  {
    $this->db = $db;
  }

  public function up(): void
  {
    $this->db->exec(sprintf(self::SQL_CREATE_POLL_TABLE, self::TABLE_POLL));
    $this->db->exec(sprintf(self::SQL_CREATE_ANSWER_TABLE, self::TABLE_ANSWER, self::TABLE_POLL));
    $this->db->exec(sprintf(self::SQL_CREATE_VOTE_TABLE, self::TABLE_VOTE, self::TABLE_ANSWER));
  }
  public function down(): void
  {
    $this->db->exec(sprintf(self::SQL_DROP_TABLE, self::TABLE_VOTE));
    $this->db->exec(sprintf(self::SQL_DROP_TABLE, self::TABLE_ANSWER));
    $this->db->exec(sprintf(self::SQL_DROP_TABLE, self::TABLE_POLL));
  }
}
