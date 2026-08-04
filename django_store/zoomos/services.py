import requests
import os
from django.conf import settings

class ZoomosAPI:
    def __init__(self):
        # In Laravel code, endpoints look like https://api.zoomos.by/export/categories, etc.
        # But this can be configured via .env
        self.api_url = os.environ.get('ZOOMOS_API_URL', 'https://api.zoomos.by/export/')
        self.api_key = os.environ.get('ZOOMOS_API_KEY', '')

    def get_products(self):
        # Based on typical export endpoints seen in similar projects
        url = f"{self.api_url}items?key={self.api_key}"
        try:
            response = requests.get(url, timeout=60)
            response.raise_for_status()
            return response.json()
        except requests.exceptions.RequestException as e:
            print(f"Error connecting to Zoomos API: {e}")
            return []

    def get_product_details(self, zoomos_id):
        # Replicating fetchProductDetails from Laravel
        url = f"https://api.zoomos.by/item/{zoomos_id}?key={self.api_key}"
        try:
            response = requests.get(url, timeout=60)
            if response.status_code == 200:
                return response.json()
        except requests.exceptions.RequestException:
            pass
        return None
