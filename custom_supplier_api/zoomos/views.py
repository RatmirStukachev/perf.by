from django.http import JsonResponse
from django.views import View
from .models import Category, Vendor, Feature, Product

class CategoriesView(View):
    def get(self, request):
        categories = Category.objects.filter(parentId__isnull=True)
        data = self._build_category_tree(categories)
        return JsonResponse(data, safe=False)

    def _build_category_tree(self, categories):
        result = []
        for cat in categories:
            node = {
                "id": cat.id,
                "name": cat.name,
                "linkRewrite": cat.linkRewrite,
                "itemsCount": cat.itemsCount,
            }
            if cat.parentId:
                node["parentId"] = cat.parentId.id

            children = cat.children.all()
            if children.exists():
                node["children"] = self._build_category_tree(children)
            result.append(node)
        return result

class VendorsView(View):
    def get(self, request):
        vendors = Vendor.objects.all()
        data = [
            {"id": v.id, "name": v.name} for v in vendors
        ]
        return JsonResponse(data, safe=False)

class FeaturesView(View):
    def get(self, request):
        features = Feature.objects.all()
        data = [
            {
                "feature_id": f.feature_id,
                "name": f.name,
                "category_name": f.category_name,
                "type": "Мультиселект",
                "value_type": "Строка"
            } for f in features
        ]
        return JsonResponse(data, safe=False)

class PricelistView(View):
    def get(self, request):
        products = Product.objects.all()
        data = []
        for p in products:
            item = {
                "id": p.id,
                "model": p.model,
                "linkRewrite": p.linkRewrite,
                "price": str(p.price) if p.price else None,
                "priceCurrency": p.priceCurrency,
                "status": p.status,
                "isNew": p.isNew,
                "image": p.image_url,
                "bar": p.bar,
                "vendor": {
                    "id": p.vendor.id if p.vendor else 0,
                    "name": p.vendor.name if p.vendor else "?"
                },
                "category": {
                    "id": p.category.id if p.category else 0,
                    "name": p.category.name if p.category else "?",
                    "linkRewrite": p.category.linkRewrite if p.category else "-"
                }
            }
            data.append(item)
        return JsonResponse(data, safe=False)

class ItemDetailView(View):
    def get(self, request, item_id):
        try:
            p = Product.objects.get(id=item_id)
        except Product.DoesNotExist:
            return JsonResponse({"error": "Item not found"}, status=404)

        # Build features blocks
        features = p.features.all()
        features_list = []
        for f in features:
            features_list.append({
                "name": f.name,
                "values": f.values
            })

        details = {
            "featuresBlocks": [
                {
                    "name": "Общие характеристики",
                    "features": features_list
                }
            ]
        } if features_list else {}

        data = {
            "id": p.id,
            "model": p.model,
            "typePrefix": p.typePrefix,
            "linkRewrite": p.linkRewrite,
            "fullDescriptionHTML": p.fullDescriptionHTML,
            "vendor": {
                "id": p.vendor.id if p.vendor else 0,
                "name": p.vendor.name if p.vendor else "?"
            },
            "category": {
                "id": p.category.id if p.category else 0,
                "name": p.category.name if p.category else "?",
                "linkRewrite": p.category.linkRewrite if p.category else "-"
            },
            "images": [p.image_url] if p.image_url else [],
            "details": details
        }
        return JsonResponse(data)