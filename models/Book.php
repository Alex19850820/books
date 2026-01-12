<?php

namespace app\models; 

use yii\db\ActiveRecord;
use app\validators\IsbnValidator;

class Book extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'book';
    }

    public function getAuthors()
    {
        return $this->hasMany(Author::class, ['id' => 'author_id'])
            ->viaTable('book_author', ['book_id' => 'id']);
    }

    public function rules()
    {
        return [
            [['title', 'year'], 'required'],
            ['title', 'string', 'max' => 255],
            ['year', 'integer', 'min' => 1000, 'max' => date('Y') + 1],
            
            // Валидация для новых полей
            ['description', 'string'],
            // Валидация ISBN
            ['isbn', IsbnValidator::class],
            ['isbn', 'match', 'pattern' => '/^(97(8|9))?\d{9}(\d|X)$/', 'message' => 'Некорректный формат ISBN'],
            ['image_path', 'string', 'max' => 255],
 
        ];
    }

    public function attributeLabels()
    {
        return [
            'title' => 'Название',
            'year' => 'Год выпуска',
            'description' => 'Описание',
            'isbn' => 'ISBN',
            'image_path' => 'Обложка',
        ];
    }


}
