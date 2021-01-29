<?php


declare(strict_types=1);

namespace App\Components;

use App\Model\BlogModel;
use Nette\Application\UI\Control;


class Tags extends Control
{
  private BlogModel $model;
  private int $postId;
  private ?array $tags = null;


  public function __construct(BlogModel $model, int $postId)
  {
    $this->model = $model;
    $this->postId = $postId;
  }


  public function getTags() : array
  {
    if ($this->tags === null) {
      $this->tags = $this->model->getTags($this->postId);
    }

    return $this->tags;
  }

  public function render() : void
  {
    $this->template->tags = $this->getTags();
    $this->template->setFile(__DIR__ . '/templates/tags.latte');
    $this->template->render();
  }
}
