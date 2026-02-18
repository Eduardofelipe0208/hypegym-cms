document.addEventListener('DOMContentLoaded', function () {
    // Configuración global de Chart.js
    Chart.defaults.color = '#888';
    Chart.defaults.borderColor = '#2A2B2E';
    Chart.defaults.font.family = "'Inter', sans-serif";

    // Cargar datos del servidor
    fetchData();

    // Actualizar cada 60 segundos (Opcional)
    // setInterval(fetchData, 60000);
});

async function fetchData() {
    try {
        const response = await fetch('../api/dashboard_stats.php');
        const data = await response.json();

        if (data.error) {
            console.error('Error API:', data.error);
            return;
        }

        updateKPIs(data);
        renderSalesChart(data.sales_last_7_days);
        renderMonthlyChart(data.sales_by_month);
        renderTopProductsChart(data.top_products);
        renderRecentOrders(data.recent_orders);

    } catch (error) {
        console.error('Error fetching dashboard data:', error);
    }
}

function renderRecentOrders(orders) {
    const tbody = document.getElementById('recent-orders-body');
    if (!tbody) return;

    if (!orders || orders.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" class="text-center" style="color:#888;">No hay pedidos recientes</td></tr>';
        return;
    }

    tbody.innerHTML = orders.map(order => {
        const date = new Date(order.created_at).toLocaleDateString('es-ES', { day: 'numeric', month: 'short' });
        let statusClass = 'status-pending';
        let statusText = 'Pendiente';

        if (order.status === 'completed') { statusClass = 'status-completed'; statusText = 'Completado'; }
        if (order.status === 'rejected') { statusClass = 'status-failed'; statusText = 'Rechazado'; }

        return `
            <tr>
                <td style="font-family:'Chakra Petch'; color:#fff;">#${order.id}</td>
                <td><div style="font-weight:500;">${order.customer_name}</div></td>
                <td style="font-weight:600; color:#fff;">$${parseFloat(order.total_amount).toFixed(2)}</td>
                <td><span class="status-badge ${statusClass}">${statusText}</span></td>
                <td style="color:#888;">${date}</td>
            </tr>
        `;
    }).join('');
}

function updateKPIs(data) {
    // Helper para formatear moneda
    const formatCurrency = (amount, currency = 'USD') => {
        return new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: currency,
            minimumFractionDigits: 2
        }).format(amount);
    };

    const formatBs = (amount) => {
        return 'Bs ' + new Intl.NumberFormat('es-VE', {
            minimumFractionDigits: 2
        }).format(amount);
    };

    // Actualizar DOM
    document.getElementById('total-orders').textContent = data.total_orders;
    document.getElementById('income-usd').textContent = formatCurrency(data.income_usd);
    document.getElementById('income-bs').textContent = formatBs(data.income_bs);
    document.getElementById('pending-orders').textContent = data.pending_orders;
}

let salesChartInstance = null;
let monthlyChartInstance = null;
let productsChartInstance = null;

function renderSalesChart(data) {
    const ctx = document.getElementById('salesChart').getContext('2d');

    if (salesChartInstance) salesChartInstance.destroy();

    const labels = data.map(item => {
        const date = new Date(item.date); // Ajustar si es necesario zona horaria
        return date.toLocaleDateString('es-ES', { weekday: 'short', day: 'numeric' });
    });
    const values = data.map(item => item.total);

    // Gradiente para el gráfico
    const gradient = ctx.createLinearGradient(0, 0, 0, 400);
    gradient.addColorStop(0, 'rgba(214, 254, 0, 0.5)'); // Color primario con opacidad
    gradient.addColorStop(1, 'rgba(214, 254, 0, 0)');

    salesChartInstance = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Ventas (USD)',
                data: values,
                borderColor: '#D6FE00',
                backgroundColor: gradient,
                borderWidth: 2,
                pointBackgroundColor: '#fff',
                pointBorderColor: '#D6FE00',
                pointRadius: 4,
                pointHoverRadius: 6,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1A1B1E',
                    titleColor: '#fff',
                    bodyColor: '#D6FE00',
                    borderColor: '#2A2B2E',
                    borderWidth: 1
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: '#2A2B2E'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
}

function renderMonthlyChart(data) {
    const ctx = document.getElementById('monthlyChart').getContext('2d');

    if (monthlyChartInstance) monthlyChartInstance.destroy();

    // Mapeo de meses (2026-01 -> Ene)
    const monthNames = ["Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic"];

    const labels = data.map(item => {
        const parts = item.month.split('-');
        return monthNames[parseInt(parts[1]) - 1];
    });
    const values = data.map(item => item.total);

    monthlyChartInstance = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Ventas Mensuales',
                data: values,
                backgroundColor: '#D6FE00',
                borderRadius: 4,
                hoverBackgroundColor: '#c4ec00'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: '#2A2B2E' }
                },
                x: {
                    grid: { display: false }
                }
            }
        }
    });
}

function renderTopProductsChart(data) {
    const ctx = document.getElementById('productsChart').getContext('2d');

    if (productsChartInstance) productsChartInstance.destroy();

    // Si no hay datos, mostrar mensaje o chart vacío
    if (!data || data.length === 0) return;

    const labels = data.map(item => item.name);
    const values = data.map(item => item.total_sold);

    productsChartInstance = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: values,
                backgroundColor: [
                    '#D6FE00',
                    '#3b82f6',
                    '#a855f7',
                    '#ef4444',
                    '#f59e0b'
                ],
                borderWidth: 0,
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        boxWidth: 12,
                        padding: 20,
                        color: '#fff'
                    }
                }
            },
            cutout: '70%'
        }
    });
}
