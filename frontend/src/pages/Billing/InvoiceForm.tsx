import React, { useEffect, useState } from 'react';
import { useNavigate, useParams } from 'react-router-dom';
import { useForm, useFieldArray, Controller } from 'react-hook-form';
import { yupResolver } from '@hookform/resolvers/yup';
import * as yup from 'yup';
import {
  Box, Typography, Button, Grid, TextField, MenuItem, Paper,
  CircularProgress, Alert, IconButton, Table, TableBody, TableCell,
  TableHead, TableRow, Divider
} from '@mui/material';
import ArrowBackIcon from '@mui/icons-material/ArrowBack';
import AddIcon from '@mui/icons-material/Add';
import DeleteIcon from '@mui/icons-material/Delete';
import { createInvoice, getInvoice, updateInvoice } from '../../api/billing';
import { getPatients } from '../../api/patients';
import { Patient } from '../../types';
import { toast } from 'react-toastify';

const itemSchema = yup.object({
  name: yup.string().required('Item name required'),
  quantity: yup.number().min(1).required(),
  unit_price: yup.string().required('Unit price required'),
});

const schema = yup.object({
  patient: yup.number().required('Patient is required'),
  payment_status: yup.string().required('Status is required'),
  payment_method: yup.string().default(''),
  due_date: yup.string().default(''),
  paid_amount: yup.string().default('0'),
  items: yup.array(itemSchema).min(1, 'Add at least one item').required(),
});

type InvoiceFormData = yup.InferType<typeof schema>;

const paymentStatuses = ['pending', 'paid', 'overdue', 'cancelled'];
const paymentMethods = ['cash', 'card', 'insurance', 'bank_transfer'];

const InvoiceForm: React.FC = () => {
  const { id } = useParams<{ id: string }>();
  const navigate = useNavigate();
  const isEdit = !!id;
  const [loading, setLoading] = useState(false);
  const [patients, setPatients] = useState<Patient[]>([]);
  const [fetchError, setFetchError] = useState<string | null>(null);
  const [total, setTotal] = useState(0);

  const { register, handleSubmit, reset, control, watch, formState: { errors, isSubmitting } } = useForm<InvoiceFormData>({
    resolver: yupResolver(schema),
    defaultValues: {
      payment_status: 'pending', payment_method: '', due_date: '', paid_amount: '0',
      items: [{ name: '', quantity: 1, unit_price: '0' }],
    },
  });

  const { fields, append, remove } = useFieldArray({ control, name: 'items' });
  const watchedItems = watch('items');

  useEffect(() => {
    const t = watchedItems?.reduce((sum, item) => {
      return sum + (item.quantity || 0) * parseFloat(item.unit_price || '0');
    }, 0) || 0;
    setTotal(t);
  }, [watchedItems]);

  useEffect(() => {
    getPatients().then(({ data }) => setPatients(data.results)).catch(() => setFetchError('Failed to load patients'));
    if (isEdit && id) {
      setLoading(true);
      getInvoice(parseInt(id))
        .then(({ data }) => reset(data as InvoiceFormData))
        .catch(() => setFetchError('Failed to load invoice'))
        .finally(() => setLoading(false));
    }
  }, [id, isEdit, reset]);

  const onSubmit = async (data: InvoiceFormData) => {
    try {
      const payload = { ...data, total_amount: total.toFixed(2) };
      if (isEdit && id) {
        await updateInvoice(parseInt(id), payload);
        toast.success('Invoice updated');
      } else {
        await createInvoice(payload);
        toast.success('Invoice created');
      }
      navigate('/billing');
    } catch {
      toast.error('Failed to save invoice');
    }
  };

  if (loading) return <Box display="flex" justifyContent="center" py={5}><CircularProgress /></Box>;

  return (
    <Box>
      <Box display="flex" alignItems="center" gap={2} mb={3}>
        <Button startIcon={<ArrowBackIcon />} onClick={() => navigate('/billing')} variant="outlined">Back</Button>
        <Typography variant="h5" fontWeight={700}>{isEdit ? 'Edit Invoice' : 'New Invoice'}</Typography>
      </Box>
      {fetchError && <Alert severity="error" sx={{ mb: 2 }}>{fetchError}</Alert>}

      <Paper sx={{ p: 3, borderRadius: 2 }} elevation={2}>
        <Box component="form" onSubmit={handleSubmit(onSubmit)} noValidate>
          <Typography variant="h6" fontWeight={600} mb={2} color="primary">Invoice Details</Typography>
          <Grid container spacing={2} mb={3}>
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
            <Grid size={{ xs: 12, sm: 3 }}>
              <Controller
                name="payment_status"
                control={control}
                render={({ field }) => (
                  <TextField {...field} select label="Payment Status" fullWidth error={!!errors.payment_status} helperText={errors.payment_status?.message}>
                    {paymentStatuses.map(s => <MenuItem key={s} value={s} sx={{ textTransform: 'capitalize' }}>{s}</MenuItem>)}
                  </TextField>
                )}
              />
            </Grid>
            <Grid size={{ xs: 12, sm: 3 }}>
              <Controller
                name="payment_method"
                control={control}
                render={({ field }) => (
                  <TextField {...field} select label="Payment Method" fullWidth>
                    <MenuItem value="">None</MenuItem>
                    {paymentMethods.map(m => <MenuItem key={m} value={m} sx={{ textTransform: 'capitalize' }}>{m.replace('_', ' ')}</MenuItem>)}
                  </TextField>
                )}
              />
            </Grid>
            <Grid size={{ xs: 12, sm: 3 }}>
              <TextField {...register('due_date')} label="Due Date" type="date" fullWidth InputLabelProps={{ shrink: true }} />
            </Grid>
            <Grid size={{ xs: 12, sm: 3 }}>
              <TextField {...register('paid_amount')} label="Paid Amount ($)" fullWidth />
            </Grid>
          </Grid>

          <Divider sx={{ my: 2 }} />
          <Box display="flex" justifyContent="space-between" alignItems="center" mb={2}>
            <Typography variant="h6" fontWeight={600} color="primary">Invoice Items</Typography>
            <Button startIcon={<AddIcon />} size="small" onClick={() => append({ name: '', quantity: 1, unit_price: '0' })}>
              Add Item
            </Button>
          </Box>

          {errors.items && typeof errors.items === 'object' && !Array.isArray(errors.items) && (
            <Alert severity="error" sx={{ mb: 1 }}>Add at least one item</Alert>
          )}

          <Table size="small">
            <TableHead>
              <TableRow sx={{ bgcolor: '#f5f5f5' }}>
                <TableCell><strong>Item Name</strong></TableCell>
                <TableCell width={120}><strong>Quantity</strong></TableCell>
                <TableCell width={140}><strong>Unit Price ($)</strong></TableCell>
                <TableCell width={120}><strong>Subtotal</strong></TableCell>
                <TableCell width={60}></TableCell>
              </TableRow>
            </TableHead>
            <TableBody>
              {fields.map((field, index) => {
                const qty = watchedItems?.[index]?.quantity || 0;
                const price = parseFloat(watchedItems?.[index]?.unit_price || '0');
                return (
                  <TableRow key={field.id}>
                    <TableCell>
                      <TextField
                        {...register(`items.${index}.name`)}
                        size="small" fullWidth
                        error={!!errors.items?.[index]?.name}
                        helperText={errors.items?.[index]?.name?.message}
                      />
                    </TableCell>
                    <TableCell>
                      <TextField {...register(`items.${index}.quantity`)} type="number" size="small" fullWidth inputProps={{ min: 1 }} />
                    </TableCell>
                    <TableCell>
                      <TextField {...register(`items.${index}.unit_price`)} size="small" fullWidth />
                    </TableCell>
                    <TableCell>${(qty * price).toFixed(2)}</TableCell>
                    <TableCell>
                      <IconButton size="small" color="error" onClick={() => remove(index)} disabled={fields.length === 1}>
                        <DeleteIcon fontSize="small" />
                      </IconButton>
                    </TableCell>
                  </TableRow>
                );
              })}
            </TableBody>
          </Table>

          <Box display="flex" justifyContent="flex-end" mt={2} mb={3}>
            <Typography variant="h6" fontWeight={700}>Total: ${total.toFixed(2)}</Typography>
          </Box>

          <Box display="flex" gap={2} justifyContent="flex-end">
            <Button variant="outlined" onClick={() => navigate('/billing')}>Cancel</Button>
            <Button type="submit" variant="contained" disabled={isSubmitting} sx={{ minWidth: 120 }}>
              {isSubmitting ? <CircularProgress size={22} color="inherit" /> : isEdit ? 'Update' : 'Create Invoice'}
            </Button>
          </Box>
        </Box>
      </Paper>
    </Box>
  );
};

export default InvoiceForm;
