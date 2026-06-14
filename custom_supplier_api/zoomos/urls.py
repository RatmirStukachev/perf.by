from django.urls import path
from .views import CategoriesView, VendorsView, FeaturesView, PricelistView, ItemDetailView

urlpatterns = [
    path('categories', CategoriesView.as_view(), name='categories'),
    path('dict/vendors/json', VendorsView.as_view(), name='vendors'),
    path('dict/features/json', FeaturesView.as_view(), name='features'),
    path('pricelist', PricelistView.as_view(), name='pricelist'),
    path('item/<int:item_id>', ItemDetailView.as_view(), name='item_detail'),
]