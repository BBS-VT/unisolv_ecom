export default class CartNotifications {
    constructor() {
        this.toastElement = document.getElementById('cartToast');
        this.toast = null;

        if (this.toastElement) {
            this.toast = new bootstrap.Toast(this.toastElement);
        }
    }

    show(message, type = 'success') {
        if (!this.toast) return;

        const toastBody = this.toastElement.querySelector('.toast-body');
        const toastHeader = this.toastElement.querySelector('.toast-header');

        toastBody.textContent = message;

        toastHeader.classList.remove('bg-success', 'bg-danger', 'bg-warning', 'text-white');

        if (type === 'success') {
            toastHeader.classList.add('bg-success', 'text-white');
        } else if (type === 'danger') {
            toastHeader.classList.add('bg-danger', 'text-white');
        } else if (type === 'warning') {
            toastHeader.classList.add('bg-warning');
        }

        this.toast.show();
    }
}
