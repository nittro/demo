<?php

declare(strict_types=1);

namespace App\Model\Events;


class PostDeletedEvent
{
  private object $post;

  public function __construct(object $post)
  {
    $this->post = $post;
  }

  public function getPost() : object
  {
    return $this->post;
  }
}
