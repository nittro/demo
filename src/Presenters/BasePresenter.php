<?php

declare(strict_types=1);

namespace App\Presenters;

use App\Model\BlogModel;
use Nittro\Bridges\NittroUI\Presenter;


class BasePresenter extends Presenter
{
  protected BlogModel $model;

  public function injectBase(BlogModel $model) : void
  {
    $this->model = $model;
  }

  protected function startup()
  {
    parent::startup();

    $this->setDefaultSnippets(['header', 'content']);
  }
}
