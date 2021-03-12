<?php

namespace Xiag\Poll\Controller;

use Xiag\Poll\Data\DataProviderInterface;
use Xiag\Poll\Exception\AppException;
use Xiag\Poll\Util\RequestInterface;
use function array_filter;
use function array_map;
use function preg_match;
use function sprintf;
use function trim;

class ApiController extends BaseApiController
{
  public const ERROR_EMPTY_FIELD = 'Field %s is empty';
  public const ERROR_INVALID_ANSWERS  = 'There must be at least two possible non-empty answers';
  public const ERROR_INVALID_CHARS_IN_FIELD  = 'Field %s=%s contains bad characters';

  /**
   * @var DataProviderInterface
   */
  protected $data;
  public function __construct(DataProviderInterface $data)
  {
    $this->data = $data;
  }

  public function createPoll(RequestInterface $request): void
  {
    $question = trim($request->get('question'));

    if (empty($question)) {
      throw new AppException(sprintf(self::ERROR_EMPTY_FIELD, 'question'));
    }

    $answers = $request->get('answers', []);
    $answers = array_map(static function ($answer) {
      return trim($answer);
    }, $answers);
    $answers = array_filter($answers);

    if (empty($answers) || count($answers) < 2) {
      throw new AppException(self::ERROR_INVALID_ANSWERS);
    }

    $pollData = $this->data->createNewPoll($question, $answers);

    $this->json($pollData);
  }

  public function submitVote(RequestInterface $request): void
  {
    $answer_id = (int) $request->get('answer_id');
    if (empty($answer_id)) {
      throw new AppException(sprintf(self::ERROR_EMPTY_FIELD, 'answer_id'));
    }
    $username = trim($request->get('username'));
    if (empty($username)) {
      throw new AppException(sprintf(self::ERROR_EMPTY_FIELD, 'username'));
    }
    if (preg_match('/[^\p{L}\d\s_-]/ui', $username)) {
      throw new AppException(sprintf(self::ERROR_INVALID_CHARS_IN_FIELD, 'username', $username));
    }

    $vote = $this->data->vote($answer_id, $username);

    $this->json($vote);
  }

  public function results(string $uuid): void
  {
    $results = $this->data->getResults($uuid);

    $this->json($results);
  }
}
