<?php

declare(strict_types=1);

namespace App\Presenters;


class HomepagePresenter extends BasePresenter {

    public function renderDefault() {
        $this->template->lastPosts = $this->model->getLatestPosts();
    }

}
