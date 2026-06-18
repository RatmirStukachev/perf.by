from django.db import models

class Category(models.Model):
    name = models.CharField(max_length=255)
    zoomos_id = models.IntegerField(unique=True)
    parent = models.ForeignKey('self', on_delete=models.CASCADE, null=True, blank=True, related_name='subcategories')
    url = models.CharField(max_length=255, unique=True, null=True, blank=True)

    def __str__(self):
        return self.name

class Product(models.Model):
    zoomos_id = models.IntegerField(unique=True)
    name = models.CharField(max_length=255)
    category = models.ForeignKey(Category, on_delete=models.CASCADE, related_name='products')
    description = models.TextField(blank=True)
    price = models.DecimalField(max_digits=10, decimal_places=2, default=0.00)
    in_stock = models.BooleanField(default=True)
    main_image = models.URLField(max_length=500, blank=True)
    source_url = models.URLField(max_length=500, unique=True, null=True, blank=True)

    def __str__(self):
        return self.name

class Characteristic(models.Model):
    name = models.CharField(max_length=255, unique=True)
    unit = models.CharField(max_length=50, blank=True)

    def __str__(self):
        return f"{self.name} ({self.unit})" if self.unit else self.name

class ProductCharacteristic(models.Model):
    product = models.ForeignKey(Product, on_delete=models.CASCADE, related_name='characteristics')
    characteristic = models.ForeignKey(Characteristic, on_delete=models.CASCADE)
    value = models.CharField(max_length=500)

    class Meta:
        unique_together = ('product', 'characteristic')

    def __str__(self):
        return f"{self.product.name} - {self.characteristic.name}: {self.value}"
