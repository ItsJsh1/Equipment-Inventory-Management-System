<!-- Network Status Indicator -->
<div x-data="networkStatus()" 
     x-show="showBanner" 
     x-cloak
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="transform -translate-y-full opacity-0"
     x-transition:enter-end="transform translate-y-0 opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="transform translate-y-0 opacity-100"
     x-transition:leave-end="transform -translate-y-full opacity-0"
     :class="isOnline ? 'bg-green-500' : 'bg-yellow-500'"
     class="fixed top-0 left-0 right-0 z-[60] py-2 px-4 text-center text-sm font-medium text-white safe-area-inset-top">
    <div class="flex items-center justify-center gap-2">
        <template x-if="!isOnline">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636a9 9 0 010 12.728m0 0l-2.829-2.829m2.829 2.829L21 21M15.536 8.464a5 5 0 010 7.072m0 0l-2.829-2.829"/>
            </svg>
        </template>
        <template x-if="isOnline">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.14 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0"/>
            </svg>
        </template>
        <span x-text="message"></span>
    </div>
</div>

<script>
function networkStatus() {
    return {
        isOnline: navigator.onLine,
        showBanner: false,
        message: '',
        hideTimeout: null,
        
        init() {
            // Show offline banner if starting offline
            if (!this.isOnline) {
                this.showOffline();
            }
            
            window.addEventListener('online', () => this.handleOnline());
            window.addEventListener('offline', () => this.handleOffline());
        },
        
        handleOnline() {
            this.isOnline = true;
            this.message = 'Back online';
            this.showBanner = true;
            
            if (this.hideTimeout) clearTimeout(this.hideTimeout);
            this.hideTimeout = setTimeout(() => {
                this.showBanner = false;
            }, 3000);
        },
        
        handleOffline() {
            this.isOnline = false;
            this.showOffline();
        },
        
        showOffline() {
            this.message = 'You\'re offline - Some features may be unavailable';
            this.showBanner = true;
            
            if (this.hideTimeout) clearTimeout(this.hideTimeout);
            // Keep offline banner visible
        }
    }
}
</script>
