<?php

namespace Tests\Data;

use PHPUnit\Framework\MockObject\MockObject;
use Throwable;
use Xiag\Poll\Data\CrudDb;
use Xiag\Poll\Data\CrudDbInterface;
use Xiag\Poll\Data\DataProvider;
use Xiag\Poll\Util\UniqIdGenInterface;
use function uniqid;

class DataProviderFunctionalTest extends SchemaAwareTestCase
{
  /**
   * @var MockObject|CrudDbInterface
   */
  private $crud;
  /**
   * @var DataProvider
   */
  private $subject;
  /**
   * @var MockObject|UniqIdGenInterface
   */
  private $uniq;

  protected function setUp(): void
  {
    parent::setUp();

    $this->uniq = $this->createMock(UniqIdGenInterface::class);

    $this->crud    = new CrudDb($this->db);
    $this->subject = new DataProvider($this->crud, $this->db, $this->uniq);
  }

  public function testCreateNewPoll(): void
  {
    $uuid = uniqid('uuid-', false);
    $this->uniq->method('generate')->willReturn($uuid);

    $question = uniqid('q-', false);

    $answer1 = uniqid('a-', false);
    $answer2 = uniqid('a-', false);

    $poll = $this->subject->createNewPoll($question, [$answer1, $answer2]);

    self::assertEquals([
        'id'       => 1,
        'uuid'     => $uuid,
        'question' => $question,
        'answers'  => [
            ['id' => 1, 'id_poll' => 1, 'title' => $answer1],
            ['id' => 2, 'id_poll' => 1, 'title' => $answer2],
        ],
    ], $poll);

    self::assertCount(1, $this->db->rows('SELECT * FROM Poll'));
    self::assertCount(2, $this->db->rows('SELECT * FROM Answer'));
  }

  public function testCreateNewPollTransactionFail(): void
  {
    $uuid = uniqid('uuid-', false);
    $this->uniq->method('generate')->willReturn($uuid);

    $question = uniqid('q-', false);

    try {
      $this->subject->createNewPoll($question, ['a', null]);
      self::fail('PDOException should be thrown here, check answer.title not null constraint');
    } catch (Throwable $ex) {
      // ignore
    }

    $answers = $this->db->rows('SELECT * FROM Answer');

    self::assertCount(0, $answers);
    self::assertCount(0, $this->db->rows('SELECT * FROM Poll'));
  }
}
