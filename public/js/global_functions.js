function displayAlert(message, type = 'success') {

    const alertContainer = document.querySelector('.alert-container');
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
