<?php

namespace app\modules\admin\controllers;

use app\models\Comments;
use Yii;
use yii\web\Controller;
use yii\web\Response;

/**
 * Default controller for the `admin` module
 */
class DefaultController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionStatistic()
    {

        return $this->render('statistic');
    }

    public function actionFbcomments()
    {
        return $this->render('fbcomments');
    }

    public function actionComment()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $comment_id = $_POST['comment-id'];
        $com = Comments::find()->where(['source_id' => $comment_id])->one();
        if ($com == null) {
            $see = true;
            $comment = new Comments();
            $comment->source_id = $comment_id;
            $comment->see = (int)$see;
            $comment->save();
        } else {
            $see = !(boolean)$com->see;
            $com->see = (int)$see;
            $com->save();
        }
        return [
            'success' => true,
            'see' => $see,
            'id' => $comment_id,
        ];

    }
}
