<?php

declare(strict_types=1);

namespace App\Forms;

use Nette\Application\UI\Form;

class CommentForm extends Form
{
  public function __construct()
  {
    parent::__construct();

    $this->addText('author_name', 'Your name:')
      ->setRequired(true);

    $this->addEmail('author_email', 'Your email:')
      ->setRequired(true);

    $this->addTextArea('content', 'Your comment:')
      ->setRequired(true);

    $this->addSubmit('add', 'Add');
  }
}
