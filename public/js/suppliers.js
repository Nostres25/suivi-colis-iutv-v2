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
 
// Client-side form validator for supplier form
class SupplierFormValidator {
    constructor(form) {
        this.form = form;
    }

    validate() {
        const errors = {};

        const companyName = document.getElementById('company-name')?.value.trim();
        if (!companyName) errors.companyName = "Le nom de l'entreprise est requis";

        const siret = document.getElementById('siret')?.value.trim();
        if (siret && !/^\d{14}$/.test(siret)) errors.siret = 'Le SIRET doit contenir 14 chiffres';

        const email = document.getElementById('email')?.value.trim();
        if (email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) errors.email = "Adresse e-mail invalide";

        const phone = document.getElementById('phone')?.value.trim();
        if (phone && !/^[0-9+\s().'\-]{6,20}$/.test(phone)) errors.phone = 'Numéro de téléphone invalide';

        const address = document.getElementById('address')?.value.trim();
        if (!address) errors.address = 'L\'adresse est requise';
        else if (address.length > 255) errors.address = 'L\'adresse est trop longue';

        const speciality = document.getElementById('speciality')?.value.trim();
        if (speciality && speciality.length > 255) errors.speciality = 'La spécialité est trop longue';

        const note = document.getElementById('note')?.value.trim();
        if (note && note.length > 2000) errors.note = 'La note est trop longue';

        return { valid: Object.keys(errors).length === 0, errors };
    }

    firstErrorMessage(errors) {
        return errors[Object.keys(errors)[0]];
    }
}

    // Gestion de la soumission du formulaire d'ajout de fournisseur
    document.addEventListener('DOMContentLoaded', function () {
        const addSupplierForm = document.getElementById('addSupplierForm');
        if (!addSupplierForm) return;

        addSupplierForm.addEventListener('submit', function (e) {
            e.preventDefault();

            // Client-side validation
            const validator = new SupplierFormValidator(addSupplierForm);
            const check = validator.validate();
            if (!check.valid) {
                displayAlert(validator.firstErrorMessage(check.errors), 'error');
                return;
            }

            const tokenInput = addSupplierForm.querySelector('input[name="_token"]');
            const token = tokenInput ? tokenInput.value : '';

            const validityField = document.getElementById('supplier-status');
            const validityValue = validityField ? validityField.value : 'pending';

            const payload = {
                companyName: document.getElementById('company-name')?.value.trim(),
                siret: document.getElementById('siret')?.value.trim(),
                email: document.getElementById('email')?.value.trim(),
                phoneNumber: document.getElementById('phone')?.value.trim(),
                contactName: document.getElementById('contact-name')?.value.trim(),
                address: document.getElementById('address')?.value.trim(),
                speciality: document.getElementById('speciality')?.value.trim(),
                note: document.getElementById('note')?.value.trim(),
                isValid: validityValue
            };

            fetch('/suppliers', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(payload)
            })
                .then(async response => {
                    const contentType = response.headers.get('content-type') || '';
                    const isJson = contentType.includes('application/json');
                    const data = isJson ? await response.json() : { message: await response.text() };

                    if (!response.ok) {
                        return Promise.reject(data);
                    }

                    return data;
                })
                .then(result => {
                    if (result && result.success) {
                        displayAlert(result.message || 'Fournisseur ajouté', 'success');
                        const modalEl = document.getElementById('addSupplierModal');
                        const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                        modal.hide();

                        document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
                        document.body.classList.remove('modal-open');
                        document.body.style.removeProperty('padding-right');
                    }
                })
                .catch(err => {
                    console.error(err);
                    if (err.errors) {
                        const first = Object.values(err.errors)[0];
                        displayAlert(first[0] || 'Erreur lors de la création', 'error');
                    } else if (err.message) {
                        displayAlert(err.message, 'error');
                    } else {
                        displayAlert('Erreur lors de la création du fournisseur', 'error');
                    }
                });
        });
    });
