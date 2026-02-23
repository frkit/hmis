import React, { useState } from 'react';
import {
  AppBar, Toolbar, Typography, IconButton, Avatar, Menu, MenuItem,
  Box, Chip
} from '@mui/material';
import LogoutIcon from '@mui/icons-material/Logout';
import AccountCircleIcon from '@mui/icons-material/AccountCircle';
import NotificationsIcon from '@mui/icons-material/Notifications';
import useAuth from '../../hooks/useAuth';
import { useNavigate } from 'react-router-dom';
import { toast } from 'react-toastify';

const DRAWER_WIDTH = 240;

const roleColors: Record<string, 'primary' | 'secondary' | 'success'> = {
  admin: 'primary',
  doctor: 'success',
  receptionist: 'secondary',
};

const TopBar: React.FC = () => {
  const { user, logout } = useAuth();
  const navigate = useNavigate();
  const [anchorEl, setAnchorEl] = useState<null | HTMLElement>(null);

  const handleLogout = async () => {
    try {
      await logout();
      navigate('/login');
      toast.success('Logged out successfully');
    } catch {
      navigate('/login');
    }
  };

  return (
    <AppBar
      position="fixed"
      elevation={0}
      sx={{
        width: `calc(100% - ${DRAWER_WIDTH}px)`,
        ml: `${DRAWER_WIDTH}px`,
        bgcolor: '#fff',
        borderBottom: '1px solid #e0e0e0',
      }}
    >
      <Toolbar>
        <Typography variant="h6" fontWeight={600} color="text.primary" sx={{ flexGrow: 1 }}>
          Hospital Information Management System
        </Typography>
        <Box display="flex" alignItems="center" gap={1}>
          <IconButton size="small" sx={{ color: 'text.secondary' }}>
            <NotificationsIcon />
          </IconButton>
          {user && (
            <Chip
              label={user.role.charAt(0).toUpperCase() + user.role.slice(1)}
              color={roleColors[user.role] || 'primary'}
              size="small"
              variant="outlined"
            />
          )}
          <IconButton onClick={(e) => setAnchorEl(e.currentTarget)} size="small">
            <Avatar sx={{ width: 32, height: 32, bgcolor: '#1976d2', fontSize: 13 }}>
              {user?.first_name?.[0]}{user?.last_name?.[0]}
            </Avatar>
          </IconButton>
          <Menu anchorEl={anchorEl} open={Boolean(anchorEl)} onClose={() => setAnchorEl(null)}>
            <MenuItem disabled>
              <AccountCircleIcon sx={{ mr: 1, fontSize: 18 }} />
              {user?.first_name} {user?.last_name}
            </MenuItem>
            <MenuItem onClick={handleLogout}>
              <LogoutIcon sx={{ mr: 1, fontSize: 18 }} />
              Logout
            </MenuItem>
          </Menu>
        </Box>
      </Toolbar>
    </AppBar>
  );
};

export default TopBar;
