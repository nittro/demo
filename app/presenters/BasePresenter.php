<?php

declare(strict_types=1);

namespace App\Presenters;

use App\Models\BlogModel;
use Nittro\Bridges\NittroUI\Presenter;


class BasePresenter extends Presenter {

    /** @var BlogModel */
    protected $model;

    public function injectBase(BlogModel $model) {
        $this->model = $model;
    }

    protected function startup() {
        parent::startup();
        $this->setDefaultSnippets(['header', 'content']);

    }


}
