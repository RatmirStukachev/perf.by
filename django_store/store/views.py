from django.shortcuts import render, get_object_or_404, redirect
from django.views.decorators.http import require_POST
from django.contrib import messages
from django.db.models import Q
from .models import Category, Product, Order, OrderItem
from .cart import Cart

def get_common_context():
    return {
        'categories': Category.objects.filter(is_active=True, parent__isnull=True),
    }

def home(request):
    context = get_common_context()
    context.update({
        'hits': Product.objects.filter(is_active=True, is_hit=True)[:8],
        'new_products': Product.objects.filter(is_active=True, is_new=True)[:8],
    })
    return render(request, 'store/home.html', context)

def catalog(request):
    context = get_common_context()
    context.update({
        'all_categories': Category.objects.filter(is_active=True),
        'products': Product.objects.filter(is_active=True)[:20] # basic pagination could be added
    })
    return render(request, 'store/catalog.html', context)

def category_detail(request, category_slug):
    category = get_object_or_404(Category, slug=category_slug, is_active=True)
    products = Product.objects.filter(category=category, is_active=True)

    context = get_common_context()
    context.update({
        'category': category,
        'products': products,
    })
    return render(request, 'store/category_detail.html', context)

def product_detail(request, product_slug):
    product = get_object_or_404(Product, slug=product_slug, is_active=True)

    context = get_common_context()
    context.update({
        'product': product,
        'characteristics': product.characteristic_values.all()
    })
    return render(request, 'store/product_detail.html', context)

def search(request):
    query = request.GET.get('q', '')
    products = []
    if query:
        products = Product.objects.filter(
            Q(title__icontains=query) | Q(sku__icontains=query) | Q(description__icontains=query),
            is_active=True
        )

    context = get_common_context()
    context.update({
        'query': query,
        'products': products
    })
    return render(request, 'store/search.html', context)

@require_POST
def cart_add(request, product_id):
    cart = Cart(request)
    product = get_object_or_404(Product, id=product_id)
    quantity = int(request.POST.get('quantity', 1))
    cart.add(product=product, quantity=quantity)
    messages.success(request, f"{product.title} добавлен в корзину.")
    return redirect('store:cart_detail')

@require_POST
def cart_remove(request, product_id):
    cart = Cart(request)
    product = get_object_or_404(Product, id=product_id)
    cart.remove(product)
    messages.success(request, f"{product.title} удален из корзины.")
    return redirect('store:cart_detail')

def cart_clear(request):
    cart = Cart(request)
    cart.clear()
    messages.success(request, "Корзина очищена.")
    return redirect('store:cart_detail')

def cart_detail(request):
    cart = Cart(request)
    context = get_common_context()
    context.update({'cart': cart})
    return render(request, 'store/cart.html', context)

def checkout(request):
    cart = Cart(request)
    if len(cart) == 0:
        return redirect('store:catalog')

    if request.method == 'POST':
        name = request.POST.get('name')
        phone = request.POST.get('phone')
        email = request.POST.get('email', '')
        address = request.POST.get('address', '')
        comment = request.POST.get('comment', '')

        order = Order.objects.create(
            name=name, phone=phone, email=email,
            address=address, comment=comment,
            total_price=cart.get_total_price()
        )

        for item in cart:
            OrderItem.objects.create(
                order=order,
                product=item['product'],
                title=item['product'].title,
                price=item['price'],
                quantity=item['quantity']
            )

        cart.clear()
        messages.success(request, f"Ваш заказ #{order.id} успешно оформлен! Мы свяжемся с вами в ближайшее время.")
        return redirect('store:home')

    context = get_common_context()
    context.update({'cart': cart})
    return render(request, 'store/checkout.html', context)
