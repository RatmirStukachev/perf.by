from django.contrib import admin
from .models import Category, Brand, Characteristic, Product, ProductCharacteristic, Order, OrderItem, Setting

@admin.register(Category)
class CategoryAdmin(admin.ModelAdmin):
    list_display = ('title', 'slug', 'is_active', 'is_top', 'parent', 'zoomos_id')
    list_filter = ('is_active', 'is_top')
    search_fields = ('title', 'zoomos_id')
    prepopulated_fields = {'slug': ('title',)}

@admin.register(Brand)
class BrandAdmin(admin.ModelAdmin):
    list_display = ('title', 'slug', 'is_active', 'is_top', 'zoomos_id')
    list_filter = ('is_active', 'is_top')
    search_fields = ('title', 'zoomos_id')
    prepopulated_fields = {'slug': ('title',)}

@admin.register(Characteristic)
class CharacteristicAdmin(admin.ModelAdmin):
    list_display = ('title', 'slug', 'zoomos_id')
    search_fields = ('title', 'zoomos_id')
    prepopulated_fields = {'slug': ('title',)}
    filter_horizontal = ('categories',)

class ProductCharacteristicInline(admin.TabularInline):
    model = ProductCharacteristic
    extra = 1

@admin.register(Product)
class ProductAdmin(admin.ModelAdmin):
    list_display = ('title', 'sku', 'price', 'quantity', 'is_active', 'is_new', 'category', 'brand', 'zoomos_id')
    list_filter = ('is_active', 'is_new', 'is_hit', 'category', 'brand')
    search_fields = ('title', 'sku', 'zoomos_id')
    prepopulated_fields = {'slug': ('title',)}
    inlines = [ProductCharacteristicInline]

class OrderItemInline(admin.TabularInline):
    model = OrderItem
    extra = 0

@admin.register(Order)
class OrderAdmin(admin.ModelAdmin):
    list_display = ('id', 'name', 'phone', 'status', 'total_price', 'created_at')
    list_filter = ('status', 'created_at')
    search_fields = ('name', 'phone', 'email', 'id')
    inlines = [OrderItemInline]
    readonly_fields = ('created_at', 'updated_at')

@admin.register(Setting)
class SettingAdmin(admin.ModelAdmin):
    list_display = ('key', 'value', 'description')
    search_fields = ('key', 'description')
