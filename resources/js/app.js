import { createApp } from 'vue'
import { createPinia } from 'pinia'
import router from './routers'
import App from './Pages/App.vue'
import './axios' // Import axios configuration

const app = createApp(App)
const pinia = createPinia()

app.use(pinia)
app.use(router)
app.mount('#app')