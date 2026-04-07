/// <reference types="vite/client" />

interface ImportMeta {
  readonly env: {
    readonly VITE_APP_NAMR: string;
    readonly VITE_APP_VERSION: string;
    readonly VITE_API_URL: string;
  };
}
