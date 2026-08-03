# Django Store (Замена Laravel на Django)

Проект полностью воссоздает основной функционал Laravel-магазина (каталог, корзина, оформление заказов, админ-панель) на фреймворке Django. Дизайн обновлен на минималистичный с желтыми акцентами, логотип и название скрыты.

## Инструкция по запуску

### 1. Установка зависимостей
Убедитесь, что у вас установлен Python (рекомендуется 3.10+). Установите необходимые пакеты:
```bash
pip install django python-dotenv pillow requests
```

### 2. Настройка переменных окружения
В папке `django_store` создайте файл `.env` (вы можете скопировать настройки из примера ниже):
```env
# Базовые настройки
SECRET_KEY=your_secret_key_here
DEBUG=True
ALLOWED_HOSTS=*

# База данных (для PostgreSQL нужно установить psycopg2 и прописать URL, пока оставлен SQLite)
# DB_URL=postgres://user:pass@localhost:5432/dbname

# Настройки Zoomos API
ZOOMOS_API_URL=https://api.zoomos.by/v1/
ZOOMOS_API_KEY=your_real_zoomos_api_key

# Настройки почты (для отправки заказов)
EMAIL_HOST=smtp.yandex.ru
EMAIL_PORT=587
EMAIL_USE_TLS=True
EMAIL_HOST_USER=your_email@yandex.ru
EMAIL_HOST_PASSWORD=your_app_password
DEFAULT_FROM_EMAIL=your_email@yandex.ru
```

### 3. Применение миграций и создание суперпользователя
В терминале, находясь в папке `django_store`, выполните:
```bash
python manage.py migrate
python manage.py createsuperuser
```
(Следуйте инструкциям на экране для создания логина и пароля админа).

### 4. Запуск сервера
```bash
python manage.py runserver
```
Сайт будет доступен по адресу `http://127.0.0.1:8000/`. Админ-панель: `http://127.0.0.1:8000/admin/`.

### 5. Автовыгрузка Zoomos
Для синхронизации товаров и цен с API Zoomos используйте созданную management-команду:
```bash
python manage.py sync_zoomos
```
*Примечание: скрипт `sync_zoomos.py` содержит базовую логику. В зависимости от точного формата JSON-ответов от боевого API Zoomos, логику парсинга в файле `zoomos/management/commands/sync_zoomos.py` нужно будет немного адаптировать.*

## Структура
- `store/` - Основное приложение (модели товаров, категорий, корзина, заказы, админка).
- `zoomos/` - Приложение для работы с Zoomos API (сервис и команда синхронизации).
- `templates/` - HTML-шаблоны без JS-фреймворков.
- `static/style.css` - Стили с минималистичным желтым дизайном.
