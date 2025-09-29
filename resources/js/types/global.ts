import type {PageProps as AppPageProps} from './common';
import type {PageProps as InertiaPageProps} from '@inertiajs/core';
import type {AxiosInstance} from 'axios';
import type {route as ziggyRoute} from 'ziggy-js';

declare global {
    interface Window {
        axios: AxiosInstance;
    }
    let route: typeof ziggyRoute;
}

declare module '@inertiajs/core' {
    interface PageProps extends InertiaPageProps, AppPageProps {}
}
