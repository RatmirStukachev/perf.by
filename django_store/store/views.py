from django.shortcuts import render, get_object_or_404, redirect
from django.views.generic import ListView, DetailView, TemplateView
from django.db.models import Q
from django.contrib import messages
from django.core.mail import send_mail
from django.conf import settings

from .models import Product, Category, OrderProduct
from .cart import Cart
from .forms import OrderCreateForm, FeedbackForm

class HomeView(TemplateView):
    template_name = 'store/home.html'

    def get_context_data(self, **kwargs):
        context = super().get_context_data(**kwargs)
        context['latest_products'] = Product.objects.filter(is_active=True)[:8]
        context['categories'] = Category.objects.filter(is_active=True, parent__isnull=True)
        return context

class CatalogView(ListView):
    model = Product
    template_name = 'store/catalog.html'
    context_object_name = 'products'
    paginate_by = 12

    def get_queryset(self):
        queryset = Product.objects.filter(is_active=True)
        q = self.request.GET.get('q')
        cat_id = self.request.GET.get('category')

        if q:
            queryset = queryset.filter(
                Q(title__icontains=q) |
                Q(article__icontains=q) |
                Q(desc__icontains=q)
            )

        if cat_id:
            queryset = queryset.filter(category_id=cat_id)

        return queryset

    def get_context_data(self, **kwargs):
        context = super().get_context_data(**kwargs)
        context['categories'] = Category.objects.filter(is_active=True)
        context['query'] = self.request.GET.get('q', '')
        return context

class ProductDetailView(DetailView):
    model = Product
    template_name = 'store/product_detail.html'
    context_object_name = 'product'

    def get_queryset(self):
        return Product.objects.filter(is_active=True)

def cart_add(request, product_id):
    cart = Cart(request)
    product = get_object_or_404(Product, id=product_id)
    cart.add(product=product, quantity=1)
    messages.success(request, f"{product.title} добавлен в корзину.")
    return redirect('store:cart_detail')

def cart_remove(request, product_id):
    cart = Cart(request)
    product = get_object_or_404(Product, id=product_id)
    cart.remove(product)
    return redirect('store:cart_detail')

def cart_detail(request):
    cart = Cart(request)
    return render(request, 'store/cart_detail.html', {'cart': cart})

def checkout(request):
    cart = Cart(request)
    if len(cart) == 0:
        return redirect('store:catalog')

    if request.method == 'POST':
        form = OrderCreateForm(request.POST)
        if form.is_valid():
            order = form.save(commit=False)
            order.total_price = cart.get_total_price()

            # Simple delivery price logic based on model (if needed, this can be expanded using DeliveryPriceRange)

            order.save()
            for item in cart:
                OrderProduct.objects.create(
                    order=order,
                    product=item['product'],
                    title=item['product'].title,
                    price=item['price'],
                    quantity=item['quantity']
                )

            # Send Email Notification
            try:
                subject = f'Новый заказ #{order.id}'
                message = f'Спасибо за заказ! Номер вашего заказа: {order.id}.\nСумма: {order.total_price} руб.\nМы скоро свяжемся с вами.'
                send_mail(
                    subject,
                    message,
                    settings.DEFAULT_FROM_EMAIL,
                    [order.email] if order.email else [settings.DEFAULT_FROM_EMAIL],
                    fail_silently=True
                )
            except Exception as e:
                pass # Fail silently for email on local

            cart.clear()
            messages.success(request, "Ваш заказ успешно оформлен! Письмо отправлено на почту.")
            return render(request, 'store/order_created.html', {'order': order})
    else:
        form = OrderCreateForm()

    return render(request, 'store/checkout.html', {'cart': cart, 'form': form})

def feedback(request):
    if request.method == 'POST':
        form = FeedbackForm(request.POST)
        if form.is_valid():
            form.save()
            messages.success(request, 'Ваше сообщение успешно отправлено! Мы свяжемся с вами в ближайшее время.')
            return redirect('store:feedback')
    else:
        form = FeedbackForm()

    return render(request, 'store/feedback.html', {'form': form})
