import api from './axios';
import { Medicine, PaginatedResponse } from '../types';

export const getMedicines = (params?: { low_stock?: boolean; search?: string; page?: number }) =>
  api.get<PaginatedResponse<Medicine>>('/api/pharmacy/medicines/', { params });

export const getMedicine = (id: number) =>
  api.get<Medicine>(`/api/pharmacy/medicines/${id}/`);

export const createMedicine = (data: Partial<Medicine>) =>
  api.post<Medicine>('/api/pharmacy/medicines/', data);

export const updateMedicine = (id: number, data: Partial<Medicine>) =>
  api.put<Medicine>(`/api/pharmacy/medicines/${id}/`, data);

export const deleteMedicine = (id: number) =>
  api.delete(`/api/pharmacy/medicines/${id}/`);
