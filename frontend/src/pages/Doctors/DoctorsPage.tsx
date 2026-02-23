import React, { useEffect, useState, useCallback } from 'react';
import {
  Box, Typography, Button, TextField, InputAdornment, Table, TableBody,
  TableCell, TableContainer, TableHead, TableRow, Paper, IconButton,
  CircularProgress, Alert, Dialog, DialogTitle, DialogContent, DialogContentText, DialogActions
} from '@mui/material';
import AddIcon from '@mui/icons-material/Add';
import SearchIcon from '@mui/icons-material/Search';
import EditIcon from '@mui/icons-material/Edit';
import DeleteIcon from '@mui/icons-material/Delete';
import { getDoctors, deleteDoctor } from '../../api/doctors';
import { Doctor } from '../../types';
import { toast } from 'react-toastify';
import { useNavigate } from 'react-router-dom';

const DoctorsPage: React.FC = () => {
  const [doctors, setDoctors] = useState<Doctor[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);
  const [search, setSearch] = useState('');
  const [deleteId, setDeleteId] = useState<number | null>(null);
  const [deleting, setDeleting] = useState(false);
  const navigate = useNavigate();

  const fetchDoctors = useCallback(async () => {
    setLoading(true);
    try {
      const { data } = await getDoctors({ search });
      setDoctors(data.results);
    } catch {
      setError('Failed to load doctors');
    } finally {
      setLoading(false);
    }
  }, [search]);

  useEffect(() => { fetchDoctors(); }, [fetchDoctors]);

  const handleDelete = async () => {
    if (!deleteId) return;
    setDeleting(true);
    try {
      await deleteDoctor(deleteId);
      toast.success('Doctor deleted');
      setDeleteId(null);
      fetchDoctors();
    } catch {
      toast.error('Failed to delete doctor');
    } finally {
      setDeleting(false);
    }
  };

  return (
    <Box>
      <Box display="flex" justifyContent="space-between" alignItems="center" mb={3}>
        <Typography variant="h5" fontWeight={700}>Doctors</Typography>
        <Button variant="contained" startIcon={<AddIcon />} onClick={() => navigate('/doctors/new')} sx={{ borderRadius: 2 }}>
          Add Doctor
        </Button>
      </Box>

      <Paper sx={{ p: 2, mb: 2, borderRadius: 2 }} elevation={2}>
        <TextField
          placeholder="Search doctors..."
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
                  <TableCell><strong>Specialization</strong></TableCell>
                  <TableCell><strong>License</strong></TableCell>
                  <TableCell><strong>Phone</strong></TableCell>
                  <TableCell><strong>Consultation Fee</strong></TableCell>
                  <TableCell><strong>Available Days</strong></TableCell>
                  <TableCell align="center"><strong>Actions</strong></TableCell>
                </TableRow>
              </TableHead>
              <TableBody>
                {doctors.length === 0 ? (
                  <TableRow>
                    <TableCell colSpan={7} align="center" sx={{ py: 4 }}>
                      <Typography color="text.secondary">No doctors found</Typography>
                    </TableCell>
                  </TableRow>
                ) : doctors.map((doc) => (
                  <TableRow key={doc.id} hover>
                    <TableCell>
                      <Typography fontWeight={600}>Dr. {doc.user?.first_name} {doc.user?.last_name}</Typography>
                      <Typography variant="caption" color="text.secondary">{doc.user?.email}</Typography>
                    </TableCell>
                    <TableCell>{doc.specialization}</TableCell>
                    <TableCell>{doc.license_number}</TableCell>
                    <TableCell>{doc.phone}</TableCell>
                    <TableCell>${doc.consultation_fee}</TableCell>
                    <TableCell>{doc.available_days}</TableCell>
                    <TableCell align="center">
                      <IconButton size="small" color="primary" onClick={() => navigate(`/doctors/${doc.id}/edit`)}>
                        <EditIcon fontSize="small" />
                      </IconButton>
                      <IconButton size="small" color="error" onClick={() => setDeleteId(doc.id)}>
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
        <DialogTitle>Delete Doctor</DialogTitle>
        <DialogContent>
          <DialogContentText>Are you sure you want to delete this doctor?</DialogContentText>
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

export default DoctorsPage;
