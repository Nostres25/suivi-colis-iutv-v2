function displayAlert(message, type = 'success') {

    if (type === 'error' && !message) message = 'Une erreur inconnue est survenue';

    const alertContainers = document.getElementsByClassName('alert-container');

    for (let i = 0; i < alertContainers.length; i++) {
        const alertContainer = alertContainers[i];
        const divAlert = document.createElement('DIV');

        divAlert.className = "alert mb-0";

        switch (type) {
            case 'error': {
                divAlert.className += " alert-danger";
                break;
            }
            case 'success': {
                divAlert.className += " alert-success";
                break;
            }

            default: {
                divAlert.className += " alert-success";
                break;
            }
        }

        divAlert.textContent = message;
        alertContainer.append(divAlert);
    }
}
