import api from './axios';
import { Appointment, PaginatedResponse } from '../types';

export const getAppointments = (params?: { date?: string; doctor?: number; status?: string; page?: number }) =>
  api.get<PaginatedResponse<Appointment>>('/api/appointments/', { params });

export const getAppointment = (id: number) =>
  api.get<Appointment>(`/api/appointments/${id}/`);

export const createAppointment = (data: Partial<Appointment>) =>
  api.post<Appointment>('/api/appointments/', data);

export const updateAppointment = (id: number, data: Partial<Appointment>) =>
  api.put<Appointment>(`/api/appointments/${id}/`, data);

export const deleteAppointment = (id: number) =>
  api.delete(`/api/appointments/${id}/`);
