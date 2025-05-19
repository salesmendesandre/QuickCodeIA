import { defineStore } from 'pinia'
import axios from 'axios'
import { readonly } from 'vue'
import { useChatIAStore } from './chatia'
const backendCopileUrl = import.meta.env.VITE_BACKEND_COPILE_URL

let autoSaveInterval = null
function toCloneable(obj, seen = new WeakMap()) {
  // primitivos seguros 💚
  if (obj === null || typeof obj !== 'object') {
    return obj;                       // number, string, boolean, undefined
  }

  // evitar ciclos
  if (seen.has(obj)) return seen.get(obj);

  let out;

  // Date → timestamp
  if (obj instanceof Date) {
    out = obj.getTime();
  }

  // Array
  else if (Array.isArray(obj)) {
    out = obj.map(v => toCloneable(v, seen));
  }

  // Map → array de pares
  else if (obj instanceof Map) {
    out = Array.from(obj.entries()).map(
      ([k, v]) => [toCloneable(k, seen), toCloneable(v, seen)]
    );
  }

  // Set → array de valores
  else if (obj instanceof Set) {
    out = Array.from(obj.values()).map(v => toCloneable(v, seen));
  }

  // ArrayBuffer / TypedArrays → clonar con slice
  else if (ArrayBuffer.isView(obj) || obj instanceof ArrayBuffer) {
    out = obj.slice ? obj.slice(0) : new Uint8Array(obj).buffer.slice(0);
  }

  // Plain object u otros prototipos → objeto simple
  else {
    out = {};
    seen.set(obj, out);               // marcar antes de recorrer propiedades
    for (const [k, v] of Object.entries(obj)) {
      if (typeof v === 'function' || typeof v === 'symbol' || v === undefined) {
        continue;                     // descartar no clonables
      }
      out[k] = toCloneable(v, seen);
    }
  }

  return out;
}

export const useExerciseStore = defineStore('exercise', {
  state: () => ({
    // 🧩 Datos base de Moodle
    viewMode: 'teacher',
    userId: null,
    cmId: null,
    isReadOnly: false,
    dueDateExpired: false,
    currentStatus: null,

    // 📋 Datos de enunciado
    statement: '',
    dueDate: null,
    language: '',

    // ⚙️ Configuración del ejercicio
    enableHints: true,
    maxHints: null,
    enableConsole: true,

    // 🧠 Estado del ejercicio
    code: '',
    terminalOutput: '',
    terminalColor: 'green',
    executionCount: 0,
    hintsUsed: 0,
    consoleCalls: 0,
    autoScore: null,
    teacherFeedback: null,
    score: null,

    // 🕹️ Control de flujo
    hasStarted: false,
    showOnlyStatement: true
  }),


  actions: {
    // 🧩 Inicializa datos desde Moodle
    initFromMoodle(data) {
      this.viewMode = data.viewMode
      this.isReadOnly = this.viewMode === 'teacher'
      this.language = data.language || 'c'
      this.userId = data.userId || null
      this.cmId = data.cmId || null
      this.statement = data.statement || ''
      this.dueDate = data.dueDate || null
      this.enableHints = data.enableHints ?? true
      this.maxHints = data.maxHints ?? null
      this.enableConsole = data.enableConsole ?? true

      console.log('[Exercise] Modo de vista:', this.viewMode)
      console.log('[Exercise] Datos de Moodle:', data)
      console.log('[Exercise] USER ID:', this.userId)
    },

    // 🔁 Carga un estado guardado (por ejemplo al volver a abrir el ejercicio)
    loadState(data) {
      this.code = data.code || ''
      this.terminalOutput = data.terminalOutput || ''
      this.terminalColor = 'gray'
      this.executionCount = data.executionCount || 0
      this.hintsUsed = data.hintsUsed || 0
      this.consoleCalls = data.consoleCalls || 0
      this.autoScore = data.autoScore ?? null
      this.teacherFeedback = data.teacherFeedback ?? null
      this.score = data.score ?? null
      this.currentStatus = data.currentStatus || null
      if (this.currentStatus === 'corrected' || this.currentStatus === 'submitted') {
        console.log('[Exercise] Estado del ejercicio:', this.currentStatus)
        this.isReadOnly = true
      }
      const chatIA = useChatIAStore()
      if (data.chatState?.length > 0) {
        chatIA.setHistoric(data.chatState)
      }
    },
   
    sendMesageToMoodle(event, data) {
      window.parent.postMessage({
        event,
        cmid: this.cmId,
        userid: this.userId,
        ...data
      }, '*')
    },

   setDueDateExpired() {
      this.dueDateExpired = true
      this.isReadOnly = true
      this.stopAutoSave()
    },

    reportScore() {
      this.sendMesageToMoodle('score_submitted', {
             score: Number(this.score),
      })

      console.log('[Exercise] Puntuación enviada a Moodle')

      this.currentStatus = 'corrected'
      this.sendMesageToMoodle('state_save', {
        currentStatus: this.currentStatus,
      })
 
      console.log('[Exercise] Puntuación enviada a Moodle')
    },

    reportFeedback() {
      this.sendMesageToMoodle('state_save', {
        teacherFeedback: this.teacherFeedback,
        currentStatus: this.currentStatus,
      })
      console.log('[Exercise] Feedback enviado a Moodle')

    },
    
    submitExercise() {
      this.stopAutoSave()
      this.currentStatus = 'submitted'
      this.reportState()
      this.isReadOnly = true
    },


    // 💾 Guardar estado del ejercicio
    reportState() {
      if (this.isReadOnly) return

      const chatIA = useChatIAStore()
      this.sendMesageToMoodle('state_save', {
        code: this.code,
        terminal_output: this.terminalOutput,
        execution_count: this.executionCount,
        hints_used: this.hintsUsed,
        console_calls: this.consoleCalls,
        currentStatus: this.currentStatus,
        chat_state: toCloneable(chatIA.chatHistory)
      })

      console.log('[Exercise] Estado enviado a Moodle')
    },

    // ▶️ Empieza el ejercicio (oculta el enunciado)
    startExercise() {
      if(this.currentStatus == null) {
        this.currentStatus = 'solving'
        this.reportState()
      }
      this.hasStarted = true
      this.showOnlyStatement = false
      if (!this.isReadOnly) {
        console.log('[Exercise] Ejercicio iniciado')
        this.startAutoSave()
      }
    },

    // ⏸️ Pausa el ejercicio (muestra solo el enunciado)
    pauseExercise() {
      this.showOnlyStatement = true
      this.stopAutoSave()
    },

    // 🔁 AutoSave cada 5s
    startAutoSave() {
      if (autoSaveInterval) return
      autoSaveInterval = setInterval(() => this.reportState(), 5000)
      console.log('[Exercise] AutoSave iniciado')
    },

    // ✋ Detiene el autosave
    stopAutoSave() {
      if (autoSaveInterval) {
        clearInterval(autoSaveInterval)
        autoSaveInterval = null
        console.log('[Exercise] AutoSave detenido')
      }
    },

    // 📊 Incrementa contador de llamadas a consola
    incrementConsoleCalls() {
      this.consoleCalls++
      this.reportState()
    },

    // 🧩 Incrementa contador de pistas usadas
    incrementHintsUsed() {
      this.hintsUsed++
      this.reportState()
    },

    // ⚙️ Compila el código actual usando el backend
    async compile() {
      this.executionCount++
      this.terminalOutput = '⏳ Ejecutando...'
      this.terminalColor = 'gray'
      this.reportState()

      try {
        const { data } = await axios.post(`${backendCopileUrl}/compile`, {
          code: this.code,
          language: this.language || 'c'
        })

        const { stdout, stderr, exit_code } = data
        this.terminalOutput = (stdout || '') + (stderr ? `\n[stderr]\n${stderr}` : '')
        this.terminalColor = exit_code === 0 ? 'green' : 'red'
        console.log('[Exercise] Resultado de compilación:', this.terminalOutput)
        this.reportState()
      } catch (err) {
        this.terminalOutput = '❌ Error al conectar con el backend'
        this.terminalColor = 'orange'
        console.error('[Exercise] Error de compilación:', err)
      }
    }
  }
})