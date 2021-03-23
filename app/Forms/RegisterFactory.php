<?php

namespace App\Forms;

use App\Models\UserModel;

class RegisterFactory
{

    const
    FORM_MSG_REQUIRED = 'this field is required',
    FORM_MSG_EMAIL = 'invalid email address';
    
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
           
        $form->addText('nickname', 'Nickname:')
            ->addRule($form::MIN_LENGTH, 'Nickname has to be minimum of %d letters', 2)
            ->addRule(
                function ($item) {
                    if ($this->user->getNicknameValue($item->value)) {
                        return false;
                    }
                    return true;
                }, "This Nickname is already taken"
            )
            ->setRequired(self::FORM_MSG_REQUIRED)
            ->setHtmlAttribute('class', 'form-control');
        $form->addText('f_name', 'First Name:')
            ->addRule($form::MIN_LENGTH, 'Name has to be minimum of %d letters', 2)
            ->setRequired(self::FORM_MSG_REQUIRED)
            ->setHtmlAttribute('class', 'form-control');
        $form->addText('l_name', 'Last Name:')
            ->addRule($form::MIN_LENGTH, 'Name has to be minimum of %d letters', 2)
            ->setRequired(self::FORM_MSG_REQUIRED)
            ->setHtmlAttribute('class', 'form-control');
        $form->addEmail('email', 'Email:')
            ->setRequired(self::FORM_MSG_REQUIRED)
            ->addRule($form::EMAIL)
             // check unique record in form
            ->addRule(
                function ($item) {
                    if ($this->user->getValue($item->value)) {
                        return false;
                    }
                    return true;
                }, "This email already registered"
            )
            ->setHtmlAttribute('class', 'form-control');
        $form->addPassword('password', 'Password:')
            ->setRequired(self::FORM_MSG_REQUIRED)
            ->addRule($form::MIN_LENGTH, 'Password has to be minimum of %d letters', 4)
            ->addRule($form::MAX_LENGTH, 'Password has to be maximum of %d letters', 40)
            ->setHtmlAttribute('class', 'form-control');
        $form->addPassword('repassword', 'Confirm Password:')
            ->addRule($form::EQUAL, 'Password mismatch', $form['password'])
            ->setHtmlAttribute('class', 'form-control')
            ->setOmitted();
        $form->addSubmit('submit', 'Submit')
            ->setHtmlAttribute('class', 'btn btn-primary');

        return $form;
    }

}