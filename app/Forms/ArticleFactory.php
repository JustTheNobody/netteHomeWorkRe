<?php

namespace App\Forms;

class ArticleFactory
{

    const
    FORM_MSG_REQUIRED = 'this field is required';
    
    public CustomFormFactory $forms;

    public function __construct(CustomFormFactory $forms)
    {
        $this->forms = $forms;
    }

    public function renderForm($article)
    {
        $form = $this->forms->create();
                
        $form->addHidden('user_id', $_SESSION['user_id']);
        $form->addHidden('article_id', (is_array($article) && !empty($article))? $article['article_id'] : '');
    
        $form->addText('title', 'Title')
            ->setValue((is_array($article) && !empty($article))? $article['title'] : '')
            ->setRequired(self::FORM_MSG_REQUIRED)
            ->addRule($form::MIN_LENGTH, 'Title has to be minimum of %d letters', 5)
            ->addRule($form::MAX_LENGTH, 'Title has to be maximum of %d letters', 25);

        $form->addTextArea('content', 'Content')
            ->setValue((is_array($article) && !empty($article))? $article['content'] : '')
            ->setHtmlAttribute('rows', 10)
            ->setHtmlAttribute('cols', 40)
            ->setRequired(self::FORM_MSG_REQUIRED)
            ->addRule($form::MIN_LENGTH, 'Content has to be minimum of %d letters', 30);
            
        $form->addText('description', 'Description')
            ->setValue((is_array($article) && !empty($article))? $article['description'] : '')
            ->setRequired(self::FORM_MSG_REQUIRED)
            ->addRule($form::MIN_LENGTH, 'Title has to be minimum of %d letters', 5)
            ->addRule($form::MAX_LENGTH, 'Title has to be maximum of %d letters', 25);

        $form->addSubmit('submit', (is_array($article) && !empty($article))? 'Update Article' : 'Add Article');
        $form->setHtmlAttribute('class', 'updateForm');

        return $form;
    }

}