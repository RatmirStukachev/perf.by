from django.db import models

class Category(models.Model):
    title = models.CharField('Название', max_length=255)
    slug = models.SlugField('Slug', max_length=255, unique=True, blank=True)
    is_active = models.BooleanField('Активна', default=True)
    is_top = models.BooleanField('Показывать сверху', default=False)
    parent = models.ForeignKey('self', on_delete=models.SET_NULL, null=True, blank=True, related_name='children', verbose_name='Родительская категория')
    zoomos_id = models.CharField('Zoomos ID', max_length=100, null=True, blank=True, unique=True)
    zoomos_parent_id = models.CharField('Zoomos Parent ID', max_length=100, null=True, blank=True)
    image = models.ImageField('Изображение', upload_to='categories/', null=True, blank=True)

    class Meta:
        verbose_name = 'Категория'
        verbose_name_plural = 'Категории'

    def __str__(self):
        return self.title

class Brand(models.Model):
    title = models.CharField('Название', max_length=255)
    slug = models.SlugField('Slug', max_length=255, unique=True, blank=True)
    is_active = models.BooleanField('Активен', default=True)
    is_top = models.BooleanField('Популярный', default=False)
    zoomos_id = models.CharField('Zoomos ID', max_length=100, null=True, blank=True, unique=True)
    image = models.ImageField('Логотип', upload_to='brands/', null=True, blank=True)

    class Meta:
        verbose_name = 'Бренд'
        verbose_name_plural = 'Бренды'

    def __str__(self):
        return self.title

class Characteristic(models.Model):
    title = models.CharField('Название', max_length=255)
    slug = models.SlugField('Slug', max_length=255, unique=True, blank=True)
    zoomos_id = models.CharField('Zoomos ID', max_length=100, null=True, blank=True, unique=True)
    categories = models.ManyToManyField(Category, related_name='characteristics', verbose_name='Категории', blank=True)

    class Meta:
        verbose_name = 'Характеристика'
        verbose_name_plural = 'Характеристики'

    def __str__(self):
        return self.title

class Product(models.Model):
    title = models.CharField('Название', max_length=255)
    slug = models.SlugField('Slug', max_length=255, unique=True, blank=True)
    sku = models.CharField('Артикул', max_length=100, null=True, blank=True)
    price = models.DecimalField('Цена', max_digits=10, decimal_places=2, default=0)
    old_price = models.DecimalField('Старая цена', max_digits=10, decimal_places=2, null=True, blank=True)
    is_active = models.BooleanField('Активен', default=True)
    is_new = models.BooleanField('Новинка', default=False)
    is_hit = models.BooleanField('Хит продаж', default=False)
    quantity = models.IntegerField('Количество на складе', default=0)
    category = models.ForeignKey(Category, on_delete=models.SET_NULL, null=True, related_name='products', verbose_name='Категория')
    brand = models.ForeignKey(Brand, on_delete=models.SET_NULL, null=True, related_name='products', verbose_name='Бренд')
    description = models.TextField('Описание', null=True, blank=True)
    zoomos_id = models.CharField('Zoomos ID', max_length=100, null=True, blank=True, unique=True)
    image = models.ImageField('Основное изображение', upload_to='products/', null=True, blank=True)

    # Store dynamic fields from zoomos as JSON
    add_info = models.JSONField('Дополнительная информация', null=True, blank=True, default=dict)
    add_images = models.JSONField('Дополнительные изображения', null=True, blank=True, default=list)

    class Meta:
        verbose_name = 'Товар'
        verbose_name_plural = 'Товары'

    def __str__(self):
        return self.title

class ProductCharacteristic(models.Model):
    product = models.ForeignKey(Product, on_delete=models.CASCADE, related_name='characteristic_values')
    characteristic = models.ForeignKey(Characteristic, on_delete=models.CASCADE, related_name='product_values')
    value = models.CharField('Значение', max_length=255)

    class Meta:
        verbose_name = 'Значение характеристики'
        verbose_name_plural = 'Значения характеристик'
        unique_together = ('product', 'characteristic')

    def __str__(self):
        return f"{self.product.title} - {self.characteristic.title}: {self.value}"

class Order(models.Model):
    STATUS_CHOICES = [
        ('new', 'Новый'),
        ('processing', 'В обработке'),
        ('completed', 'Выполнен'),
        ('canceled', 'Отменен'),
    ]

    name = models.CharField('Имя', max_length=255)
    phone = models.CharField('Телефон', max_length=50)
    email = models.EmailField('Email', null=True, blank=True)
    address = models.CharField('Адрес', max_length=255, null=True, blank=True)
    comment = models.TextField('Комментарий', null=True, blank=True)
    status = models.CharField('Статус', max_length=50, choices=STATUS_CHOICES, default='new')
    total_price = models.DecimalField('Сумма', max_digits=10, decimal_places=2, default=0)
    created_at = models.DateTimeField('Создан', auto_now_add=True)
    updated_at = models.DateTimeField('Обновлен', auto_now=True)

    class Meta:
        verbose_name = 'Заказ'
        verbose_name_plural = 'Заказы'
        ordering = ['-created_at']

    def __str__(self):
        return f"Заказ #{self.id} от {self.created_at.strftime('%d.%m.%Y %H:%M')}"

class OrderItem(models.Model):
    order = models.ForeignKey(Order, on_delete=models.CASCADE, related_name='items')
    product = models.ForeignKey(Product, on_delete=models.SET_NULL, null=True, verbose_name='Товар')
    title = models.CharField('Название', max_length=255)
    price = models.DecimalField('Цена', max_digits=10, decimal_places=2)
    quantity = models.IntegerField('Количество', default=1)

    class Meta:
        verbose_name = 'Товар в заказе'
        verbose_name_plural = 'Товары в заказе'

    def __str__(self):
        return f"{self.title} x {self.quantity}"

class Setting(models.Model):
    key = models.CharField('Ключ', max_length=255, unique=True)
    value = models.TextField('Значение', null=True, blank=True)
    description = models.CharField('Описание', max_length=255, null=True, blank=True)

    class Meta:
        verbose_name = 'Настройка'
        verbose_name_plural = 'Настройки'

    def __str__(self):
        return self.key
