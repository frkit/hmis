from rest_framework import viewsets, permissions
from rest_framework.filters import SearchFilter, OrderingFilter
from django_filters.rest_framework import DjangoFilterBackend
from .models import Medicine, Prescription, PrescriptionItem, Dispensing
from .serializers import (
    MedicineSerializer, PrescriptionSerializer, PrescriptionWriteSerializer,
    PrescriptionItemSerializer, DispensingSerializer
)


class MedicineViewSet(viewsets.ModelViewSet):
    queryset = Medicine.objects.all()
    serializer_class = MedicineSerializer
    permission_classes = [permissions.IsAuthenticated]
    filter_backends = [DjangoFilterBackend, SearchFilter, OrderingFilter]
    filterset_fields = ['category', 'is_active']
    search_fields = ['name', 'generic_name', 'manufacturer']
    ordering_fields = ['name', 'stock_quantity', 'expiry_date']
    ordering = ['name']


class PrescriptionViewSet(viewsets.ModelViewSet):
    queryset = Prescription.objects.select_related(
        'patient', 'doctor', 'doctor__user'
    ).prefetch_related('items__medicine', 'items__dispensings').all()
    permission_classes = [permissions.IsAuthenticated]
    filter_backends = [DjangoFilterBackend, SearchFilter, OrderingFilter]
    filterset_fields = ['patient', 'doctor', 'is_active']
    search_fields = ['patient__first_name', 'patient__last_name',
                     'doctor__user__last_name']
    ordering_fields = ['prescribed_at']
    ordering = ['-prescribed_at']

    def get_serializer_class(self):
        if self.action in ['create', 'update', 'partial_update']:
            return PrescriptionWriteSerializer
        return PrescriptionSerializer


class PrescriptionItemViewSet(viewsets.ModelViewSet):
    queryset = PrescriptionItem.objects.select_related(
        'prescription', 'medicine'
    ).prefetch_related('dispensings').all()
    serializer_class = PrescriptionItemSerializer
    permission_classes = [permissions.IsAuthenticated]
    filter_backends = [DjangoFilterBackend]
    filterset_fields = ['prescription', 'medicine']


class DispensingViewSet(viewsets.ModelViewSet):
    queryset = Dispensing.objects.select_related(
        'prescription_item__medicine', 'dispensed_by'
    ).all()
    serializer_class = DispensingSerializer
    permission_classes = [permissions.IsAuthenticated]
    filter_backends = [DjangoFilterBackend, OrderingFilter]
    filterset_fields = ['prescription_item', 'dispensed_by']
    ordering_fields = ['dispensed_at']
    ordering = ['-dispensed_at']
