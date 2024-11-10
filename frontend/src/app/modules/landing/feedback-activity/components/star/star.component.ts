import { Component, Input } from '@angular/core';
import { Output, EventEmitter } from '@angular/core';

@Component({
  selector: 'app-star',
  templateUrl: './star.component.html',
  styleUrls: ['./star.component.scss']
})
export class StarComponent {
    @Input() activityCompletionStatus:boolean = false; 
    @Input() filledIndex:number = -1; 

    @Output() ratingChange = new EventEmitter<number>();

    stars:any[] = [0,1,2,3,4];

    onClick(indx:any){
      if(this.activityCompletionStatus == false){
          this.filledIndex = indx;
          this.ratingChange.emit(indx + 1);
      }
     
    }
}
