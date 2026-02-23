from rest_framework import serializers
from apps.accounts.models import CustomUser
from .models import Doctor


class DoctorUserSerializer(serializers.ModelSerializer):
    class Meta:
        model = CustomUser
        fields = ['id', 'username', 'first_name', 'last_name', 'email', 'phone']


class DoctorSerializer(serializers.ModelSerializer):
    user = DoctorUserSerializer(read_only=True)
    user_id = serializers.PrimaryKeyRelatedField(
        queryset=CustomUser.objects.filter(role='doctor'),
        source='user', write_only=True, required=False, allow_null=True
    )
    full_name = serializers.ReadOnlyField()
    email = serializers.ReadOnlyField()

    class Meta:
        model = Doctor
        fields = ['id', 'user', 'user_id', 'full_name', 'email',
                  'specialization', 'license_number', 'phone',
                  'available_days', 'consultation_fee', 'bio',
                  'is_active', 'created_at', 'updated_at']
        read_only_fields = ['id', 'created_at', 'updated_at']


class DoctorListSerializer(serializers.ModelSerializer):
    full_name = serializers.ReadOnlyField()
    email = serializers.ReadOnlyField()

    class Meta:
        model = Doctor
        fields = ['id', 'full_name', 'email', 'specialization',
                  'license_number', 'phone', 'available_days',
                  'consultation_fee', 'is_active']
