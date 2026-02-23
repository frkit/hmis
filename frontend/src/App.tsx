import React from 'react';
import { BrowserRouter, Routes, Route, Navigate } from 'react-router-dom';
import { ThemeProvider, createTheme, CssBaseline } from '@mui/material';
import { ToastContainer } from 'react-toastify';
import 'react-toastify/dist/ReactToastify.css';

import { AuthProvider } from './contexts/AuthContext';
import PrivateRoute from './components/PrivateRoute';
import MainLayout from './components/Layout/MainLayout';

import LoginPage from './pages/Login/LoginPage';
import DashboardPage from './pages/Dashboard/DashboardPage';
import PatientsPage from './pages/Patients/PatientsPage';
import PatientForm from './pages/Patients/PatientForm';
import DoctorsPage from './pages/Doctors/DoctorsPage';
import DoctorForm from './pages/Doctors/DoctorForm';
import AppointmentsPage from './pages/Appointments/AppointmentsPage';
import AppointmentForm from './pages/Appointments/AppointmentForm';
import BillingPage from './pages/Billing/BillingPage';
import InvoiceForm from './pages/Billing/InvoiceForm';
import PharmacyPage from './pages/Pharmacy/PharmacyPage';
import LaboratoryPage from './pages/Laboratory/LaboratoryPage';

const theme = createTheme({
  palette: {
    primary: { main: '#1976d2' },
    background: { default: '#f5f5f5' },
  },
  typography: {
    fontFamily: '"Inter", "Roboto", "Helvetica", "Arial", sans-serif',
  },
  components: {
    MuiButton: {
      styleOverrides: {
        root: { textTransform: 'none', fontWeight: 600 },
      },
    },
    MuiCard: {
      styleOverrides: {
        root: { borderRadius: 12 },
      },
    },
  },
});

const App: React.FC = () => {
  return (
    <ThemeProvider theme={theme}>
      <CssBaseline />
      <AuthProvider>
        <BrowserRouter>
          <Routes>
            <Route path="/login" element={<LoginPage />} />

            <Route path="/" element={
              <PrivateRoute roles={['admin']}>
                <MainLayout><DashboardPage /></MainLayout>
              </PrivateRoute>
            } />

            <Route path="/patients" element={
              <PrivateRoute roles={['admin', 'doctor', 'receptionist']}>
                <MainLayout><PatientsPage /></MainLayout>
              </PrivateRoute>
            } />
            <Route path="/patients/new" element={
              <PrivateRoute roles={['admin', 'receptionist']}>
                <MainLayout><PatientForm /></MainLayout>
              </PrivateRoute>
            } />
            <Route path="/patients/:id/edit" element={
              <PrivateRoute roles={['admin', 'receptionist']}>
                <MainLayout><PatientForm /></MainLayout>
              </PrivateRoute>
            } />

            <Route path="/doctors" element={
              <PrivateRoute roles={['admin']}>
                <MainLayout><DoctorsPage /></MainLayout>
              </PrivateRoute>
            } />
            <Route path="/doctors/new" element={
              <PrivateRoute roles={['admin']}>
                <MainLayout><DoctorForm /></MainLayout>
              </PrivateRoute>
            } />
            <Route path="/doctors/:id/edit" element={
              <PrivateRoute roles={['admin']}>
                <MainLayout><DoctorForm /></MainLayout>
              </PrivateRoute>
            } />

            <Route path="/appointments" element={
              <PrivateRoute roles={['admin', 'doctor', 'receptionist']}>
                <MainLayout><AppointmentsPage /></MainLayout>
              </PrivateRoute>
            } />
            <Route path="/appointments/new" element={
              <PrivateRoute roles={['admin', 'receptionist']}>
                <MainLayout><AppointmentForm /></MainLayout>
              </PrivateRoute>
            } />
            <Route path="/appointments/:id/edit" element={
              <PrivateRoute roles={['admin', 'receptionist']}>
                <MainLayout><AppointmentForm /></MainLayout>
              </PrivateRoute>
            } />

            <Route path="/billing" element={
              <PrivateRoute roles={['admin', 'receptionist']}>
                <MainLayout><BillingPage /></MainLayout>
              </PrivateRoute>
            } />
            <Route path="/billing/new" element={
              <PrivateRoute roles={['admin', 'receptionist']}>
                <MainLayout><InvoiceForm /></MainLayout>
              </PrivateRoute>
            } />
            <Route path="/billing/:id/edit" element={
              <PrivateRoute roles={['admin', 'receptionist']}>
                <MainLayout><InvoiceForm /></MainLayout>
              </PrivateRoute>
            } />
            <Route path="/billing/:id" element={
              <PrivateRoute roles={['admin', 'receptionist']}>
                <MainLayout><InvoiceForm /></MainLayout>
              </PrivateRoute>
            } />

            <Route path="/pharmacy" element={
              <PrivateRoute roles={['admin', 'receptionist']}>
                <MainLayout><PharmacyPage /></MainLayout>
              </PrivateRoute>
            } />
            <Route path="/pharmacy/new" element={
              <PrivateRoute roles={['admin', 'receptionist']}>
                <MainLayout><PharmacyPage /></MainLayout>
              </PrivateRoute>
            } />
            <Route path="/pharmacy/:id/edit" element={
              <PrivateRoute roles={['admin', 'receptionist']}>
                <MainLayout><PharmacyPage /></MainLayout>
              </PrivateRoute>
            } />

            <Route path="/laboratory" element={
              <PrivateRoute roles={['admin', 'doctor']}>
                <MainLayout><LaboratoryPage /></MainLayout>
              </PrivateRoute>
            } />
            <Route path="/laboratory/new" element={
              <PrivateRoute roles={['admin', 'doctor']}>
                <MainLayout><LaboratoryPage /></MainLayout>
              </PrivateRoute>
            } />
            <Route path="/laboratory/:id/edit" element={
              <PrivateRoute roles={['admin', 'doctor']}>
                <MainLayout><LaboratoryPage /></MainLayout>
              </PrivateRoute>
            } />

            <Route path="*" element={<Navigate to="/" replace />} />
          </Routes>
        </BrowserRouter>
      </AuthProvider>
      <ToastContainer position="top-right" autoClose={3000} />
    </ThemeProvider>
  );
};

export default App;
