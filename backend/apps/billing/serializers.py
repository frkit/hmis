from rest_framework import serializers
from apps.patients.serializers import PatientListSerializer
from .models import Invoice, InvoiceItem


class InvoiceItemSerializer(serializers.ModelSerializer):
    subtotal = serializers.ReadOnlyField()

    class Meta:
        model = InvoiceItem
        fields = ['id', 'name', 'description', 'quantity', 'unit_price', 'subtotal']


class InvoiceSerializer(serializers.ModelSerializer):
    items = InvoiceItemSerializer(many=True)
    patient_detail = PatientListSerializer(source='patient', read_only=True)
    balance_due = serializers.ReadOnlyField()

    class Meta:
        model = Invoice
        fields = ['id', 'patient', 'patient_detail', 'appointment',
                  'invoice_number', 'items', 'total_amount', 'paid_amount',
                  'balance_due', 'payment_status', 'payment_method',
                  'due_date', 'notes', 'created_at', 'updated_at']
        read_only_fields = ['id', 'invoice_number', 'created_at', 'updated_at']

    def create(self, validated_data):
        items_data = validated_data.pop('items', [])
        invoice = Invoice.objects.create(**validated_data)
        total = 0
        for item_data in items_data:
            item = InvoiceItem.objects.create(invoice=invoice, **item_data)
            total += item.subtotal
        invoice.total_amount = total
        invoice.save()
        return invoice

    def update(self, instance, validated_data):
        items_data = validated_data.pop('items', None)
        for attr, value in validated_data.items():
            setattr(instance, attr, value)
        if items_data is not None:
            instance.items.all().delete()
            total = 0
            for item_data in items_data:
                item = InvoiceItem.objects.create(invoice=instance, **item_data)
                total += item.subtotal
            instance.total_amount = total
        instance.save()
        return instance


class InvoiceListSerializer(serializers.ModelSerializer):
    patient_name = serializers.SerializerMethodField()
    balance_due = serializers.ReadOnlyField()

    class Meta:
        model = Invoice
        fields = ['id', 'invoice_number', 'patient', 'patient_name',
                  'total_amount', 'paid_amount', 'balance_due',
                  'payment_status', 'payment_method', 'due_date', 'created_at']

    def get_patient_name(self, obj):
        return obj.patient.full_name
