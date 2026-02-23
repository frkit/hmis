#!/usr/bin/env python
"""
Standalone script to populate the HMIS database with sample data.
Run from the backend directory: python create_sample_data.py
"""
import os
import sys
import django

# Setup Django environment
os.environ.setdefault('DJANGO_SETTINGS_MODULE', 'hmis.settings')
sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
django.setup()

from datetime import date, timedelta
from django.utils import timezone
import random

from apps.accounts.models import CustomUser
from apps.patients.models import Patient
from apps.doctors.models import Doctor
from apps.appointments.models import Appointment
from apps.billing.models import Invoice, InvoiceItem
from apps.pharmacy.models import Medicine, Prescription, PrescriptionItem
from apps.laboratory.models import LabTest, LabOrder, LabOrderItem


def create_users_and_doctors():
    print("Creating users and doctors...")
    # Admin
    admin, _ = CustomUser.objects.get_or_create(
        username='admin',
        defaults=dict(
            email='admin@hmis.com', role='admin',
            first_name='Admin', last_name='User', is_staff=True, is_superuser=True
        )
    )
    admin.set_password('admin123')
    admin.save()

    # Receptionist
    rec, _ = CustomUser.objects.get_or_create(
        username='receptionist1',
        defaults=dict(
            email='reception@hmis.com', role='receptionist',
            first_name='Jane', last_name='Smith', phone='555-0101'
        )
    )
    rec.set_password('reception123')
    rec.save()

    doctors = []
    doctor_data = [
        ('dr.jones', 'drjones@hmis.com', 'doctor123', 'Robert', 'Jones',
         'Cardiology', 'LIC-CARD-001', '555-0201', 150.00),
        ('dr.patel', 'drpatel@hmis.com', 'doctor123', 'Priya', 'Patel',
         'Pediatrics', 'LIC-PED-002', '555-0202', 120.00),
        ('dr.chen', 'drchen@hmis.com', 'doctor123', 'Wei', 'Chen',
         'Neurology', 'LIC-NEU-003', '555-0203', 180.00),
        ('dr.brown', 'drbrown@hmis.com', 'doctor123', 'Sarah', 'Brown',
         'Orthopedics', 'LIC-ORT-004', '555-0204', 160.00),
    ]
    for uname, email, pwd, fn, ln, spec, lic, phone, fee in doctor_data:
        user, _ = CustomUser.objects.get_or_create(
            username=uname,
            defaults=dict(
                email=email, role='doctor',
                first_name=fn, last_name=ln, phone=phone
            )
        )
        user.set_password(pwd)
        user.save()
        doctor, _ = Doctor.objects.get_or_create(
            user=user,
            defaults=dict(
                specialization=spec, license_number=lic, phone=phone,
                available_days='Mon,Tue,Wed,Thu,Fri',
                consultation_fee=fee,
            )
        )
        doctors.append(doctor)
        print(f"  Doctor: Dr. {fn} {ln} ({spec})")
    return doctors


def create_patients():
    print("Creating patients...")
    patients_data = [
        ('Alice', 'Johnson', date(1985, 3, 15), 'female', 'A+',
         '555-1001', 'alice@email.com', '123 Main St', 'Penicillin', 'Hypertension'),
        ('Bob', 'Williams', date(1970, 7, 22), 'male', 'O-',
         '555-1002', 'bob@email.com', '456 Oak Ave', 'None', 'Type 2 Diabetes'),
        ('Carol', 'Davis', date(1992, 11, 5), 'female', 'B+',
         '555-1003', 'carol@email.com', '789 Pine Rd', 'Aspirin', 'Asthma'),
        ('David', 'Martinez', date(1960, 1, 30), 'male', 'AB+',
         '555-1004', 'david@email.com', '321 Elm St', 'Sulfa drugs', 'Arthritis'),
        ('Eva', 'Brown', date(2000, 6, 18), 'female', 'A-',
         '555-1005', 'eva@email.com', '654 Maple Dr', 'None', 'No significant history'),
    ]
    patients = []
    for fn, ln, dob, gender, blood, phone, email, addr, allergies, history in patients_data:
        patient, created = Patient.objects.get_or_create(
            first_name=fn, last_name=ln,
            defaults=dict(
                date_of_birth=dob, gender=gender, blood_type=blood,
                phone=phone, email=email, address=addr,
                allergies=allergies, medical_history=history,
                emergency_contact_name=f"Emergency {ln}",
                emergency_contact_phone='555-9999',
                emergency_contact_relation='Family',
            )
        )
        patients.append(patient)
        if created:
            print(f"  Patient: {fn} {ln}")
    return patients


def create_appointments(patients, doctors):
    print("Creating appointments...")
    statuses = ['scheduled', 'confirmed', 'completed', 'cancelled']
    appointments = []
    now = timezone.now()
    for i in range(8):
        patient = random.choice(patients)
        doctor = random.choice(doctors)
        delta = timedelta(days=random.randint(-10, 10))
        appt, created = Appointment.objects.get_or_create(
            patient=patient, doctor=doctor,
            scheduled_at=now + delta,
            defaults=dict(
                duration_minutes=random.choice([15, 30, 45, 60]),
                status=random.choice(statuses),
                chief_complaint=random.choice([
                    'Chest pain', 'Headache', 'Fever', 'Back pain',
                    'Routine checkup', 'Follow-up', 'Cough', 'Fatigue'
                ]),
            )
        )
        appointments.append(appt)
    print(f"  Created/verified {len(appointments)} appointments")
    return appointments


def create_medicines():
    print("Creating medicines...")
    meds_data = [
        ('Amoxicillin', 'Amoxicillin', 'capsule', 'caps', 500, 50, 0.80, date(2026, 6, 1)),
        ('Metformin 500mg', 'Metformin', 'tablet', 'tabs', 1000, 100, 0.25, date(2026, 12, 1)),
        ('Paracetamol 500mg', 'Paracetamol', 'tablet', 'tabs', 2000, 200, 0.10, date(2025, 9, 1)),
        ('Salbutamol Inhaler', 'Salbutamol', 'inhaler', 'pcs', 50, 10, 12.00, date(2025, 6, 1)),
        ('Ibuprofen 400mg', 'Ibuprofen', 'tablet', 'tabs', 800, 80, 0.15, date(2026, 3, 1)),
        ('Omeprazole 20mg', 'Omeprazole', 'capsule', 'caps', 600, 60, 0.50, date(2026, 8, 1)),
        ('Atorvastatin 10mg', 'Atorvastatin', 'tablet', 'tabs', 300, 30, 1.20, date(2026, 1, 1)),
    ]
    medicines = []
    for name, generic, cat, unit, stock, reorder, price, expiry in meds_data:
        med, created = Medicine.objects.get_or_create(
            name=name,
            defaults=dict(
                generic_name=generic, category=cat, unit=unit,
                stock_quantity=stock, reorder_level=reorder,
                unit_price=price, expiry_date=expiry
            )
        )
        medicines.append(med)
        if created:
            print(f"  Medicine: {name}")
    return medicines


def create_lab_tests():
    print("Creating lab tests...")
    tests_data = [
        ('CBC', 'Complete Blood Count', 'hematology', 25.00, 4, '4.5-11.0 x10^9/L', 'x10^9/L'),
        ('BMP', 'Basic Metabolic Panel', 'biochemistry', 45.00, 8, 'Varies', '-'),
        ('HbA1c', 'Glycated Hemoglobin', 'biochemistry', 35.00, 24, '4.0-5.6%', '%'),
        ('LFT', 'Liver Function Test', 'biochemistry', 55.00, 12, 'Varies', '-'),
        ('UA', 'Urinalysis', 'biochemistry', 15.00, 4, 'Normal', '-'),
        ('XRAY-CHEST', 'Chest X-Ray', 'radiology', 80.00, 2, 'Normal', '-'),
        ('ECG', 'Electrocardiogram', 'other', 40.00, 1, 'Normal sinus rhythm', '-'),
    ]
    tests = []
    for code, name, cat, price, hours, ref, units in tests_data:
        test, created = LabTest.objects.get_or_create(
            code=code,
            defaults=dict(
                name=name, category=cat, price=price,
                turnaround_hours=hours, reference_range=ref, units=units
            )
        )
        tests.append(test)
        if created:
            print(f"  Lab Test: {code} - {name}")
    return tests


def create_invoices(patients, appointments):
    print("Creating sample invoices...")
    for i in range(3):
        patient = patients[i]
        appt = appointments[i] if i < len(appointments) else None
        inv, created = Invoice.objects.get_or_create(
            patient=patient,
            appointment=appt,
            defaults=dict(
                payment_status=random.choice(['pending', 'paid', 'partial']),
                payment_method=random.choice(['cash', 'card']),
                due_date=date.today() + timedelta(days=30),
            )
        )
        if created:
            items = [
                InvoiceItem(invoice=inv, name='Consultation Fee', quantity=1, unit_price=150.00),
                InvoiceItem(invoice=inv, name='Lab Tests', quantity=1, unit_price=45.00),
            ]
            InvoiceItem.objects.bulk_create(items)
            inv.total_amount = sum(i.unit_price * i.quantity for i in items)
            if inv.payment_status == 'paid':
                inv.paid_amount = inv.total_amount
            elif inv.payment_status == 'partial':
                inv.paid_amount = inv.total_amount / 2
            inv.save()
            print(f"  Invoice: {inv.invoice_number} for {patient}")


if __name__ == '__main__':
    print("=" * 50)
    print("HMIS Sample Data Population Script")
    print("=" * 50)
    doctors = create_users_and_doctors()
    patients = create_patients()
    appointments = create_appointments(patients, doctors)
    create_medicines()
    create_lab_tests()
    create_invoices(patients, appointments)
    print("=" * 50)
    print("Done! Sample data has been created.")
    print("\nLogin credentials:")
    print("  Admin:        admin / admin123")
    print("  Receptionist: receptionist1 / reception123")
    print("  Doctor:       dr.jones / doctor123")
    print("=" * 50)
