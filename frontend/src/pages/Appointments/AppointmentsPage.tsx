import React, { useEffect, useState, useCallback } from 'react';
import {
  Box, Typography, Button, TextField, MenuItem, Table, TableBody, TableCell,
  TableContainer, TableHead, TableRow, Paper, IconButton, Chip, CircularProgress,
  Alert, Dialog, DialogTitle, DialogContent, DialogContentText, DialogActions, Grid
} from '@mui/material';
import AddIcon from '@mui/icons-material/Add';
import EditIcon from '@mui/icons-material/Edit';
import DeleteIcon from '@mui/icons-material/Delete';
import { getAppointments, deleteAppointment } from '../../api/appointments';
import { Appointment } from '../../types';
import { format } from 'date-fns';
import { toast } from 'react-toastify';
import { useNavigate } from 'react-router-dom';

const statusColors: Record<string, 'default' | 'primary' | 'success' | 'warning' | 'error'> = {
  scheduled: 'primary',
  completed: 'success',
  cancelled: 'error',
  no_show: 'warning',
};

const AppointmentsPage: React.FC = () => {
  const [appointments, setAppointments] = useState<Appointment[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);
  const [statusFilter, setStatusFilter] = useState('');
  const [dateFilter, setDateFilter] = useState('');
  const [deleteId, setDeleteId] = useState<number | null>(null);
  const [deleting, setDeleting] = useState(false);
  const navigate = useNavigate();

  const fetchAppointments = useCallback(async () => {
    setLoading(true);
    try {
      const params: { status?: string; date?: string } = {};
      if (statusFilter) params.status = statusFilter;
      if (dateFilter) params.date = dateFilter;
      const { data } = await getAppointments(params);
      setAppointments(data.results);
    } catch {
      setError('Failed to load appointments');
    } finally {
      setLoading(false);
    }
  }, [statusFilter, dateFilter]);

  useEffect(() => { fetchAppointments(); }, [fetchAppointments]);

  const handleDelete = async () => {
    if (!deleteId) return;
    setDeleting(true);
    try {
      await deleteAppointment(deleteId);
      toast.success('Appointment deleted');
      setDeleteId(null);
      fetchAppointments();
    } catch {
      toast.error('Failed to delete appointment');
    } finally {
      setDeleting(false);
    }
  };

  return (
    <Box>
      <Box display="flex" justifyContent="space-between" alignItems="center" mb={3}>
        <Typography variant="h5" fontWeight={700}>Appointments</Typography>
        <Button variant="contained" startIcon={<AddIcon />} onClick={() => navigate('/appointments/new')} sx={{ borderRadius: 2 }}>
          New Appointment
        </Button>
      </Box>

      <Paper sx={{ p: 2, mb: 2, borderRadius: 2 }} elevation={2}>
        <Grid container spacing={2} alignItems="center">
          <Grid size={{ xs: 12, sm: 4 }}>
            <TextField
              label="Filter by Date"
              type="date"
              value={dateFilter}
              onChange={(e) => setDateFilter(e.target.value)}
              size="small"
              fullWidth
              InputLabelProps={{ shrink: true }}
            />
          </Grid>
          <Grid size={{ xs: 12, sm: 4 }}>
            <TextField
              select label="Filter by Status"
              value={statusFilter}
              onChange={(e) => setStatusFilter(e.target.value)}
              size="small"
              fullWidth
            >
              <MenuItem value="">All Statuses</MenuItem>
              <MenuItem value="scheduled">Scheduled</MenuItem>
              <MenuItem value="completed">Completed</MenuItem>
              <MenuItem value="cancelled">Cancelled</MenuItem>
              <MenuItem value="no_show">No Show</MenuItem>
            </TextField>
          </Grid>
          <Grid size={{ xs: 12, sm: 4 }}>
            <Button variant="outlined" onClick={() => { setStatusFilter(''); setDateFilter(''); }}>Clear Filters</Button>
          </Grid>
        </Grid>
      </Paper>

      {error && <Alert severity="error" sx={{ mb: 2 }}>{error}</Alert>}

      <Paper sx={{ borderRadius: 2 }} elevation={2}>
        {loading ? (
          <Box display="flex" justifyContent="center" py={5}><CircularProgress /></Box>
        ) : (
          <TableContainer>
            <Table>
              <TableHead sx={{ bgcolor: '#f5f5f5' }}>
                <TableRow>
                  <TableCell><strong>Patient</strong></TableCell>
                  <TableCell><strong>Doctor</strong></TableCell>
                  <TableCell><strong>Date & Time</strong></TableCell>
                  <TableCell><strong>Duration</strong></TableCell>
                  <TableCell><strong>Chief Complaint</strong></TableCell>
                  <TableCell><strong>Status</strong></TableCell>
                  <TableCell align="center"><strong>Actions</strong></TableCell>
                </TableRow>
              </TableHead>
              <TableBody>
                {appointments.length === 0 ? (
                  <TableRow>
                    <TableCell colSpan={7} align="center" sx={{ py: 4 }}>
                      <Typography color="text.secondary">No appointments found</Typography>
                    </TableCell>
                  </TableRow>
                ) : appointments.map((appt) => (
                  <TableRow key={appt.id} hover>
                    <TableCell>{appt.patient_name}</TableCell>
                    <TableCell>{appt.doctor_name}</TableCell>
                    <TableCell>{format(new Date(appt.scheduled_at), 'MMM d, yyyy HH:mm')}</TableCell>
                    <TableCell>{appt.duration_minutes} min</TableCell>
                    <TableCell>{appt.chief_complaint || '-'}</TableCell>
                    <TableCell>
                      <Chip label={appt.status} size="small" color={statusColors[appt.status] || 'default'} />
                    </TableCell>
                    <TableCell align="center">
                      <IconButton size="small" color="primary" onClick={() => navigate(`/appointments/${appt.id}/edit`)}>
                        <EditIcon fontSize="small" />
                      </IconButton>
                      <IconButton size="small" color="error" onClick={() => setDeleteId(appt.id)}>
                        <DeleteIcon fontSize="small" />
                      </IconButton>
                    </TableCell>
                  </TableRow>
                ))}
              </TableBody>
            </Table>
          </TableContainer>
        )}
      </Paper>

      <Dialog open={!!deleteId} onClose={() => setDeleteId(null)}>
        <DialogTitle>Delete Appointment</DialogTitle>
        <DialogContent>
          <DialogContentText>Are you sure you want to delete this appointment?</DialogContentText>
        </DialogContent>
        <DialogActions>
          <Button onClick={() => setDeleteId(null)}>Cancel</Button>
          <Button onClick={handleDelete} color="error" disabled={deleting} variant="contained">
            {deleting ? <CircularProgress size={20} /> : 'Delete'}
          </Button>
        </DialogActions>
      </Dialog>
    </Box>
  );
};

export default AppointmentsPage;
