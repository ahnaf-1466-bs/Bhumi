import { Component, Input } from '@angular/core';

@Component({
  selector: 'app-course-list-item',
  templateUrl: './course-list-item.component.html',
  styleUrls: ['./course-list-item.component.scss']
})
export class CourseListItemComponent {
    @Input() image_url: string;
    @Input() name: string;
   
    @Input() instructors: any[];

    insName:string = ""; 

}




