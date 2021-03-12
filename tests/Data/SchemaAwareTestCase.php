<?php

namespace Tests\Data;

use PDO;
use PHPUnit\Framework\TestCase;
use Xiag\Poll\Data\PdoDB;
use Xiag\Poll\Data\SchemaManager;
use function strpos;

abstract class SchemaAwareTestCase extends TestCase
{
  protected const DSN  = 'sqlite::memory:';
  protected const USER = null;
  protected const PASS = null;

  /**
   * @var SchemaManager
   */
  protected $schema;

  /**
   * @var PdoDB
   */
  protected $db;

  protected function setUp(): void
  {
    $pdo = new PDO(static::DSN, static::USER, static::PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);

    $this->db = new PdoDB($pdo);

    $this->schema = new SchemaManager($this->db);
    $this->schema->up();

    if (strpos(static::DSN, 'sqlite') === 0) {
      $this->db->exec('PRAGMA foreign_keys = ON');
    }
  }

  protected function tearDown(): void
  {
    $this->schema->down();
  }
}
