import { Component } from '@angular/core';
import { Certificate } from './comoponents/past-certificate/models/certificate';
import { Course } from './models/course';
import { EnrolledCourseService } from './services/enrolled-course.service';
import { PastCertificateService } from './services/past-certificate.service';
import { UpcomingCourseService } from './services/upcoming-course.service';
import { Router } from '@angular/router';
import { AuthService } from 'app/core/auth/auth.service';
import { GetCourseDetailsService } from '../course-list/services/get-course-details.service';
import { TranslateService } from '@ngx-translate/core';

@Component({
    selector: 'app-dashboard',
    templateUrl: './dashboard.component.html',
    styleUrls: ['./dashboard.component.scss'],
})
    
export class DashboardComponent {
     
      userName:string = localStorage.getItem('user-name');
      visibility:boolean = false;
      loading:boolean = false;
      enrolledCourseList:Course[] = [];
      upcomingCourseList:Course[] = [];
      favouriteCourseList:Course[] = [];
      recommendedCourseList:Course[] = [];
      pastCertificateList:Certificate[] = [];
      cntDataSet:number = 0;
     
    
      constructor(
          
          private enrolledCourseApi: EnrolledCourseService,
          private upcomingCourseApi: UpcomingCourseService,
          private pastCertificateApi:PastCertificateService,
          private _authService: AuthService,
          private _router:Router,
          private getCourseDetails: GetCourseDetailsService,
        private translateService: TranslateService

      ) {}
    
      ngOnInit(){
        if(localStorage.getItem('auth-token') && localStorage.getItem('user-id')){
            this.visibility = true;
        }
        else{
            this._router.navigate(['login']);
            return;
        }
       
        
        this.getEnrolledCourses();
        this.upcomingCourseApi.getFavouriteCourses().subscribe((res: any) => {
            this.cntDataSet++;
            if (Array.isArray(res)) { // check if res is an array
              for (let favCourse of res) {
                let course = {} as Course;
                course.id = favCourse.id;
                course.fullname = favCourse.fullname;
                if (course.fullname.length > 40) {
                  course.fullname = course.fullname.substring(0, 40);
                  course.fullname += "...";
                }
          
                course.summary = favCourse.summary;
                
          
                course.picurl = favCourse.courseimage;
          
                course.favourite = true;
                this.favouriteCourseList.push(course);
              }
              this.getUpcomingCourses();
              this.getRecommendedCourses();
            } 
        });
          
        this.pastCertificateApi.getPastCertificates().subscribe( (res:any)=>{
            this.cntDataSet++;
            if(res.certificateList){
               for(let cert of res.certificateList){
                    let certificate = {} as Certificate;
                    certificate.name = cert.courseshortname;
                    certificate.url = cert.url;
                    certificate.issueDate = cert.issueddate;
                    this.pastCertificateList.push(certificate);
               }
           
               
            }
       })
    
      }
    
      resetCourseState(updatedCourse:Course){
              const indexUpcomingList = this.upcomingCourseList.findIndex((item) => item.id == updatedCourse.id);
              const indexRecommendedList = this.recommendedCourseList.findIndex((item) => item.id == updatedCourse.id);
              if (indexUpcomingList !== -1) {
                  this.upcomingCourseList[indexUpcomingList].favourite = updatedCourse.favourite;
              }
              if (indexRecommendedList !== -1) {
                  this.recommendedCourseList[indexRecommendedList].favourite = updatedCourse.favourite;
              }
    
              if(updatedCourse.favourite == true){
                    const favCourseExists = this.favouriteCourseList.some((item) => item.id == updatedCourse.id); 
                    if(!favCourseExists){
                        this.favouriteCourseList.push(updatedCourse);
                    }   
              }
              else{
                    const itemIndexToRemove = this.favouriteCourseList.findIndex((item) => item.id == updatedCourse.id);
                    if (itemIndexToRemove !== -1) {
                      this.favouriteCourseList.splice(itemIndexToRemove, 1);
                    }
              }
         
      }
    
    
    
    
      unfavouriteCourse(courseID){
          const courseIndexUpcoming = this.upcomingCourseList.findIndex((item) => item.id == courseID);
          const courseIndexRecommended = this.recommendedCourseList.findIndex((item) => item.id == courseID);

          this.upcomingCourseList[courseIndexUpcoming].favourite = false;
          this.recommendedCourseList[courseIndexRecommended].favourite = false;
          
      }
      
    
      getEnrolledCourses(){
        let visibleCourses:any[];
        this.enrolledCourseApi.getEnrolledCourses().subscribe((res: any) => {
          this.getCourseDetails.getCourseDetails().subscribe((res2)=>{
            visibleCourses=res2.courses.filter((c)=>{return c.visible==1;});

            if(res.errorcode && res.errorcode == 'invalidtoken'){
              this._authService.signOut();
              
              this._router.navigate(['login']);
              return;
            } 
          
            this.cntDataSet++;
            if (Array.isArray(res)) { // check if res is an array
              for (let myCourse of res) {
                let course = {} as Course;
                course.id = myCourse.id;
                course.fullname = myCourse.fullname;
          
                course.summary = myCourse.summary;
                
          
                if (myCourse.overviewfiles && myCourse.overviewfiles.length > 0) { // check if overviewfiles exist
                  course.picurl = myCourse.overviewfiles[0]?.fileurl;
                  course.picurl = course.picurl.replaceAll('/webservice', '')
                }
          
                course.progress = Math.floor(myCourse.progress);

                if(this.translateService.getDefaultLang()==='bn')
                {
                  const crse = visibleCourses.find(c=>c.id===course.id);

                  const bengaliName = crse.customfields.find(field=>field.shortname==='cname').value;
                  const bengaliDescription = crse.customfields.find(field=>field.shortname==='cdescription').value;

                  course.fullname = bengaliName?.length>0 ? bengaliName: '*';
                  
                  course.summary = bengaliDescription?.length>0 ? bengaliDescription: '*';


                }
                

                this.enrolledCourseList.push(course);
              }
            }
          })
        });
          
      }
    
  getUpcomingCourses() {
    let visibleCourses: any[];
    this.upcomingCourseApi.getUpcomingCourses().subscribe((res: any) => {
      this.getCourseDetails.getCourseDetails().subscribe((res2) => {
        visibleCourses = res2.courses.filter((c) => { return c.visible == 1; });

        this.cntDataSet++;

        this.getCourseDetails.getAllCoursesList().subscribe((courses: any) => {
          const visibleCourseIDs = courses.filter(course => course.visible === 1)
            .map(course => course.id);

          if (res.future_courses) {

            for (let upcomingCourse of res.future_courses
              ?.filter(course => visibleCourseIDs.includes(course.id))) {
              let course = {} as Course;
              course.id = upcomingCourse.id;
              course.fullname = upcomingCourse.fullname;

              course.summary = upcomingCourse.summary;
              

              course.picurl = upcomingCourse.overviewfiles[0]?.fileurl;
              if (course.picurl) course.picurl = course.picurl.replaceAll('/webservice', '')

              let favCourse = this.favouriteCourseList.find(o => o.id == course.id);
              if (favCourse) {
                course.favourite = true;
              }
              else {
                course.favourite = false;
              }

              if (this.translateService.getDefaultLang() === 'bn') {
                const crse = visibleCourses.find(c => c.id === course.id);

                const bengaliName = crse.customfields.find(field => field.shortname === 'cname').value;
                const bengaliDescription = crse.customfields.find(field => field.shortname === 'cdescription').value;

                course.fullname = bengaliName?.length > 0 ? bengaliName : '*';

                course.summary = bengaliDescription?.length > 0 ? bengaliDescription : '*';

                
              }

              this.upcomingCourseList.push(course);
            }


          }
        }
        )

      }
      )

    })
  }
    
     
    
      getRecommendedCourses(){
        this.upcomingCourseApi.getRecommendedCourses().subscribe( (res:any)=>{
           this.cntDataSet++;  
         
          if( res.recommended_courses ){
             
                 for(let recommededCourse of res.recommended_courses){
                          let course = {} as Course;
                          course.id = recommededCourse.id;
                          course.fullname = recommededCourse.fullname;
    
                          course.summary = recommededCourse.summary;
                          
                          
                          course.picurl = recommededCourse.overviewfiles[0]?.fileurl;
                          course.picurl = course.picurl.replaceAll('/webservice', '')
                          
                          let favCourse = this.favouriteCourseList.find(o => o.id == course.id );
                          if( favCourse ){
                              course.favourite = true;
                          }
                          else{
                              course.favourite = false;
                          }
                        
    
                          this.recommendedCourseList.push(course);
                 }
           }
          
      })
      }
     

}
    

