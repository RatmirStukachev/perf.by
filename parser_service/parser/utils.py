import requests
from bs4 import BeautifulSoup
import re
import logging
import time
from decimal import Decimal
from .models import Category, Product, Characteristic, ProductCharacteristic

logger = logging.getLogger(__name__)

BASE_URL = "https://xn--90an0a.xn--90ais"

def get_soup(url):
    try:
        response = requests.get(url, verify=True, timeout=15)
        response.raise_for_status()
        return BeautifulSoup(response.content, 'html.parser')
    except Exception as e:
        logger.error(f"Failed to fetch {url}: {e}")
        return None

def parse_product(url, category):
    soup = get_soup(url)
    if not soup:
        return

    title = soup.title.text.strip() if soup.title else "Unknown Product"
    title = title.split(' - ')[0].strip()

    price_val = Decimal('0.00')
    price_tag = soup.find('div', class_=lambda x: x and 'price' in x.lower())
    if price_tag:
        price_text = re.sub(r'[^\d.]', '', price_tag.text.replace(',', '.'))
        try:
            price_val = Decimal(price_text)
        except:
            pass

    in_stock = True
    stock_text = soup.find(string=re.compile("В наличии"))
    if not stock_text:
        stock_text = soup.find(string=re.compile("Нет в наличии"))
        if stock_text:
            in_stock = False

    desc_text = ""
    tabs_body = soup.find_all('div', class_=lambda c: c and 'tabs-body' in c)
    if len(tabs_body) > 2:
        desc_text = tabs_body[2].text.strip()
    elif len(tabs_body) > 0:
        desc_text = tabs_body[-1].text.strip()

    img_url = ""
    img = soup.find('img', class_=lambda c: c and 'product' in c.lower() and 'image' in c.lower())
    if not img:
        imgs = soup.find_all('img', src=re.compile(r'/files/products/.*'))
        if imgs:
            img_url = imgs[0].get('src')
    else:
        img_url = img.get('src')

    if img_url and not img_url.startswith('http'):
        img_url = BASE_URL + img_url

    import hashlib
    url_hash = int(hashlib.md5(url.encode()).hexdigest()[:8], 16)

    product, created = Product.objects.update_or_create(
        source_url=url,
        defaults={
            'zoomos_id': url_hash,
            'name': title,
            'category': category,
            'description': desc_text,
            'price': price_val,
            'in_stock': in_stock,
            'main_image': img_url
        }
    )

    if tabs_body:
        chars_tab = tabs_body[0]
        rows = chars_tab.find_all('li', class_='data-list__row')
        for row in rows:
            name_tag = row.find('span', class_='data-list__title')
            val_tag = row.find('span', class_='data-list__description')
            if name_tag and val_tag:
                char_name = name_tag.text.strip()
                char_val = val_tag.text.strip()

                unit = ""
                unit_match = re.search(r'\s+([а-яА-Яa-zA-Z]+)$', char_val)
                if unit_match:
                    unit = unit_match.group(1)
                    char_val = char_val[:unit_match.start()].strip()

                char_obj, _ = Characteristic.objects.get_or_create(name=char_name, defaults={'unit': unit})
                if not char_obj.unit and unit:
                    char_obj.unit = unit
                    char_obj.save()

                ProductCharacteristic.objects.update_or_create(
                    product=product,
                    characteristic=char_obj,
                    defaults={'value': char_val}
                )

def parse_category(url, parent_category=None):
    soup = get_soup(url)
    if not soup:
        return

    title = soup.title.text.strip() if soup.title else "Unknown Category"
    title = title.split(' - ')[0].strip()

    import hashlib
    url_hash = int(hashlib.md5(url.encode()).hexdigest()[:8], 16)

    category, created = Category.objects.update_or_create(
        url=url,
        defaults={
            'name': title,
            'zoomos_id': url_hash,
            'parent': parent_category
        }
    )

    product_links = []
    for a in soup.find_all('a', href=True):
        if '/products/' in a['href'] and a.text.strip():
            full_link = a['href'] if a['href'].startswith('http') else BASE_URL + a['href']
            product_links.append(full_link)

    product_links = list(set(product_links))
    logger.info(f"Found {len(product_links)} products in category {title} page 1")

    for link in product_links:
        parse_product(link, category)
        time.sleep(0.1)

    paginator = soup.find('div', class_=lambda c: c and 'pagination' in c)
    if paginator:
        pages = []
        for a in paginator.find_all('a', href=True):
            if 'page=' in a['href'] or 'p=' in a['href']:
                full_link = a['href'] if a['href'].startswith('http') else BASE_URL + a['href']
                pages.append(full_link)

        pages = list(set(pages))
        for p_url in pages:
            p_soup = get_soup(p_url)
            if p_soup:
                p_links = []
                for a in p_soup.find_all('a', href=True):
                    if '/products/' in a['href'] and a.text.strip():
                        full_link = a['href'] if a['href'].startswith('http') else BASE_URL + a['href']
                        p_links.append(full_link)
                p_links = list(set(p_links))
                logger.info(f"Found {len(p_links)} products on page {p_url}")
                for link in p_links:
                    parse_product(link, category)
                    time.sleep(0.1)

def run_full_parser():
    Category.objects.all().delete()
    Product.objects.all().delete()
    Characteristic.objects.all().delete()

    soup = get_soup(BASE_URL + "/catalog")
    if not soup:
        parse_category(BASE_URL + "/catalog/elektroinstrument")
        return

    cats = []
    for a in soup.find_all('a', href=True):
        if '/catalog/' in a['href'] and a['href'] != (BASE_URL + "/catalog") and a.text.strip():
            if '#' not in a['href'] and '?page' not in a['href']:
                cats.append(a['href'] if a['href'].startswith('http') else BASE_URL + a['href'])

    cats = list(set(cats))
    logger.info(f"Found {len(cats)} root categories to parse.")

    for cat_url in cats:
        parse_category(cat_url)
        time.sleep(1)
