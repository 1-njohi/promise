import type { CapacitorConfig } from "@capacitor/cli";

const config: CapacitorConfig = {
    "appId": "com.promiseshop.app",
    "appName": "Promise Shop",
    "webDir": "public/build",
    "server": {
        "url": "https://promise.adilirealestate.com",
        "cleartext": false,
        "androidScheme": "https",
        "allowNavigation": ["*.adilirealestate.com"]
    }
};

export default config;
