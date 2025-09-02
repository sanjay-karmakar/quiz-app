import { CommonModule } from '@angular/common';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Component } from '@angular/core';
import { Router, RouterModule, RouterOutlet } from '@angular/router';
import { environment as env } from '../../../environments/environment.development';

@Component({
  selector: 'app-layout',
  imports: [
    CommonModule,
    RouterOutlet,
    RouterModule
  ],
  templateUrl: './layout.component.html',
  styleUrl: './layout.component.css'
})
export class LayoutComponent {
  responseData: any;

  constructor(
    private router: Router,
    private http: HttpClient
  ) {}

  get isLoggedIn() {
    return !!localStorage.getItem('_token');
  }

  logout() {
    const _token = localStorage.getItem('_token');
    const header = new HttpHeaders({
      'Content-Type': 'application/json',
      Accept: 'application/json',
      Authorization: `Bearer ${_token}`,
    });

    localStorage.removeItem('_username');
    localStorage.removeItem('_usertype');
    localStorage.removeItem('_token');
    localStorage.removeItem('quizSubmitted');

    this.router.navigate(['/login']);

    this.http.get(env.backendUrl + 'logout.php', {headers: header, withCredentials: true})
      .subscribe({
        next: (res) => {
          this.responseData = res;

          console.log('Logout response', this.responseData);

          if (this.responseData && this.responseData?.response?.status?.code === 200) {
            
          } else {
            console.error('Lorout failed');
          }
        },
        error: (err) => {
          console.log('Something went wrong', err);
        },
      });
  }
}
