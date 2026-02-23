import React, { useEffect, useState } from 'react';
import { useNavigate, useParams } from 'react-router-dom';
import { useForm } from 'react-hook-form';
import { yupResolver } from '@hookform/resolvers/yup';
import * as yup from 'yup';
import {
  Box, Typography, Button, Grid, TextField, Paper, CircularProgress, Alert
} from '@mui/material';
import ArrowBackIcon from '@mui/icons-material/ArrowBack';
import { createDoctor, getDoctor, updateDoctor } from '../../api/doctors';
import { Doctor } from '../../types';
import { toast } from 'react-toastify';

const schema = yup.object({
  first_name: yup.string().required('First name is required'),
  last_name: yup.string().required('Last name is required'),
  email: yup.string().email('Invalid email').required('Email is required'),
  specialization: yup.string().required('Specialization is required'),
  license_number: yup.string().required('License number is required'),
  phone: yup.string().required('Phone is required'),
  consultation_fee: yup.string().required('Consultation fee is required'),
  available_days: yup.string().default(''),
});

type DoctorFormData = yup.InferType<typeof schema>;

const DoctorForm: React.FC = () => {
  const { id } = useParams<{ id: string }>();
  const navigate = useNavigate();
  const isEdit = !!id;
  const [loading, setLoading] = useState(false);
  const [fetchError, setFetchError] = useState<string | null>(null);

  const { register, handleSubmit, reset, formState: { errors, isSubmitting } } = useForm<DoctorFormData>({
    resolver: yupResolver(schema),
    defaultValues: { available_days: '' },
  });

  useEffect(() => {
    if (isEdit && id) {
      setLoading(true);
      getDoctor(parseInt(id))
        .then(({ data }) => {
          reset({
            first_name: data.user?.first_name || '',
            last_name: data.user?.last_name || '',
            email: data.user?.email || '',
            specialization: data.specialization,
            license_number: data.license_number,
            phone: data.phone,
            consultation_fee: data.consultation_fee,
            available_days: data.available_days || '',
          });
        })
        .catch(() => setFetchError('Failed to load doctor'))
        .finally(() => setLoading(false));
    }
  }, [id, isEdit, reset]);

  const onSubmit = async (data: DoctorFormData) => {
    try {
      const payload: Record<string, unknown> = {
        specialization: data.specialization,
        license_number: data.license_number,
        phone: data.phone,
        consultation_fee: data.consultation_fee,
        available_days: data.available_days,
        user: { first_name: data.first_name, last_name: data.last_name, email: data.email },
      };
      if (isEdit && id) {
        await updateDoctor(parseInt(id), payload as Partial<Doctor>);
        toast.success('Doctor updated');
      } else {
        await createDoctor(payload as Partial<Doctor>);
        toast.success('Doctor created');
      }
      navigate('/doctors');
    } catch {
      toast.error('Failed to save doctor');
    }
  };

  if (loading) return <Box display="flex" justifyContent="center" py={5}><CircularProgress /></Box>;

  return (
    <Box>
      <Box display="flex" alignItems="center" gap={2} mb={3}>
        <Button startIcon={<ArrowBackIcon />} onClick={() => navigate('/doctors')} variant="outlined">Back</Button>
        <Typography variant="h5" fontWeight={700}>{isEdit ? 'Edit Doctor' : 'Add Doctor'}</Typography>
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
              <TextField {...register('email')} label="Email" fullWidth error={!!errors.email} helperText={errors.email?.message} />
            </Grid>
            <Grid size={{ xs: 12, sm: 6 }}>
              <TextField {...register('phone')} label="Phone" fullWidth error={!!errors.phone} helperText={errors.phone?.message} />
            </Grid>
          </Grid>

          <Typography variant="h6" fontWeight={600} mb={2} color="primary">Professional Information</Typography>
          <Grid container spacing={2} mb={3}>
            <Grid size={{ xs: 12, sm: 6 }}>
              <TextField {...register('specialization')} label="Specialization" fullWidth error={!!errors.specialization} helperText={errors.specialization?.message} />
            </Grid>
            <Grid size={{ xs: 12, sm: 6 }}>
              <TextField {...register('license_number')} label="License Number" fullWidth error={!!errors.license_number} helperText={errors.license_number?.message} />
            </Grid>
            <Grid size={{ xs: 12, sm: 6 }}>
              <TextField {...register('consultation_fee')} label="Consultation Fee ($)" fullWidth error={!!errors.consultation_fee} helperText={errors.consultation_fee?.message} />
            </Grid>
            <Grid size={{ xs: 12, sm: 6 }}>
              <TextField {...register('available_days')} label="Available Days (e.g. Mon, Wed, Fri)" fullWidth />
            </Grid>
          </Grid>

          <Box display="flex" gap={2} justifyContent="flex-end">
            <Button variant="outlined" onClick={() => navigate('/doctors')}>Cancel</Button>
            <Button type="submit" variant="contained" disabled={isSubmitting} sx={{ minWidth: 120 }}>
              {isSubmitting ? <CircularProgress size={22} color="inherit" /> : isEdit ? 'Update' : 'Create'}
            </Button>
          </Box>
        </Box>
      </Paper>
    </Box>
  );
};

export default DoctorForm;
