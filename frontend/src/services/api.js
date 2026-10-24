import axios from 'axios'

export const TOKEN_KEY = 'orderly_token'

const api = axios.create({
  baseURL: import.meta.env.VITE_API_URL,
  timeout: 10000,
  headers: { Accept: 'application/json' },
})

api.interceptors.request.use((config) => {
  const token = sessionStorage.getItem(TOKEN_KEY)

  if (token) {
    config.headers.Authorization = `Bearer ${token}`
  }

  return config
})

api.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response?.status === 401 && error.config?.url !== '/login') {
      sessionStorage.removeItem(TOKEN_KEY)
      window.location.replace('/login')
    }

    return Promise.reject(error)
  },
)

export default api
