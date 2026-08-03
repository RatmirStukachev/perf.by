from django.contrib import admin
from .models import (
    Brand, Category, Characteristic, Product, ProductCharacteristic,
    Delivery, DeliveryPriceRange, PaymentType, Order, OrderProduct
)

@admin.register(Brand)
class BrandAdmin(admin.ModelAdmin):
    list_display = ('title', 'slug', 'pos', 'is_active')
    list_editable = ('pos', 'is_active')
    prepopulated_fields = {'slug': ('title',)}
    search_fields = ('title',)

@admin.register(Category)
class CategoryAdmin(admin.ModelAdmin):
    list_display = ('title', 'parent', 'level', 'pos', 'is_active')
    list_editable = ('pos', 'is_active')
    list_filter = ('level', 'is_active')
    prepopulated_fields = {'slug': ('title',)}
    search_fields = ('title',)

@admin.register(Characteristic)
class CharacteristicAdmin(admin.ModelAdmin):
    list_display = ('title', 'type', 'pos')
    list_editable = ('pos',)
    list_filter = ('type',)
    search_fields = ('title',)
    filter_horizontal = ('categories',)

class ProductCharacteristicInline(admin.TabularInline):
    model = ProductCharacteristic
    extra = 1

@admin.register(Product)
class ProductAdmin(admin.ModelAdmin):
    list_display = ('title', 'article', 'price', 'category', 'brand', 'is_active', 'pos')
    list_editable = ('price', 'is_active', 'pos')
    list_filter = ('is_active', 'category', 'brand')
    prepopulated_fields = {'slug': ('title',)}
    search_fields = ('title', 'article', 'zoomos_id')
    inlines = [ProductCharacteristicInline]
    filter_horizontal = ('similars',)

class DeliveryPriceRangeInline(admin.TabularInline):
    model = DeliveryPriceRange
    extra = 1

@admin.register(Delivery)
class DeliveryAdmin(admin.ModelAdmin):
    list_display = ('title', 'pos', 'is_active')
    list_editable = ('pos', 'is_active')
    inlines = [DeliveryPriceRangeInline]

@admin.register(PaymentType)
class PaymentTypeAdmin(admin.ModelAdmin):
    list_display = ('title', 'pos', 'is_active')
    list_editable = ('pos', 'is_active')

class OrderProductInline(admin.TabularInline):
    model = OrderProduct
    extra = 0
    readonly_fields = ('price', 'title')

@admin.register(Order)
class OrderAdmin(admin.ModelAdmin):
    list_display = ('id', 'status', 'first_name', 'phone', 'total_price', 'created_at')
    list_filter = ('status', 'customer_type', 'created_at')
    search_fields = ('id', 'first_name', 'last_name', 'phone', 'email')
    list_editable = ('status',)
    inlines = [OrderProductInline]
    readonly_fields = ('created_at',)

from .models import Feedback

@admin.register(Feedback)
class FeedbackAdmin(admin.ModelAdmin):
    list_display = ('name', 'email', 'created_at')
    readonly_fields = ('created_at',)
    search_fields = ('name', 'email', 'message')
