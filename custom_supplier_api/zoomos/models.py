from django.db import models

class Category(models.Model):
    id = models.IntegerField(primary_key=True)
    name = models.CharField(max_length=255)
    linkRewrite = models.CharField(max_length=255, blank=True)
    itemsCount = models.IntegerField(default=0)
    parentId = models.ForeignKey('self', on_delete=models.SET_NULL, null=True, blank=True, related_name='children')

    def __str__(self):
        return self.name

class Vendor(models.Model):
    id = models.IntegerField(primary_key=True)
    name = models.CharField(max_length=255)

    def __str__(self):
        return self.name

class Feature(models.Model):
    feature_id = models.IntegerField(primary_key=True)
    name = models.CharField(max_length=255)
    category_name = models.CharField(max_length=255, blank=True)

    def __str__(self):
        return self.name

class Product(models.Model):
    id = models.IntegerField(primary_key=True)
    model = models.CharField(max_length=255)
    typePrefix = models.CharField(max_length=100, blank=True)
    linkRewrite = models.CharField(max_length=255, blank=True)
    price = models.DecimalField(max_digits=10, decimal_places=2, null=True, blank=True)
    priceCurrency = models.CharField(max_length=10, default="BYN")
    status = models.IntegerField(default=1)
    isNew = models.IntegerField(default=1)
    image_url = models.URLField(max_length=1000, blank=True)
    bar = models.CharField(max_length=100, blank=True)

    vendor = models.ForeignKey(Vendor, on_delete=models.SET_NULL, null=True, blank=True)
    category = models.ForeignKey(Category, on_delete=models.SET_NULL, null=True, blank=True)

    fullDescriptionHTML = models.TextField(blank=True)

    def __str__(self):
        return self.model

class ProductFeature(models.Model):
    product = models.ForeignKey(Product, on_delete=models.CASCADE, related_name='features')
    name = models.CharField(max_length=255)
    values = models.JSONField(default=list) # Store as list of strings

    def __str__(self):
        return f"{self.product.model} - {self.name}"
