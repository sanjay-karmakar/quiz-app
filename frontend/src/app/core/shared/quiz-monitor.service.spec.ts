import { TestBed } from '@angular/core/testing';

import { QuizMonitorService } from './quiz-monitor.service';

describe('QuizMonitorService', () => {
  let service: QuizMonitorService;

  beforeEach(() => {
    TestBed.configureTestingModule({});
    service = TestBed.inject(QuizMonitorService);
  });

  it('should be created', () => {
    expect(service).toBeTruthy();
  });
});
