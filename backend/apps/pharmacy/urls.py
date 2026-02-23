from django.urls import path, include
from rest_framework.routers import DefaultRouter
from .views import MedicineViewSet, PrescriptionViewSet, PrescriptionItemViewSet, DispensingViewSet

router = DefaultRouter()
router.register(r'medicines', MedicineViewSet, basename='medicine')
router.register(r'prescriptions', PrescriptionViewSet, basename='prescription')
router.register(r'prescription-items', PrescriptionItemViewSet, basename='prescriptionitem')
router.register(r'dispensings', DispensingViewSet, basename='dispensing')

urlpatterns = [path('', include(router.urls))]
