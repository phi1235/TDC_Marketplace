import { createApp } from 'vue'
import { createPinia } from 'pinia'
import router from './router'
import App from './App.vue'
import './style.css'
import axios from 'axios'

// Gửi cookie session với mọi request
axios.defaults.withCredentials = true
axios.defaults.baseURL = 'http://localhost:8001'; // URL Laravel

const app = createApp(App)

app.use(createPinia())
app.use(router)

app.mount('#app')