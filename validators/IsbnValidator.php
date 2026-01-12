<?php

namespace app\validators;

use yii\validators\Validator;
use Yii;

class IsbnValidator extends Validator
{
    /**
     * Основные проверки формата и контрольной суммы
     */
   public function validateAttribute($model, $attribute)
    {
        $value = trim($model->$attribute);
        if (empty($value)) {
            return;
        }

        // Очищаем: удаляем дефисы, пробелы, приводим к верхнему регистру
        $clean = preg_replace('/[-\s]/', '', strtoupper($value));


        // Проверяем длину и формат
        switch (strlen($clean)) {
            case 10:
                $this->validateIsbn10($model, $attribute, $clean);
                break;
            case 13:
                $this->validateIsbn13($model, $attribute, $clean);
                break;
            default:
                $model->addError($attribute, 'ISBN должен содержать 10 или 13 символов после удаления дефисов и пробелов.');
        }
    }

    /**
     * Валидация ISBN-10
     */
    private function validateIsbn10($model, $attribute, $isbn)
    {
        $sum = 0;
        for ($i = 0; $i < 9; $i++) {
            $sum += (int)$isbn[$i] * (10 - $i);
        }

        $check = $isbn[9];
        $sum += ($check === 'X') ? 10 : (int)$check;


        if ($sum % 11 !== 0) {
            $model->addError($attribute, 'Неверная контрольная сумма ISBN‑10. Проверьте ввод.');
            return false;
        }
        return true;
    }

    /**
     * Валидация ISBN-13
     */
    private function validateIsbn13($model, $attribute, $isbn)
    {
        // Только цифры
        if (!preg_match('/^\d{13}$/', $isbn)) {
            $model->addError($attribute, 'Неверный формат ISBN-13. Должны быть только цифры.');
            return false;
        }

        // Расчёт контрольной суммы
        $sum = 0;
        for ($i = 0; $i < 12; $i++) {
            $sum += (int)$isbn[$i] * ($i % 2 === 0 ? 1 : 3);
        }
        $calculatedCheck = (10 - ($sum % 10)) % 10;

        if ((int)$isbn[12] !== $calculatedCheck) {
            $model->addError($attribute, 'Неверная контрольная сумма ISBN-13.');
        }
    }
}
