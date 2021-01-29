<?php


declare(strict_types=1);

namespace App\Presenters;

use App\Components\CommentCount;
use App\Components\Comments;
use App\Components\ICommentCountFactory;
use App\Components\ICommentsFactory;
use App\Components\ITagsFactory;
use App\Components\Tags;
use App\Forms\IPostFormFactory;
use App\Forms\PostForm;
use App\Model\EntryNotFoundException;


class PostPresenter extends BasePresenter
{
  private ICommentCountFactory $commentCountFactory;
  private ICommentsFactory $commentsFactory;
  private ITagsFactory $tagsFactory;
  private IPostFormFactory $postFormFactory;
  private int $postId;

  public function __construct(
    ICommentCountFactory $commentCountFactory,
    ICommentsFactory $commentsFactory,
    ITagsFactory $tagsFactory,
    IPostFormFactory $postFormFactory
  )
  {
    parent::__construct();
    $this->commentCountFactory = $commentCountFactory;
    $this->commentsFactory = $commentsFactory;
    $this->tagsFactory = $tagsFactory;
    $this->postFormFactory = $postFormFactory;
  }

  public function actionDefault(int $id) : void
  {
    $this->postId = $id;
  }


  public function actionDelete(int $id) : void
  {
    $this->model->deletePost($id);

    $this->flashMessage('Post has been deleted', 'success');

    $this->redirect('Homepage:default');
  }


  public function renderDefault(int $id) : void
  {
    try {
      $this->template->post = $this->model->getPost($id);

    } catch (EntryNotFoundException $e) {
      $this->error('Post not found');
    }
  }


  public function renderNew() : void
  {
    $this->setView('form');
  }


  public function renderEdit(int $id) : void
  {
    try {
      $post = $this->model->getPost($id);

      $this->setView('form');
      $this->getComponent('postForm')->setDefaults($this->model->getPostFormDefaults($post));
      $this->template->post = $post;

    } catch (EntryNotFoundException $e) {
      $this->error('Post not found');
    }
  }


  public function savePost(PostForm $form, array $values) : void
  {
    $post = $this->model->savePost($values);

    $this->flashMessage('Your changes have been saved', 'success');
    $this->redirect('default', ['id' => $post->id]);

  }

  public function createComponentPostForm() : PostForm
  {
    $form = $this->postFormFactory->create($this->model->getTagOptions());
    $form->onSuccess[] = [$this, 'savePost'];
    return $form;
  }

  public function createComponentCommentCount() : CommentCount
  {
    return $this->commentCountFactory->create($this->postId);
  }

  public function createComponentComments() : Comments
  {
    return $this->commentsFactory->create($this->postId);
  }

  public function createComponentTags() : Tags
  {
    return $this->tagsFactory->create($this->postId);
  }
}
