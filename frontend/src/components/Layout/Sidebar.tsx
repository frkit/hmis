import React from 'react';
import { useNavigate, useLocation } from 'react-router-dom';
import {
  Drawer, List, ListItem, ListItemButton, ListItemIcon, ListItemText,
  Toolbar, Typography, Box, Divider, Avatar
} from '@mui/material';
import DashboardIcon from '@mui/icons-material/Dashboard';
import PeopleIcon from '@mui/icons-material/People';
import LocalHospitalIcon from '@mui/icons-material/LocalHospital';
import EventIcon from '@mui/icons-material/Event';
import ReceiptIcon from '@mui/icons-material/Receipt';
import MedicationIcon from '@mui/icons-material/Medication';
import ScienceIcon from '@mui/icons-material/Science';
import useAuth from '../../hooks/useAuth';

const DRAWER_WIDTH = 240;

const navItems = [
  { label: 'Dashboard', icon: <DashboardIcon />, path: '/', roles: ['admin'] },
  { label: 'Patients', icon: <PeopleIcon />, path: '/patients', roles: ['admin', 'doctor', 'receptionist'] },
  { label: 'Doctors', icon: <LocalHospitalIcon />, path: '/doctors', roles: ['admin'] },
  { label: 'Appointments', icon: <EventIcon />, path: '/appointments', roles: ['admin', 'doctor', 'receptionist'] },
  { label: 'Billing', icon: <ReceiptIcon />, path: '/billing', roles: ['admin', 'receptionist'] },
  { label: 'Pharmacy', icon: <MedicationIcon />, path: '/pharmacy', roles: ['admin', 'receptionist'] },
  { label: 'Laboratory', icon: <ScienceIcon />, path: '/laboratory', roles: ['admin', 'doctor'] },
];

const Sidebar: React.FC = () => {
  const { user, hasRole } = useAuth();
  const navigate = useNavigate();
  const location = useLocation();

  return (
    <Drawer
      variant="permanent"
      sx={{
        width: DRAWER_WIDTH,
        flexShrink: 0,
        '& .MuiDrawer-paper': {
          width: DRAWER_WIDTH,
          boxSizing: 'border-box',
          background: 'linear-gradient(180deg, #1976d2 0%, #1565c0 100%)',
          color: '#fff',
        },
      }}
    >
      <Toolbar>
        <Box display="flex" alignItems="center" gap={1}>
          <LocalHospitalIcon sx={{ fontSize: 28, color: '#fff' }} />
          <Typography variant="h6" fontWeight={700} color="#fff" noWrap>
            HMIS
          </Typography>
        </Box>
      </Toolbar>
      <Divider sx={{ borderColor: 'rgba(255,255,255,0.2)' }} />

      <Box px={2} py={2} display="flex" alignItems="center" gap={1}>
        <Avatar sx={{ bgcolor: 'rgba(255,255,255,0.3)', width: 36, height: 36, fontSize: 14 }}>
          {user?.first_name?.[0]}{user?.last_name?.[0]}
        </Avatar>
        <Box>
          <Typography variant="body2" fontWeight={600} color="#fff">
            {user?.first_name} {user?.last_name}
          </Typography>
          <Typography variant="caption" color="rgba(255,255,255,0.7)" sx={{ textTransform: 'capitalize' }}>
            {user?.role}
          </Typography>
        </Box>
      </Box>
      <Divider sx={{ borderColor: 'rgba(255,255,255,0.2)' }} />

      <List sx={{ pt: 1 }}>
        {navItems
          .filter((item) => hasRole(item.roles))
          .map((item) => {
            const isActive = location.pathname === item.path ||
              (item.path !== '/' && location.pathname.startsWith(item.path));
            return (
              <ListItem key={item.path} disablePadding sx={{ mb: 0.5 }}>
                <ListItemButton
                  onClick={() => navigate(item.path)}
                  sx={{
                    mx: 1,
                    borderRadius: 2,
                    backgroundColor: isActive ? 'rgba(255,255,255,0.2)' : 'transparent',
                    '&:hover': { backgroundColor: 'rgba(255,255,255,0.15)' },
                  }}
                >
                  <ListItemIcon sx={{ color: '#fff', minWidth: 36 }}>{item.icon}</ListItemIcon>
                  <ListItemText
                    primary={item.label}
                    primaryTypographyProps={{ fontSize: 14, fontWeight: isActive ? 700 : 400, color: '#fff' }}
                  />
                </ListItemButton>
              </ListItem>
            );
          })}
      </List>
    </Drawer>
  );
};

export default Sidebar;
