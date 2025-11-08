import api from '@/services/api'

export async function sendContactMail(payload: {
  name: string; email: string; subject?: string|null; message: string
}) {
  const { data } = await api.post('/support/contact', {
    name: payload.name,
    email: payload.email,
    subject: payload.subject ?? null,
    message: payload.message,
  })
  return data
}
