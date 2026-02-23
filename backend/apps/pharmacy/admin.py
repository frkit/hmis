from django.contrib import admin
from .models import Medicine, Prescription, PrescriptionItem, Dispensing


@admin.register(Medicine)
class MedicineAdmin(admin.ModelAdmin):
    list_display = ['name', 'generic_name', 'category', 'unit',
                    'stock_quantity', 'reorder_level', 'unit_price',
                    'expiry_date', 'is_active']
    list_filter = ['category', 'is_active']
    search_fields = ['name', 'generic_name', 'manufacturer']
    readonly_fields = ['created_at', 'updated_at']


class PrescriptionItemInline(admin.TabularInline):
    model = PrescriptionItem
    extra = 1


@admin.register(Prescription)
class PrescriptionAdmin(admin.ModelAdmin):
    list_display = ['id', 'patient', 'doctor', 'prescribed_at', 'is_active']
    list_filter = ['is_active']
    search_fields = ['patient__first_name', 'patient__last_name', 'doctor__user__last_name']
    raw_id_fields = ['patient', 'doctor', 'appointment']
    inlines = [PrescriptionItemInline]


@admin.register(Dispensing)
class DispensingAdmin(admin.ModelAdmin):
    list_display = ['id', 'prescription_item', 'quantity_dispensed', 'dispensed_at', 'dispensed_by']
    raw_id_fields = ['prescription_item', 'dispensed_by']
