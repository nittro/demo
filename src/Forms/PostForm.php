<?php

declare(strict_types=1);

namespace App\Forms;

use Nette\Application\UI\Form;

class PostForm extends Form
{

  public function __construct(array $tags)
  {
    parent::__construct();

    $this->addText('title', 'Title:')
      ->setRequired(true);

    $this->addUpload('picture', 'Picture:')
      ->setRequired(false)
      ->addCondition(Form::FILLED)
      ->addRule(Form::IMAGE);

    $this->addTextArea('content', 'Content:', 80, 10)
      ->setRequired(true);

    $this->addCheckboxList('tags', 'Tags:', $tags);

    $this->addHidden('id');

    $this->addSubmit('save', 'Save');
  }

}
