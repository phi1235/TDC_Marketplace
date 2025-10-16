import api from './api'

export const userService = {
  getAllUsers() {
    return api.get('/users')
  }
}
