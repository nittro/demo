<?php

declare(strict_types=1);

namespace App\Components;

interface ICommentsFactory
{
  public function create(int $postId) : Comments;
}
