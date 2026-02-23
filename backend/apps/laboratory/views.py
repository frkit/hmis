from rest_framework import viewsets, permissions
from rest_framework.filters import SearchFilter, OrderingFilter
from django_filters.rest_framework import DjangoFilterBackend
from .models import LabTest, LabOrder, LabOrderItem, LabReport
from .serializers import (
    LabTestSerializer, LabOrderSerializer, LabOrderWriteSerializer,
    LabOrderListSerializer, LabOrderItemSerializer, LabReportSerializer
)


class LabTestViewSet(viewsets.ModelViewSet):
    queryset = LabTest.objects.all()
    serializer_class = LabTestSerializer
    permission_classes = [permissions.IsAuthenticated]
    filter_backends = [DjangoFilterBackend, SearchFilter, OrderingFilter]
    filterset_fields = ['category', 'is_active']
    search_fields = ['name', 'code', 'category']
    ordering_fields = ['name', 'price', 'turnaround_hours']
    ordering = ['name']


class LabOrderViewSet(viewsets.ModelViewSet):
    queryset = LabOrder.objects.select_related(
        'patient', 'doctor', 'doctor__user', 'ordered_by'
    ).prefetch_related('items__test', 'items__report').all()
    permission_classes = [permissions.IsAuthenticated]
    filter_backends = [DjangoFilterBackend, SearchFilter, OrderingFilter]
    filterset_fields = ['status', 'patient', 'doctor']
    search_fields = ['patient__first_name', 'patient__last_name',
                     'doctor__user__last_name']
    ordering_fields = ['ordered_at', 'status']
    ordering = ['-ordered_at']

    def get_serializer_class(self):
        if self.action == 'list':
            return LabOrderListSerializer
        if self.action in ['create', 'update', 'partial_update']:
            return LabOrderWriteSerializer
        return LabOrderSerializer


class LabOrderItemViewSet(viewsets.ModelViewSet):
    queryset = LabOrderItem.objects.select_related('order', 'test').all()
    serializer_class = LabOrderItemSerializer
    permission_classes = [permissions.IsAuthenticated]
    filter_backends = [DjangoFilterBackend]
    filterset_fields = ['order', 'test', 'status']


class LabReportViewSet(viewsets.ModelViewSet):
    queryset = LabReport.objects.select_related(
        'order_item__test', 'order_item__order__patient', 'reported_by'
    ).all()
    serializer_class = LabReportSerializer
    permission_classes = [permissions.IsAuthenticated]
    filter_backends = [DjangoFilterBackend, OrderingFilter]
    filterset_fields = ['is_abnormal', 'reported_by', 'order_item']
    ordering_fields = ['reported_at']
    ordering = ['-reported_at']
