<template>
  <div class="ai-chat-wrapper">
    <div class="history" ref="historyContainer">
      <div
        v-for="(msg, i) in chatIAStore.visibleMessages"
        :key="i"
        :class="msg.role"
      >
        <div v-if="!msg.hidden" v-html="chatIAStore.formattedMessage(msg)"></div>
      </div>
    </div>

    <v-textarea
      v-model="userInput"
      label="Escribe una pregunta o solicita ayuda"
      rows="2"
      auto-grow
      class="mt-2"
      @keyup.enter="askQuestion"
      :disabled="exerciseStore.isReadOnly"
    />

    <v-row dense class="mt-2">
      <v-col cols="6">
        <v-btn
          block
          color="info"
          @click="chatIAStore.requestHint"
          :loading="chatIAStore.isProcessing"
          :disabled="exerciseStore.enableHints == 0 || chatIAStore.isProcessing || exerciseStore.isReadOnly"
        >
          🧩 Pista IA
          <template v-if="exerciseStore.enableHints != 0">
            ({{ exerciseStore.maxHints - exerciseStore.hintsUsed }}/{{ exerciseStore.maxHints }})
          </template>
        </v-btn>
      </v-col>
      <v-col cols="6">
        <v-btn
          block
          color="primary"
          @click="askQuestion"
          :loading="chatIAStore.isProcessing"
          :disabled="chatIAStore.isProcessing || exerciseStore.isReadOnly"
        >
          Realizar consulta
        </v-btn>
      </v-col>
    </v-row>
  </div>
</template>

<script setup>
import { ref, watch, nextTick } from 'vue'
import { useChatIAStore } from '@/stores/chatia'
import { useExerciseStore } from '@/stores/exercise'
import hljs from 'highlight.js'

const chatIAStore = useChatIAStore()
const exerciseStore = useExerciseStore()
const userInput = ref('')
const historyContainer = ref(null)

chatIAStore.initStatementContext()

watch(() => chatIAStore.visibleMessages.length, async () => {
  await nextTick()
  const container = historyContainer.value
  if (container) container.scrollTop = container.scrollHeight

  document.querySelectorAll('.history pre code').forEach((block) => {
    hljs.highlightElement(block)
  })
})

function askQuestion() {
  if (userInput.value.trim()) {
    chatIAStore.askQuestion(userInput.value)
    userInput.value = ''
  }
}
</script>

<style>
.ai-chat-wrapper {
  border: 1px solid #ccc;
  padding: 1em;
  border-radius: 8px;
  background: #fafafa;
}

.history {
  max-height: calc(100vh - 370px);
  min-height: calc(100vh - 370px);
  overflow-y: auto;
  margin-bottom: 1em;
  font-size: 0.95rem;
  scrollbar-width: none;
  -ms-overflow-style: none;
}

.history::-webkit-scrollbar {
  display: none;
}

.user {
  text-align: right;
  background: #def;
  padding: 0.5em;
  border-radius: 6px;
  margin: 0.3em 0;
}

.assistant {
  text-align: left;
  background: #f4f4f4;
  padding: 0.5em;
  border-radius: 6px;
  margin: 0.3em 0;
}

.hljs {
  background: transparent;
  color: inherit;
}

.history pre,
.history code {
  font-family: 'Fira Code', 'Courier New', monospace;
  font-size: 0.9em;
  background: #f5f5f5;
  padding: 0.75em;
  border-radius: 6px;
  overflow-x: auto;
}
</style>
