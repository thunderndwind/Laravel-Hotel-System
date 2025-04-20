<template>
  <div>
    <div ref="editorRoot" />
  </div>
</template>

<script setup>
import { onMounted, onBeforeUnmount, ref, defineExpose } from 'vue'
import '@toast-ui/editor/dist/toastui-editor.css'

const props = defineProps({
  initialValue: {
    type: String,
    default: ''
  },
  height: {
    type: String,
    default: '400px'
  },
  placeholder: {
    type: String,
    default: 'Enter your text here...'
  },
  initialEditType: {
    type: String,
    default: 'wysiwyg' // or 'markdown'
  }
})

const emit = defineEmits(['update:content', 'load'])

const editorRoot = ref(null)
let editorInstance = null

onMounted(async () => {
  // Dynamically import the Toast Editor library (to avoid SSR issues)
  const { default: Editor } = await import('@toast-ui/editor')
  
  // Create editor
  editorInstance = new Editor({
    el: editorRoot.value,
    height: props.height,
    initialEditType: props.initialEditType,
    previewStyle: 'vertical',
    initialValue: props.initialValue,
    placeholder: props.placeholder,
    events: {
      change: () => {
        emit('update:content', editorInstance.getMarkdown())
      }
    }
  })
  
  emit('load', editorInstance)
})

onBeforeUnmount(() => {
  // Clean up the editor instance to prevent memory leaks
  if (editorInstance) {
    editorInstance.destroy()
    editorInstance = null
  }
})

// Expose methods to parent component
defineExpose({
  getMarkdown: () => editorInstance?.getMarkdown() || '',
  getHTML: () => editorInstance?.getHTML() || '',
  insertText: (text) => editorInstance?.insertText(text),
  setMarkdown: (markdown) => editorInstance?.setMarkdown(markdown),
  focus: () => editorInstance?.focus()
})
</script> 