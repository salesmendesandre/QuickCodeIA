<template>
  <div class="terminal-wrapper">
    <div class="terminal-output" :style="{ color: exerciseStore.terminalColor }">
      {{ exerciseStore.terminalOutput || 'Terminal de salida' }}
    </div>

    <v-row dense class="mt-2">
      <v-col cols="6">
        <v-btn block color="primary" @click="compileCode">
          ▶️ Compilar
        </v-btn>
      </v-col>
      <v-col cols="6">
        <v-btn
          block
          :disabled="!compiled || exerciseStore.enableConsole==0 || chatIAStore.isProcessing || exerciseStore.isReadOnly"
          :loading="chatIAStore.isProcessing"
          color="secondary"
          @click="chatIAStore.requestConsoleHelp"
        >
          🤖 Ayuda con la terminal
        </v-btn>
      </v-col>
    </v-row>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useExerciseStore } from '../stores/exercise'
import { useChatIAStore } from '../stores/chatia'

const exerciseStore = useExerciseStore()
const chatIAStore = useChatIAStore()
const compiled = ref(false)

async function compileCode() {
  compiled.value = true
  await exerciseStore.compile()
}
</script>


<style scoped>
.terminal-wrapper {
  border-top: 1px solid #ccc;
  padding-top: 10px;
  margin-top: 10px;
  display: flex;
  flex-direction: column;
  gap: 0.5em;
}

.terminal-output {
  min-height: calc(20vh - 155px);
  max-height: calc(20vh - 155px);
  overflow-y: auto;
  background-color: #000;
  padding: 1em;
  font-family: monospace;
  white-space: pre-wrap;
  border-radius: 8px;
  border: 1px solid #333;
}
</style>
