<template>
  <div ref="container" class="editor-container"></div>
</template>

<script setup>
import * as monaco from 'monaco-editor/esm/vs/editor/editor.api'
import 'monaco-editor/esm/vs/basic-languages/cpp/cpp.contribution'
import 'monaco-editor/esm/vs/basic-languages/python/python.contribution'
import 'monaco-editor/esm/vs/basic-languages/javascript/javascript.contribution'
import 'monaco-editor/esm/vs/basic-languages/html/html.contribution'
import 'monaco-editor/esm/vs/basic-languages/css/css.contribution'
import 'monaco-editor/esm/vs/basic-languages/markdown/markdown.contribution'
import 'monaco-editor/esm/vs/basic-languages/shell/shell.contribution'
import 'monaco-editor/esm/vs/basic-languages/java/java.contribution'

import { onMounted, onBeforeUnmount, ref, watch, nextTick } from 'vue'
import { useExerciseStore } from '@/stores/exercise'

const exerciseStore = useExerciseStore()
const container = ref(null)
let editor = null
let isUpdatingFromStore = false

onMounted(() => {
  nextTick(() => {
    if (!container.value) {
      console.warn('[CodeEditor] El contenedor no está listo')
      return
    }

    editor = monaco.editor.create(container.value, {
      value: exerciseStore.code,
      language: exerciseStore.language || 'c',
      theme: 'vs-dark',
      automaticLayout: true,
      fontSize: 14,
      minimap: { enabled: false },
      readOnly: exerciseStore.isReadOnly,
    })

    // Sync Monaco -> Store
    editor.onDidChangeModelContent(() => {
      if (!isUpdatingFromStore) {
        exerciseStore.code = editor.getValue()
      }
    })
  })
})

// Sync Store -> Monaco (contenido)
watch(() => exerciseStore.code, (newCode) => {
  if (editor && newCode !== editor.getValue()) {
    isUpdatingFromStore = true
    editor.setValue(newCode)
    isUpdatingFromStore = false
  }
})

// Sync lenguaje del store -> Monaco
watch(() => exerciseStore.language, (newLang) => {
  if (editor && newLang && editor.getModel()) {
    monaco.editor.setModelLanguage(editor.getModel(), newLang)
  }
})


onBeforeUnmount(() => {
  editor?.dispose()
})
</script>

<style scoped>
.editor-container {
  width: 100%;
  height: 100%;
  border-top: 1px solid #ccc;
  padding-top: 10px;
}
</style>
