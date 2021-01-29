<?php

declare(strict_types=1);

namespace App\Model\Events;


class CommentDeletedEvent
{
  private object $comment;

  public function __construct(object $comment)
  {
    $this->comment = $comment;
  }

  public function getComment() : object
  {
    return $this->comment;
  }
}
