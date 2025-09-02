import { Component, NgZone } from '@angular/core';

@Component({
  selector: 'app-about-us',
  imports: [],
  templateUrl: './about-us.component.html',
  styleUrl: './about-us.component.css'
})
export class AboutUsComponent {
  changedData: string | null = null;

  constructor(private ngZone: NgZone) {}

  private storageUpdate = (event: StorageEvent) => {
    if (event.key === 'sharedData') {
      const parsedData = JSON.parse(event.newValue || '{}')
      

      this.ngZone.run(() => {
        this.changedData = parsedData.value;
      })
    }
  }

  ngOnInit(): void {
    window.addEventListener('storage', this.storageUpdate)
  }

  ngOnDestroy() {
    window.removeEventListener('storage', this.storageUpdate)
  }

}
