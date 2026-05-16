import axios from 'axios'

const instance = axios.create({
  baseURL: 'http://sarwary-api.test/api',
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  }
})

// Interceptor to add token to every request
instance.interceptors.request.use((config) => {
  const token = localStorage.getItem('token')
  if (token) {
    config.headers.Authorization = `Bearer ${token}`
  }
  return config
})

// Interceptor to handle unauthorized responses (logout)
instance.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response) {
      const status = error.response.status
      
      if (status === 401) {
        localStorage.removeItem('token')
        localStorage.removeItem('user')
        if (window.location.pathname !== '/login') {
          window.location.href = '/login'
        }
      } else if (status === 403) {
        window.location.href = '/error/403'
      } else if (status === 500) {
        window.location.href = '/error/500'
      }
    }
    return Promise.reject(error)
  }
)

export default instance
