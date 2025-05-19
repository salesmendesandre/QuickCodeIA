import { ref } from 'vue'

export function useFullscreen() {
  const isFullScreen = ref(false)

  function toggleFullScreen() {
    const doc = document
    const el = doc.documentElement

    if (!isFullScreen.value) {
      (el.requestFullscreen || el.webkitRequestFullscreen || el.msRequestFullscreen)?.call(el)
      isFullScreen.value = true
    } else {
      (doc.exitFullscreen || doc.webkitExitFullscreen || doc.msExitFullscreen)?.call(doc)
      isFullScreen.value = false
    }
  }

  return {
    isFullScreen,
    toggleFullScreen
  }
}
