<?php

declare(strict_types=1);

namespace App\Forms;

interface IPostFormFactory
{
  public function create(array $tags) : PostForm;
}
