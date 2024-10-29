import { Component  } from '@angular/core';
import { Course } from 'app/modules/landing/dashboard/models/course';
import { UpcomingCourseService } from 'app/modules/landing/dashboard/services/upcoming-course.service';
import { Router } from '@angular/router';
import { AuthService } from 'app/core/auth/auth.service';


@Component({
  selector: 'app-favourite-course',
  templateUrl: './favourite-course.component.html',
  styleUrls: ['./favourite-course.component.scss']
})
export class FavouriteCourseComponent {

  favouriteCourseList:Course[] = [];
  loading:boolean = true;
  logoPic:any= "";
  
  constructor(
      private upcomingCourseApi: UpcomingCourseService,
      private _router: Router,
      private _authService: AuthService,
  ) {}



  ngOnInit() {
    this.upcomingCourseApi.getFavouriteCourses().subscribe( (res:any)=>{
        
        if( res.exception || res.errorcode){
            this._authService.signOut();
            this._router.navigate(['login']);
            return;
        }
        
       
        for(let favCourse of res){
                 let course = {} as Course;
                 course.id = favCourse.id;
                 course.fullname = favCourse.fullname;
                 if(course.fullname.length > 40){
                    course.fullname = course.fullname.substring(0,40);
                    course.fullname += "...";
                 }

                 course.summary = favCourse.summary;
                 if(course.summary.length > 90){
                     course.summary = course.summary.substring(0,90);
                     course.summary += "...";
                 }
                 
                 course.picurl = favCourse.courseimage;
                
                 course.favourite = true;
                 this.favouriteCourseList.push(course);
        
            
        }
        this.loading = false;
    })
   
  }

}
