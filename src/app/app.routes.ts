import { Routes } from '@angular/router';

export const routes: Routes = [
    {
        path: '',
        pathMatch: 'full',
        redirectTo : '/registro'
    },
    {
        path: 'registro',
        loadComponent: () => import('@feature/registro/registro.component').then(m => m.RegistroComponent)
    },
];
