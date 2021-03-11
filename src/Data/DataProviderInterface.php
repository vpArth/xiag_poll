<?php

namespace Xiag\Poll\Data;

interface DataProviderInterface
{
  public function createNewPoll(string $question, array $answers): array;
  public function findPoll(string $uuid): array;
  public function vote(int $answer_id, string $username): array;
}
