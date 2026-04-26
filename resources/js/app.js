import './bootstrap';

import Alpine from 'alpinejs';
import {
    Chart,
    Filler,
    LineController,
    LineElement,
    LinearScale,
    PointElement,
    CategoryScale,
    Legend,
    Tooltip,
} from 'chart.js';

window.Alpine = Alpine;
Alpine.start();

Chart.register(
    Filler,
    LineController,
    LineElement,
    LinearScale,
    PointElement,
    CategoryScale,
    Legend,
    Tooltip,
);

function initDashboardProfitChart() {
    const canvas = document.getElementById('dashboard-profit-chart');
    if (!canvas) return;

    const labels = JSON.parse(canvas.dataset.labels || '[]');
    const values = JSON.parse(canvas.dataset.values || '[]');
    const ctx = canvas.getContext('2d');
    const gradient = ctx.createLinearGradient(0, 0, 0, canvas.clientHeight || 260);

    gradient.addColorStop(0, 'rgba(250, 204, 21, 0.35)');
    gradient.addColorStop(1, 'rgba(250, 204, 21, 0)');

    new Chart(canvas, {
        type: 'line',
        data: {
            labels,
            datasets: [
                {
                    data: values,
                    borderColor: '#ca8a04',
                    backgroundColor: gradient,
                    pointBackgroundColor: '#111827',
                    pointBorderColor: '#facc15',
                    pointBorderWidth: 2,
                    pointRadius: 3,
                    pointHoverRadius: 5,
                    borderWidth: 3,
                    fill: true,
                    tension: 0.35,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                intersect: false,
                mode: 'index',
            },
            plugins: {
                legend: {
                    display: false,
                },
                tooltip: {
                    displayColors: false,
                    callbacks: {
                        label: (context) => {
                            const value = context.parsed.y || 0;
                            const sign = value > 0 ? '+' : '';
                            return `${sign}${value.toLocaleString()} coins`;
                        },
                    },
                },
            },
            scales: {
                x: {
                    grid: {
                        display: false,
                    },
                    ticks: {
                        color: '#6b7280',
                        maxRotation: 0,
                        autoSkip: true,
                        maxTicksLimit: 5,
                    },
                    border: {
                        display: false,
                    },
                },
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(107, 114, 128, 0.12)',
                    },
                    ticks: {
                        color: '#6b7280',
                        callback: (value) => Number(value).toLocaleString(),
                    },
                    border: {
                        display: false,
                    },
                },
            },
        },
    });
}

function initAdminActivityChart() {
    const canvas = document.getElementById('admin-activity-chart');
    if (!canvas) return;

    const labels = JSON.parse(canvas.dataset.labels || '[]');
    const coins = JSON.parse(canvas.dataset.coins || '[]');
    const bets = JSON.parse(canvas.dataset.bets || '[]');

    new Chart(canvas, {
        type: 'line',
        data: {
            labels,
            datasets: [
                {
                    label: 'Coins staked',
                    data: coins,
                    borderColor: '#ca8a04',
                    backgroundColor: 'rgba(250, 204, 21, 0.18)',
                    pointBackgroundColor: '#ca8a04',
                    pointRadius: 3,
                    borderWidth: 3,
                    tension: 0.35,
                    yAxisID: 'coins',
                },
                {
                    label: 'Bets',
                    data: bets,
                    borderColor: '#111827',
                    backgroundColor: 'rgba(17, 24, 39, 0.08)',
                    pointBackgroundColor: '#111827',
                    pointRadius: 3,
                    borderWidth: 3,
                    tension: 0.35,
                    yAxisID: 'bets',
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                intersect: false,
                mode: 'index',
            },
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        boxWidth: 10,
                        color: '#374151',
                        font: {
                            weight: 'bold',
                        },
                    },
                },
            },
            scales: {
                x: {
                    grid: {
                        display: false,
                    },
                    ticks: {
                        color: '#6b7280',
                        maxRotation: 0,
                        autoSkip: true,
                        maxTicksLimit: 5,
                    },
                    border: {
                        display: false,
                    },
                },
                coins: {
                    type: 'linear',
                    position: 'left',
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(107, 114, 128, 0.12)',
                    },
                    ticks: {
                        color: '#ca8a04',
                        callback: (value) => Number(value).toLocaleString(),
                    },
                    border: {
                        display: false,
                    },
                },
                bets: {
                    type: 'linear',
                    position: 'right',
                    beginAtZero: true,
                    grid: {
                        drawOnChartArea: false,
                    },
                    ticks: {
                        color: '#111827',
                        precision: 0,
                    },
                    border: {
                        display: false,
                    },
                },
            },
        },
    });
}

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
            el.textContent = 'Betting closed';
            el.classList.remove('text-gray-500');
            el.classList.add('text-red-500');
            return;
        }

        const totalSeconds = Math.floor(diff / 1000);
        const hours = Math.floor(totalSeconds / 3600);
        const minutes = Math.floor((totalSeconds % 3600) / 60);
        const seconds = totalSeconds % 60;

        el.textContent = `Closes in ${hours}h ${minutes}m ${seconds}s`;
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
    initDashboardProfitChart();
    initAdminActivityChart();
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
