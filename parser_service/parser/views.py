from django.shortcuts import render
from django.http import JsonResponse, HttpResponse
from rest_framework.decorators import api_view
from rest_framework.response import Response
from .models import Category, Product, ProductCharacteristic
from django.contrib.admin.views.decorators import staff_member_required
from .utils import run_full_parser
import threading

@staff_member_required
def trigger_parser(request):
    thread = threading.Thread(target=run_full_parser)
    thread.daemon = True
    thread.start()
    return HttpResponse("Парсер запущен в фоновом режиме. Вы можете закрыть эту страницу.")

@api_view(['GET'])
def zoomos_categories(request):
    """ Mimics https://api.zoomos.by/categories """
    categories = Category.objects.all()
    data = []
    for cat in categories:
        data.append({
            "id": cat.zoomos_id,
            "parent_id": cat.parent.zoomos_id if cat.parent else None,
            "name": cat.name,
        })
    return Response(data)

@api_view(['GET'])
def zoomos_pricelist(request):
    """ Mimics https://api.zoomos.by/pricelist """
    products = Product.objects.all()
    data = []
    for p in products:
        data.append({
            "id": p.zoomos_id,
            "name": p.name,
            "price": str(p.price),
            "currency": "BYN",
            "is_available": p.in_stock,
            "brand": {"id": 1, "name": "Unknown"},
            "category_id": p.category.zoomos_id,
        })
    return Response(data)

@api_view(['GET'])
def zoomos_item(request, zoomos_id):
    """ Mimics https://api.zoomos.by/item/{id} """
    try:
        product = Product.objects.get(zoomos_id=zoomos_id)
    except Product.DoesNotExist:
        return Response({"error": "Not found"}, status=404)

    features = []
    for pc in product.characteristics.all():
        features.append({
            "name": pc.characteristic.name,
            "values": [pc.value],
            "unit": pc.characteristic.unit
        })

    data = {
        "id": product.zoomos_id,
        "name": product.name,
        "fullDescriptionHTML": product.description,
        "details": {
            "featuresBlocks": [
                {
                    "name": "Основные",
                    "features": features
                }
            ]
        },
        "images": [
            {
                "url": product.main_image,
                "is_main": True
            }
        ] if product.main_image else []
    }
    return Response(data)
