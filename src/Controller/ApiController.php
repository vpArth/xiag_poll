<?php

namespace Xiag\Poll\Controller;

use Xiag\Poll\Data\DataProviderInterface;
use Xiag\Poll\Exception\AppException;
use Xiag\Poll\Util\RequestInterface;
use function array_filter;
use function array_map;
use function trim;

class ApiController extends BaseApiController
{
  public const ERROR_INVALID_QUESTION = 'Field question is empty';
  public const ERROR_INVALID_ANSWERS  = 'There must be at least two possible non-empty answers';
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
    $question = $request->get('question');

    if (empty($question)) {
      throw new AppException(self::ERROR_INVALID_QUESTION);
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
  public function submitVote(): void
  {
    $this->json(['status' => self::class . '::submitVote not implemented']);
  }
  public function results(string $uuid): void
  {
    $this->json([
        'status' => self::class . '::results not implemented',
        'uuid' => $uuid,
    ]);
  }
}
