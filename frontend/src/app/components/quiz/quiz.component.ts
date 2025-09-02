import { CommonModule } from '@angular/common';
import { Component } from '@angular/core';
import {ReactiveFormsModule, FormControl, FormGroup, FormBuilder, Validators} from '@angular/forms';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { environment as env } from '../../../environments/environment.development';
import { Router } from '@angular/router';
import { QuizMonitorService } from '../../core/shared/quiz-monitor.service';

@Component({
  selector: 'app-quiz',
  imports: [CommonModule, ReactiveFormsModule],
  templateUrl: './quiz.component.html',
  styleUrl: './quiz.component.css',
})
export class QuizComponent {
  quizForm!: FormGroup;
  responseData: any;
  allQuestions: any[] = [];
  showSuccess = false;
  notAnswered = false;

  constructor(
    private fb: FormBuilder,
    private http: HttpClient,
    private router: Router,
    private quizMonitorService: QuizMonitorService
  ) {
    this.quizForm = this.fb.group({});
  }

  ngOnInit() {
    const _token = localStorage.getItem('_token');
    const header = new HttpHeaders({
      'Content-Type': 'application/json',
      Accept: 'application/json',
      Authorization: `Bearer ${_token}`,
    });

    this.http.get(env.backendUrl + 'questions.php', {headers: header, withCredentials: true})
      .subscribe({
        next: (res) => {
          this.responseData = res;

          if (this.responseData && this.responseData?.response?.status?.code === 200) {
            this.allQuestions = this.responseData?.response?.dataset || [];

            console.log('Question fetched successful', this.allQuestions);

            const group: any = {};
            this.allQuestions.forEach((q, index) => {
              group[`question_${q.id}`] = new FormControl(null);
            });
            this.quizForm = this.fb.group(group);

          } else {
            console.error('No questions found');
          }
        },
        error: (err) => {
          console.log('Error fetching questions', err);
        },
      });
  }

  onSubmit() {
    // console.log(this.quizForm.value);
    const dataToSend = { answers: this.quizForm.value };

    console.log('Data to send:', dataToSend);

    if (this.quizForm.valid) {
      const _token = localStorage.getItem('_token');
      const header = new HttpHeaders({
        'Content-Type': 'application/json',
        Accept: 'application/json',
        Authorization: `Bearer ${_token}`,
      });

      this.http.post(env.backendUrl + 'submit.php', dataToSend, {headers: header, withCredentials: true})
        .subscribe({
          next: (res) => {
            this.responseData = res;

            if (this.responseData && this.responseData?.response?.status?.code === 200) {
              // console.log('Quiz form submitted successfully');

              localStorage.setItem('sharedData', JSON.stringify({ value: 'Form Submitted' }));

              this.quizForm.reset();
              this.showSuccess = true;
              setTimeout(() => {
                this.showSuccess = false;
              }, 5000);
            } else if (this.responseData && this.responseData?.response?.status?.code === 400) {
              this.notAnswered = true;
              setTimeout(() => {
                this.notAnswered = false;
              }, 5000);
            } else {
              console.error('Quiz form failed');
            }
          },
          error: (error) => {
            console.error('Quiz failed', error);
          },
        });
    } else {
      console.log('Quiz form is invalid');
      return;
    }
  }
}