<template>
  <div class="feedback-viewer-container">


    <!--<h3 class="mb-2">Score Automático (IA):</h3>
    <p class="pl-4">{{ exerciseStore.autoScore !== null ? exerciseStore.autoScore : "No disponible" }}</p>-->

    <h3 class="mb-2">Nota por el profesor:</h3>
    <div v-if="exerciseStore.viewMode === 'teacher'">
    <v-text-field
        v-model="exerciseStore.score"
        label="Nota del profesor"
        type="number"
        class="mt-2"
    />
    </div>
    <div v-else class="pl-4">
    <p>{{ exerciseStore.score !== null ? exerciseStore.score : "No disponible" }}</p>
    </div>

    <h3 class="mb-2">Retroalimentación:</h3>
    <div v-if="exerciseStore.viewMode === 'teacher'" >
    <v-textarea
        v-model="exerciseStore.teacherFeedback"
        label="Escribe la retroalimentación"
        rows="6"
        auto-grow
        class="mt-2"
    />
    <v-btn
        color="primary"
        @click="saveFeedback"
        block
    >
        Guardar Feedback
    </v-btn>
    </div>
    <div v-else class="pl-4">
    <p v-if="exerciseStore.teacherFeedback">{{ exerciseStore.teacherFeedback }}</p>
    <p v-else class="text-grey">No hay retroalimentación aún.</p>
    </div>

  </div>
</template>

<script setup>
import { useExerciseStore } from '../stores/exercise'
const exerciseStore = useExerciseStore()

function saveFeedback() {
  exerciseStore.reportScore()
  exerciseStore.reportFeedback()
}
</script>

<style scoped>
.feedback-viewer-container {
  height: 100%;
  padding-top: 15px;
  padding-left: 10px;
}

.feedback-viewer-container p,
.feedback-viewer-container .pl-section {
  padding-left: 1rem;
}
</style>
