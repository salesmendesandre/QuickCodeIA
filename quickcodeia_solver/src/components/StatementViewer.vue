<template>
  <div class="statement-viewer-container">
    <div class="enunciado-header">
      <h2 class="mb-2">Enunciado:</h2>
    </div>

    <div class="statement-viewer">
      <div v-html="store.statement"></div>
    </div>
  </div>
</template>

<script setup>
import { useExerciseStore } from '../stores/exercise'
const store = useExerciseStore()
</script>

<style>
.enunciado-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding-top: 15px;
  padding-left: 10px;
}

.statement-viewer {
  overflow-y: auto;
  overflow-x: hidden;
  padding: 20px;
  background-color: #fff;
  font-size: 16px;
  line-height: 1.6;
  box-sizing: border-box;
  scrollbar-gutter: stable;
}

.statement-viewer ul {
  padding-left: 1.5em;
  list-style-position: inside;
}

.statement-viewer li {
  margin-bottom: 6px;
}

.statement-viewer-container {
  height: 100%;
}
</style>
