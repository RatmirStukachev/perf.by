from django.urls import path
from . import views

app_name = 'store'

urlpatterns = [
    path('', views.HomeView.as_view(), name='home'),
    path('catalog/', views.CatalogView.as_view(), name='catalog'),
    path('product/<slug:slug>/', views.ProductDetailView.as_view(), name='product_detail'),

    # Cart
    path('cart/', views.cart_detail, name='cart_detail'),
    path('cart/add/<int:product_id>/', views.cart_add, name='cart_add'),
    path('cart/remove/<int:product_id>/', views.cart_remove, name='cart_remove'),

    # Checkout
    path('checkout/', views.checkout, name='checkout'),

    # Feedback
    path('feedback/', views.feedback, name='feedback'),
]
