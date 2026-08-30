import type { CapacitorConfig } from "@capacitor/cli";

const config: CapacitorConfig = {
    appId: "com.promiseshop.app",
    appName: "Promise Shop",
    webDir: "public/build",
    server: {
        url: "https://8768-102-0-16-184.ngrok-free.app",
        cleartext: false,
        androidScheme: "https",
        iosScheme: "capacitor",
        allowNavigation: ["*.ngrok-free.app"],
    },
};

export default config;
