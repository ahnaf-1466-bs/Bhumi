import { Component } from '@angular/core';

@Component({
  selector: 'app-welcome-banner',
  templateUrl: './welcome-banner.component.html',
  styleUrls: ['./welcome-banner.component.scss']
})
export class WelcomeBannerComponent {
  username: string = localStorage.getItem('user-firstname'); // Replace with the user's actual name
 
  constructor() {}

}
