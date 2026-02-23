import React, { useEffect, useState, useCallback } from 'react';
import {
  Box, Typography, Button, Table, TableBody, TableCell, TableContainer, TableHead, TableRow,
  Paper, Chip, CircularProgress, Alert, MenuItem, TextField, Grid,
  Dialog, DialogTitle, DialogContent, DialogContentText, DialogActions, IconButton
} from '@mui/material';
import AddIcon from '@mui/icons-material/Add';
import EditIcon from '@mui/icons-material/Edit';
import DeleteIcon from '@mui/icons-material/Delete';
import { getLabOrders, deleteLabOrder, updateLabOrder } from '../../api/laboratory';
import { LabOrder } from '../../types';
import { format } from 'date-fns';
import { toast } from 'react-toastify';
import { useNavigate } from 'react-router-dom';

const statusColors: Record<string, 'default' | 'primary' | 'warning' | 'success' | 'error'> = {
  pending: 'warning',
  in_progress: 'primary',
  completed: 'success',
  cancelled: 'error',
};

const LaboratoryPage: React.FC = () => {
  const [orders, setOrders] = useState<LabOrder[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);
  const [statusFilter, setStatusFilter] = useState('');
  const [deleteId, setDeleteId] = useState<number | null>(null);
  const [deleting, setDeleting] = useState(false);
  const navigate = useNavigate();

  const fetchOrders = useCallback(async () => {
    setLoading(true);
    try {
      const params: { status?: string } = {};
      if (statusFilter) params.status = statusFilter;
      const { data } = await getLabOrders(params);
      setOrders(data.results);
    } catch {
      setError('Failed to load lab orders');
    } finally {
      setLoading(false);
    }
  }, [statusFilter]);

  useEffect(() => { fetchOrders(); }, [fetchOrders]);

  const handleStatusChange = async (id: number, status: string) => {
    try {
      await updateLabOrder(id, { status });
      toast.success('Status updated');
      fetchOrders();
    } catch {
      toast.error('Failed to update status');
    }
  };

  const handleDelete = async () => {
    if (!deleteId) return;
    setDeleting(true);
    try {
      await deleteLabOrder(deleteId);
      toast.success('Lab order deleted');
      setDeleteId(null);
      fetchOrders();
    } catch {
      toast.error('Failed to delete lab order');
    } finally {
      setDeleting(false);
    }
  };

  return (
    <Box>
      <Box display="flex" justifyContent="space-between" alignItems="center" mb={3}>
        <Typography variant="h5" fontWeight={700}>Laboratory</Typography>
        <Button variant="contained" startIcon={<AddIcon />} onClick={() => navigate('/laboratory/new')} sx={{ borderRadius: 2 }}>
          New Lab Order
        </Button>
      </Box>

      <Paper sx={{ p: 2, mb: 2, borderRadius: 2 }} elevation={2}>
        <Grid container spacing={2} alignItems="center">
          <Grid size={{ xs: 12, sm: 4 }}>
            <TextField
              select label="Filter by Status"
              value={statusFilter}
              onChange={(e) => setStatusFilter(e.target.value)}
              size="small"
              fullWidth
            >
              <MenuItem value="">All Statuses</MenuItem>
              <MenuItem value="pending">Pending</MenuItem>
              <MenuItem value="in_progress">In Progress</MenuItem>
              <MenuItem value="completed">Completed</MenuItem>
              <MenuItem value="cancelled">Cancelled</MenuItem>
            </TextField>
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
                  <TableCell><strong>Order ID</strong></TableCell>
                  <TableCell><strong>Patient</strong></TableCell>
                  <TableCell><strong>Doctor</strong></TableCell>
                  <TableCell><strong>Ordered At</strong></TableCell>
                  <TableCell><strong>Status</strong></TableCell>
                  <TableCell><strong>Update Status</strong></TableCell>
                  <TableCell align="center"><strong>Actions</strong></TableCell>
                </TableRow>
              </TableHead>
              <TableBody>
                {orders.length === 0 ? (
                  <TableRow>
                    <TableCell colSpan={7} align="center" sx={{ py: 4 }}>
                      <Typography color="text.secondary">No lab orders found</Typography>
                    </TableCell>
                  </TableRow>
                ) : orders.map((order) => (
                  <TableRow key={order.id} hover>
                    <TableCell><Typography fontWeight={600} color="primary">#{order.id}</Typography></TableCell>
                    <TableCell>{order.patient_name}</TableCell>
                    <TableCell>{order.doctor_name}</TableCell>
                    <TableCell>{format(new Date(order.ordered_at), 'MMM d, yyyy HH:mm')}</TableCell>
                    <TableCell>
                      <Chip label={order.status.replace('_', ' ')} size="small" color={statusColors[order.status] || 'default'} />
                    </TableCell>
                    <TableCell>
                      <TextField
                        select size="small" value={order.status}
                        onChange={(e) => handleStatusChange(order.id, e.target.value)}
                        sx={{ minWidth: 130 }}
                      >
                        <MenuItem value="pending">Pending</MenuItem>
                        <MenuItem value="in_progress">In Progress</MenuItem>
                        <MenuItem value="completed">Completed</MenuItem>
                        <MenuItem value="cancelled">Cancelled</MenuItem>
                      </TextField>
                    </TableCell>
                    <TableCell align="center">
                      <IconButton size="small" color="primary" onClick={() => navigate(`/laboratory/${order.id}/edit`)}>
                        <EditIcon fontSize="small" />
                      </IconButton>
                      <IconButton size="small" color="error" onClick={() => setDeleteId(order.id)}>
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
        <DialogTitle>Delete Lab Order</DialogTitle>
        <DialogContent>
          <DialogContentText>Are you sure you want to delete this lab order?</DialogContentText>
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

export default LaboratoryPage;
