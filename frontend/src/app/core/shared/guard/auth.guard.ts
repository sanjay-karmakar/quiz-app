import { inject } from '@angular/core';
import { CanActivateFn, Router } from '@angular/router';

export const authGuard: CanActivateFn = (route, state) => {
  const router = inject(Router);

  const token = localStorage.getItem('_token');
  const userType = localStorage.getItem('_usertype');

  if (!token) {
    router.navigate(['/login']);
    return false;
  }

  if (userType !== 'User') {
    router.navigate(['/quiz-monitoring']);
    return false;
  }

  return true;
};
