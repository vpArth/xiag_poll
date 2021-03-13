<?php

namespace Tests\Controller;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Twig\Environment;
use Xiag\Poll\Controller\UIController;
use Xiag\Poll\Data\DataProviderInterface;
use Xiag\Poll\Data\DBException;
use function uniqid;

class UIControllerTest extends TestCase
{
  /**
   * @var MockObject|Environment
   */
  private $twig;
  /**
   * @var MockObject|DataProviderInterface
   */
  private $dp;
  /**
   * @var UIController
   */
  private $subject;

  protected function setUp(): void
  {
    $this->twig = $this->createMock(Environment::class);
    $this->dp   = $this->createMock(DataProviderInterface::class);

    $this->subject = new UIController($this->twig, $this->dp);
  }

  public function testCreatePollPage(): void
  {
    $this->twig->expects(self::once())
        ->method('render')
        ->with('create_poll.twig', [
            'name'  => UIController::PAGE_NAME_POLL,
            'title' => UIController::PAGE_TITLE_POLL,
        ]);

    $this->subject->createPollPage();
  }

  public function testVotePage(): void
  {
    $uuid = uniqid('uuid-', false);
    $poll = [uniqid('polldata-', false)];

    $this->dp->expects(self::once())->method('findPoll')->with($uuid)->willReturn($poll);
    $this->twig->expects(self::once())
        ->method('render')
        ->with('vote.twig', [
            'name'  => UIController::PAGE_NAME_VOTE,
            'title' => UIController::PAGE_TITLE_VOTE,
            'poll'  => $poll,
        ]);

    $this->subject->votePage($uuid);
  }

  public function testVotePageNotFound(): void
  {
    $uuid = uniqid('uuid-', false);
    $msg  = uniqid('Not Found message: ', false);

    $this->dp->expects(self::once())->method('findPoll')->with($uuid)
        ->willThrowException(new DBException($msg));

    $this->twig->expects(self::once())
        ->method('render')
        ->with('not_found.twig', [
            'name'    => UIController::PAGE_NAME_VOTE,
            'title'   => 'Not Found',
            'message' => $msg,
        ]);

    $this->subject->votePage($uuid);
  }
}
