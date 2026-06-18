from django.contrib import admin
from django.urls import reverse
from django.utils.html import format_html
from .models import Category, Product, Characteristic, ProductCharacteristic

class ProductCharacteristicInline(admin.TabularInline):
    model = ProductCharacteristic
    extra = 1

@admin.register(Product)
class ProductAdmin(admin.ModelAdmin):
    list_display = ('name', 'category', 'price', 'in_stock', 'zoomos_id')
    search_fields = ('name',)
    list_filter = ('category', 'in_stock')
    inlines = [ProductCharacteristicInline]

@admin.register(Category)
class CategoryAdmin(admin.ModelAdmin):
    list_display = ('name', 'parent', 'zoomos_id')

@admin.register(Characteristic)
class CharacteristicAdmin(admin.ModelAdmin):
    list_display = ('name', 'unit')
