import api from './axios';
import { Doctor, PaginatedResponse } from '../types';

export const getDoctors = (params?: { search?: string }) =>
  api.get<PaginatedResponse<Doctor>>('/api/doctors/', { params });

export const getDoctor = (id: number) =>
  api.get<Doctor>(`/api/doctors/${id}/`);

export const createDoctor = (data: Partial<Doctor>) =>
  api.post<Doctor>('/api/doctors/', data);

export const updateDoctor = (id: number, data: Partial<Doctor>) =>
  api.put<Doctor>(`/api/doctors/${id}/`, data);

export const deleteDoctor = (id: number) =>
  api.delete(`/api/doctors/${id}/`);
