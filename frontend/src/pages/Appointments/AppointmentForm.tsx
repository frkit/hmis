import React, { useEffect, useState } from 'react';
import { useNavigate, useParams } from 'react-router-dom';
import { useForm, Controller } from 'react-hook-form';
import { yupResolver } from '@hookform/resolvers/yup';
import * as yup from 'yup';
import {
  Box, Typography, Button, Grid, TextField, MenuItem, Paper, CircularProgress, Alert
} from '@mui/material';
import ArrowBackIcon from '@mui/icons-material/ArrowBack';
import { createAppointment, getAppointment, updateAppointment } from '../../api/appointments';
import { getPatients } from '../../api/patients';
import { getDoctors } from '../../api/doctors';
import { Patient, Doctor } from '../../types';
import { toast } from 'react-toastify';

const schema = yup.object({
  patient: yup.number().required('Patient is required'),
  doctor: yup.number().required('Doctor is required'),
  scheduled_at: yup.string().required('Date and time is required'),
  duration_minutes: yup.number().min(5).required('Duration is required'),
  status: yup.string().required('Status is required'),
  chief_complaint: yup.string().default(''),
  notes: yup.string().default(''),
});

type ApptFormData = yup.InferType<typeof schema>;

const statuses = ['scheduled', 'completed', 'cancelled', 'no_show'];

const AppointmentForm: React.FC = () => {
  const { id } = useParams<{ id: string }>();
  const navigate = useNavigate();
  const isEdit = !!id;
  const [loading, setLoading] = useState(false);
  const [patients, setPatients] = useState<Patient[]>([]);
  const [doctors, setDoctors] = useState<Doctor[]>([]);
  const [fetchError, setFetchError] = useState<string | null>(null);

  const { register, handleSubmit, reset, control, formState: { errors, isSubmitting } } = useForm<ApptFormData>({
    resolver: yupResolver(schema),
    defaultValues: { duration_minutes: 30, status: 'scheduled', chief_complaint: '', notes: '' },
  });

  useEffect(() => {
    Promise.all([getPatients(), getDoctors()])
      .then(([p, d]) => { setPatients(p.data.results); setDoctors(d.data.results); })
      .catch(() => setFetchError('Failed to load patients/doctors'));

    if (isEdit && id) {
      setLoading(true);
      getAppointment(parseInt(id))
        .then(({ data }) => {
          const dt = new Date(data.scheduled_at);
          const local = new Date(dt.getTime() - dt.getTimezoneOffset() * 60000).toISOString().slice(0, 16);
          reset({ ...data, scheduled_at: local } as ApptFormData);
        })
        .catch(() => setFetchError('Failed to load appointment'))
        .finally(() => setLoading(false));
    }
  }, [id, isEdit, reset]);

  const onSubmit = async (data: ApptFormData) => {
    try {
      if (isEdit && id) {
        await updateAppointment(parseInt(id), data);
        toast.success('Appointment updated');
      } else {
        await createAppointment(data);
        toast.success('Appointment created');
      }
      navigate('/appointments');
    } catch {
      toast.error('Failed to save appointment');
    }
  };

  if (loading) return <Box display="flex" justifyContent="center" py={5}><CircularProgress /></Box>;

  return (
    <Box>
      <Box display="flex" alignItems="center" gap={2} mb={3}>
        <Button startIcon={<ArrowBackIcon />} onClick={() => navigate('/appointments')} variant="outlined">Back</Button>
        <Typography variant="h5" fontWeight={700}>{isEdit ? 'Edit Appointment' : 'New Appointment'}</Typography>
      </Box>
      {fetchError && <Alert severity="error" sx={{ mb: 2 }}>{fetchError}</Alert>}
      <Paper sx={{ p: 3, borderRadius: 2 }} elevation={2}>
        <Box component="form" onSubmit={handleSubmit(onSubmit)} noValidate>
          <Grid container spacing={2}>
            <Grid size={{ xs: 12, sm: 6 }}>
              <Controller
                name="patient"
                control={control}
                render={({ field }) => (
                  <TextField {...field} select label="Patient" fullWidth error={!!errors.patient} helperText={errors.patient?.message}>
                    {patients.map(p => <MenuItem key={p.id} value={p.id}>{p.first_name} {p.last_name}</MenuItem>)}
                  </TextField>
                )}
              />
            </Grid>
            <Grid size={{ xs: 12, sm: 6 }}>
              <Controller
                name="doctor"
                control={control}
                render={({ field }) => (
                  <TextField {...field} select label="Doctor" fullWidth error={!!errors.doctor} helperText={errors.doctor?.message}>
                    {doctors.map(d => <MenuItem key={d.id} value={d.id}>Dr. {d.user?.first_name} {d.user?.last_name}</MenuItem>)}
                  </TextField>
                )}
              />
            </Grid>
            <Grid size={{ xs: 12, sm: 6 }}>
              <TextField
                {...register('scheduled_at')}
                label="Date & Time"
                type="datetime-local"
                fullWidth
                InputLabelProps={{ shrink: true }}
                error={!!errors.scheduled_at}
                helperText={errors.scheduled_at?.message}
              />
            </Grid>
            <Grid size={{ xs: 12, sm: 3 }}>
              <TextField {...register('duration_minutes')} label="Duration (minutes)" type="number" fullWidth error={!!errors.duration_minutes} helperText={errors.duration_minutes?.message} />
            </Grid>
            <Grid size={{ xs: 12, sm: 3 }}>
              <Controller
                name="status"
                control={control}
                render={({ field }) => (
                  <TextField {...field} select label="Status" fullWidth error={!!errors.status} helperText={errors.status?.message}>
                    {statuses.map(s => <MenuItem key={s} value={s} sx={{ textTransform: 'capitalize' }}>{s.replace('_', ' ')}</MenuItem>)}
                  </TextField>
                )}
              />
            </Grid>
            <Grid size={{ xs: 12 }}>
              <TextField {...register('chief_complaint')} label="Chief Complaint" fullWidth multiline rows={2} />
            </Grid>
            <Grid size={{ xs: 12 }}>
              <TextField {...register('notes')} label="Notes" fullWidth multiline rows={3} />
            </Grid>
          </Grid>

          <Box display="flex" gap={2} justifyContent="flex-end" mt={3}>
            <Button variant="outlined" onClick={() => navigate('/appointments')}>Cancel</Button>
            <Button type="submit" variant="contained" disabled={isSubmitting} sx={{ minWidth: 120 }}>
              {isSubmitting ? <CircularProgress size={22} color="inherit" /> : isEdit ? 'Update' : 'Create'}
            </Button>
          </Box>
        </Box>
      </Paper>
    </Box>
  );
};

export default AppointmentForm;
