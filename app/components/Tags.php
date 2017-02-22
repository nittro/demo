<?php


declare(strict_types=1);

namespace App\Components;

use App\Models\BlogModel;
use Nette\Application\UI\Control;


class Tags extends Control {

    /** @var BlogModel */
    private $model;

    /** @var int */
    private $postId;

    /** @var array */
    private $tags = null;


    public function __construct(BlogModel $model, int $postId) {
        parent::__construct();

        $this->model = $model;
        $this->postId = $postId;
    }


    public function getTags() : array {
        if ($this->tags === null) {
            $this->tags = $this->model->getTags($this->postId);
        }

        return $this->tags;
    }

    public function render() {
        $this->template->tags = $this->getTags();
        $this->template->setFile(__DIR__ . '/templates/tags.latte');
        $this->template->render();
    }

}
