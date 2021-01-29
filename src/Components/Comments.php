<?php


declare(strict_types=1);

namespace App\Components;

use App\Forms\CommentForm;
use App\Model\BlogModel;
use Nette\Application\UI\Control;


class Comments extends Control
{

  private BlogModel $model;
  private int $postId;
  private ?array $comments = null;


  public function __construct(BlogModel $model, int $postId)
  {
    $this->model = $model;
    $this->postId = $postId;
  }

  public function getComments() : array
  {
    if ($this->comments === null) {
      $this->comments = $this->model->getComments($this->postId);
    }

    return $this->comments;
  }

  public function handleDelete(int $id) : void
  {
    $this->model->deleteComment($id);

    $this->flashMessage('Comment deleted.', 'success');

    /* Replace $presenter->redirect() with $presenter->postGet() ... */
    $this->getPresenter()->postGet('this');

    /* Now we need to redraw a nonexistent snippet instead of the comment list.
       This is because we still want Nette to render an AJAX response at this point,
       but we know we don't want to redraw any snippets of this component and we
       shouldn't know or care if any other component will be redrawing snippets
       in reaction to us deleting the comment here - but if no component has any
       snippets marked for redrawing Nette will just render a plain text/html response
       instead of a proper AJAX response. */
    $this->redrawControl('dummy');
  }

  public function render() : void
  {
    $this->template->comments = $this->getComments();
    $this->template->setFile(__DIR__ . '/templates/comments.latte');
    $this->template->render();
  }


  public function addComment(CommentForm $form, array $values) : void
  {
    $comment = $this->model->addComment($this->postId, $values);

    $this->flashMessage('Comment added.', 'success');

    /* Same as above. */
    $this->getPresenter()->postGet('this');
    $this->redrawControl('list');

    /* This will make the component render only the newly added comment.
       Check out the getComments() method - it only loads comments from
       the model if they haven't been previously set elsewhere. */
    $this->comments = [ $comment ];
  }


  public function createComponentCommentForm() : CommentForm
  {
    $form = new CommentForm();
    $form->onSuccess[] = [$this, 'addComment'];

    return $form;
  }

}
