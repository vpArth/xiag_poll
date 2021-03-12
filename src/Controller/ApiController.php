<?php

namespace Xiag\Poll\Controller;

use Xiag\Poll\Data\DataProviderInterface;

class ApiController extends BaseApiController
{
  /**
   * @var DataProviderInterface
   */
  protected $data;
  public function __construct(DataProviderInterface $data)
  {
    $this->data = $data;
  }

  public function createPoll(): void
  {
    $this->json(['status' => self::class . '::createPoll not implemented']);
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
