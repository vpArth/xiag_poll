<?php

namespace Tests\Data;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Xiag\Poll\Data\CrudDbInterface;
use Xiag\Poll\Data\DataProvider;
use Xiag\Poll\Util\UniqIdGenInterface;
use function random_int;
use function uniqid;

class DataProviderTest extends TestCase
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

  private $testData;

  protected function setUp(): void
  {
    $this->crud    = $this->createMock(CrudDbInterface::class);
    $this->uniq    = $this->createMock(UniqIdGenInterface::class);
    $this->subject = new DataProvider($this->crud, $this->uniq);

    $this->testData = [
        'ok' => [
            'poll'    => [
                'id'       => random_int(100, 199),
                'uuid'     => uniqid('uuid-', false),
                'question' => uniqid('question-', false),
            ],
            'answers' => [
                [
                    'id'    => random_int(200, 299),
                    'title' => uniqid('answer-', false),
                ],
                [
                    'id'    => random_int(300, 399),
                    'title' => uniqid('answer-', false),
                ],
            ],
        ],
    ];
  }

  public function testCreateNewPoll(): void
  {
    $data = $this->testData['ok'];

    $id_poll  = $data['poll']['id'];
    $uuid     = $data['poll']['uuid'];
    $question = $data['poll']['question'];

    $id_answer_1 = $data['answers'][0]['id'];
    $answer1     = $data['answers'][0]['title'];

    $id_answer_2 = $data['answers'][1]['id'];
    $answer2     = $data['answers'][1]['title'];

    $this->uniq->method('generate')->willReturn($uuid);

    $this->crud->expects(self::exactly(3))
        ->method('insert')
        ->withConsecutive(...[
            ['Poll', ['question' => $question, 'uuid' => $uuid]],
            ['Answer', ['title' => $answer1, 'id_poll' => $id_poll]],
            ['Answer', ['title' => $answer2, 'id_poll' => $id_poll]],
        ])
        ->willReturnOnConsecutiveCalls($id_poll, $id_answer_1, $id_answer_2);

    $poll = $this->subject->createNewPoll($question, [$answer1, $answer2]);

    self::assertEquals([
        'id'       => $id_poll,
        'uuid'     => $uuid,
        'question' => $question,
        'answers'  => [
            ['id' => $id_answer_1, 'id_poll' => $id_poll, 'title' => $answer1],
            ['id' => $id_answer_2, 'id_poll' => $id_poll, 'title' => $answer2],
        ],
    ], $poll);
  }
  /** @group ignore */
  public function testFindPoll(): void
  {
  }
  /** @group ignore */
  public function testVote(): void
  {
  }
}