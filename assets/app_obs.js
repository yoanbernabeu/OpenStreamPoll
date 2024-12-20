import './styles/obs.css';
import 'htmx.org';
import Alpine from 'alpinejs';

window.Alpine = Alpine;

document.addEventListener('alpine:init', () => {
    Alpine.data('timer', (endDate) => ({
        minutes: '00',
        seconds: '00',
        isExpired: false,
        
        init() {
            this.updateTimer()
            setInterval(() => this.updateTimer(), 1000)
        },

        updateTimer() {
            const end = new Date(endDate)
            const now = new Date()
            const diff = end - now

            if (diff <= 0) {
                this.isExpired = true
                this.minutes = '00'
                this.seconds = '00'
                return
            }

            const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60))
            const seconds = Math.floor((diff % (1000 * 60)) / 1000)

            this.minutes = minutes.toString().padStart(2, '0')
            this.seconds = seconds.toString().padStart(2, '0')
        }
    }))
});

Alpine.start();
