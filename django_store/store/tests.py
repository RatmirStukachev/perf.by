from django.test import TestCase, Client
from django.urls import reverse
from .models import Product, Category

class StoreViewsTest(TestCase):
    def setUp(self):
        self.client = Client()
        self.category = Category.objects.create(title="Тест Категория", slug="test-category")
        self.product = Product.objects.create(
            title="Тест Товар",
            slug="test-product",
            price=10.00,
            category=self.category,
            is_active=True,
            is_hit=True
        )

    def test_home_view(self):
        response = self.client.get(reverse('store:home'))
        self.assertEqual(response.status_code, 200)
        self.assertContains(response, "Тест Товар")

    def test_catalog_view(self):
        response = self.client.get(reverse('store:catalog'))
        self.assertEqual(response.status_code, 200)
        self.assertContains(response, "Тест Товар")

    def test_product_detail_view(self):
        response = self.client.get(reverse('store:product_detail', args=['test-product']))
        self.assertEqual(response.status_code, 200)
        self.assertContains(response, "Тест Товар")
        self.assertContains(response, "10,00 BYN")

    def test_search_view(self):
        response = self.client.get(reverse('store:search') + '?q=Тест')
        self.assertEqual(response.status_code, 200)
        self.assertContains(response, "Тест Товар")

    def test_cart_add_and_view(self):
        response = self.client.post(reverse('store:cart_add', args=[self.product.id]), {'quantity': 2})
        self.assertRedirects(response, reverse('store:cart_detail'))

        response = self.client.get(reverse('store:cart_detail'))
        self.assertEqual(response.status_code, 200)
        self.assertContains(response, "Тест Товар")
        self.assertContains(response, "20,00 BYN") # 10.00 * 2
