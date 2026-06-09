<?php

namespace App\Enums;

enum PageContentEnum: string
{
    use EnumToArray;

    case main_arrivals = 'Главная: Новинки';
    case main_second = 'Главная: Контент (второй блок)';
    case main_popular = 'Главная: Популярные товары';
    case main_slide = 'Главная: Слайд';
    case main_brands = 'Главная страница: Бренды';
    case main_news = 'Главная страница: Новости';
    case product_delivery = 'Товар: Доставка (Сквозной блок)';
    case cart_delivery = 'Корзина: Доставка (Сквозной блок)';
    case callback_form = 'Форма обратной связи';

    public static function valueOne($name)
    {
        foreach (self::cases() as $value) {
            if ($name === $value->name) {
                return $value->value;
            }
        }
    }
}
