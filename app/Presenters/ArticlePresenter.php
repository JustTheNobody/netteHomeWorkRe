<?php

declare(strict_types=1);

namespace App\Presenters;

use App\Models\UserModel;
use Nette\Utils\ArrayHash;
use App\Forms\ArticleFactory;
use app\Models\ArticleModel;
use Nette\Application\UI\Form;
use Nette\Application\UI\Presenter;
use Nette\Forms\Controls\HiddenField;
use Nette\Application\BadRequestException;

/**
 * Article Presenter
 * @package App\Presenters
 */
final class ArticlePresenter extends Presenter
{
    const
    FORM_MSG_REQUIRED = 'This field is required';

    public UserModel $user;

    public string $status = '';
    //Home page article => last added -> by id?
    private $defaultArticleId;
    public $result = '';

    /** @var ArticleModel Article Model. */
    private $articleModel;

    //for edit/delete
    public string $article = '';
    public int $article_id = 0;
    public string $title = '';
    public string $description = '';
    public string $content = '';
    public int $user_id = 0;
    
    public array $articles = [];
    public ArticleFactory $forms;

     /**
     * Construct with default Article id
     * @param int $defaultArticleId Article ID
     * @param ArticleModel $articleModel   Article Model
     */
    public function __construct(
        string $defaultArticleId = null,
        ArticleModel $articleModel,
        UserModel $user,
        ArticleFactory $forms
    ) {
        parent::__construct();
        $this->defaultArticleId = $defaultArticleId;
        $this->articleModel = $articleModel;
        $this->user = $user;
        $this->forms = $forms;

        isset($_SESSION['user_id'])? $this->user_id = $_SESSION['user_id'] : 0;
    }

    public function checkAuth()
    {
        //check if user loged
        if ($this->user_id == 0) {
            $this->flashMessage('Sorry, it look like you are not loged in.', 'alert');
            $this->redirect('Login:default');
        }
    }

    /**
     * Read the Default Article template.
     * @param string|null $id Article id
     * @throws BadRequestException if not found
     */
    public function renderDefault()
    {
        $articles = $this->articleModel->getArticles();

        // Read the Articles -> 404 if not found.
        if ($articles == 'No Article') {
            $this->flashMessage('There are not any articles in here yet.', 'fail');
        }

        $this->template->articles = $articles; // Send to template.
    }

    public function handleDelete($article_id, $user_id)
    {
        $result = $this->articleModel->removeArticle($article_id, $user_id);

        if ($result == "success") {
            $this->status = "success";
            $this->flashMessage('Article has been deleted.', 'success');
        } else {
            //redirect
            $this->status = "fail";
            $this->flashMessage('Sorry, there was a unexpected error in deleting the Article.', 'fail');
        }
        $this->redirect('Article:default');
    }
    
    /**
     * Add the Article section
     */
    public function renderAdd()
    {
        //check if loged in -> if not redirect
        $this->checkAuth();
        $this->articleModel->getArticles();
    }

    protected function createComponentArticleForm()
    {
        $form = $this->forms->renderForm("");
        $form->onSuccess[] = [$this, 'articleFormSucceeded'];
        return $form;
    }

    public function articleFormSucceeded(ArrayHash $values)
    {
        $result = $this->articleModel->saveArticle($values);

        if ($result == "success") {
            //redirect 2 userPage
            $this->flashMessage('Article has been saved.', 'success');
            $this->redirect('Article:default');
        } else {
            //redirect
            $this->flashMessage('Sorry, there was a unexpected error in saving the Article.', 'fail');
            $this->redirect('Article:add');
        }
    }

    /**
     * Edit the Article section
     */
    public function renderEdit(array $article)
    {
        $this->articles = $article;
        $this->template->article = $this->article; // Send to template.
    }

    protected function createComponentEditForm()
    {
        $form = $this->forms->renderForm($this->articles);
        $form->onSuccess[] = [$this, 'editFormSucceeded'];
        return $form;
    }

    public function editFormSucceeded(ArrayHash $article)
    {
   
        $result = $this->articleModel->updateArticle($article);

        if ($result == "success") {
            //redirect 2 userPage
            $this->status = "success";
            $this->flashMessage('Article has been updated.', 'success');
        } else {
            //redirect
            $this->status = "fail";
            $this->flashMessage('Sorry, there was a unexpected error in updating of the Article.', 'fail');
        }
        $this->redirect('Article:default');
    }
}
