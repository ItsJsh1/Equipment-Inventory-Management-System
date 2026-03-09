<!-- PWA Install Prompt -->
<div x-data="pwaInstall()" 
     x-show="showInstallPrompt" 
     x-cloak
     class="pwa-install-banner safe-area-inset-bottom"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="translate-y-full"
     x-transition:enter-end="translate-y-0"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="translate-y-0"
     x-transition:leave-end="translate-y-full">
    <div class="max-w-screen-xl mx-auto flex items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="flex-shrink-0">
                <img src="{{ asset('icons/icon-72x72.png') }}" alt="EIMS" class="w-12 h-12 rounded-lg">
            </div>
            <div>
                <p class="font-semibold text-gray-900 dark:text-white text-sm sm:text-base">Install EIMS App</p>
                <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400">Add to home screen for quick access</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <button @click="dismissPrompt()" 
                    class="px-3 py-2 text-sm text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200">
                Not now
            </button>
            <button @click="installApp()" 
                    class="px-4 py-2 bg-black text-white dark:bg-white dark:text-black text-sm font-medium rounded-lg hover:bg-gray-800 dark:hover:bg-gray-200 transition-colors">
                Install
            </button>
        </div>
    </div>
</div>

<!-- Standalone mode indicator (optional) -->
<div x-data="{ isStandalone: window.matchMedia('(display-mode: standalone)').matches }"
     x-show="isStandalone"
     x-cloak
     class="hidden">
    <!-- App is running in standalone mode -->
</div>

<script>
function pwaInstall() {
    return {
        showInstallPrompt: false,
        deferredPrompt: null,
        
        init() {
            // Check if already installed or dismissed
            if (localStorage.getItem('pwa-install-dismissed')) {
                const dismissedTime = parseInt(localStorage.getItem('pwa-install-dismissed'));
                // Show again after 7 days
                if (Date.now() - dismissedTime < 7 * 24 * 60 * 60 * 1000) {
                    return;
                }
            }
            
            // Check if running in standalone mode
            if (window.matchMedia('(display-mode: standalone)').matches) {
                return;
            }
            
            // Listen for the beforeinstallprompt event
            window.addEventListener('beforeinstallprompt', (e) => {
                e.preventDefault();
                this.deferredPrompt = e;
                this.showInstallPrompt = true;
            });
            
            // Listen for successful installation
            window.addEventListener('appinstalled', () => {
                this.showInstallPrompt = false;
                this.deferredPrompt = null;
                localStorage.removeItem('pwa-install-dismissed');
            });
        },
        
        async installApp() {
            if (!this.deferredPrompt) return;
            
            this.deferredPrompt.prompt();
            const { outcome } = await this.deferredPrompt.userChoice;
            
            if (outcome === 'accepted') {
                console.log('User accepted the install prompt');
            }
            
            this.deferredPrompt = null;
            this.showInstallPrompt = false;
        },
        
        dismissPrompt() {
            this.showInstallPrompt = false;
            localStorage.setItem('pwa-install-dismissed', Date.now().toString());
        }
    }
}
</script>
