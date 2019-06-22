<?php
/**
 * Created by PhpStorm.
 * User: dimas
 * Date: 11.06.2019
 * Time: 0:11
 */

namespace app\models;


class Post
{
    public $id;
    public $photo;
    public $user;
    public $date;
    public $likes;
    public $comments;
    public $markers = [];
    public $source;
    public $attach;
}