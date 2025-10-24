// Gestion des stations
import '../styles/stations.css';

document.addEventListener('DOMContentLoaded', function() {
    // Gestion des formulaires AJAX
    const ajaxForms = document.querySelectorAll('.ajax-form');

    ajaxForms.forEach(form => {
        form.addEventListener('submit', async function(e) {
            e.preventDefault();

            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;

            // État de chargement
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Chargement...';
            submitBtn.disabled = true;

            try {
                const formData = new FormData(this);
                const response = await fetch(this.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (response.redirected) {
                    window.location.href = response.url;
                } else if (response.ok) {
                    const result = await response.json();
                    showNotification(result.message, 'success');

                    // Recharger la page si spécifié
                    if (this.dataset.refresh) {
                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                    }
                }
            } catch (error) {
                console.error('Error:', error);
                showNotification('Une erreur est survenue', 'error');
            } finally {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }
        });
    });

    // Confirmation de suppression
    const deleteForms = document.querySelectorAll('form[onsubmit]');
    deleteForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!confirm('Êtes-vous sûr de vouloir effectuer cette action ?')) {
                e.preventDefault();
            }
        });
    });

    // Animation des éléments
    function animateElements() {
        const cards = document.querySelectorAll('.station-card');
        cards.forEach((card, index) => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';

            setTimeout(() => {
                card.style.transition = 'all 0.5s ease';
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, index * 100);
        });
    }

    // Notifications
    function showNotification(message, type = 'info') {
        const alertClass = {
            'success': 'alert-success',
            'error': 'alert-danger',
            'warning': 'alert-warning',
            'info': 'alert-info'
        }[type];

        const notification = document.createElement('div');
        notification.className = `alert ${alertClass} alert-dismissible fade show`;
        notification.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;

        // Ajouter au début du contenu
        const container = document.querySelector('.container-fluid') || document.querySelector('.container');
        container.insertBefore(notification, container.firstChild);

        // Auto-remove après 5 secondes
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 5000);
    }

    // Filtres et recherche
    const searchInput = document.getElementById('station-search');
    if (searchInput) {
        searchInput.addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const stations = document.querySelectorAll('.station-item');

            stations.forEach(station => {
                const stationName = station.querySelector('.station-name').textContent.toLowerCase();
                if (stationName.includes(searchTerm)) {
                    station.style.display = '';
                } else {
                    station.style.display = 'none';
                }
            });
        });
    }

    // Initialisation
    animateElements();
});

// Export pour utilisation ailleurs
export { showNotification };
