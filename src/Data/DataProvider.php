<?php

namespace Xiag\Poll\Data;

use Throwable;
use Xiag\Poll\Util\UniqIdGenInterface;
use function array_filter;
use function explode;
use function mb_strpos;
use function sprintf;

class DataProvider implements DataProviderInterface
{
  public const ERROR_WRONG_USERNAME   = 'Comma is forbidden character for username';
  public const ERROR_ENTITY_NOT_FOUND = '%s#%s not found';

  /**
   * @var CrudDbInterface
   */
  protected $crud;

  /**
   * @var UniqIdGenInterface
   */
  protected $uniqIdGen;
  /**
   * @var SqlDbInterface
   */
  protected $db;

  public function __construct(CrudDbInterface $crud, SqlDbInterface $db, UniqIdGenInterface $uniqIdGen)
  {
    $this->crud = $crud;
    $this->db   = $db;

    $this->uniqIdGen = $uniqIdGen;
  }

  public function createNewPoll(string $question, array $answers): array
  {
    $uuidCode = $this->uniqIdGen->generate();

    $poll = [
        'question' => $question,
        'uuid'     => $uuidCode,
    ];

    $this->db->beginTransaction();
    try {
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
      $this->db->commit();
    } catch (Throwable $ex) {
      $this->db->rollBack();
      throw $ex;
    }

    return $poll;
  }
  public function findPoll(string $uuid): array
  {
    $result = $this->crud->find('Poll', [
        'uuid' => $uuid,
    ]);
    if (!$result) {
      throw new DBException(sprintf(self::ERROR_ENTITY_NOT_FOUND, 'Poll', $uuid));
    }

    $result['answers'] = $this->crud->find('Answer', [
        'id_poll' => $result['id'],
    ], 'rows');

    return $result;
  }
  public function vote(int $answer_id, string $username): array
  {
    if (mb_strpos($username, ',')) {
      // if you need to allow comma, change separator in GROUP_CONCAT of getResults method
      throw new DBException(self::ERROR_WRONG_USERNAME);
    }
    $vote = [
        'id_answer' => $answer_id,
        'username'  => $username,
    ];
    try {
      $vote['id'] = $this->crud->insert('Vote', $vote);
    } catch (DBException $ex) {
      throw new DBException(sprintf(self::ERROR_ENTITY_NOT_FOUND, 'Answer', $answer_id), 404, $ex);
    }

    return $vote;
  }

  /*
   * note:
   * There are GROUP_CONCAT function in both SQLite and MySQL RDBMS
   * But if other need to be supported some abstraction layer will be required
   * Platform-dependent implementations of SqlDbInterface would be sufficient for that
   *
   * Default separator(comma) is used, because it is forbidden in validation
   */
  public const SQL_RESULTS = <<<SQL
SELECT a.id, a.title, GROUP_CONCAT(v.username) usernames
FROM Poll p
JOIN Answer a ON a.id_poll = p.id
LEFT JOIN Vote v ON v.id_answer = a.id
WHERE p.uuid = ?
GROUP BY a.id, a.title
SQL;

  public function getResults(string $uuid): array
  {
    $data = $this->db->rows(self::SQL_RESULTS, [$uuid]);
    if (empty($data)) {
      throw new DBException(sprintf(self::ERROR_ENTITY_NOT_FOUND, 'Poll', $uuid));
    }

    foreach ($data as &$row) {
      $row['usernames'] = array_filter(explode(',', $row['usernames']));
    }
    unset($row);

    return $data;
  }
}
