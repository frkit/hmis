import api from './axios';
import { LabOrder, PaginatedResponse } from '../types';

export const getLabOrders = (params?: { status?: string; page?: number }) =>
  api.get<PaginatedResponse<LabOrder>>('/api/laboratory/orders/', { params });

export const getLabOrder = (id: number) =>
  api.get<LabOrder>(`/api/laboratory/orders/${id}/`);

export const createLabOrder = (data: Partial<LabOrder>) =>
  api.post<LabOrder>('/api/laboratory/orders/', data);

export const updateLabOrder = (id: number, data: Partial<LabOrder>) =>
  api.put<LabOrder>(`/api/laboratory/orders/${id}/`, data);

export const deleteLabOrder = (id: number) =>
  api.delete(`/api/laboratory/orders/${id}/`);
