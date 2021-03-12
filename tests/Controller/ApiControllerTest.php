<?php

namespace Tests\Controller;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Xiag\Poll\Controller\ApiController;
use Xiag\Poll\Data\DataProviderInterface;
use Xiag\Poll\Exception\AppException;
use Xiag\Poll\Util\RequestInterface;
use function json_decode;
use function ob_get_clean;
use function ob_start;
use function random_int;
use function sprintf;
use function uniqid;

class ApiControllerTest extends TestCase
{
  /**
   * @var ApiController
   */
  private $subject;

  /**
   * @var MockObject|DataProviderInterface
   */
  private $dp;

  /**
   * @var MockObject|RequestInterface
   */
  private $request;

  protected function setUp(): void
  {
    $this->request = $this->createMock(RequestInterface::class);
    $this->dp      = $this->createMock(DataProviderInterface::class);

    $this->subject = new ApiController($this->dp);
  }

  public function testCreatePoll(): void
  {
    $data = [
        'question' => uniqid('q-', false),
        'answers'  => [
            uniqid('a-', false),
            uniqid('a-', false),
        ],
    ];
    $this->request->method('get')
        ->willReturnCallback(static function ($key, $default = null) use ($data) {
          return $data[$key] ?? $default;
        });

    $response = $data;
    $id       = uniqid('id_', false);
    $uuid     = uniqid('uuid_', false);
    $a1_id    = uniqid('aid_', false);
    $a2_id    = uniqid('aid_', false);

    $response['id']      = $id;
    $response['uuid']    = $uuid;
    $response['answers'] = [
        ['id' => $a1_id, 'id_poll' => $id, 'title' => $data['answers'][0]],
        ['id' => $a2_id, 'id_poll' => $id, 'title' => $data['answers'][1]],
    ];
    $this->dp->expects(self::once())
        ->method('createNewPoll')
        ->with($data['question'], $data['answers'])
        ->willReturn($response);

    ob_start();
    $this->subject->createPoll($this->request);
    $echo = ob_get_clean();

    self::assertEquals($response, json_decode($echo, true));
  }
  public function testCreatePollNoQuestion(): void
  {
    $data = [
        'title'   => 'Isn\'t it?',
        'answers' => ['Yes', 'No'],
    ];
    $this->request->method('get')
        ->willReturnCallback(static function ($key, $default = null) use ($data) {
          return $data[$key] ?? $default;
        });

    $this->expectException(AppException::class);
    $this->expectExceptionMessage(sprintf(ApiController::ERROR_EMPTY_FIELD, 'question'));

    $this->subject->createPoll($this->request);
  }
  public function testCreatePollNoAnswers(): void
  {
    $data = [
        'question' => 'Isn\'t it?',
        'answers'  => [],
    ];
    $this->request->method('get')
        ->willReturnCallback(static function ($key, $default = null) use ($data) {
          return $data[$key] ?? $default;
        });

    $this->expectException(AppException::class);
    $this->expectExceptionMessage(ApiController::ERROR_INVALID_ANSWERS);

    $this->subject->createPoll($this->request);
  }

  public function testSubmitVote(): void
  {
    $data = [
        'answer_id' => random_int(501, 599),
        'username'  => uniqid('user_', false),
    ];
    $this->request->method('get')
        ->willReturnCallback(static function ($key, $default = null) use ($data) {
          return $data[$key] ?? $default;
        });

    $response       = $data;
    $response['id'] = uniqid('id_', false);

    $this->dp->expects(self::once())
        ->method('vote')
        ->with($data['answer_id'], $data['username'])
        ->willReturn($response);

    ob_start();
    $this->subject->submitVote($this->request);
    $echo = ob_get_clean();

    self::assertEquals($response, json_decode($echo, true));
  }
  public function testSubmitVoteNoAnswer(): void
  {
    $data = [
        'answer_id' => 'none',
        'username'  => uniqid('user_', false),
    ];
    $this->request->method('get')
        ->willReturnCallback(static function ($key, $default = null) use ($data) {
          return $data[$key] ?? $default;
        });

    $this->expectException(AppException::class);
    $this->expectExceptionMessage(sprintf(ApiController::ERROR_EMPTY_FIELD, 'answer_id'));

    $this->subject->submitVote($this->request);
  }
  public function testSubmitVoteNoUsername(): void
  {
    $data = [
        'answer_id' => 1,
        'username'  => "   \t  ",
    ];
    $this->request->method('get')
        ->willReturnCallback(static function ($key, $default = null) use ($data) {
          return $data[$key] ?? $default;
        });

    $this->expectException(AppException::class);
    $this->expectExceptionMessage(sprintf(ApiController::ERROR_EMPTY_FIELD, 'username'));

    $this->subject->submitVote($this->request);
  }
}