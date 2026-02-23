from django.db import models
from django.conf import settings


class Medicine(models.Model):
    CATEGORY_CHOICES = [
        ('tablet', 'Tablet'),
        ('capsule', 'Capsule'),
        ('syrup', 'Syrup'),
        ('injection', 'Injection'),
        ('cream', 'Cream'),
        ('drops', 'Drops'),
        ('inhaler', 'Inhaler'),
        ('other', 'Other'),
    ]

    name = models.CharField(max_length=200)
    generic_name = models.CharField(max_length=200, blank=True)
    category = models.CharField(max_length=20, choices=CATEGORY_CHOICES, default='tablet')
    unit = models.CharField(max_length=50, default='pcs', help_text='e.g. pcs, ml, mg')
    stock_quantity = models.PositiveIntegerField(default=0)
    reorder_level = models.PositiveIntegerField(default=10)
    unit_price = models.DecimalField(max_digits=10, decimal_places=2, default=0)
    expiry_date = models.DateField(null=True, blank=True)
    manufacturer = models.CharField(max_length=200, blank=True)
    description = models.TextField(blank=True)
    is_active = models.BooleanField(default=True)
    created_at = models.DateTimeField(auto_now_add=True)
    updated_at = models.DateTimeField(auto_now=True)

    class Meta:
        ordering = ['name']

    def __str__(self):
        return f"{self.name} ({self.generic_name})" if self.generic_name else self.name

    @property
    def is_low_stock(self):
        return self.stock_quantity <= self.reorder_level


class Prescription(models.Model):
    patient = models.ForeignKey(
        'patients.Patient', on_delete=models.CASCADE, related_name='prescriptions'
    )
    doctor = models.ForeignKey(
        'doctors.Doctor', on_delete=models.CASCADE, related_name='prescriptions'
    )
    appointment = models.ForeignKey(
        'appointments.Appointment', on_delete=models.SET_NULL,
        null=True, blank=True, related_name='prescriptions'
    )
    prescribed_at = models.DateTimeField(auto_now_add=True)
    notes = models.TextField(blank=True)
    is_active = models.BooleanField(default=True)

    class Meta:
        ordering = ['-prescribed_at']

    def __str__(self):
        return f"Rx #{self.pk} - {self.patient} by {self.doctor}"


class PrescriptionItem(models.Model):
    prescription = models.ForeignKey(
        Prescription, on_delete=models.CASCADE, related_name='items'
    )
    medicine = models.ForeignKey(
        Medicine, on_delete=models.CASCADE, related_name='prescription_items'
    )
    dosage = models.CharField(max_length=100, help_text='e.g. 500mg')
    frequency = models.CharField(max_length=100, help_text='e.g. twice daily')
    duration = models.CharField(max_length=100, help_text='e.g. 7 days')
    quantity_prescribed = models.PositiveIntegerField(default=1)
    instructions = models.TextField(blank=True)

    def __str__(self):
        return f"{self.medicine} - {self.dosage} {self.frequency}"


class Dispensing(models.Model):
    prescription_item = models.ForeignKey(
        PrescriptionItem, on_delete=models.CASCADE, related_name='dispensings'
    )
    quantity_dispensed = models.PositiveIntegerField()
    dispensed_at = models.DateTimeField(auto_now_add=True)
    dispensed_by = models.ForeignKey(
        settings.AUTH_USER_MODEL, on_delete=models.SET_NULL,
        null=True, blank=True, related_name='dispensings'
    )
    notes = models.TextField(blank=True)

    class Meta:
        ordering = ['-dispensed_at']

    def __str__(self):
        return (f"Dispensed {self.quantity_dispensed} x "
                f"{self.prescription_item.medicine} at {self.dispensed_at}")
