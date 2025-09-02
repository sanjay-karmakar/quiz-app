import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { BehaviorSubject, interval, Subscription } from 'rxjs';
import { environment as env } from '../../../environments/environment.development';

@Injectable({
  providedIn: 'root'
})
export class QuizMonitorService {
  private submissionsSubject = new BehaviorSubject<any[]>([]);
  submissions$ = this.submissionsSubject.asObservable();

  private submissions?: Subscription;

  constructor(private http: HttpClient) {}

  startGettingSubmissions() {
    if (this.submissions) return;

    // Trigger every 5 seconds
    this.submissions = interval(5000).subscribe(() => {
      this.fetchSubmissions();
    });

    this.fetchSubmissions();  // Initial fetch
  }

  private fetchSubmissions() {
    const _token = localStorage.getItem('_token');
    const headers = new HttpHeaders({
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      'Authorization': `Bearer ${_token}`
    });

    this.http.get<any>(env.backendUrl + 'submissions.php', {headers, withCredentials: true}).subscribe(res => {
      if (res?.response?.status?.code === 200) {
        this.submissionsSubject.next(res.response.dataset || []);
      } else {
        this.submissionsSubject.next([]);
      }
    });
  }

  stopGettingSubmissions() {
    this.submissions?.unsubscribe();
    this.submissions = undefined;
  }
}
