<?php

namespace Xiag\Poll\Controller;

use Twig\Environment;
use Xiag\Poll\Data\DataProviderInterface;
use Xiag\Poll\Data\DBException;

class UIController
{
  const PAGE_NAME_POLL  = 'Create Poll page';
  const PAGE_NAME_VOTE  = 'Vote page';
  const PAGE_TITLE_POLL = 'Enter data';
  const PAGE_TITLE_VOTE = 'Select answer';
  /**
   * @var Environment
   */
  protected $twig;

  /**
   * @var DataProviderInterface
   */
  protected $data;

  public function __construct(Environment $twig, DataProviderInterface $data)
  {
    $this->twig = $twig;
    $this->data = $data;
  }
  public function createPollPage(): void
  {
    echo $this->twig->render('create_poll.twig', [
        'name'  => self::PAGE_NAME_POLL,
        'title' => self::PAGE_TITLE_POLL,
    ]);
  }
  public function votePage(string $uuid): void
  {
    $tplData = [
        'name'  => self::PAGE_NAME_VOTE,
        'title' => self::PAGE_TITLE_VOTE,
    ];
    try {
      $poll            = $this->data->findPoll($uuid);
      $tplData['poll'] = $poll;

      $page = $this->twig->render('vote.twig', $tplData);
    } catch (DBException $ex) {
      $page = $this->twig->render('not_found.twig', [
              'title'   => 'Not Found',
              'message' => $ex->getMessage(),
          ] + $tplData);
    }

    echo $page;
  }
}

