import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;
Alpine.start();

/*
|--------------------------------------------------------------------------
| Render event start time in user's local timezone
|--------------------------------------------------------------------------
*/
function renderEventStartTimes() {
    document.querySelectorAll('.event-start').forEach(el => {
        const iso = el.dataset.start;
        if (!iso) return;

        const date = new Date(iso);
        if (isNaN(date)) return;

        el.textContent = date.toLocaleString(undefined, {
            year: 'numeric',
            month: 'short',
            day: '2-digit',
            hour: '2-digit',
            minute: '2-digit',
            hour12: false,
        });
    });
}

/*
|--------------------------------------------------------------------------
| Countdown until betting closes (local time display)
|--------------------------------------------------------------------------
*/
function startCountdown(el) {
    const iso = el.dataset.closesAt;
    if (!iso) return;

    const closesAt = new Date(iso);
    if (isNaN(closesAt)) return;

    function update() {
        const now = new Date();
        const diff = closesAt - now;

        if (diff <= 0) {
            el.textContent = 'Apuestas cerradas';
            el.classList.remove('text-gray-500');
            el.classList.add('text-red-500');
            return;
        }

        const totalSeconds = Math.floor(diff / 1000);
        const hours = Math.floor(totalSeconds / 3600);
        const minutes = Math.floor((totalSeconds % 3600) / 60);
        const seconds = totalSeconds % 60;

        el.textContent = `Cierra en ${hours}h ${minutes}m ${seconds}s`;
    }

    update();
    setInterval(update, 1000);
}

/*
|--------------------------------------------------------------------------
| Init
|--------------------------------------------------------------------------
*/
document.addEventListener('DOMContentLoaded', () => {
    renderEventStartTimes();
    document.querySelectorAll('.countdown').forEach(startCountdown);
});

document.addEventListener("DOMContentLoaded", () => {

    const balanceEl = document.getElementById("nav-balance");
    if (!balanceEl) return;

    const delta = parseInt(balanceEl.dataset.delta);
    const current = parseInt(balanceEl.dataset.current);
    const last = parseInt(balanceEl.dataset.last);

    if (delta > 0) {

        // ⬇️ Delay para esperar el fade-in de la pill
        setTimeout(() => {

            let start = last;
            const end = current;

            balanceEl.textContent = start;

            const duration = 700; 
            const steps = 40;
            const increment = (end - start) / steps;
            let currentStep = 0;

            const interval = setInterval(() => {
                currentStep++;
                start += increment;

                balanceEl.textContent = Math.floor(start);

                if (currentStep >= steps) {
                    clearInterval(interval);
                    balanceEl.textContent = end;

                    fetch('/user/consume-coins-delta', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document
                                .querySelector('meta[name="csrf-token"]')
                                .getAttribute('content')
                        }
                    });
                }

            }, duration / steps);

        }, 900); // 👈 mismo delay que tu CSS
    }
});

