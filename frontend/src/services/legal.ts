import api from '@/services/api';

export const getTerms = () => api.get('/legal/terms');
export const getConsentStatus = () => api.get('/legal/consent-status');        // cần token
export const postConsent = (payload: { version: string; accept: boolean }) =>
  api.post('/legal/consent', payload);                                          // cần token