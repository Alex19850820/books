<?php

namespace app\models;

use yii\base\Model;

class MainForm extends Model
{
    public $url;

    public function rules()
    {
        return [
            [['url'],'url', 'defaultScheme' => 'http'],
            [['url'], 'required'],
        ];
    }

    public function attributeLabels() {
        return [
            'url' => 'Введите ссылку',
        ];
    }
}