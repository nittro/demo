<?php


declare(strict_types=1);

namespace App\Components;

use App\Models\BlogModel;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;


class Comments extends Control {

    /** @var BlogModel */
    private $model;

    /** @var int */
    private $postId;

    /** @var array */
    private $comments = null;


    public function __construct(BlogModel $model, int $postId) {
        parent::__construct();

        $this->model = $model;
        $this->postId = $postId;
    }

    public function getComments() : array {
        if ($this->comments === null) {
            $this->comments = $this->model->getComments($this->postId);
        }

        return $this->comments;
    }

    public function handleDelete(int $id) {
        $this->model->deleteComment($id);

        $this->flashMessage('Comment deleted.', 'success');
        $this->getPresenter()->redirect('this');
    }

    public function render() {
        $this->template->comments = $this->getComments();
        $this->template->setFile(__DIR__ . '/templates/comments.latte');
        $this->template->render();
    }


    public function doAddComment(Form $form, array $values) {
        $this->model->addComment($this->postId, $values);

        $this->flashMessage('Comment added.', 'success');
        $this->getPresenter()->redirect('this');
    }


    public function createComponentCommentForm() : Form {
        $form = new Form();

        $form->addText('author_name', 'Your name:')
            ->setRequired(true);

        $form->addEmail('author_email', 'Your email:')
            ->setRequired(true);

        $form->addTextArea('content', 'Your comment:')
            ->setRequired(true);

        $form->addSubmit('add', 'Add');

        $form->onSuccess[] = [$this, 'doAddComment'];

        return $form;
    }

}
