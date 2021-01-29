<?php

declare(strict_types=1);

namespace App\Components;

use App\Model\BlogModel;
use Nette\Application\UI\Control;

class CommentCount extends Control
{
  private BlogModel $model;
  private int $postId;
  private ?int $comments = null;

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
}
