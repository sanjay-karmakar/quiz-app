import { ComponentFixture, TestBed } from '@angular/core/testing';

import { QuizMonitoringComponent } from './quiz-monitoring.component';

describe('QuizMonitoringComponent', () => {
  let component: QuizMonitoringComponent;
  let fixture: ComponentFixture<QuizMonitoringComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [QuizMonitoringComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(QuizMonitoringComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
