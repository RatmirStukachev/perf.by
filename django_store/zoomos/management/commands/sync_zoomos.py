from django.core.management.base import BaseCommand
from store.models import Product, Brand, Category
from zoomos.services import ZoomosAPI

class Command(BaseCommand):
    help = 'Sync products and prices from Zoomos API'

    def handle(self, *args, **kwargs):
        self.stdout.write("Starting Zoomos sync...")
        api = ZoomosAPI()

        # Example API response parsing.
        # Actual implementation heavily depends on real Zoomos response format.
        products_data = api.get_products()

        if not products_data:
            self.stdout.write(self.style.WARNING("No products retrieved from Zoomos."))
            return

        updated_count = 0
        created_count = 0

        for item in products_data.get('data', []):
            zoomos_id = str(item.get('id'))

            # Simple category fallback
            category, _ = Category.objects.get_or_create(title="Uncategorized (Zoomos)")

            # Simple brand fallback
            brand_name = item.get('brand', 'Unknown Brand')
            brand, _ = Brand.objects.get_or_create(title=brand_name)

            product, created = Product.objects.update_or_create(
                zoomos_id=zoomos_id,
                defaults={
                    'title': item.get('name', 'Unknown Product'),
                    'price': item.get('price', 0),
                    'article': item.get('article', ''),
                    'desc': item.get('description', ''),
                    'category': category,
                    'brand': brand,
                }
            )

            if created:
                created_count += 1
            else:
                updated_count += 1

        self.stdout.write(self.style.SUCCESS(
            f"Successfully synced with Zoomos. Created: {created_count}, Updated: {updated_count}"
        ))
