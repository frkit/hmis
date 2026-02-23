from rest_framework import serializers
from apps.patients.serializers import PatientListSerializer
from apps.doctors.serializers import DoctorListSerializer
from .models import LabTest, LabOrder, LabOrderItem, LabReport


class LabTestSerializer(serializers.ModelSerializer):
    class Meta:
        model = LabTest
        fields = '__all__'
        read_only_fields = ['id']


class LabReportSerializer(serializers.ModelSerializer):
    reported_by_name = serializers.SerializerMethodField()

    class Meta:
        model = LabReport
        fields = ['id', 'order_item', 'result_value', 'result_text',
                  'is_abnormal', 'reported_at', 'reported_by',
                  'reported_by_name', 'notes', 'updated_at']
        read_only_fields = ['id', 'reported_at', 'reported_by', 'updated_at']

    def get_reported_by_name(self, obj):
        if obj.reported_by:
            return obj.reported_by.get_full_name()
        return None

    def create(self, validated_data):
        request = self.context.get('request')
        if request and request.user.is_authenticated:
            validated_data['reported_by'] = request.user
        report = super().create(validated_data)
        # Mark order item as completed
        report.order_item.status = 'completed'
        report.order_item.save()
        return report


class LabOrderItemSerializer(serializers.ModelSerializer):
    test_detail = LabTestSerializer(source='test', read_only=True)
    report = LabReportSerializer(read_only=True)

    class Meta:
        model = LabOrderItem
        fields = ['id', 'order', 'test', 'test_detail', 'status', 'report']


class LabOrderSerializer(serializers.ModelSerializer):
    items = LabOrderItemSerializer(many=True, read_only=True)
    patient_detail = PatientListSerializer(source='patient', read_only=True)
    doctor_detail = DoctorListSerializer(source='doctor', read_only=True)
    ordered_by_name = serializers.SerializerMethodField()

    class Meta:
        model = LabOrder
        fields = ['id', 'patient', 'patient_detail', 'doctor', 'doctor_detail',
                  'ordered_at', 'status', 'notes',
                  'ordered_by', 'ordered_by_name', 'items']
        read_only_fields = ['id', 'ordered_at', 'ordered_by']

    def get_ordered_by_name(self, obj):
        if obj.ordered_by:
            return obj.ordered_by.get_full_name()
        return None

    def create(self, validated_data):
        request = self.context.get('request')
        if request and request.user.is_authenticated:
            validated_data['ordered_by'] = request.user
        return super().create(validated_data)


class LabOrderWriteSerializer(serializers.ModelSerializer):
    test_ids = serializers.PrimaryKeyRelatedField(
        queryset=LabTest.objects.filter(is_active=True),
        many=True, write_only=True, required=False
    )

    class Meta:
        model = LabOrder
        fields = ['id', 'patient', 'doctor', 'notes', 'status', 'test_ids']
        read_only_fields = ['id']

    def create(self, validated_data):
        test_ids = validated_data.pop('test_ids', [])
        request = self.context.get('request')
        if request and request.user.is_authenticated:
            validated_data['ordered_by'] = request.user
        order = LabOrder.objects.create(**validated_data)
        for test in test_ids:
            LabOrderItem.objects.create(order=order, test=test)
        return order


class LabOrderListSerializer(serializers.ModelSerializer):
    patient_name = serializers.SerializerMethodField()
    doctor_name = serializers.SerializerMethodField()
    test_count = serializers.SerializerMethodField()

    class Meta:
        model = LabOrder
        fields = ['id', 'patient', 'patient_name', 'doctor', 'doctor_name',
                  'ordered_at', 'status', 'test_count']

    def get_patient_name(self, obj):
        return obj.patient.full_name

    def get_doctor_name(self, obj):
        return str(obj.doctor) if obj.doctor else None

    def get_test_count(self, obj):
        return obj.items.count()
