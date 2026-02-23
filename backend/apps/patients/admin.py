from django.contrib import admin
from .models import Patient


@admin.register(Patient)
class PatientAdmin(admin.ModelAdmin):
    list_display = ['patient_id', 'full_name', 'date_of_birth', 'gender',
                    'blood_type', 'phone', 'is_active', 'created_at']
    list_filter = ['gender', 'blood_type', 'is_active']
    search_fields = ['first_name', 'last_name', 'patient_id', 'phone', 'email']
    readonly_fields = ['patient_id', 'created_at', 'updated_at']
