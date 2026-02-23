import React, { useEffect, useState, useCallback } from 'react';
import {
  Box, Typography, Button, Table, TableBody, TableCell, TableContainer, TableHead, TableRow,
  Paper, Chip, CircularProgress, Alert, LinearProgress, TextField, InputAdornment,
  Dialog, DialogTitle, DialogContent, DialogContentText, DialogActions, IconButton
} from '@mui/material';
import AddIcon from '@mui/icons-material/Add';
import SearchIcon from '@mui/icons-material/Search';
import WarningIcon from '@mui/icons-material/Warning';
import EditIcon from '@mui/icons-material/Edit';
import DeleteIcon from '@mui/icons-material/Delete';
import { getMedicines, deleteMedicine } from '../../api/pharmacy';
import { Medicine } from '../../types';
import { format } from 'date-fns';
import { toast } from 'react-toastify';
import { useNavigate } from 'react-router-dom';

const PharmacyPage: React.FC = () => {
  const [medicines, setMedicines] = useState<Medicine[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);
  const [search, setSearch] = useState('');
  const [deleteId, setDeleteId] = useState<number | null>(null);
  const [deleting, setDeleting] = useState(false);
  const navigate = useNavigate();

  const fetchMedicines = useCallback(async () => {
    setLoading(true);
    try {
      const { data } = await getMedicines({ search });
      setMedicines(data.results);
    } catch {
      setError('Failed to load medicines');
    } finally {
      setLoading(false);
    }
  }, [search]);

  useEffect(() => { fetchMedicines(); }, [fetchMedicines]);

  const handleDelete = async () => {
    if (!deleteId) return;
    setDeleting(true);
    try {
      await deleteMedicine(deleteId);
      toast.success('Medicine deleted');
      setDeleteId(null);
      fetchMedicines();
    } catch {
      toast.error('Failed to delete medicine');
    } finally {
      setDeleting(false);
    }
  };

  const lowStockCount = medicines.filter(m => m.stock_quantity <= m.reorder_level).length;

  return (
    <Box>
      <Box display="flex" justifyContent="space-between" alignItems="center" mb={3}>
        <Box>
          <Typography variant="h5" fontWeight={700}>Pharmacy</Typography>
          {lowStockCount > 0 && (
            <Box display="flex" alignItems="center" gap={0.5} mt={0.5}>
              <WarningIcon fontSize="small" color="warning" />
              <Typography variant="body2" color="warning.main">{lowStockCount} item(s) below reorder level</Typography>
            </Box>
          )}
        </Box>
        <Button variant="contained" startIcon={<AddIcon />} onClick={() => navigate('/pharmacy/new')} sx={{ borderRadius: 2 }}>
          Add Medicine
        </Button>
      </Box>

      <Paper sx={{ p: 2, mb: 2, borderRadius: 2 }} elevation={2}>
        <TextField
          placeholder="Search medicines..."
          value={search}
          onChange={(e) => setSearch(e.target.value)}
          size="small"
          sx={{ width: 320 }}
          InputProps={{ startAdornment: <InputAdornment position="start"><SearchIcon fontSize="small" /></InputAdornment> }}
        />
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
                  <TableCell><strong>Name</strong></TableCell>
                  <TableCell><strong>Generic Name</strong></TableCell>
                  <TableCell><strong>Category</strong></TableCell>
                  <TableCell><strong>Unit</strong></TableCell>
                  <TableCell><strong>Stock</strong></TableCell>
                  <TableCell><strong>Stock Level</strong></TableCell>
                  <TableCell><strong>Unit Price</strong></TableCell>
                  <TableCell><strong>Expiry</strong></TableCell>
                  <TableCell align="center"><strong>Actions</strong></TableCell>
                </TableRow>
              </TableHead>
              <TableBody>
                {medicines.length === 0 ? (
                  <TableRow>
                    <TableCell colSpan={9} align="center" sx={{ py: 4 }}>
                      <Typography color="text.secondary">No medicines found</Typography>
                    </TableCell>
                  </TableRow>
                ) : medicines.map((med) => {
                  const isLow = med.stock_quantity <= med.reorder_level;
                  const stockPercent = Math.min((med.stock_quantity / (med.reorder_level * 3)) * 100, 100);
                  return (
                    <TableRow key={med.id} hover sx={{ bgcolor: isLow ? '#fff3e0' : 'inherit' }}>
                      <TableCell>
                        <Box display="flex" alignItems="center" gap={1}>
                          {isLow && <WarningIcon fontSize="small" color="warning" />}
                          <Typography fontWeight={600}>{med.name}</Typography>
                        </Box>
                      </TableCell>
                      <TableCell>{med.generic_name}</TableCell>
                      <TableCell>
                        <Chip label={med.category} size="small" variant="outlined" />
                      </TableCell>
                      <TableCell>{med.unit}</TableCell>
                      <TableCell>
                        <Typography fontWeight={isLow ? 700 : 400} color={isLow ? 'error.main' : 'text.primary'}>
                          {med.stock_quantity}
                        </Typography>
                        <Typography variant="caption" color="text.secondary">Min: {med.reorder_level}</Typography>
                      </TableCell>
                      <TableCell sx={{ minWidth: 120 }}>
                        <LinearProgress
                          variant="determinate"
                          value={stockPercent}
                          color={isLow ? 'error' : 'success'}
                          sx={{ height: 8, borderRadius: 4 }}
                        />
                      </TableCell>
                      <TableCell>${parseFloat(med.unit_price).toFixed(2)}</TableCell>
                      <TableCell>{med.expiry_date ? format(new Date(med.expiry_date), 'MMM yyyy') : '-'}</TableCell>
                      <TableCell align="center">
                        <IconButton size="small" color="primary" onClick={() => navigate(`/pharmacy/${med.id}/edit`)}>
                          <EditIcon fontSize="small" />
                        </IconButton>
                        <IconButton size="small" color="error" onClick={() => setDeleteId(med.id)}>
                          <DeleteIcon fontSize="small" />
                        </IconButton>
                      </TableCell>
                    </TableRow>
                  );
                })}
              </TableBody>
            </Table>
          </TableContainer>
        )}
      </Paper>

      <Dialog open={!!deleteId} onClose={() => setDeleteId(null)}>
        <DialogTitle>Delete Medicine</DialogTitle>
        <DialogContent>
          <DialogContentText>Are you sure you want to delete this medicine?</DialogContentText>
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

export default PharmacyPage;
