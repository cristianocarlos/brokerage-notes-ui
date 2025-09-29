import eslint from '@eslint/js';
import prettierConfig from 'eslint-config-prettier';
import reactPlugin from 'eslint-plugin-react';
import importPlugin from 'eslint-plugin-import';
import reactHooksPlugin from 'eslint-plugin-react-hooks';
import reactRefreshPlugin from 'eslint-plugin-react-refresh';
import unusedImportsPlugin from 'eslint-plugin-unused-imports';
import tseslint from 'typescript-eslint';

export default tseslint.config(
    {
        extends: [
            eslint.configs.recommended,
            importPlugin.flatConfigs.recommended,
            ...tseslint.configs.recommended,
            prettierConfig,
        ],
        plugins: {
            react: reactPlugin,
            'react-hooks': reactHooksPlugin,
            'react-refresh': reactRefreshPlugin,
            'unused-imports': unusedImportsPlugin
        },
        files: ['resources/js/**/*.{ts,tsx}'],
        rules: {
            '@typescript-eslint/consistent-type-assertions': ['error', {assertionStyle: 'as'}],
            '@typescript-eslint/consistent-type-imports': ['error'],
            '@typescript-eslint/no-inferrable-types': ['error'],
            '@typescript-eslint/no-unused-expressions': ['error', {allowShortCircuit: true}], // Permite &&
            '@typescript-eslint/no-unused-vars': ['error', {
                argsIgnorePattern: '^_',
                caughtErrorsIgnorePattern: '^_',
                varsIgnorePattern: 'React',
            }],
            'import/no-unresolved': ['off'],
            'no-useless-escape': ['off'],
            'react-hooks/exhaustive-deps': ['warn', {additionalHooks: 'useDidUpdateEffect'}],
            'react/jsx-filename-extension': [2, {extensions: ['.ts', '.tsx']}], // Permit js and jsx references inside ts and tsx components
            'react/jsx-sort-props': ['warn', {
                ignoreCase: true,
                callbacksLast: false,
                shorthandFirst: false,
                shorthandLast: false,
                noSortAlphabetically: false,
            }],
            'import/order': ['error',
                {
                    alphabetize: {'order': 'asc', 'caseInsensitive': true},
                    groups: [['builtin', 'external'], 'internal', ['parent', 'sibling'], 'type'],
                    'newlines-between': 'always-and-inside-groups',
                    pathGroups: [
                        {group: 'builtin', pattern: '*.css', patternOptions: {matchBase: true}, position: 'before'},
                        {group: 'builtin', pattern: '~/phpgen/yiit*', position: 'before'},
                        {group: 'internal', pattern: '@/**', position: 'before'},
                    ],
                    pathGroupsExcludedImportTypes: ['type'],
                },
            ],
            'sort-keys': ['warn', 'asc', {caseSensitive: false}],
            'unused-imports/no-unused-imports': ['warn'],
        },
    },
);
