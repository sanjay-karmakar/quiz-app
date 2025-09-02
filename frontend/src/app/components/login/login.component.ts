import { CommonModule } from '@angular/common';
import { Component } from '@angular/core';
import { FormBuilder, FormGroup, FormControl, ReactiveFormsModule, Validators } from '@angular/forms';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { environment as env } from '../../../environments/environment.development';
import { Router } from '@angular/router';

@Component({
  selector: 'app-login',
  imports: [
    CommonModule,
    ReactiveFormsModule,
  ],
  templateUrl: './login.component.html',
  styleUrl: './login.component.css'
})
export class LoginComponent {
  loginForm: FormGroup;
  isFormSubmitted = false;
  responseData: any;
  showError = false;

  constructor(
    private fb: FormBuilder,
    private http: HttpClient,
    private router: Router,
  ) {
    this.loginForm = this.fb.group({
      username: new FormControl('', [Validators.required])
    });
  }

  onSubmit() {
    this.isFormSubmitted = true;
    const dataToSend = { username: this.loginForm.value.username };

    if (this.loginForm.valid) {      
      // console.log('Form Submitted!', this.loginForm.value);
      
      const header = new HttpHeaders({
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      });

      this.http.post(env.backendUrl+'login.php', dataToSend, { headers: header, withCredentials: true })
        .subscribe({
          next: (res) => {
            this.responseData = res;

            if (this.responseData && this.responseData?.response?.status?.code === 200) {
              let userData = this.responseData?.response?.dataset?.[0];

              console.log('Login successful', userData);

              localStorage.setItem('_username', userData?.username ?? '');
              localStorage.setItem('_usertype', userData?.usertype ?? '');
              localStorage.setItem('_token', userData?._auth_token ?? '');
              
              if (userData?.usertype === 'User') {
                this.router.navigate(['/quiz']);
              } else {
                this.router.navigate(['/quiz-monitoring']);
              }              
            } else if (this.responseData && this.responseData?.response?.status?.code === 400) {
              
              this.showError = true;
              setTimeout(() => {
                this.showError = false;
                this.loginForm.reset();
              }, 5000);

            } else {
              console.error('Login failed');
            }
          },
          error: (error) => {
            console.error('Login failed', error);
          }
        });
    } else {
      console.log('Form is invalid');
    }
  }
}