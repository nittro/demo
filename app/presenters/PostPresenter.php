<?php


declare(strict_types=1);

namespace App\Presenters;

use App\Components\Comments;
use App\Components\Tags;
use Nette\Application\UI\Form;


class PostPresenter extends BasePresenter {

    /** @var int */
    private $postId;


    public function actionDefault(int $id) {
        $this->postId = $id;
    }



    public function actionDelete(int $id) {
        $this->model->deletePost($id);

        $this->flashMessage('Post has been deleted', 'success');

        $this->redirect('Homepage:default');
    }




    public function renderDefault(int $id) {
        try {
            $this->template->post = $this->model->getPost($id);

        } catch (\Exception $e) {
            $this->error('Post not found');

        }
    }



    public function renderNew() {
        $this->setView('form');

        /*
            Only redraw the main content snippet which will be shown
            inside a dialog. We could redraw other snippets here,
            but in this scenario it doesn't make much sense.
         */
        $this->redrawControl('content');
    }



    public function renderEdit(int $id) {
        try {
            $post = $this->model->getPost($id);

            $this->setView('form');
            $this->getComponent('postForm')->setDefaults($this->model->getPostFormDefaults($post));
            $this->template->post = $post;

            /*
                Same as above.
             */
            $this->redrawControl('content');

        } catch (\Exception $e) {
            $this->error('Post not found');

        }
    }



    public function doSavePost(Form $form, array $values) {
        $post = $this->model->savePost($values);

        $this->flashMessage('Your changes have been saved', 'success');

        /*
            Close the dialog if it was open.
         */
        $this->closeDialog('post');

        $this->redirect('default', ['id' => $post->id]);
    }

    public function createComponentPostForm() : Form {
        $form = new Form();

        $form->addText('title', 'Title:')
            ->setRequired(true);

        $form->addUpload('picture', 'Picture:')
            ->setRequired(false)
            ->addCondition(Form::FILLED)
            ->addRule(Form::IMAGE);

        $form->addTextArea('content', 'Content:', 80, 10)
            ->setRequired(true);

        $tags = $this->model->getTagOptions();
        $form->addCheckboxList('tags', 'Tags:', $tags);

        $form->addHidden('id');

        $form->addSubmit('save', 'Save');

        $form->onSuccess[] = [$this, 'doSavePost'];

        return $form;
    }

    public function createComponentComments() : Comments {
        return new Comments($this->model, $this->postId);
    }

    public function createComponentTags() : Tags {
        return new Tags($this->model, $this->postId);
    }
}
