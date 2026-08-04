from django.core.management.base import BaseCommand
from store.zoomos_service import ZoomosService

class Command(BaseCommand):
    help = 'Imports data from Zoomos API'

    def add_arguments(self, parser):
        parser.add_argument('--brands', action='store_true', help='Import brands')
        parser.add_argument('--categories', action='store_true', help='Import categories')
        parser.add_argument('--products', action='store_true', help='Import products')
        parser.add_argument('--all', action='store_true', help='Import everything')
        parser.add_argument('--limit', type=int, default=100, help='Limit for product import')

    def handle(self, *args, **options):
        service = ZoomosService()

        if options['all'] or options['brands']:
            self.stdout.write('Importing brands...')
            count = service.import_brands()
            self.stdout.write(self.style.SUCCESS(f'Successfully imported/updated {count} brands'))

        if options['all'] or options['categories']:
            self.stdout.write('Importing categories...')
            count = service.import_categories()
            self.stdout.write(self.style.SUCCESS(f'Successfully imported/updated {count} categories'))

        if options['all'] or options['products']:
            self.stdout.write('Importing products...')
            count = service.import_products(limit=options['limit'])
            self.stdout.write(self.style.SUCCESS(f'Successfully imported/updated {count} products'))
