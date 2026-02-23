import React, { useEffect, useState, useCallback } from 'react';
import {
  Grid, Card, CardContent, Typography, Box, CircularProgress,
  Table, TableBody, TableCell, TableContainer, TableHead, TableRow,
  Paper, Chip, Alert
} from '@mui/material';
import PeopleIcon from '@mui/icons-material/People';
import LocalHospitalIcon from '@mui/icons-material/LocalHospital';
import EventIcon from '@mui/icons-material/Event';
import ReceiptIcon from '@mui/icons-material/Receipt';
import WarningIcon from '@mui/icons-material/Warning';
import {
  BarChart, Bar, XAxis, YAxis, CartesianGrid, Tooltip, ResponsiveContainer,
  LineChart, Line, Legend
} from 'recharts';
import { getPatients } from '../../api/patients';
import { getDoctors } from '../../api/doctors';
import { getAppointments } from '../../api/appointments';
import { getInvoices } from '../../api/billing';
import { getMedicines } from '../../api/pharmacy';
import { Medicine, Appointment } from '../../types';
import { format } from 'date-fns';

const appointmentData = [
  { month: 'Jan', appointments: 65 },
  { month: 'Feb', appointments: 78 },
  { month: 'Mar', appointments: 90 },
  { month: 'Apr', appointments: 81 },
  { month: 'May', appointments: 95 },
  { month: 'Jun', appointments: 110 },
  { month: 'Jul', appointments: 102 },
];

const revenueData = [
  { month: 'Jan', revenue: 42000 },
  { month: 'Feb', revenue: 55000 },
  { month: 'Mar', revenue: 48000 },
  { month: 'Apr', revenue: 61000 },
  { month: 'May', revenue: 70000 },
  { month: 'Jun', revenue: 85000 },
  { month: 'Jul', revenue: 79000 },
];

interface StatsCardProps {
  title: string;
  value: number | string;
  icon: React.ReactNode;
  color: string;
  loading?: boolean;
}

const StatsCard: React.FC<StatsCardProps> = ({ title, value, icon, color, loading }) => (
  <Card sx={{ borderRadius: 2, boxShadow: 2 }}>
    <CardContent>
      <Box display="flex" justifyContent="space-between" alignItems="flex-start">
        <Box>
          <Typography variant="body2" color="text.secondary" gutterBottom>{title}</Typography>
          {loading ? (
            <CircularProgress size={20} />
          ) : (
            <Typography variant="h4" fontWeight={700} color="text.primary">{value}</Typography>
          )}
        </Box>
        <Box sx={{ bgcolor: color, borderRadius: 2, p: 1.5, color: '#fff' }}>{icon}</Box>
      </Box>
    </CardContent>
  </Card>
);

const statusColors: Record<string, 'default' | 'primary' | 'success' | 'warning' | 'error'> = {
  scheduled: 'primary',
  completed: 'success',
  cancelled: 'error',
  no_show: 'warning',
};

const DashboardPage: React.FC = () => {
  const [stats, setStats] = useState({ patients: 0, doctors: 0, todayAppts: 0, pendingBills: 0 });
  const [loading, setLoading] = useState(true);
  const [recentAppointments, setRecentAppointments] = useState<Appointment[]>([]);
  const [lowStockMeds, setLowStockMeds] = useState<Medicine[]>([]);
  const [error, setError] = useState<string | null>(null);

  const fetchData = useCallback(async () => {
    try {
      const today = format(new Date(), 'yyyy-MM-dd');
      const [patientsRes, doctorsRes, apptRes, billRes, medsRes] = await Promise.all([
        getPatients(),
        getDoctors(),
        getAppointments({ date: today }),
        getInvoices({ payment_status: 'pending' }),
        getMedicines({ low_stock: true }),
      ]);
      setStats({
        patients: patientsRes.data.count,
        doctors: doctorsRes.data.count,
        todayAppts: apptRes.data.count,
        pendingBills: billRes.data.count,
      });
      setRecentAppointments(apptRes.data.results.slice(0, 5));
      setLowStockMeds(medsRes.data.results);
    } catch {
      setError('Failed to load dashboard data');
    } finally {
      setLoading(false);
    }
  }, []);

  useEffect(() => { fetchData(); }, [fetchData]);

  return (
    <Box>
      <Typography variant="h5" fontWeight={700} mb={3}>Dashboard</Typography>
      {error && <Alert severity="error" sx={{ mb: 2 }}>{error}</Alert>}

      <Grid container spacing={3} mb={3}>
        <Grid size={{ xs: 12, sm: 6, md: 3 }}>
          <StatsCard title="Total Patients" value={stats.patients} icon={<PeopleIcon />} color="#1976d2" loading={loading} />
        </Grid>
        <Grid size={{ xs: 12, sm: 6, md: 3 }}>
          <StatsCard title="Total Doctors" value={stats.doctors} icon={<LocalHospitalIcon />} color="#388e3c" loading={loading} />
        </Grid>
        <Grid size={{ xs: 12, sm: 6, md: 3 }}>
          <StatsCard title="Today's Appointments" value={stats.todayAppts} icon={<EventIcon />} color="#f57c00" loading={loading} />
        </Grid>
        <Grid size={{ xs: 12, sm: 6, md: 3 }}>
          <StatsCard title="Pending Bills" value={stats.pendingBills} icon={<ReceiptIcon />} color="#d32f2f" loading={loading} />
        </Grid>
      </Grid>

      <Grid container spacing={3} mb={3}>
        <Grid size={{ xs: 12, md: 6 }}>
          <Card sx={{ borderRadius: 2, boxShadow: 2, p: 2 }}>
            <Typography variant="h6" fontWeight={600} mb={2}>Monthly Appointments</Typography>
            <ResponsiveContainer width="100%" height={220}>
              <BarChart data={appointmentData}>
                <CartesianGrid strokeDasharray="3 3" />
                <XAxis dataKey="month" />
                <YAxis />
                <Tooltip />
                <Bar dataKey="appointments" fill="#1976d2" radius={[4, 4, 0, 0]} />
              </BarChart>
            </ResponsiveContainer>
          </Card>
        </Grid>
        <Grid size={{ xs: 12, md: 6 }}>
          <Card sx={{ borderRadius: 2, boxShadow: 2, p: 2 }}>
            <Typography variant="h6" fontWeight={600} mb={2}>Monthly Revenue</Typography>
            <ResponsiveContainer width="100%" height={220}>
              <LineChart data={revenueData}>
                <CartesianGrid strokeDasharray="3 3" />
                <XAxis dataKey="month" />
                <YAxis />
                <Tooltip formatter={(v: number | undefined) => v !== undefined ? [`$${v.toLocaleString()}`, 'Revenue'] : ['', 'Revenue']} />
                <Legend />
                <Line type="monotone" dataKey="revenue" stroke="#1976d2" strokeWidth={2} dot={{ r: 4 }} />
              </LineChart>
            </ResponsiveContainer>
          </Card>
        </Grid>
      </Grid>

      <Grid container spacing={3}>
        <Grid size={{ xs: 12, md: 8 }}>
          <Card sx={{ borderRadius: 2, boxShadow: 2 }}>
            <CardContent>
              <Typography variant="h6" fontWeight={600} mb={2}>Today's Appointments</Typography>
              {loading ? (
                <Box display="flex" justifyContent="center" py={3}><CircularProgress /></Box>
              ) : recentAppointments.length === 0 ? (
                <Typography color="text.secondary" textAlign="center" py={2}>No appointments today</Typography>
              ) : (
                <TableContainer component={Paper} elevation={0}>
                  <Table size="small">
                    <TableHead>
                      <TableRow>
                        <TableCell><strong>Patient</strong></TableCell>
                        <TableCell><strong>Doctor</strong></TableCell>
                        <TableCell><strong>Time</strong></TableCell>
                        <TableCell><strong>Status</strong></TableCell>
                      </TableRow>
                    </TableHead>
                    <TableBody>
                      {recentAppointments.map((appt) => (
                        <TableRow key={appt.id} hover>
                          <TableCell>{appt.patient_name}</TableCell>
                          <TableCell>{appt.doctor_name}</TableCell>
                          <TableCell>{format(new Date(appt.scheduled_at), 'HH:mm')}</TableCell>
                          <TableCell>
                            <Chip
                              label={appt.status}
                              size="small"
                              color={statusColors[appt.status] || 'default'}
                            />
                          </TableCell>
                        </TableRow>
                      ))}
                    </TableBody>
                  </Table>
                </TableContainer>
              )}
            </CardContent>
          </Card>
        </Grid>

        <Grid size={{ xs: 12, md: 4 }}>
          <Card sx={{ borderRadius: 2, boxShadow: 2 }}>
            <CardContent>
              <Box display="flex" alignItems="center" gap={1} mb={2}>
                <WarningIcon color="warning" />
                <Typography variant="h6" fontWeight={600}>Low Stock Alerts</Typography>
              </Box>
              {loading ? (
                <Box display="flex" justifyContent="center" py={3}><CircularProgress /></Box>
              ) : lowStockMeds.length === 0 ? (
                <Typography color="text.secondary" textAlign="center" py={2}>No low stock alerts</Typography>
              ) : (
                lowStockMeds.map((med) => (
                  <Box key={med.id} p={1.5} mb={1} bgcolor="#fff3e0" borderRadius={1}>
                    <Typography variant="body2" fontWeight={600}>{med.name}</Typography>
                    <Typography variant="caption" color="text.secondary">
                      Stock: {med.stock_quantity} {med.unit} (Min: {med.reorder_level})
                    </Typography>
                  </Box>
                ))
              )}
            </CardContent>
          </Card>
        </Grid>
      </Grid>
    </Box>
  );
};

export default DashboardPage;
