from django.contrib import admin
from .models import Appointment


@admin.register(Appointment)
class AppointmentAdmin(admin.ModelAdmin):
    list_display = ['id', 'patient', 'doctor', 'scheduled_at',
                    'duration_minutes', 'status', 'created_at']
    list_filter = ['status', 'doctor__specialization']
    search_fields = ['patient__first_name', 'patient__last_name',
                     'doctor__user__last_name', 'chief_complaint']
    raw_id_fields = ['patient', 'doctor', 'created_by']
    date_hierarchy = 'scheduled_at'
