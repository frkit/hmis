import api from './axios';
import { Patient, PaginatedResponse } from '../types';

export const getPatients = (params?: { search?: string; page?: number }) =>
  api.get<PaginatedResponse<Patient>>('/api/patients/', { params });

export const getPatient = (id: number) =>
  api.get<Patient>(`/api/patients/${id}/`);

export const createPatient = (data: Partial<Patient>) =>
  api.post<Patient>('/api/patients/', data);

export const updatePatient = (id: number, data: Partial<Patient>) =>
  api.put<Patient>(`/api/patients/${id}/`, data);

export const deletePatient = (id: number) =>
  api.delete(`/api/patients/${id}/`);
