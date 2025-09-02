import { CommonModule } from '@angular/common';
import { Component } from '@angular/core';
import { RouterModule } from '@angular/router';

@Component({
  selector: 'app-unauthorised',
  imports: [
    CommonModule,
    RouterModule
  ],
  templateUrl: './unauthorised.component.html',
  styleUrl: './unauthorised.component.css'
})
export class UnauthorisedComponent {

}
