import React, { useEffect, useState } from 'react';
import { useNavigate, useParams } from 'react-router-dom';
import { useForm, Controller } from 'react-hook-form';
import { yupResolver } from '@hookform/resolvers/yup';
import * as yup from 'yup';
import {
  Box, Typography, Button, Grid, TextField, MenuItem, Paper,
  CircularProgress, Alert
} from '@mui/material';
import ArrowBackIcon from '@mui/icons-material/ArrowBack';
import { createPatient, getPatient, updatePatient } from '../../api/patients';
import { Patient } from '../../types';
import { toast } from 'react-toastify';

const schema = yup.object({
  first_name: yup.string().required('First name is required'),
  last_name: yup.string().required('Last name is required'),
  date_of_birth: yup.string().required('Date of birth is required'),
  gender: yup.string().required('Gender is required'),
  blood_type: yup.string().required('Blood type is required'),
  phone: yup.string().required('Phone is required'),
  email: yup.string().email('Invalid email').required('Email is required'),
  address: yup.string().required('Address is required'),
  emergency_contact_name: yup.string().default(''),
  emergency_contact_phone: yup.string().default(''),
  allergies: yup.string().default(''),
  medical_history: yup.string().default(''),
});

type PatientFormData = yup.InferType<typeof schema>;

const genders = ['Male', 'Female', 'Other'];
const bloodTypes = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];

const PatientForm: React.FC = () => {
  const { id } = useParams<{ id: string }>();
  const navigate = useNavigate();
  const isEdit = !!id;
  const [loading, setLoading] = useState(false);
  const [fetchError, setFetchError] = useState<string | null>(null);

  const { register, handleSubmit, reset, control, formState: { errors, isSubmitting } } = useForm<PatientFormData>({
    resolver: yupResolver(schema),
    defaultValues: {
      emergency_contact_name: '', emergency_contact_phone: '', allergies: '', medical_history: '',
    },
  });

  useEffect(() => {
    if (isEdit && id) {
      setLoading(true);
      getPatient(parseInt(id))
        .then(({ data }) => reset(data as PatientFormData))
        .catch(() => setFetchError('Failed to load patient data'))
        .finally(() => setLoading(false));
    }
  }, [id, isEdit, reset]);

  const onSubmit = async (data: PatientFormData) => {
    try {
      if (isEdit && id) {
        await updatePatient(parseInt(id), data as Partial<Patient>);
        toast.success('Patient updated successfully');
      } else {
        await createPatient(data as Partial<Patient>);
        toast.success('Patient created successfully');
      }
      navigate('/patients');
    } catch {
      toast.error('Failed to save patient');
    }
  };

  if (loading) return <Box display="flex" justifyContent="center" py={5}><CircularProgress /></Box>;

  return (
    <Box>
      <Box display="flex" alignItems="center" gap={2} mb={3}>
        <Button startIcon={<ArrowBackIcon />} onClick={() => navigate('/patients')} variant="outlined">
          Back
        </Button>
        <Typography variant="h5" fontWeight={700}>{isEdit ? 'Edit Patient' : 'Add Patient'}</Typography>
      </Box>

      {fetchError && <Alert severity="error" sx={{ mb: 2 }}>{fetchError}</Alert>}

      <Paper sx={{ p: 3, borderRadius: 2 }} elevation={2}>
        <Box component="form" onSubmit={handleSubmit(onSubmit)} noValidate>
          <Typography variant="h6" fontWeight={600} mb={2} color="primary">Personal Information</Typography>
          <Grid container spacing={2} mb={3}>
            <Grid size={{ xs: 12, sm: 6 }}>
              <TextField {...register('first_name')} label="First Name" fullWidth error={!!errors.first_name} helperText={errors.first_name?.message} />
            </Grid>
            <Grid size={{ xs: 12, sm: 6 }}>
              <TextField {...register('last_name')} label="Last Name" fullWidth error={!!errors.last_name} helperText={errors.last_name?.message} />
            </Grid>
            <Grid size={{ xs: 12, sm: 6 }}>
              <TextField {...register('date_of_birth')} label="Date of Birth" type="date" fullWidth InputLabelProps={{ shrink: true }} error={!!errors.date_of_birth} helperText={errors.date_of_birth?.message} />
            </Grid>
            <Grid size={{ xs: 12, sm: 3 }}>
              <Controller
                name="gender"
                control={control}
                render={({ field }) => (
                  <TextField {...field} select label="Gender" fullWidth error={!!errors.gender} helperText={errors.gender?.message}>
                    {genders.map(g => <MenuItem key={g} value={g.toLowerCase()}>{g}</MenuItem>)}
                  </TextField>
                )}
              />
            </Grid>
            <Grid size={{ xs: 12, sm: 3 }}>
              <Controller
                name="blood_type"
                control={control}
                render={({ field }) => (
                  <TextField {...field} select label="Blood Type" fullWidth error={!!errors.blood_type} helperText={errors.blood_type?.message}>
                    {bloodTypes.map(b => <MenuItem key={b} value={b}>{b}</MenuItem>)}
                  </TextField>
                )}
              />
            </Grid>
          </Grid>

          <Typography variant="h6" fontWeight={600} mb={2} color="primary">Contact Information</Typography>
          <Grid container spacing={2} mb={3}>
            <Grid size={{ xs: 12, sm: 6 }}>
              <TextField {...register('phone')} label="Phone" fullWidth error={!!errors.phone} helperText={errors.phone?.message} />
            </Grid>
            <Grid size={{ xs: 12, sm: 6 }}>
              <TextField {...register('email')} label="Email" fullWidth error={!!errors.email} helperText={errors.email?.message} />
            </Grid>
            <Grid size={{ xs: 12 }}>
              <TextField {...register('address')} label="Address" fullWidth multiline rows={2} error={!!errors.address} helperText={errors.address?.message} />
            </Grid>
          </Grid>

          <Typography variant="h6" fontWeight={600} mb={2} color="primary">Emergency Contact</Typography>
          <Grid container spacing={2} mb={3}>
            <Grid size={{ xs: 12, sm: 6 }}>
              <TextField {...register('emergency_contact_name')} label="Emergency Contact Name" fullWidth />
            </Grid>
            <Grid size={{ xs: 12, sm: 6 }}>
              <TextField {...register('emergency_contact_phone')} label="Emergency Contact Phone" fullWidth />
            </Grid>
          </Grid>

          <Typography variant="h6" fontWeight={600} mb={2} color="primary">Medical Information</Typography>
          <Grid container spacing={2} mb={3}>
            <Grid size={{ xs: 12 }}>
              <TextField {...register('allergies')} label="Allergies" fullWidth multiline rows={2} />
            </Grid>
            <Grid size={{ xs: 12 }}>
              <TextField {...register('medical_history')} label="Medical History" fullWidth multiline rows={3} />
            </Grid>
          </Grid>

          <Box display="flex" gap={2} justifyContent="flex-end">
            <Button variant="outlined" onClick={() => navigate('/patients')}>Cancel</Button>
            <Button type="submit" variant="contained" disabled={isSubmitting} sx={{ minWidth: 120 }}>
              {isSubmitting ? <CircularProgress size={22} color="inherit" /> : isEdit ? 'Update' : 'Create'}
            </Button>
          </Box>
        </Box>
      </Paper>
    </Box>
  );
};

export default PatientForm;
