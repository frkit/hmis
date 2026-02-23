import api from './axios';
import { Invoice, PaginatedResponse } from '../types';

export const getInvoices = (params?: { payment_status?: string; page?: number }) =>
  api.get<PaginatedResponse<Invoice>>('/api/billing/invoices/', { params });

export const getInvoice = (id: number) =>
  api.get<Invoice>(`/api/billing/invoices/${id}/`);

export const createInvoice = (data: Partial<Invoice>) =>
  api.post<Invoice>('/api/billing/invoices/', data);

export const updateInvoice = (id: number, data: Partial<Invoice>) =>
  api.put<Invoice>(`/api/billing/invoices/${id}/`, data);

export const deleteInvoice = (id: number) =>
  api.delete(`/api/billing/invoices/${id}/`);
