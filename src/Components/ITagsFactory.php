<?php

declare(strict_types=1);

namespace App\Components;

interface ITagsFactory
{
  public function create(int $postId) : Tags;
}
