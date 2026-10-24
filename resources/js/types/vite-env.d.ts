/// <reference types="vite/client" />

/** Variáveis de ambiente expostas ao front-end (prefixo VITE_ no .env). */
interface ImportMetaEnv {
    readonly VITE_APP_NAME: string;
}

interface ImportMeta {
    readonly env: ImportMetaEnv;
}
