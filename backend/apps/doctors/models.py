from django.db import models
from django.conf import settings


class Doctor(models.Model):
    user = models.OneToOneField(
        settings.AUTH_USER_MODEL,
        on_delete=models.CASCADE,
        related_name='doctor_profile',
        null=True, blank=True,
    )
    specialization = models.CharField(max_length=100)
    license_number = models.CharField(max_length=50, unique=True)
    phone = models.CharField(max_length=20, blank=True)
    available_days = models.CharField(
        max_length=100, blank=True,
        help_text='Comma-separated days, e.g. Mon,Tue,Wed'
    )
    consultation_fee = models.DecimalField(max_digits=10, decimal_places=2, default=0)
    bio = models.TextField(blank=True)
    is_active = models.BooleanField(default=True)
    created_at = models.DateTimeField(auto_now_add=True)
    updated_at = models.DateTimeField(auto_now=True)

    class Meta:
        ordering = ['specialization', 'user__last_name']

    def __str__(self):
        if self.user:
            return f"Dr. {self.user.get_full_name()} ({self.specialization})"
        return f"Doctor #{self.pk} ({self.specialization})"

    @property
    def full_name(self):
        if self.user:
            return self.user.get_full_name()
        return ''

    @property
    def email(self):
        if self.user:
            return self.user.email
        return ''
