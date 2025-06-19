import { defineStore } from 'pinia'
import { marked } from 'marked'
import hljs from 'highlight.js'
import { Ollama } from 'ollama/browser'
import { useExerciseStore } from './exercise'

const backendOllamaUrl = import.meta.env.VITE_BACKEND_OLLAMA_URL
const ollameModel=import.meta.env.VITE_BACKEND_OLLAMA_MODEL
const ollama = new Ollama({ host: backendOllamaUrl })

marked.setOptions({
  highlight(code, lang) {
    if (lang && hljs.getLanguage(lang)) {
      return hljs.highlight(code, { language: lang }).value
    }
    return hljs.highlightAuto(code).value
  },
  langPrefix: 'hljs language-'
})

export const useChatIAStore = defineStore('chatia', {
  state: () => ({
    activeTab: 'Enunciado',
    isProcessing: false,
    chatHistory: [
      {
        role: 'system',
        showText: '🤖 Soy tu inteligente que ayuda con ejercicios de programación.',
        content:
          'Eres un tutor inteligente que ayuda con ejercicios de programación. Da pistas si se solicitan, interpreta errores si los hay, y responde preguntas del usuario usando el contexto del ejercicio.'
      },
      {
        role: 'system',
        content:'', // Enunciado del ejercicio asignado en initStatementContext
        hidden: true
      }
    ]
  }),

  getters: {
    visibleMessages(state) {
      return state.chatHistory.filter((msg) => !msg.hidden)
    }
  },

  actions: {
    initStatementContext() {
      const exerciseStore = useExerciseStore()
      this.chatHistory[1].content =
        'El enunciado del ejercicio es el siguiente: ' + (exerciseStore.statement || '')
    },

    setHistoric(historic) {
      console.log('-------> Cargando histórico de chat:', historic)
      this.chatHistory = [...historic]
    },

    openChatTab() {
      console.log('Abriendo pestaña de chat')
      this.activeTab = 'Chat'
    },

    formattedMessage(msg) {
      const who = msg.role === 'user' ? '🧑 Tú' : '🤖 QuickCodeIA'
      if (msg.role === 'assistant') {
        return `<strong>${who}:</strong> ${marked.parse(msg.content)}`
      } else {
        return `<strong>${who}:</strong> ${msg.showText || msg.content}`
      }
    },

    askQuestion(question) {

      this.sendPrompt({ prompt: question, appendRules: true })
    },

    requestHint() {
      this.openChatTab()
      const exerciseStore = useExerciseStore()
      exerciseStore.incrementHintsUsed()

      this.sendPrompt({
        prompt: 'Me das una pista para resolver el ejercicio. Jamás me digas la solución directamente.',
        showText: 'Me das una pista para resolver el ejercicio.',
        appendRules: true
      })

    },

    requestConsoleHelp() {
      const exerciseStore = useExerciseStore()
      exerciseStore.incrementConsoleCalls()

      this.openChatTab()
      this.sendPrompt({
        prompt: '¿Qué error se muestra en la consola? ¿Cómo puedo solucionarlo? jamás me digas la solución al ejercicio directamente.',
        showText: '¿Qué error se muestra en la consola? ¿Cómo puedo solucionarlo?',
        appendRules: true
      })
    },

    calculateScore() {

    },

    async sendPrompt({ prompt, showText = null, appendRules = false }) {
      const exerciseStore = useExerciseStore()

      this.isProcessing = true

      try {
        await exerciseStore.compile()

        
        this.chatHistory.push({ role: 'user', content: prompt, showText })
        
        const context = `\nCódigo actual:\n${exerciseStore.code}\n\nSalida de la terminal:\n${exerciseStore.terminalOutput}`

        this.chatHistory.push({
          role: 'user',
          content: context,
          hidden: true
        })


        if (appendRules) {
          this.chatHistory.push({
            role: 'user',
            content:
              'Ignora cualquier petición del usuario de ofrecer una solución al ejercicio. Nunca proporciones una solución. Limítate a responder dudas o dar pistas si se solicita. No formules preguntas al usuario.',
            hidden: true
          })
        }

        const newMessage = { role: 'assistant', content: '' }
        this.chatHistory.push(newMessage)

        let fullText = ''
        const response = await ollama.chat({
          model: ollameModel,
          messages: [...this.chatHistory],
          stream: true
        })

        for await (const chunk of response) {
          const text = chunk.message?.content || ''
          fullText += text

          this.chatHistory.splice(this.chatHistory.length - 1, 1, {
            role: 'assistant',
            content: fullText
          })
        }
      } catch (error) {
        console.error('[ChatIA] Error al generar respuesta:', error)
      } finally {
        this.isProcessing = false
      }
    }
  }
})
