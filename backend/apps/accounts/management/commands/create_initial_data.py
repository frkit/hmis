from django.core.management.base import BaseCommand
from django.utils import timezone
from datetime import date, timedelta
import random


class Command(BaseCommand):
    help = 'Create initial sample data for HMIS'

    def handle(self, *args, **kwargs):
        self.stdout.write('Creating initial data...')
        self._create_users()
        self._create_patients()
        self.stdout.write(self.style.SUCCESS('Initial data created successfully.'))

    def _create_users(self):
        from apps.accounts.models import CustomUser
        from apps.doctors.models import Doctor

        # Admin
        if not CustomUser.objects.filter(username='admin').exists():
            CustomUser.objects.create_superuser(
                username='admin', email='admin@hmis.com', password='admin123',
                role='admin', first_name='Admin', last_name='User'
            )
            self.stdout.write('  Created admin user')

        # Receptionist
        if not CustomUser.objects.filter(username='receptionist1').exists():
            CustomUser.objects.create_user(
                username='receptionist1', email='reception@hmis.com',
                password='reception123', role='receptionist',
                first_name='Jane', last_name='Smith', phone='555-0101'
            )
            self.stdout.write('  Created receptionist user')

        # Doctors
        doctor_data = [
            ('dr.jones', 'drjones@hmis.com', 'doctor123', 'Robert', 'Jones',
             'Cardiology', 'LIC-CARD-001', '555-0201', 150.00),
            ('dr.patel', 'drpatel@hmis.com', 'doctor123', 'Priya', 'Patel',
             'Pediatrics', 'LIC-PED-002', '555-0202', 120.00),
            ('dr.chen', 'drchen@hmis.com', 'doctor123', 'Wei', 'Chen',
             'Neurology', 'LIC-NEU-003', '555-0203', 180.00),
        ]
        for uname, email, pwd, fn, ln, spec, lic, phone, fee in doctor_data:
            if not CustomUser.objects.filter(username=uname).exists():
                user = CustomUser.objects.create_user(
                    username=uname, email=email, password=pwd, role='doctor',
                    first_name=fn, last_name=ln, phone=phone
                )
                Doctor.objects.get_or_create(
                    user=user,
                    defaults={
                        'specialization': spec,
                        'license_number': lic,
                        'phone': phone,
                        'available_days': 'Mon,Tue,Wed,Thu,Fri',
                        'consultation_fee': fee,
                    }
                )
                self.stdout.write(f'  Created doctor {fn} {ln}')

    def _create_patients(self):
        from apps.patients.models import Patient

        patients = [
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
        for fn, ln, dob, gender, blood, phone, email, addr, allergies, history in patients:
            patient_id = f"P{random.randint(10000, 99999)}"
            while Patient.objects.filter(patient_id=patient_id).exists():
                patient_id = f"P{random.randint(10000, 99999)}"
            if not Patient.objects.filter(first_name=fn, last_name=ln).exists():
                Patient.objects.create(
                    patient_id=patient_id,
                    first_name=fn, last_name=ln,
                    date_of_birth=dob, gender=gender,
                    blood_type=blood, phone=phone,
                    email=email, address=addr,
                    allergies=allergies,
                    medical_history=history,
                    emergency_contact_name=f"Emergency {ln}",
                    emergency_contact_phone='555-9999',
                    emergency_contact_relation='Family',
                )
                self.stdout.write(f'  Created patient {fn} {ln}')
