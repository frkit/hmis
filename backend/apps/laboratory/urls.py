from django.urls import path, include
from rest_framework.routers import DefaultRouter
from .views import LabTestViewSet, LabOrderViewSet, LabOrderItemViewSet, LabReportViewSet

router = DefaultRouter()
router.register(r'tests', LabTestViewSet, basename='labtest')
router.register(r'orders', LabOrderViewSet, basename='laborder')
router.register(r'order-items', LabOrderItemViewSet, basename='laborderitem')
router.register(r'reports', LabReportViewSet, basename='labreport')

urlpatterns = [path('', include(router.urls))]
