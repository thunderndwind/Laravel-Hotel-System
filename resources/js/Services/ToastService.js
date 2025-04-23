import { ref, reactive } from "vue";

const toasts = reactive([]);
let toastIdCounter = 0;

export default {
    // Show a toast notification
    show(type, title, message = "", options = {}) {
        const id = toastIdCounter++;
        const toast = {
            id,
            type,
            title,
            message,
            duration: options.duration !== undefined ? options.duration : 5000,
            persistent: options.persistent || false,
        };

        toasts.push(toast);

        // Auto-remove the toast after the duration (if not persistent)
        if (!toast.persistent && toast.duration > 0 && type !== "loading") {
            setTimeout(() => {
                this.remove(id);
            }, toast.duration);
        }

        return id;
    },

    // Show a loading toast
    loading(title, message = "") {
        return this.show("loading", title, message, { persistent: true });
    },

    // Show a success toast
    success(title, message = "") {
        return this.show("success", title, message);
    },

    // Show an error toast
    error(title, message = "") {
        return this.show("error", title, message);
    },

    // Show an info toast
    info(title, message = "") {
        return this.show("info", title, message);
    },

    // Update a loading toast to success
    loadingToSuccess(id, title, message = "") {
        this.updateToast(id, "success", title, message);
        setTimeout(() => {
            this.remove(id);
        }, 5000);
    },

    // Update a loading toast to error
    loadingToError(id, title, message = "") {
        this.updateToast(id, "error", title, message);
        setTimeout(() => {
            this.remove(id);
        }, 5000);
    },

    // Update a toast
    updateToast(id, type, title, message = "") {
        const index = toasts.findIndex((toast) => toast.id === id);
        if (index !== -1) {
            toasts[index] = {
                ...toasts[index],
                type,
                title,
                message,
                persistent: false,
            };
        }
    },

    // Remove a toast
    remove(id) {
        const index = toasts.findIndex((toast) => toast.id === id);
        if (index !== -1) {
            toasts.splice(index, 1);
        }
    },

    // Get all toasts
    getToasts() {
        return toasts;
    },
};
