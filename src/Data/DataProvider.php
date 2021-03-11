<?php

namespace Xiag\Poll\Data;

use Xiag\Poll\Exception\AppException;
use Xiag\Poll\Util\UniqIdGenInterface;

class DataProvider implements DataProviderInterface
{
  /**
   * @var CrudDbInterface
   */
  protected $crud;

  /**
   * @var UniqIdGenInterface
   */
  protected $uniqIdGen;

  public function __construct(CrudDbInterface $crud, UniqIdGenInterface $uniqIdGen)
  {
    $this->crud      = $crud;
    $this->uniqIdGen = $uniqIdGen;
  }

  public function createNewPoll(string $question, array $answers): array
  {
    $uuidCode = $this->uniqIdGen->generate();

    $poll = [
        'question' => $question,
        'uuid'     => $uuidCode,
    ];

    $poll['id'] = $this->crud->insert('Poll', $poll);

    $poll['answers'] = [];
    foreach ($answers as $answerTitle) {
      $answer = [
          'id_poll' => $poll['id'],
          'title'   => $answerTitle,
      ];

      $answer['id'] = $this->crud->insert('Answer', $answer);

      $poll['answers'][] = $answer;
    }

    return $poll;
  }
  public function findPoll(string $uuid): array
  {
    $result = $this->crud->find('Poll', [
        'uuid' => $uuid,
    ]);
    if (!$result) {
      throw new AppException("Poll#{$uuid} not found");
    }

    $result['answers'] = $this->crud->find('Answer', [
        'id_poll' => $result['id'],
    ], 'rows');

    return $result;
  }
  public function vote(int $answer_id, string $username): array
  {
    throw new AppException('Not implemented yet');
  }
}
