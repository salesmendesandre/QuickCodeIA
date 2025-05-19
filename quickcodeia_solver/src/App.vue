<template>
  <v-app>
    <!-- Barra superior fija -->
    <v-app-bar
      v-if="exerciseStore.hasStarted && !exerciseStore.showOnlyStatement"
      dark dense app fixed elevate-on-scroll
    >
      <v-toolbar-title class="d-flex align-center">
        <span class="text-h6 font-weight-bold">QuickCodeIA</span>
      </v-toolbar-title>

      <v-spacer></v-spacer>

      <h3>Estado: </h3>

      <v-chip
        v-if="exerciseStore.currentStatus == 'solving'"
        color="red"
        class="mr-2"
        style="margin: 10px;"
        >Resolviendo</v-chip>

      <v-chip
        v-if="exerciseStore.currentStatus == 'submitted'"
        color="green"
        class="mr-2"
        style="margin: 10px;"
        >Enviado</v-chip>

      <v-chip
        v-if="exerciseStore.currentStatus == 'corrected'"
        color="blue"
        class="mr-2"
        style="margin: 10px;"
        >Corregido</v-chip>

      <!-- Enviar tarea + diálogo de confirmación -->
      <v-btn
        color="primary"
        class="mr-2"
        v-if="exerciseStore.viewMode === 'student' && exerciseStore.currentStatus === 'solving' && !exerciseStore.isReadOnly"
        @click="showConfirmDialog = true"
      >
        Enviar Tarea
      </v-btn>

      <!-- Modal -->
      <v-dialog v-model="showConfirmDialog" max-width="500px">
        <v-card>
          <v-card-title class="headline">¿Confirmar envío?</v-card-title>
          <v-card-text>
            Una vez que envíes la tarea, no podrás modificarla.
            ¿Estás seguro de que deseas continuar?
          </v-card-text>

          <v-card-actions>
            <v-spacer />
            <v-btn color="grey" text @click="showConfirmDialog = false">Cancelar</v-btn>
            <v-btn color="primary" @click="confirmSubmit">Confirmar</v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>



      <v-btn @click="toggleFullScreen" icon>
        <v-icon>
          {{ isFullScreen ? 'mdi-fullscreen-exit' : 'mdi-fullscreen' }}
        </v-icon>
      </v-btn>
    </v-app-bar>

    <!-- VISTA 1: solo el enunciado -->
    <v-container
      v-if="exerciseStore.showOnlyStatement"
      class="fill-height d-flex flex-column justify-center align-center pa-8 start-page"
    >
      <v-row class="mb-4">
        <v-col cols="12" class="text-center">
          <v-img
            :src="icon"
            width="100"
            height="100"
            class="mb-4 mx-auto"
            contain
          />
          <h1 class="display-1 font-weight-bold">QuickCodeIA</h1>
          <p class="text-h6">¡Bienvenido al entorno de programación!</p>
        </v-col>


         <v-col cols="12" style="max-height: 200px; overflow-y: auto;" v-if="exerciseStore.score !== null">
          <h2>Este ejercico se encutra corregido por el profesor:</h2>
          <div class="pl-4">
              <h4>Nota : {{ exerciseStore.score }}</h4>
          </div>
          <div class="pl-4">
              <h4>Retroalimentación del profesor:</h4>
              <p class="text-h6" >{{ exerciseStore.teacherFeedback }}</p>
          </div>    
   
        </v-col>
        <v-col cols="12" style="max-height: 200px; overflow-y: auto;" v-else >
          <StatementViewer />
        </v-col>



        <v-col cols="12" class="text-center" v-if="exerciseStore.viewMode=='student'">
          <v-btn
            color="primary"
            class="mt-6"
            @click="startAndFullscreen"
            block
          >
           <span v-if="exerciseStore.currentStatus=='corrected'">Visualizar Ejercicio</span>
            <span v-else-if="exerciseStore.currentStatus=='submitted'">Visualizar Ejercicio</span>
            <span v-else-if="exerciseStore.currentStatus=='solving'">Continuar Ejercicio</span>
            <span v-else-if="exerciseStore.currentStatus==null">Empezar Ejercicio</span>
          </v-btn>
        </v-col>



        <v-col cols="12" class="text-center" v-if="exerciseStore.viewMode=='teacher'">
          <v-btn
            color="primary"
            class="mt-6"
            @click="startAndFullscreen"
            block
          >
            {{ "Revisar ejercicio" }}
          </v-btn>
        </v-col>

      </v-row>
    </v-container>

    <!-- Vista principal: editor + terminal + panel derecho -->
    <v-container v-else class="app-content pa-4" fluid>
      <v-row dense>
        <!-- Columna izquierda -->
        <v-col cols="12" md="8">
          <v-card class="pa-2 code-editor" style="background-color: #1e1e1e;">
            <v-card-title class="text-h6" style="color: aliceblue;">
              <b>Editor de Código</b>
            </v-card-title>
            <CodeEditor />
          </v-card>

          <v-card class="pa-2 mt-4" style="background-color: #1e1e1e;">
            <v-card-title class="text-h6" style="color: aliceblue;">
              <b>Terminal</b>
            </v-card-title>
            <TerminalView />
          </v-card>
        </v-col>

        <!-- Columna derecha: Tabs Enunciado / Chat -->
        <v-col cols="12" md="4">
          <v-card class="full-height-card d-flex flex-column pa-0">
            <v-tabs v-model="chatStore.activeTab" grow>
              <v-tab text="Enunciado" value="Enunciado" />
              <v-tab text="Chat" value="Chat" />
              <v-tab text="Retroalimentación" value="Feedback" v-if="exerciseStore.currentStatus=='submitted' || exerciseStore.currentStatus=='corrected'" />
            </v-tabs>

            <v-tabs-window v-model="chatStore.activeTab" class="flex-grow-1 tabs-container">
              <v-tabs-window-item value="Enunciado">
                <StatementViewer />
              </v-tabs-window-item>
              <v-tabs-window-item value="Chat">
                <ChatBot />
              </v-tabs-window-item>
        
              <v-tabs-window-item value="Feedback">
                <div class="p-4">
                  <Feedback />
                </div>
              </v-tabs-window-item>
            </v-tabs-window>
          </v-card>
        </v-col>
      </v-row>

      <!-- Modal: fuera de plazo -->
      <v-dialog v-model="showDueDateModal" max-width="500px">
        <v-card>
          <v-card-title class="headline text-red">¡Fuera de Plazo!</v-card-title>
          <v-card-text>
            Este ejercicio ya no está disponible para ser enviado porque ha expirado el plazo de entrega.
            Puedes seguir revisando tu código, pero no podrás enviarlo.
          </v-card-text>

          <v-card-actions>
            <v-spacer />
            <v-btn color="primary" @click="showDueDateModal = false">Aceptar</v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>

    </v-container>
  </v-app>
</template>

<script setup>
import { onMounted, ref, watch } from 'vue'
import { useExerciseStore } from './stores/exercise'
import { useChatIAStore } from './stores/chatia'
import { useFullscreen } from './composables/useFullscreen'

import StatementViewer from './components/StatementViewer.vue'
import CodeEditor from './components/CodeEditor.vue'
import ChatBot from './components/ChatBot.vue'
import TerminalView from './components/TerminalView.vue'
import Feedback from './components/Feedback.vue'
import icon from '@/assets/icon.png'

const { isFullScreen, toggleFullScreen } = useFullscreen()
const exerciseStore = useExerciseStore()
const chatStore = useChatIAStore()

onMounted(() => {
  window.addEventListener('message', (event) => {
    if (typeof event.data !== 'object') return

    console.log('[Exercise] Mensaje recibido:', event.data)

    if (event.data.event === 'init') {
      exerciseStore.initFromMoodle(event.data)
    }

    if (event.data.event === 'state_loaded') {
      exerciseStore.loadState(event.data)
    }

    if (event.data.event === 'error') {
      console.error('[Exercise] Error recibido:', event.data)
      if (event.data.code == "duedate") {
        console.log("------> Dude date")

        exerciseStore.setDueDateExpired()
      }
    }
  })
})

const showConfirmDialog = ref(false)

function confirmSubmit() {
  exerciseStore.submitExercise()
  showConfirmDialog.value = false
}

const showDueDateModal = ref(false)

watch(() => exerciseStore.dueDateExpired, (expired) => {
  if (expired) {
    showDueDateModal.value = true
  }
})

function startAndFullscreen() {
  exerciseStore.startExercise()
  toggleFullScreen()
}

watch(isFullScreen, (active) => {
  if (!active && exerciseStore.hasStarted) {
    exerciseStore.pauseExercise()
  }
})
</script>



<style>
html,
body,
#app {
  height: 100%;
  margin: 0;
  background-color: #f5f5f5;
}

.app-content {
  margin-top: 56px; /* Ajusta si cambias el tamaño de la barra */
}

.start-page {
  padding: 20px;
  border: 2px solid #1867C0;
  border-radius: 20px;
}

/* Tabs */
.v-tabs,
.v-tab,
.v-tabs-window {
  flex-shrink: 0;
}

.v-tabs-window {
  flex-grow: 1;
  overflow-y: auto;
}

.v-tabs-window-item {
  padding: 1em;
  overflow-y: auto;
  flex-grow: 1;
}

.full-height-card {
  height: calc(100vh - 90px);
  overflow: hidden;
}

.code-editor {
  height: calc(80vh - 90px);
  background-color: #1e1e1e;
}

.tabs-container {
  height: calc(100vh - 90px);
}
</style>
