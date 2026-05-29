// Recharger les commandes à la sortie d'un modal
document.addEventListener('hidden.bs.modal', () => {
    fetchOrdersTable();
});


function fetchOrdersTable() {

    const ordersTableContainer = document.getElementById('orders-table-container');
    // supprimer l'ancien tableau

    const url = ordersTableContainer.getAttribute('data-url');

    // Il faut que la div de la navbar et de l'alerte suive l'écran
    fetch(url)
        .then(response => response.text())
        .then(html => {
            ordersTableContainer.innerHTML = html;
        })
        .catch(error => {
            console.error("Erreur lors de l'actualisation des commandes :");
            console.error(error);

            displayAlert("Erreur lors de l'actualisation des commandes", 'error')
        });
}
