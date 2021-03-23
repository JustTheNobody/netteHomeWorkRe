<?php

declare(strict_types=1);

namespace app\Models;

use Nette\SmartObject;
use Nette\Utils\ArrayHash;
use Nette\Database\Explorer;

class ArticleModel
{
    use SmartObject;

    const
        TABLE_NAME = 'articles',
        COLUMN_ID = 'article_id',
        COLUMN_USER = 'user_id';

    public string $article = '';
    public int $article_id = 0;
    public string $title = '';
    public string $description = '';
    public string $content = '';
    public int $user_id = 0;

    private $database;

    public function __construct(Explorer $database)
    {
        $this->database = $database;
    }

    //get all Articles -> display one at home page rest in Article page
    public function getArticles()
    {
        $row = $this->database->fetchAll('SELECT * FROM articles ORDER BY article_id DESC');

        if (empty($row)) {
            return "No Article";
        }
        return $row;
    }

    //get Article with bigest ID if there any Article at all
    public function getLast()
    {
        $maxId = $this->database->fetchAll('SELECT article_id FROM articles');

        if (empty($maxId)) {
            return ["No Article"];
        } else {
            $id = $maxId[count($maxId)-1]->article_id;
            $row = $this->database->fetchAll(
                "SELECT * FROM articles
                WHERE article_id = $id"
            );
            return $row;
        }
    }

    //get Article by id
    public function getArticle($id)
    {
        return $this->database->table(self::TABLE_NAME)->where(self::COLUMN_ID, $id)->fetch();
    }

    /**
     * Save Article
     * @param array|ArrayHash $article
     */
    public function saveArticle(ArrayHash $article)
    {
        $this->title = $article->title;
        $this->description = $article->description;
        $this->content = $article->content;
        $this->user_id = $_SESSION['user_id'];

        $this->database->query(
            'INSERT INTO articles ?', [ 
            'title' => $this->title,
            'description' => $this->description,
            'content' => $this->content,
            'user_id' => $this->user_id,]
        );

        // it return's auto-increment of the inserted article
        $id = $this->database->getInsertId();

        return ($id > 0) ? "success" : "fail";
    }

    /**
     * Remove Article with given ID.
     */
    public function removeArticle($article_id, $user_id)
    {
        $this->article_id = intval($article_id);
        $this->user_id = intval($user_id);

        $query = $this->database->query(
            'DELETE FROM articles WHERE ?', [
            self::COLUMN_ID => $this->article_id,
            self::COLUMN_USER => $this->user_id]
        );
        
        return ($query->getRowCount() !== 1) ? "fail" : "success";
    }

    /**
     * Update Article with given ID.
     */
    public function updateArticle(object $values)
    {
        $query = $this->database->query(
            'UPDATE articles SET', [
            'title' => $values['title'],
            'content' => $values['content'],
            'description' => $values['description']
            ], 'WHERE article_id = ?', $values['article_id']
        );
      
        return ($query->getRowCount() !== 1) ? "fail" : "success";
    }
}
