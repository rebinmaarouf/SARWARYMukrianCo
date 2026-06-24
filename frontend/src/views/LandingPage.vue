<template>
  <div class="landing-container" dir="rtl">
    <!-- Cyberpunk grid background -->
    <div class="cyber-grid"></div>
    
    <!-- Animated particles / orbs -->
    <div class="glow-orb orb-primary"></div>
    <div class="glow-orb orb-secondary"></div>
    <div class="glow-orb orb-tertiary"></div>

    <div class="content-wrapper">
      <div class="advanced-glass-panel">
        <!-- Scanning line effect -->
        <div class="scan-line"></div>
        
        <!-- Corner decorations for high-tech feel -->
        <div class="corner top-right"></div>
        <div class="corner top-left"></div>
        <div class="corner bottom-right"></div>
        <div class="corner bottom-left"></div>

        <!-- Logo Section -->
        <div class="logo-section">
          <div class="hologram-effect">
            <img src="/img/logo_green.png" alt="Sarwary Mukiryan Co." class="company-logo" />
            <div class="logo-pulse"></div>
          </div>
        </div>

        <!-- Typography Section -->
        <div class="text-content">
          <div class="badge-container">
            <div class="tech-badge">
              <span class="pulse-dot"></span>
              <span class="badge-text">سیستەم چالاکە و پارێزراوە</span>
            </div>
          </div>
          
          <h1 class="main-title">
            کۆمپانیای <span class="text-gradient">سەروەری موکریان</span>
          </h1>
          
          <h2 class="sub-title">
            <span class="bracket">[</span> سیستەمی پێشکەوتووی بەڕێوەبردنی دارایی و ئاڵوگۆڕی دراو <span class="bracket">]</span>
          </h2>
          
          <p class="description">
            ژێرخانی دارایی نەوەی نوێ بۆ ئاڵوگۆڕی دراوی پرۆفیشناڵ و بەڕێوەبردنی سەرمایە لەسەر ئاستی جیهانی. پارێزراوە بە نوێترین تەکنەلۆژیای شفرەکردن و ئاسایشی ئەلیکترۆنی.
          </p>
        </div>

        <!-- Action Section -->
        <div class="action-section">
          <button @click="enterSystem" class="cyber-btn">
            <span class="btn-glitch-layer"></span>
            <span class="btn-content">
              چوونە ژوورەوە بۆ سیستەم
              <svg class="btn-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
              </svg>
            </span>
          </button>
          
          <div class="security-note">
            <svg class="lock-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8V7a4 4 0 00-8 0v4h8z" />
            </svg>
            پارێزراوە بە سیستەمی 2FA و شفرەکردنی سەربازی
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useRouter } from 'vue-router'

const router = useRouter()

const enterSystem = () => {
  document.querySelector('.advanced-glass-panel').style.transform = 'scale(0.95) translateY(20px)';
  document.querySelector('.advanced-glass-panel').style.opacity = '0';
  setTimeout(() => {
    router.push('/login')
  }, 400);
}
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Noto+Kufi+Arabic:wght@300;400;600;800&display=swap');

* {
  box-sizing: border-box;
}

.landing-container {
  position: relative;
  min-height: 100vh;
  width: 100vw;
  background-color: #03070b;
  overflow: hidden;
  display: flex;
  justify-content: center;
  align-items: center;
  font-family: 'Noto Kufi Arabic', Tahoma, sans-serif;
  color: #ffffff;
}

/* Cyber Grid Background */
.cyber-grid {
  position: absolute;
  inset: 0;
  background-image: 
    linear-gradient(to right, rgba(16, 185, 129, 0.05) 1px, transparent 1px),
    linear-gradient(to bottom, rgba(16, 185, 129, 0.05) 1px, transparent 1px);
  background-size: 40px 40px;
  transform: perspective(500px) rotateX(60deg) translateY(-100px) translateZ(-200px);
  animation: gridMove 20s linear infinite;
  pointer-events: none;
}

.cyber-grid::after {
  content: '';
  position: absolute;
  inset: 0;
  background: radial-gradient(circle at center, transparent 10%, #03070b 80%);
}

/* Glowing Orbs */
.glow-orb {
  position: absolute;
  border-radius: 50%;
  filter: blur(80px);
  opacity: 0.3;
  animation: floatOrb 15s ease-in-out infinite alternate;
}

.orb-primary {
  width: 600px;
  height: 600px;
  background: radial-gradient(circle, #059669 0%, transparent 70%);
  top: -100px;
  right: -200px;
}

.orb-secondary {
  width: 500px;
  height: 500px;
  background: radial-gradient(circle, #0f766e 0%, transparent 70%);
  bottom: -150px;
  left: -100px;
  animation-delay: -5s;
}

.orb-tertiary {
  width: 400px;
  height: 400px;
  background: radial-gradient(circle, #10b981 0%, transparent 70%);
  top: 40%;
  left: 30%;
  opacity: 0.15;
  animation-delay: -10s;
}

/* Glass Panel */
.content-wrapper {
  position: relative;
  z-index: 10;
  width: 100%;
  max-width: 850px;
  padding: 2rem;
  perspective: 1200px;
}

.advanced-glass-panel {
  position: relative;
  background: linear-gradient(145deg, rgba(15, 23, 42, 0.7) 0%, rgba(3, 7, 18, 0.8) 100%);
  backdrop-filter: blur(25px);
  -webkit-backdrop-filter: blur(25px);
  border: 1px solid rgba(16, 185, 129, 0.15);
  border-radius: 20px;
  padding: 4rem 3rem;
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  box-shadow: 
    0 30px 60px -15px rgba(0, 0, 0, 0.8),
    inset 0 1px 0 rgba(16, 185, 129, 0.2),
    inset 0 -1px 0 rgba(0, 0, 0, 0.5),
    0 0 40px rgba(16, 185, 129, 0.05);
  transform-style: preserve-3d;
  animation: panelEntry 1.2s cubic-bezier(0.16, 1, 0.3, 1) forwards;
  transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
  overflow: hidden;
}

/* Tech Corners */
.corner {
  position: absolute;
  width: 30px;
  height: 30px;
  border-color: rgba(16, 185, 129, 0.5);
  border-style: solid;
  border-width: 0;
  transition: all 0.3s ease;
}

.top-right { top: 0; right: 0; border-top-width: 2px; border-right-width: 2px; border-top-right-radius: 20px; }
.top-left { top: 0; left: 0; border-top-width: 2px; border-left-width: 2px; border-top-left-radius: 20px; }
.bottom-right { bottom: 0; right: 0; border-bottom-width: 2px; border-right-width: 2px; border-bottom-right-radius: 20px; }
.bottom-left { bottom: 0; left: 0; border-bottom-width: 2px; border-left-width: 2px; border-bottom-left-radius: 20px; }

.advanced-glass-panel:hover .corner {
  width: 40px;
  height: 40px;
  border-color: #10b981;
  box-shadow: 0 0 15px rgba(16, 185, 129, 0.3);
}

/* Scan Line */
.scan-line {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 2px;
  background: linear-gradient(90deg, transparent, rgba(16, 185, 129, 0.8), transparent);
  opacity: 0.5;
  animation: scan 4s linear infinite;
  box-shadow: 0 0 10px rgba(16, 185, 129, 0.5);
}

/* Logo Section */
.logo-section {
  position: relative;
  margin-bottom: 2.5rem;
  z-index: 2;
}

.hologram-effect {
  position: relative;
  display: inline-block;
  animation: floatLogo 6s ease-in-out infinite;
}

.company-logo {
  height: 140px;
  width: auto;
  position: relative;
  z-index: 2;
  filter: drop-shadow(0 0 20px rgba(16, 185, 129, 0.6));
}

.logo-pulse {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  width: 120%;
  height: 120%;
  background: radial-gradient(circle, rgba(16, 185, 129, 0.3) 0%, transparent 60%);
  border-radius: 50%;
  z-index: 1;
  animation: pulse 2.5s ease-in-out infinite alternate;
}

/* Typography */
.text-content {
  margin-bottom: 3.5rem;
  z-index: 2;
  position: relative;
}

.badge-container {
  display: flex;
  justify-content: center;
  margin-bottom: 1.5rem;
}

.tech-badge {
  display: inline-flex;
  align-items: center;
  gap: 0.6rem;
  background: rgba(16, 185, 129, 0.1);
  padding: 0.4rem 1.2rem;
  border-radius: 999px;
  border: 1px solid rgba(16, 185, 129, 0.3);
  box-shadow: 0 0 20px rgba(16, 185, 129, 0.1) inset;
}

.pulse-dot {
  width: 8px;
  height: 8px;
  background-color: #34d399;
  border-radius: 50%;
  box-shadow: 0 0 12px #34d399;
  animation: blink 1.5s ease-in-out infinite;
}

.badge-text {
  font-size: 0.85rem;
  color: #34d399;
  font-weight: 600;
  letter-spacing: 0.5px;
}

.main-title {
  font-size: 3.2rem;
  font-weight: 800;
  margin: 0 0 0.8rem 0;
  color: #ffffff;
  line-height: 1.2;
  text-shadow: 0 4px 20px rgba(0,0,0,0.5);
}

.text-gradient {
  background: linear-gradient(to left, #34d399, #059669);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  color: transparent;
  text-shadow: none;
  position: relative;
}

.text-gradient::after {
  content: 'سەروەری موکریان';
  position: absolute;
  left: 0;
  top: 0;
  z-index: -1;
  text-shadow: 0 0 30px rgba(16, 185, 129, 0.4);
}

.sub-title {
  font-size: 1.15rem;
  font-weight: 600;
  color: #94a3b8;
  margin: 0 0 1.5rem 0;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
}

.bracket {
  color: #10b981;
  font-weight: 800;
  font-size: 1.4rem;
  opacity: 0.8;
}

.description {
  font-size: 1rem;
  line-height: 1.8;
  color: #cbd5e1;
  max-width: 650px;
  margin: 0 auto;
  font-weight: 300;
}

/* Action Section */
.action-section {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 1.2rem;
  z-index: 2;
  position: relative;
  width: 100%;
}

.cyber-btn {
  position: relative;
  background: transparent;
  color: #ffffff;
  border: 1px solid #10b981;
  border-radius: 8px;
  padding: 1rem 3rem;
  font-size: 1.15rem;
  font-weight: 700;
  font-family: inherit;
  cursor: pointer;
  overflow: hidden;
  transition: all 0.3s ease;
  box-shadow: 0 0 20px rgba(16, 185, 129, 0.2);
  width: 100%;
  max-width: 400px;
}

.cyber-btn::before {
  content: '';
  position: absolute;
  top: 0;
  right: 0;
  bottom: 0;
  left: 0;
  background: linear-gradient(90deg, rgba(16, 185, 129, 0.8), rgba(5, 150, 105, 0.9));
  z-index: 0;
  transform: scaleX(0);
  transform-origin: right;
  transition: transform 0.4s cubic-bezier(0.16, 1, 0.3, 1);
}

.cyber-btn:hover {
  box-shadow: 0 0 30px rgba(16, 185, 129, 0.5);
  border-color: #34d399;
}

.cyber-btn:hover::before {
  transform: scaleX(1);
  transform-origin: left;
}

.btn-content {
  position: relative;
  z-index: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.8rem;
}

.btn-icon {
  width: 22px;
  height: 22px;
  transition: transform 0.3s ease;
}

.cyber-btn:hover .btn-icon {
  transform: translateX(-6px);
}

.security-note {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.85rem;
  color: #64748b;
  margin-top: 0.5rem;
}

.lock-icon {
  width: 14px;
  height: 14px;
  color: #10b981;
}

/* Animations */
@keyframes panelEntry {
  0% {
    opacity: 0;
    transform: translateY(60px) scale(0.95) rotateX(5deg);
  }
  100% {
    opacity: 1;
    transform: translateY(0) scale(1) rotateX(0);
  }
}

@keyframes gridMove {
  0% { background-position: 0 0; }
  100% { background-position: 0 40px; }
}

@keyframes floatOrb {
  0% { transform: translate(0, 0); }
  100% { transform: translate(50px, 30px); }
}

@keyframes floatLogo {
  0%, 100% { transform: translateY(0); }
  50% { transform: translateY(-10px); }
}

@keyframes pulse {
  0% { transform: translate(-50%, -50%) scale(0.9); opacity: 0.5; }
  100% { transform: translate(-50%, -50%) scale(1.1); opacity: 1; }
}

@keyframes blink {
  0%, 100% { opacity: 1; }
  50% { opacity: 0.3; }
}

@keyframes scan {
  0% { top: 0; opacity: 0; }
  10% { opacity: 0.5; }
  90% { opacity: 0.5; }
  100% { top: 100%; opacity: 0; }
}

/* Responsive */
@media (max-width: 768px) {
  .advanced-glass-panel {
    padding: 3rem 1.5rem;
  }
  
  .main-title {
    font-size: 2.2rem;
  }
  
  .sub-title {
    font-size: 0.95rem;
    flex-direction: column;
    gap: 0.2rem;
  }
  
  .bracket { display: none; }
  
  .company-logo {
    height: 100px;
  }
  
  .description {
    font-size: 0.9rem;
  }
  
  .cyber-btn {
    padding: 0.9rem 2rem;
    font-size: 1rem;
  }
}
</style>
