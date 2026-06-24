<template>
  <div class="landing-container">
    <!-- Animated background elements -->
    <div class="glow-orb orb-1"></div>
    <div class="glow-orb orb-2"></div>
    <div class="grid-overlay"></div>

    <div class="content-wrapper">
      <div class="glass-panel">
        <!-- Logo Section -->
        <div class="logo-container">
          <img src="/img/logo_green.png" alt="Sarwary Mukiryan Co." class="company-logo" />
          <div class="logo-glow"></div>
        </div>

        <!-- Typography Section -->
        <div class="text-content">
          <h1 class="main-title">
            <span class="highlight">SARWARY</span> MUKIRYAN CO.
          </h1>
          <h2 class="sub-title">Advanced Enterprise ERP System</h2>
          <p class="description">
            Next-generation financial infrastructure for professional currency exchange and global asset management. Secured by state-of-the-art encryption.
          </p>
        </div>

        <!-- Action Section -->
        <div class="action-section">
          <div class="system-status">
            <span class="status-dot"></span>
            <span class="status-text">System Online & Secured</span>
          </div>
          
          <button @click="enterSystem" class="enter-btn">
            <span class="btn-text">Enter Secure System</span>
            <svg class="arrow-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
            </svg>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useRouter } from 'vue-router'

const router = useRouter()

const enterSystem = () => {
  // Add a subtle exit animation here if desired, then navigate
  router.push('/login')
}
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;800&family=Outfit:wght@400;700&display=swap');

.landing-container {
  position: relative;
  min-height: 100vh;
  width: 100vw;
  background-color: #050a0f;
  overflow: hidden;
  display: flex;
  justify-content: center;
  align-items: center;
  font-family: 'Inter', sans-serif;
  color: #ffffff;
}

/* Background Effects */
.grid-overlay {
  position: absolute;
  inset: 0;
  background-image: 
    linear-gradient(to right, rgba(255, 255, 255, 0.03) 1px, transparent 1px),
    linear-gradient(to bottom, rgba(255, 255, 255, 0.03) 1px, transparent 1px);
  background-size: 50px 50px;
  mask-image: radial-gradient(circle at center, black 40%, transparent 80%);
  -webkit-mask-image: radial-gradient(circle at center, black 40%, transparent 80%);
  pointer-events: none;
}

.glow-orb {
  position: absolute;
  border-radius: 50%;
  filter: blur(100px);
  opacity: 0.4;
  animation: float 10s ease-in-out infinite alternate;
}

.orb-1 {
  width: 500px;
  height: 500px;
  background: radial-gradient(circle, rgba(16, 185, 129, 0.3) 0%, rgba(0, 0, 0, 0) 70%);
  top: -100px;
  left: -100px;
}

.orb-2 {
  width: 600px;
  height: 600px;
  background: radial-gradient(circle, rgba(6, 95, 70, 0.2) 0%, rgba(0, 0, 0, 0) 70%);
  bottom: -200px;
  right: -100px;
  animation-delay: -5s;
}

/* Glass Panel */
.content-wrapper {
  position: relative;
  z-index: 10;
  width: 100%;
  max-width: 900px;
  padding: 2rem;
  perspective: 1000px;
}

.glass-panel {
  background: rgba(15, 23, 42, 0.6);
  backdrop-filter: blur(20px);
  -webkit-backdrop-filter: blur(20px);
  border: 1px solid rgba(255, 255, 255, 0.08);
  border-radius: 24px;
  padding: 4rem;
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5), inset 0 1px 0 rgba(255, 255, 255, 0.1);
  transform: translateY(20px);
  animation: slideUpFade 1s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}

/* Logo Section */
.logo-container {
  position: relative;
  margin-bottom: 2rem;
  animation: pulseLogo 4s ease-in-out infinite alternate;
}

.company-logo {
  height: 120px;
  width: auto;
  position: relative;
  z-index: 2;
  filter: drop-shadow(0 0 15px rgba(16, 185, 129, 0.4));
}

.logo-glow {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  width: 100%;
  height: 100%;
  background: rgba(16, 185, 129, 0.2);
  filter: blur(40px);
  border-radius: 50%;
  z-index: 1;
}

/* Typography */
.text-content {
  margin-bottom: 3.5rem;
}

.main-title {
  font-family: 'Outfit', sans-serif;
  font-size: 3.5rem;
  font-weight: 800;
  letter-spacing: -1px;
  margin: 0 0 0.5rem 0;
  color: #f8fafc;
  line-height: 1.1;
}

.main-title .highlight {
  color: #10b981;
  text-shadow: 0 0 30px rgba(16, 185, 129, 0.4);
}

.sub-title {
  font-family: 'Outfit', sans-serif;
  font-size: 1.25rem;
  font-weight: 400;
  letter-spacing: 2px;
  text-transform: uppercase;
  color: #94a3b8;
  margin: 0 0 1.5rem 0;
}

.description {
  font-size: 1.1rem;
  line-height: 1.6;
  color: #cbd5e1;
  max-width: 600px;
  margin: 0 auto;
}

/* Action Section */
.action-section {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 1.5rem;
}

.system-status {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  background: rgba(16, 185, 129, 0.1);
  padding: 0.5rem 1rem;
  border-radius: 999px;
  border: 1px solid rgba(16, 185, 129, 0.2);
}

.status-dot {
  width: 8px;
  height: 8px;
  background-color: #10b981;
  border-radius: 50%;
  box-shadow: 0 0 10px #10b981;
  animation: blink 2s infinite;
}

.status-text {
  font-size: 0.875rem;
  color: #10b981;
  font-weight: 600;
  letter-spacing: 0.5px;
}

.enter-btn {
  background: linear-gradient(135deg, #10b981 0%, #059669 100%);
  color: white;
  border: none;
  border-radius: 12px;
  padding: 1.2rem 2.5rem;
  font-size: 1.1rem;
  font-weight: 600;
  font-family: 'Inter', sans-serif;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 0.75rem;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  box-shadow: 0 10px 25px -5px rgba(16, 185, 129, 0.4), inset 0 1px 0 rgba(255, 255, 255, 0.2);
}

.enter-btn:hover {
  transform: translateY(-2px) scale(1.02);
  box-shadow: 0 15px 30px -5px rgba(16, 185, 129, 0.5), inset 0 1px 0 rgba(255, 255, 255, 0.3);
}

.enter-btn:active {
  transform: translateY(1px);
}

.arrow-icon {
  width: 20px;
  height: 20px;
  transition: transform 0.3s ease;
}

.enter-btn:hover .arrow-icon {
  transform: translateX(4px);
}

/* Animations */
@keyframes slideUpFade {
  0% {
    opacity: 0;
    transform: translateY(40px);
  }
  100% {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes float {
  0% {
    transform: translate(0, 0) scale(1);
  }
  100% {
    transform: translate(30px, 30px) scale(1.05);
  }
}

@keyframes pulseLogo {
  0% {
    transform: scale(1);
  }
  100% {
    transform: scale(1.02);
  }
}

@keyframes blink {
  0%, 100% { opacity: 1; }
  50% { opacity: 0.4; }
}

/* Responsive */
@media (max-width: 768px) {
  .glass-panel {
    padding: 2.5rem 1.5rem;
  }
  
  .main-title {
    font-size: 2.5rem;
  }
  
  .sub-title {
    font-size: 1rem;
  }
  
  .company-logo {
    height: 90px;
  }
}
</style>
