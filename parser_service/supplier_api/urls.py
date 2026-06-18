from django.contrib import admin
from django.urls import path
from parser import views

urlpatterns = [
    path('admin/trigger-parser/', views.trigger_parser, name='trigger_parser'),
    path('admin/', admin.site.urls),
    path('categories', views.zoomos_categories),
    path('pricelist', views.zoomos_pricelist),
    path('item/<int:zoomos_id>', views.zoomos_item),
]
