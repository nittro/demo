<?php

declare(strict_types=1);

namespace App\Presenters;

use App\Model\BlogModel;
use Nette\Application\UI\Presenter;


class BasePresenter extends Presenter
{
  protected BlogModel $model;

  public function injectBase(BlogModel $model) : void
  {
    $this->model = $model;
  }
}
