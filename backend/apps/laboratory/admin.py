from django.contrib import admin
from .models import LabTest, LabOrder, LabOrderItem, LabReport


@admin.register(LabTest)
class LabTestAdmin(admin.ModelAdmin):
    list_display = ['code', 'name', 'category', 'price', 'turnaround_hours', 'is_active']
    list_filter = ['category', 'is_active']
    search_fields = ['name', 'code']


class LabOrderItemInline(admin.TabularInline):
    model = LabOrderItem
    extra = 1


@admin.register(LabOrder)
class LabOrderAdmin(admin.ModelAdmin):
    list_display = ['id', 'patient', 'doctor', 'ordered_at', 'status']
    list_filter = ['status']
    search_fields = ['patient__first_name', 'patient__last_name', 'doctor__user__last_name']
    raw_id_fields = ['patient', 'doctor', 'ordered_by']
    inlines = [LabOrderItemInline]
    date_hierarchy = 'ordered_at'


@admin.register(LabReport)
class LabReportAdmin(admin.ModelAdmin):
    list_display = ['id', 'order_item', 'result_value', 'is_abnormal', 'reported_at', 'reported_by']
    list_filter = ['is_abnormal']
    raw_id_fields = ['order_item', 'reported_by']
