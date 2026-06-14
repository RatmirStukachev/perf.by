from django.contrib import admin
from .models import Category, Vendor, Feature, Product, ProductFeature

admin.site.register(Category)
admin.site.register(Vendor)
admin.site.register(Feature)

class ProductFeatureInline(admin.TabularInline):
    model = ProductFeature
    extra = 1

from django.core.management import call_command
from django.contrib import messages
from django.http import HttpResponseRedirect
from django.urls import path

@admin.register(Product)
class ProductAdmin(admin.ModelAdmin):
    inlines = [ProductFeatureInline]
    list_display = ('id', 'model', 'vendor', 'category', 'price', 'status')
    search_fields = ('model', 'id')
    list_filter = ('vendor', 'category', 'status')

    change_list_template = "admin/product_changelist.html"

    def get_urls(self):
        urls = super().get_urls()
        custom_urls = [
            path('parse-eco/', self.admin_site.admin_view(self.parse_eco_action), name='parse-eco')
        ]
        return custom_urls + urls

    def parse_eco_action(self, request):
        try:
            call_command('parse_eco')
            self.message_user(request, "Успешно спарсили товары ECO!", level=messages.SUCCESS)
        except Exception as e:
            self.message_user(request, f"Ошибка при парсинге: {str(e)}", level=messages.ERROR)

        return HttpResponseRedirect("../")
