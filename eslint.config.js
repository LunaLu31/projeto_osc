import js from '@eslint/js';
import react from 'eslint-plugin-react';
import reactHooks from 'eslint-plugin-react-hooks';
import globals from 'globals';
import tseslint from 'typescript-eslint';

export default tseslint.config(
    { ignores: ['public/**', 'vendor/**', 'node_modules/**', 'bootstrap/ssr/**', 'resources/js/types/ziggy.d.ts', 'resources/js/types/api.d.ts'] },
    js.configs.recommended,
    ...tseslint.configs.recommended,
    {
        files: ['resources/js/**/*.{ts,tsx}'],
        plugins: { react, 'react-hooks': reactHooks },
        languageOptions: {
            ...react.configs.flat.recommended.languageOptions,
            globals: { ...globals.browser },
        },
        settings: { react: { version: 'detect' } },
        rules: {
            ...react.configs.flat.recommended.rules,
            ...react.configs.flat['jsx-runtime'].rules,
            ...reactHooks.configs.recommended.rules,
            // Os tipos das props são verificados pelo TypeScript.
            'react/prop-types': 'off',
            // `head-key` é o atributo do <Head> do Inertia que evita tags duplicadas.
            'react/no-unknown-property': ['error', { ignore: ['head-key'] }],
        },
    },
);
