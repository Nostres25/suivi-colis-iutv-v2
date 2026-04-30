// Recharger les fournisseurs à la sortie d'un modal
document.addEventListener('hidden.bs.modal', () => {
    fetchSuppliersTable();
});

function fetchSuppliersTable() {

    const suppliersTableContainer = document.getElementById('suppliers-table-container');
    // supprimer l'ancien tableau

    const url = suppliersTableContainer.getAttribute('data-url');

    // Il faut que la div de la navbar et de l'alerte suive l'écran
    fetch(url)
        .then(response => response.text())
        .then(html => {
            suppliersTableContainer.innerHTML = html;
        })
        .catch(error => {
            console.error("Erreur lors de l'actualisation des fournisseurs :");
            console.error(error);

            displayAlert("Erreur lors de l'actualisation des fournisseurs", 'error')
        });
}
