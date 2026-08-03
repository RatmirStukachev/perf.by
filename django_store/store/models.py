from django.db import models

class Brand(models.Model):
    title = models.CharField(max_length=255, verbose_name="Название")
    slug = models.SlugField(max_length=255, unique=True, verbose_name="URL", blank=True)
    desc = models.TextField(blank=True, null=True, verbose_name="Краткое описание")
    content = models.TextField(blank=True, null=True, verbose_name="Полное описание")
    image = models.ImageField(upload_to="brands/", blank=True, null=True, verbose_name="Изображение")
    pos = models.IntegerField(default=0, verbose_name="Позиция")
    is_active = models.BooleanField(default=True, verbose_name="Активен")

    class Meta:
        verbose_name = "Бренд"
        verbose_name_plural = "Бренды"
        ordering = ["pos", "title"]

    def __str__(self):
        return self.title


class Category(models.Model):
    title = models.CharField(max_length=255, verbose_name="Название")
    slug = models.SlugField(max_length=255, unique=True, verbose_name="URL", blank=True)
    parent = models.ForeignKey('self', on_delete=models.SET_NULL, null=True, blank=True, related_name='children', verbose_name="Родительская категория")
    desc = models.TextField(blank=True, null=True, verbose_name="Описание")
    image = models.ImageField(upload_to="categories/", blank=True, null=True, verbose_name="Изображение")
    level = models.IntegerField(default=1, verbose_name="Уровень вложенности")
    pos = models.IntegerField(default=0, verbose_name="Позиция")
    is_active = models.BooleanField(default=True, verbose_name="Активна")

    class Meta:
        verbose_name = "Категория"
        verbose_name_plural = "Категории"
        ordering = ["pos", "title"]

    def __str__(self):
        return f"{self.parent.title} -> {self.title}" if self.parent else self.title


class Characteristic(models.Model):
    TYPE_CHOICES = (
        ('string', 'Строка'),
        ('number', 'Число'),
        ('boolean', 'Да/Нет'),
    )
    title = models.CharField(max_length=255, verbose_name="Название")
    type = models.CharField(max_length=50, choices=TYPE_CHOICES, default='string', verbose_name="Тип значения")
    pos = models.IntegerField(default=0, verbose_name="Позиция")
    categories = models.ManyToManyField(Category, related_name="characteristics", blank=True, verbose_name="Категории")

    class Meta:
        verbose_name = "Характеристика"
        verbose_name_plural = "Характеристики"
        ordering = ["pos", "title"]

    def __str__(self):
        return self.title


class Product(models.Model):
    title = models.CharField(max_length=255, verbose_name="Название")
    slug = models.SlugField(max_length=255, unique=True, verbose_name="URL", blank=True)
    article = models.CharField(max_length=100, blank=True, null=True, verbose_name="Артикул")
    price = models.DecimalField(max_digits=10, decimal_places=2, verbose_name="Цена")
    old_price = models.DecimalField(max_digits=10, decimal_places=2, blank=True, null=True, verbose_name="Старая цена")
    category = models.ForeignKey(Category, on_delete=models.CASCADE, related_name="products", verbose_name="Категория")
    brand = models.ForeignKey(Brand, on_delete=models.SET_NULL, null=True, blank=True, related_name="products", verbose_name="Бренд")
    desc = models.TextField(blank=True, null=True, verbose_name="Описание")
    image = models.ImageField(upload_to="products/", blank=True, null=True, verbose_name="Изображение")
    zoomos_id = models.CharField(max_length=100, blank=True, null=True, verbose_name="ID в Zoomos")
    is_active = models.BooleanField(default=True, verbose_name="Активен")
    pos = models.IntegerField(default=0, verbose_name="Позиция")
    created_at = models.DateTimeField(auto_now_add=True)
    updated_at = models.DateTimeField(auto_now=True)

    similars = models.ManyToManyField('self', blank=True, verbose_name="Похожие товары")

    class Meta:
        verbose_name = "Товар"
        verbose_name_plural = "Товары"
        ordering = ["pos", "-created_at"]

    def __str__(self):
        return self.title


class ProductCharacteristic(models.Model):
    product = models.ForeignKey(Product, on_delete=models.CASCADE, related_name="characteristic_values")
    characteristic = models.ForeignKey(Characteristic, on_delete=models.CASCADE)
    value = models.CharField(max_length=500, verbose_name="Значение")

    class Meta:
        verbose_name = "Значение характеристики"
        verbose_name_plural = "Значения характеристик"
        unique_together = ('product', 'characteristic')

    def __str__(self):
        return f"{self.product.title} - {self.characteristic.title}: {self.value}"


class Delivery(models.Model):
    title = models.CharField(max_length=255, verbose_name="Название")
    desc = models.TextField(blank=True, null=True, verbose_name="Описание")
    is_active = models.BooleanField(default=True, verbose_name="Активна")
    pos = models.IntegerField(default=0, verbose_name="Позиция")

    class Meta:
        verbose_name = "Способ доставки"
        verbose_name_plural = "Способы доставки"
        ordering = ["pos"]

    def __str__(self):
        return self.title


class DeliveryPriceRange(models.Model):
    delivery = models.ForeignKey(Delivery, on_delete=models.CASCADE, related_name="price_ranges")
    from_sum = models.DecimalField(max_digits=10, decimal_places=2, verbose_name="От суммы")
    to_sum = models.DecimalField(max_digits=10, decimal_places=2, blank=True, null=True, verbose_name="До суммы (включительно)")
    price = models.DecimalField(max_digits=10, decimal_places=2, verbose_name="Стоимость доставки")

    class Meta:
        verbose_name = "Диапазон цен доставки"
        verbose_name_plural = "Диапазоны цен доставки"
        ordering = ["from_sum"]

    def __str__(self):
        return f"{self.delivery.title}: {self.from_sum} - {self.to_sum or '∞'} = {self.price}"


class PaymentType(models.Model):
    title = models.CharField(max_length=255, verbose_name="Название")
    desc = models.TextField(blank=True, null=True, verbose_name="Описание")
    is_active = models.BooleanField(default=True, verbose_name="Активен")
    pos = models.IntegerField(default=0, verbose_name="Позиция")

    class Meta:
        verbose_name = "Способ оплаты"
        verbose_name_plural = "Способы оплаты"
        ordering = ["pos"]

    def __str__(self):
        return self.title


class Order(models.Model):
    CUSTOMER_TYPES = (
        ('physical', 'Физическое лицо'),
        ('legal', 'Юридическое лицо'),
    )
    STATUS_CHOICES = (
        ('new', 'Новый'),
        ('processing', 'В обработке'),
        ('completed', 'Выполнен'),
        ('cancelled', 'Отменен'),
    )

    customer_type = models.CharField(max_length=50, choices=CUSTOMER_TYPES, default='physical', verbose_name="Тип покупателя")
    status = models.CharField(max_length=50, choices=STATUS_CHOICES, default='new', verbose_name="Статус")

    first_name = models.CharField(max_length=255, verbose_name="Имя")
    last_name = models.CharField(max_length=255, blank=True, null=True, verbose_name="Фамилия")
    phone = models.CharField(max_length=50, verbose_name="Телефон")
    email = models.EmailField(blank=True, null=True, verbose_name="Email")

    company_name = models.CharField(max_length=255, blank=True, null=True, verbose_name="Название компании")
    inn = models.CharField(max_length=50, blank=True, null=True, verbose_name="УНП")

    delivery = models.ForeignKey(Delivery, on_delete=models.SET_NULL, null=True, verbose_name="Доставка")
    delivery_price = models.DecimalField(max_digits=10, decimal_places=2, default=0, verbose_name="Стоимость доставки")
    payment_type = models.ForeignKey(PaymentType, on_delete=models.SET_NULL, null=True, verbose_name="Способ оплаты")

    address = models.TextField(blank=True, null=True, verbose_name="Адрес доставки")
    comment = models.TextField(blank=True, null=True, verbose_name="Комментарий к заказу")

    total_price = models.DecimalField(max_digits=10, decimal_places=2, default=0, verbose_name="Общая сумма")
    created_at = models.DateTimeField(auto_now_add=True, verbose_name="Дата создания")

    class Meta:
        verbose_name = "Заказ"
        verbose_name_plural = "Заказы"
        ordering = ["-created_at"]

    def __str__(self):
        return f"Заказ #{self.id} от {self.created_at.strftime('%d.%m.%Y')}"


class OrderProduct(models.Model):
    order = models.ForeignKey(Order, on_delete=models.CASCADE, related_name="items")
    product = models.ForeignKey(Product, on_delete=models.SET_NULL, null=True)
    title = models.CharField(max_length=255, verbose_name="Название товара")
    price = models.DecimalField(max_digits=10, decimal_places=2, verbose_name="Цена")
    quantity = models.PositiveIntegerField(default=1, verbose_name="Количество")

    class Meta:
        verbose_name = "Товар в заказе"
        verbose_name_plural = "Товары в заказе"

    def __str__(self):
        return f"{self.title} x {self.quantity}"

class Feedback(models.Model):
    name = models.CharField(max_length=255, verbose_name="Имя")
    email = models.EmailField(verbose_name="Email")
    message = models.TextField(verbose_name="Сообщение")
    created_at = models.DateTimeField(auto_now_add=True, verbose_name="Дата отправки")

    class Meta:
        verbose_name = "Обратная связь"
        verbose_name_plural = "Сообщения обратной связи"
        ordering = ["-created_at"]

    def __str__(self):
        return f"Сообщение от {self.name} ({self.created_at.strftime('%d.%m.%Y')})"
