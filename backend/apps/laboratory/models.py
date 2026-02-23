from django.db import models
from django.conf import settings


class LabTest(models.Model):
    CATEGORY_CHOICES = [
        ('hematology', 'Hematology'),
        ('biochemistry', 'Biochemistry'),
        ('microbiology', 'Microbiology'),
        ('radiology', 'Radiology'),
        ('pathology', 'Pathology'),
        ('immunology', 'Immunology'),
        ('other', 'Other'),
    ]

    name = models.CharField(max_length=200)
    code = models.CharField(max_length=50, unique=True)
    category = models.CharField(max_length=30, choices=CATEGORY_CHOICES, default='other')
    price = models.DecimalField(max_digits=10, decimal_places=2, default=0)
    turnaround_hours = models.PositiveIntegerField(default=24, help_text='Expected hours to complete')
    reference_range = models.CharField(max_length=200, blank=True)
    units = models.CharField(max_length=50, blank=True)
    description = models.TextField(blank=True)
    is_active = models.BooleanField(default=True)

    class Meta:
        ordering = ['name']

    def __str__(self):
        return f"{self.code} - {self.name}"


class LabOrder(models.Model):
    STATUS_CHOICES = [
        ('ordered', 'Ordered'),
        ('sample_collected', 'Sample Collected'),
        ('processing', 'Processing'),
        ('completed', 'Completed'),
        ('cancelled', 'Cancelled'),
    ]

    patient = models.ForeignKey(
        'patients.Patient', on_delete=models.CASCADE, related_name='lab_orders'
    )
    doctor = models.ForeignKey(
        'doctors.Doctor', on_delete=models.SET_NULL,
        null=True, blank=True, related_name='lab_orders'
    )
    ordered_at = models.DateTimeField(auto_now_add=True)
    status = models.CharField(max_length=30, choices=STATUS_CHOICES, default='ordered')
    notes = models.TextField(blank=True)
    ordered_by = models.ForeignKey(
        settings.AUTH_USER_MODEL, on_delete=models.SET_NULL,
        null=True, blank=True, related_name='lab_orders_created'
    )

    class Meta:
        ordering = ['-ordered_at']

    def __str__(self):
        return f"LabOrder #{self.pk} - {self.patient}"


class LabOrderItem(models.Model):
    STATUS_CHOICES = [
        ('pending', 'Pending'),
        ('sample_collected', 'Sample Collected'),
        ('processing', 'Processing'),
        ('completed', 'Completed'),
        ('cancelled', 'Cancelled'),
    ]

    order = models.ForeignKey(LabOrder, on_delete=models.CASCADE, related_name='items')
    test = models.ForeignKey(LabTest, on_delete=models.CASCADE, related_name='order_items')
    status = models.CharField(max_length=30, choices=STATUS_CHOICES, default='pending')

    class Meta:
        unique_together = ['order', 'test']

    def __str__(self):
        return f"{self.test} in Order #{self.order_id}"


class LabReport(models.Model):
    order_item = models.OneToOneField(
        LabOrderItem, on_delete=models.CASCADE, related_name='report'
    )
    result_value = models.CharField(max_length=200, blank=True)
    result_text = models.TextField(blank=True)
    is_abnormal = models.BooleanField(default=False)
    reported_at = models.DateTimeField(auto_now_add=True)
    reported_by = models.ForeignKey(
        settings.AUTH_USER_MODEL, on_delete=models.SET_NULL,
        null=True, blank=True, related_name='lab_reports'
    )
    notes = models.TextField(blank=True)
    updated_at = models.DateTimeField(auto_now=True)

    class Meta:
        ordering = ['-reported_at']

    def __str__(self):
        return f"Report for {self.order_item} - {'Abnormal' if self.is_abnormal else 'Normal'}"
