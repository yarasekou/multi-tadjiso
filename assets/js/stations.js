// Gestion des formulaires stations
document.addEventListener('DOMContentLoaded', function() {
    // Auto-refresh après ajout carburant
    const carburantForm = document.querySelector('#carburant-form');
    if (carburantForm) {
        carburantForm.addEventListener('submit', function(e) {
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        });
    }

    // Animation pour nouveaux éléments
    const newItems = document.querySelectorAll('.list-group-item');
    newItems.forEach(item => {
        item.style.opacity = '0';
        item.style.transform = 'translateY(10px)';

        setTimeout(() => {
            item.style.transition = 'all 0.3s ease';
            item.style.opacity = '1';
            item.style.transform = 'translateY(0)';
        }, 100);
    });
});
