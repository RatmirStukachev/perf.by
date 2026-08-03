import requests
import os
from django.conf import settings

class ZoomosAPI:
    def __init__(self):
        self.api_url = os.environ.get('ZOOMOS_API_URL', 'https://api.zoomos.by/v1/')
        self.api_key = os.environ.get('ZOOMOS_API_KEY', '')

    def _get_headers(self):
        return {
            'Authorization': f'Bearer {self.api_key}',
            'Content-Type': 'application/json'
        }

    def get_products(self):
        # NOTE: This endpoint and its response format is just an example based on common API structures.
        # It needs to be adjusted based on real Zoomos API documentation.
        url = f"{self.api_url}products"
        try:
            response = requests.get(url, headers=self._get_headers())
            response.raise_for_status()
            return response.json()
        except requests.exceptions.RequestException as e:
            print(f"Error connecting to Zoomos API: {e}")
            return []
