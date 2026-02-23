from rest_framework import serializers
from .models import Patient


class PatientSerializer(serializers.ModelSerializer):
    full_name = serializers.ReadOnlyField()
    age = serializers.ReadOnlyField()

    class Meta:
        model = Patient
        fields = '__all__'
        read_only_fields = ['id', 'patient_id', 'created_at', 'updated_at']


class PatientListSerializer(serializers.ModelSerializer):
    full_name = serializers.ReadOnlyField()
    age = serializers.ReadOnlyField()

    class Meta:
        model = Patient
        fields = ['id', 'patient_id', 'full_name', 'first_name', 'last_name',
                  'date_of_birth', 'age', 'gender', 'blood_type', 'phone',
                  'email', 'is_active', 'created_at']
