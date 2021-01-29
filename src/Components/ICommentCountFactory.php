<?php

declare(strict_types=1);

namespace App\Components;

interface ICommentCountFactory
{
  public function create(int $postId) : CommentCount;
}
