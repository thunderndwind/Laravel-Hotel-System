import "../css/app.css";
import "./bootstrap";

import { createInertiaApp } from "@inertiajs/vue3";
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";
import { createApp, h } from "vue";
import { ZiggyVue } from "../../vendor/tightenco/ziggy";
import ToastEditor from "./Components/ToastEditor.vue";
import ToastContainer from "./Components/ToastContainer.vue";

const appName = import.meta.env.VITE_APP_NAME || "Laravel";

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob("./Pages/**/*.vue")
        ),
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) });

        app.component("ToastEditor", ToastEditor);
        app.component("ToastContainer", ToastContainer);

        return app.use(plugin).use(ZiggyVue).mount(el);
    },
    progress: {
        color: "#4B5563",
    },
});
