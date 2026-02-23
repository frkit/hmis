from rest_framework import serializers
from apps.patients.serializers import PatientListSerializer
from apps.doctors.serializers import DoctorListSerializer
from .models import Medicine, Prescription, PrescriptionItem, Dispensing


class MedicineSerializer(serializers.ModelSerializer):
    is_low_stock = serializers.ReadOnlyField()

    class Meta:
        model = Medicine
        fields = '__all__'
        read_only_fields = ['id', 'created_at', 'updated_at']


class DispensingSerializer(serializers.ModelSerializer):
    dispensed_by_name = serializers.SerializerMethodField()

    class Meta:
        model = Dispensing
        fields = ['id', 'prescription_item', 'quantity_dispensed',
                  'dispensed_at', 'dispensed_by', 'dispensed_by_name', 'notes']
        read_only_fields = ['id', 'dispensed_at', 'dispensed_by']

    def get_dispensed_by_name(self, obj):
        if obj.dispensed_by:
            return obj.dispensed_by.get_full_name()
        return None

    def create(self, validated_data):
        request = self.context.get('request')
        if request and request.user.is_authenticated:
            validated_data['dispensed_by'] = request.user
        dispensing = super().create(validated_data)
        # Deduct from stock
        medicine = dispensing.prescription_item.medicine
        medicine.stock_quantity = max(0, medicine.stock_quantity - dispensing.quantity_dispensed)
        medicine.save()
        return dispensing


class PrescriptionItemSerializer(serializers.ModelSerializer):
    medicine_detail = MedicineSerializer(source='medicine', read_only=True)
    dispensings = DispensingSerializer(many=True, read_only=True)

    class Meta:
        model = PrescriptionItem
        fields = ['id', 'medicine', 'medicine_detail', 'dosage', 'frequency',
                  'duration', 'quantity_prescribed', 'instructions', 'dispensings']


class PrescriptionSerializer(serializers.ModelSerializer):
    items = PrescriptionItemSerializer(many=True, read_only=True)
    patient_detail = PatientListSerializer(source='patient', read_only=True)
    doctor_detail = DoctorListSerializer(source='doctor', read_only=True)

    class Meta:
        model = Prescription
        fields = ['id', 'patient', 'patient_detail', 'doctor', 'doctor_detail',
                  'appointment', 'prescribed_at', 'notes', 'is_active', 'items']
        read_only_fields = ['id', 'prescribed_at']


class PrescriptionWriteSerializer(serializers.ModelSerializer):
    class Meta:
        model = Prescription
        fields = ['id', 'patient', 'doctor', 'appointment', 'notes', 'is_active']
        read_only_fields = ['id']
