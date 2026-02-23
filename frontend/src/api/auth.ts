import api from './axios';
import { AuthTokens } from '../types';

export const login = (username: string, password: string) =>
  api.post<AuthTokens>('/api/auth/login/', { username, password });

export const logout = (refresh: string) =>
  api.post('/api/auth/logout/', { refresh });

export const refreshToken = (refresh: string) =>
  api.post<{ access: string }>('/api/auth/token/refresh/', { refresh });
