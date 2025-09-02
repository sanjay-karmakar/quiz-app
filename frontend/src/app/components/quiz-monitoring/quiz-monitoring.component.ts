import { Component, OnInit, OnDestroy } from '@angular/core';
import { CommonModule } from '@angular/common';
import { QuizMonitorService } from '../../core/shared/quiz-monitor.service';

@Component({
  selector: 'app-quiz-monitoring',
  standalone: true,
  imports: [
    CommonModule
  ],
  templateUrl: './quiz-monitoring.component.html',
  styleUrl: './quiz-monitoring.component.css'
})
export class QuizMonitoringComponent implements OnInit, OnDestroy {
  dataToDisplay: any[] = [];

  constructor(
    private quizService: QuizMonitorService
  ) {}

  ngOnInit() {
    this.quizService.startGettingSubmissions();

    // Subscribe to BehaviorSubject updates
    this.quizService.submissions$.subscribe(data => {
      this.dataToDisplay = data;
    });
  }

  ngOnDestroy() {
    this.quizService.stopGettingSubmissions();
  }
}
