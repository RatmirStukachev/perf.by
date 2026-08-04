import os
import requests
import logging
from django.core.files.base import ContentFile
from django.utils.text import slugify
from .models import Category, Brand, Characteristic, Product, ProductCharacteristic

logger = logging.getLogger(__name__)

class ZoomosService:
    def __init__(self):
        self.api_url = os.environ.get('ZOOMOS_API_URL', 'https://api.zoomos.by').rstrip('/')
        self.api_key = os.environ.get('ZOOMOS_API_KEY')
        self.session = requests.Session()
        self.base_params = {
            'key': self.api_key,
        }

    def _get(self, endpoint, extra_params=None):
        # Allow passing full URL for local dev mock API, otherwise construct from api_url
        if endpoint.startswith('http'):
            url = endpoint
        else:
            url = f"{self.api_url}/{endpoint}"

        params = self.base_params.copy()
        if extra_params:
            params.update(extra_params)

        try:
            response = self.session.get(url, params=params, timeout=60)
            response.raise_for_status()
            return response.json()
        except requests.exceptions.RequestException as e:
            logger.error(f"Zoomos API Request failed: {e}")
            return None
        except ValueError:
            logger.error(f"Zoomos API returned non-JSON response from {url}")
            return None

    def get_categories(self):
        return self._get('categories')

    def get_brands(self):
        return self._get('dict/vendors/json')

    def get_products(self):
        return self._get('pricelist')

    def import_brands(self):
        brands_data = self.get_brands()
        if not brands_data:
            return 0

        count = 0
        # Check structure based on standard mock API structure
        data = brands_data.get('data', brands_data) if isinstance(brands_data, dict) else brands_data

        if isinstance(data, list):
            for item in data:
                brand_id = str(item.get('id', item.get('vendor_id', '')))
                name = item.get('name', item.get('title', ''))
                if not name or not brand_id:
                    continue

                brand, created = Brand.objects.update_or_create(
                    zoomos_id=brand_id,
                    defaults={
                        'title': name,
                        'slug': slugify(name) or f"brand-{brand_id}"
                    }
                )
                if created:
                    count += 1
        return count

    def import_categories(self):
        categories_data = self.get_categories()
        if not categories_data:
            return 0

        count = 0
        data = categories_data.get('data', categories_data) if isinstance(categories_data, dict) else categories_data

        if isinstance(data, list):
            # First pass: create all categories
            for item in data:
                cat_id = str(item.get('id', ''))
                name = item.get('name', item.get('title', ''))
                parent_id = item.get('parent_id')
                if parent_id:
                    parent_id = str(parent_id)

                if not name or not cat_id:
                    continue

                Category.objects.update_or_create(
                    zoomos_id=cat_id,
                    defaults={
                        'title': name,
                        'slug': slugify(name) or f"cat-{cat_id}",
                        'zoomos_parent_id': parent_id
                    }
                )
                count += 1

            # Second pass: set parents
            for cat in Category.objects.exclude(zoomos_parent_id__isnull=True).exclude(zoomos_parent_id=''):
                parent = Category.objects.filter(zoomos_id=cat.zoomos_parent_id).first()
                if parent:
                    cat.parent = parent
                    cat.save()

        return count

    def import_products(self, limit=None):
        products_data = self.get_products()
        if not products_data:
            return 0

        count = 0
        data = products_data.get('data', products_data) if isinstance(products_data, dict) else products_data

        if isinstance(data, list):
            if limit:
                data = data[:limit]

            for item in data:
                product_id = str(item.get('id', item.get('item_id', '')))
                name = item.get('name', item.get('title', ''))
                price = item.get('price', 0)
                quantity = item.get('quantity', item.get('stock', 0))
                sku = item.get('article', item.get('sku', ''))
                brand_name = item.get('vendor', item.get('brand', ''))
                cat_id = str(item.get('category_id', ''))
                description = item.get('description', '')

                if not name or not product_id:
                    continue

                # Resolve Category and Brand
                category = Category.objects.filter(zoomos_id=cat_id).first() if cat_id else None

                brand = None
                if brand_name:
                    brand, _ = Brand.objects.get_or_create(title=brand_name, defaults={'slug': slugify(brand_name) or f"brand-tmp-{count}"})

                defaults = {
                    'title': name,
                    'slug': slugify(name) or f"product-{product_id}",
                    'sku': sku,
                    'price': price,
                    'quantity': quantity,
                    'category': category,
                    'brand': brand,
                    'description': description,
                    'is_active': True,
                }

                product, created = Product.objects.update_or_create(
                    zoomos_id=product_id,
                    defaults=defaults
                )
                if created:
                    count += 1

                # Process characteristics if they exist in the payload
                chars_data = item.get('characteristics', item.get('features', []))
                if chars_data and isinstance(chars_data, list):
                    for char in chars_data:
                        char_name = char.get('name')
                        char_value = char.get('value')
                        if not char_name or not char_value:
                            continue

                        characteristic, _ = Characteristic.objects.get_or_create(
                            title=char_name,
                            defaults={'slug': slugify(char_name) or f"char-tmp-{count}"}
                        )

                        if category:
                            characteristic.categories.add(category)

                        ProductCharacteristic.objects.update_or_create(
                            product=product,
                            characteristic=characteristic,
                            defaults={'value': str(char_value)}
                        )

        return count
