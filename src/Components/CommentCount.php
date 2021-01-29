<?php

declare(strict_types=1);

namespace App\Components;

use App\Model\BlogModel;
use App\Model\Events\CommentCreatedEvent;
use App\Model\Events\CommentDeletedEvent;
use Nette\Application\UI\Control;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CommentCount extends Control implements EventSubscriberInterface
{
  private BlogModel $model;
  private int $postId;
  private ?int $comments = null;

  public static function getSubscribedEvents() : array
  {
    return [
      CommentCreatedEvent::class => 'refresh',
      CommentDeletedEvent::class => 'refresh',
    ];
  }


  public function __construct(BlogModel $model, int $postId)
  {
    $this->model = $model;
    $this->postId = $postId;
  }

  public function getCommentCount() : int
  {
    if ($this->comments === null) {
      $this->comments = $this->model->countComments($this->postId);
    }

    return $this->comments;
  }

  public function render() : void
  {
    $this->template->count = $this->getCommentCount();
    $this->template->setFile(__DIR__ . '/templates/commentCount.latte');
    $this->template->render();
  }

  public function refresh() : void
  {
    $this->redrawControl();
  }
}
