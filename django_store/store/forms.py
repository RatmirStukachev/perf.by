from django import forms
from .models import Order

class OrderCreateForm(forms.ModelForm):
    class Meta:
        model = Order
        fields = [
            'customer_type', 'first_name', 'last_name', 'phone', 'email',
            'company_name', 'inn', 'delivery', 'payment_type', 'address', 'comment'
        ]

from .models import Feedback

class FeedbackForm(forms.ModelForm):
    class Meta:
        model = Feedback
        fields = ['name', 'email', 'message']
