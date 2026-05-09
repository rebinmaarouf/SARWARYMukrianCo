import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import { fileURLToPath, URL } from 'node:url'

// https://vite.dev/config/
export default defineConfig(({ mode }) => ({
  plugins: [vue()],
  // ئیتر کێڵگەی بیڵدەکە دەزانێت کە لە پرۆداکشن دەبێت فۆڵدەری /build/ بەکاربهێنێت بۆ dynamic imports
  base: mode === 'production' ? '/build/' : '/',
  build: {
    outDir: '../backend/public/build',
    manifest: true,
    emptyOutDir: true,
  },
  resolve: {
    alias: {
      '@': fileURLToPath(new URL('./src', import.meta.url))
    }
  },
  server: {
    port: 5173,
    cors: true,
  }
}))
