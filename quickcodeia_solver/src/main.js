import { createApp } from 'vue'
import App from './App.vue'

// Vuetify
import 'vuetify/styles'
import { createVuetify } from 'vuetify'
import * as components from 'vuetify/components'
import * as directives from 'vuetify/directives'
import { createPinia } from 'pinia'
import 'highlight.js/styles/github.css'

const vuetify = createVuetify({
  components,
  directives,
})



const app = createApp(App)
app.use(vuetify)
app.use(createPinia())
app.mount('#app')