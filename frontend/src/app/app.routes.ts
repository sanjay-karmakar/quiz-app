import { Routes } from '@angular/router';
import { LayoutComponent } from './core/layout/layout.component';
import { adminGuard } from './core/shared/guard/admin.guard';
import { authGuard } from './core/shared/guard/auth.guard';

export const routes: Routes = [
    {
        path: '',
        component: LayoutComponent,
        children: [
            {
                path: '',
                loadComponent: () => import('./components/home-page/home-page.component').then(c => c.HomePageComponent)
            },
            {
                path: 'login',
                loadComponent: () => import('./components/login/login.component').then(c => c.LoginComponent)
            },
            {
                path: 'quiz',
                loadComponent: () => import('./components/quiz/quiz.component').then(c => c.QuizComponent),
                canActivate: [authGuard]
            },
            {
                path: 'quiz-monitoring',
                loadComponent: () => import('./components/quiz-monitoring/quiz-monitoring.component').then(c => c.QuizMonitoringComponent),
                canActivate: [adminGuard]
            },
            {
                path: 'about-us',
                loadComponent: () => import('./components/about-us/about-us.component').then(c => c.AboutUsComponent),
                canActivate: [authGuard]
            },
            {
                path: 'unauthorised',
                loadComponent: () => import('./components/unauthorised/unauthorised.component').then(c => c.UnauthorisedComponent)
            },
            {
                path: '**',
                loadComponent: () => import('./components/page-not-found/page-not-found.component').then(c => c.PageNotFoundComponent)
            }
        ]
    }
];
