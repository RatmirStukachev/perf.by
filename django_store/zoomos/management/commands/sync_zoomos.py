from django.core.management.base import BaseCommand
from store.models import Product, Brand, Category
from zoomos.services import ZoomosAPI
import os

class Command(BaseCommand):
    help = 'Sync products and prices from Zoomos API'

    def handle(self, *args, **kwargs):
        api_key = os.environ.get('ZOOMOS_API_KEY', '')
        if not api_key or api_key == 'your_real_zoomos_api_key':
            self.stdout.write(self.style.ERROR("Zoomos API key is not configured. Please set ZOOMOS_API_KEY in your .env file."))
            return

        self.stdout.write("Starting Zoomos sync...")
        api = ZoomosAPI()

        products_data = api.get_products()

        if not products_data:
            self.stdout.write(self.style.WARNING("No products retrieved from Zoomos."))
            return

        updated_count = 0
        created_count = 0

        # Adaptation for different JSON structures
        items = products_data if isinstance(products_data, list) else products_data.get('data', [])

        for item in items:
            zoomos_id = str(item.get('id', item.get('zoomos_id', '')))
            if not zoomos_id:
                continue

            category_name = item.get('category_name', item.get('category', 'Uncategorized (Zoomos)'))
            category, _ = Category.objects.get_or_create(title=category_name)

            brand_name = item.get('brand_name', item.get('brand', 'Unknown Brand'))
            brand, _ = Brand.objects.get_or_create(title=brand_name)

            price = item.get('price', item.get('cost', 0))
            if isinstance(price, str):
                try:
                    price = float(price.replace(' ', '').replace(',', '.'))
                except:
                    price = 0

            product, created = Product.objects.update_or_create(
                zoomos_id=zoomos_id,
                defaults={
                    'title': item.get('name', item.get('title', 'Unknown Product')),
                    'price': price,
                    'article': item.get('article', ''),
                    'desc': item.get('description', ''),
                    'category': category,
                    'brand': brand,
                    'is_active': item.get('is_active', True)
                }
            )

            if created:
                created_count += 1
            else:
                updated_count += 1

        self.stdout.write(self.style.SUCCESS(
            f"Successfully synced with Zoomos. Created: {created_count}, Updated: {updated_count}"
        ))
