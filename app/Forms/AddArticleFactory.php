<?php

namespace App\Forms;

use App\Models\UserModel;

class AddArticleFactory
{

    const
    FORM_MSG_REQUIRED = 'this field is required';
    
    public CustomFormFactory $forms;
    public UserModel $user;

    public function __construct(UserModel $user, CustomFormFactory $forms)
    {
        $this->user = $user;
        $this->forms = $forms;
    }

    public function renderForm()
    {
        $form = $this->forms->create();

        $form->addText('f_name', 'First Name:')
            ->setRequired(self::FORM_MSG_REQUIRED)
            ->addRule($form::MIN_LENGTH, 'Name has to be minimum of %d letters', 2)
            ->setHtmlAttribute('class', 'form-control');
        $form->addPassword('password', 'Password:')
            ->setRequired(self::FORM_MSG_REQUIRED)
            ->setHtmlAttribute('class', 'form-control');
        $form->addSubmit('submit', 'Submit')
            ->setHtmlAttribute('class', 'btn btn-primary');
        return $form;
    }

}