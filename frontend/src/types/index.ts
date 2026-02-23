export interface User {
  id: number;
  username: string;
  email: string;
  first_name: string;
  last_name: string;
  role: 'admin' | 'doctor' | 'receptionist';
}

export interface Patient {
  id: number;
  first_name: string;
  last_name: string;
  date_of_birth: string;
  gender: string;
  blood_type: string;
  phone: string;
  email: string;
  address: string;
  emergency_contact_name: string;
  emergency_contact_phone: string;
  allergies: string;
  medical_history: string;
  created_at: string;
}

export interface Doctor {
  id: number;
  user: User;
  specialization: string;
  license_number: string;
  phone: string;
  consultation_fee: string;
  available_days: string;
}

export interface Appointment {
  id: number;
  patient: number;
  patient_name: string;
  doctor: number;
  doctor_name: string;
  scheduled_at: string;
  duration_minutes: number;
  status: string;
  chief_complaint: string;
  notes: string;
}

export interface InvoiceItem {
  id?: number;
  name: string;
  quantity: number;
  unit_price: string;
}

export interface Invoice {
  id: number;
  invoice_number: string;
  patient: number;
  patient_name: string;
  items: InvoiceItem[];
  total_amount: string;
  paid_amount: string;
  payment_status: string;
  payment_method: string;
  due_date: string;
  created_at: string;
}

export interface Medicine {
  id: number;
  name: string;
  generic_name: string;
  category: string;
  unit: string;
  stock_quantity: number;
  reorder_level: number;
  unit_price: string;
  expiry_date: string;
}

export interface LabOrder {
  id: number;
  patient: number;
  patient_name: string;
  doctor: number;
  doctor_name: string;
  ordered_at: string;
  status: string;
}

export interface PaginatedResponse<T> {
  count: number;
  next: string | null;
  previous: string | null;
  results: T[];
}

export interface AuthTokens {
  access: string;
  refresh: string;
  user: User;
}
