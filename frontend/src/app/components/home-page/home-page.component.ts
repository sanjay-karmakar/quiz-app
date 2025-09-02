import { CommonModule } from '@angular/common';
import { Component, NgZone, OnInit, OnDestroy } from '@angular/core';



@Component({
  selector: 'app-home-page',
  imports: [
    CommonModule
  ],
  templateUrl: './home-page.component.html',
  styleUrl: './home-page.component.css'
})
export class HomePageComponent {
  updatedValue: string | null = null;

  constructor(private ngZone: NgZone) {}

  private storageListener = (event: StorageEvent) => {
    if (event.key === 'sharedData') {
      const parsed = JSON.parse(event.newValue || '{}');
      this.ngZone.run(() => {
        this.updatedValue = parsed.value;
        console.log('Updated Value from another tab:', this.updatedValue);
        // Trigger any UI updates here
      });
    }
  };

  ngOnInit(): void {
    window.addEventListener('storage', this.storageListener);
  }

  ngOnDestroy(): void {
    window.removeEventListener('storage', this.storageListener);
  }



}
