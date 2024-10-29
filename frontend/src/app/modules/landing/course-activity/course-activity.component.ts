import { Component, HostListener,OnInit } from '@angular/core';
import { ActivatedRoute, ParamMap, Router } from '@angular/router';
import { ActivityApiService } from '../pdf-activity/services/activity-api.service';
import { QuizService } from '../quiz-activity/services/quiz.service';
import { GetActivityStatusService } from '../video-activity/services/get-activity-status.service';
import { ActivityCompletionService } from 'app/services/activity-completion.service';
import { MatDialog } from '@angular/material/dialog';
import { IncompleteModal } from './incomplete-modal/incomplete-modal.component';
import { TranslateService } from '@ngx-translate/core';
import { AuthService } from 'app/core/auth/auth.service';
import { GetCertificateService } from '../video-activity/services/get-certificate.service';

@Component({
  selector: 'app-course-activity',
  templateUrl: './course-activity.component.html',
  styleUrls: ['./course-activity.component.scss']
})
export class CourseActivityComponent implements OnInit {
  courseId: any;
  modules: any;
  activityId: any;
  moduleName: any;
  contents: any;
  moduleType: any;
  quizId: any;
  quiz: any;
  currentActivityId: any;
  currentTopicId: any;
  userId: string;
  showAccordion:boolean=true;
  isMobileWidth: boolean=false;
  allTopics:any;
  activityStatus:Map<number,boolean>= new Map<number,boolean>();
  topicStatus:Map<number,boolean> = new Map<number,boolean>();

  constructor(
    private activatedRoute: ActivatedRoute,
    private activityApiService: ActivityApiService,
    private router: Router,
    private quizApiService: QuizService,
    private getActivityStatus: GetActivityStatusService,
    private activityCompletion:ActivityCompletionService,
    private dialog: MatDialog,
    private translateService: TranslateService,
    private authService: AuthService,
    private getCertificateApi: GetCertificateService,
  ) {
    this.activatedRoute.queryParams.subscribe(params => {
      // gets the current activity id for highlighting it on the menu of activities
      this.currentActivityId = params.activity;
      this.currentTopicId    = params.topic;
    });
    // observing for any activity completion
    this.activityCompletion.activityStatus$.subscribe((activityId)=>{
      this.activityStatus.set(activityId,true);
      this.updateAllTopicsStatus();
    })
  }
  openIncompleteActivityModal()
  {
    this.dialog.open(IncompleteModal,
      { data: { message: "You cannot access this activity without completing the previous one." } }
    );
  }
  openIncompleteCourseModal(){
    this.dialog.open(IncompleteModal,
      { data: { message: "Complete all activities for your certificate!" } }
    );
  }
  ngOnInit(): void {
    this.activatedRoute.paramMap.subscribe((params: ParamMap) => {
      this.isMobileWidth = window.innerWidth<960; 

      this.courseId = params.get('id');

      this.quizApiService.getQuiz(localStorage.getItem('auth-token'), this.courseId)
        .subscribe((res: any) => {

          this.quiz = res;
          this.quizId =
            res && res.quizzes && res.quizzes.length > 0
              ? res.quizzes[0].id
              : undefined;

          // fetches all activities of course
          this.activityApiService.getActivities(this.courseId)
            .subscribe((response) => {
              this.allTopics = response.slice(1); 
              console.log("this.allTopics",this.allTopics);
                         

              // if language is bengali then load up bengali names of all activities & topics as well
              if (this.translateService.getDefaultLang() === 'bn') {
                this.allTopics.forEach(topic => {
                  // set topic name to bengali
                  topic.name = topic.summary;

                  topic.modules.forEach(module => {
                    // fetch bengali name
                    this.activityApiService
                      .getBengaliDetailsActivity(module.id)
                      .subscribe((res: any) => {
                        const bengaliName = res.results[0].category_details[0].field_value
                        module.name = bengaliName;
                      })
                  })
                });
              }
              
              

              this.userId = localStorage.getItem('user-id');
              // fetches and stores all statuses of all activities
              this.getActivityStatus.getActivityStatus(this.courseId, this.userId).subscribe((res: any) => {
                const activities: any[] = res.statuses;
                console.log("activitieStatuses",activities);
                
                for(let activity of activities)
                {
                  if(activity.modname==='quiz')
                  {
                    this.activityStatus.set(activity.cmid,(activity.state==2));
                  }
                  else{
                    this.activityStatus.set(activity.cmid,(activity.state!=0));
                  }
                }
                /*  
                get all topic ids in an array (allTopicIds)

                generate a hashmap of number->boolean
                    that stores the completion status of each topic
                    
                    topicId->status 
                    150 -> false
                    145 -> true

                use this.allTopics and allTopicIds to figure out topicStatus
                */
              this.updateAllTopicsStatus();
              })

              // checks if topic 1 exists 
              if (this.allTopics[0].modules) {
                this.modules = this.allTopics[0].modules;
                const topicId = this.allTopics[0].id;

                // redirect to last visited activity if found from local storage
                const lastActivityInfo = this.getLastActivity(this.userId,this.courseId)
                if(lastActivityInfo)
                {                  
                  if(lastActivityInfo.type==='quiz')
                  {
                  console.log("navigating to quiz");
                    this.router.navigate(['quiz'], {
                      relativeTo: this.activatedRoute,
                      queryParams: {
                        id: lastActivityInfo.quizId,
                        course: this.courseId,
                        activity: lastActivityInfo.activityId,
                        topic: lastActivityInfo.topicId
                      },
                    });
                  }
                  else{
                  console.log("navigating to others");
                    this.router.navigate([lastActivityInfo.type], {
                      relativeTo: this.activatedRoute,
                      queryParams: {
                        course: this.courseId,
                        activity: lastActivityInfo.activityId,
                        topic: lastActivityInfo.topicId
                      },
                    });
                  }
                }
                // navigate to first activity of the first topic if there are no child routes 
                else if (this.activatedRoute.firstChild===null && (this.modules && this.modules.length > 0)) {
                  this.activityId = this.modules[0].id;
                  this.moduleName = this.modules[0].modname;
                  this.contents   = this.modules[0]?.contents?.[0];
                  this.moduleType = this.contents?.mimetype;

                  if (this.moduleName === 'resource') {
                    let route = '';
                    if (this.moduleType.includes('pdf')) {
                      route = 'pdf';
                    } else if (this.moduleType.includes('video')) {
                      route = 'video';
                    }

                    this.router.navigate([`${route}`], {
                      relativeTo: this.activatedRoute,
                      queryParams: {
                        course: this.courseId,
                        activity: this.activityId,
                        topic:topicId
                      },
                    });
                  } else if (this.moduleName === 'quiz') {
                    this.router.navigate(['quiz'], {
                      relativeTo: this.activatedRoute,
                      queryParams: {
                        id: this.quizId,
                        course: this.courseId,
                        activity: this.activityId,
                        topic:topicId
                      },
                    });
                  } else if (this.moduleName === 'zoom') {
                    this.router.navigate(['meeting'], {
                      relativeTo: this.activatedRoute,
                      queryParams: {
                        course: this.courseId,
                        activity: this.activityId,
                        topic:topicId
                      },
                    });
                  }
                  else if (this.moduleName === 'videoplus') {
                    this.router.navigate(['videoplus'], {
                      relativeTo: this.activatedRoute,
                      queryParams: {
                        course: this.courseId,
                        activity: this.activityId,
                        topic:topicId
                      },
                    });
                  }
                }
              }
            });
        });
    });
  }

  @HostListener('window:resize', ['$event'])
  onResize(event) {
    this.isMobileWidth = window.innerWidth<960;
  }

  private updateAllTopicsStatus() {
    const allTopicIds = this.allTopics?.map((topic) => {
      return topic.id;
    })
    const isStatusComplete = (module) => {
      return this.activityStatus.get(module.id);
    };
    if(allTopicIds)
    {
      for (let topicId of allTopicIds) {
        // topic is completed only if all NON-CERTIFICATE activities are completed 
        const allModulesOfTopic = this.allTopics.find((topic) => topic.id === topicId).modules.filter(mod=>mod.modname !== 'customcert'); 
        this.topicStatus.set(topicId,allModulesOfTopic.length>0 && allModulesOfTopic.every(isStatusComplete));
      }
    }
  }

  /* 
     this function is used to check if user can access the module,
     and redirects to the module if so
  */
  clickActivityFromMenu(module: any, moduleIndex: number, topicIndex:number) {
    // how do we know if the module should be viewed or not?
    /* 
        if module is the very 1st one then it is always accssible
        else if prev module is completed, then we can access it
          -> what do we mean by completed?
             -> for pdf:  course.state != 0
             -> for video: course.state == 1
             -> for quiz: this.status == 2
             -> for meeting: course.state != 0
             -> for feedback: NOT NEEDED TO CHECK
    */
    const topicModules = this.allTopics[topicIndex].modules;
    const topicId = this.allTopics[topicIndex].id;
    

    /* if module clicked is not the 1st one of its topic */
    if ((moduleIndex > 0 && topicModules?.length > 0)) {
      console.log("if module clicked is not the 1st one of its topic");
      
      const prevModule = topicModules[moduleIndex - 1];
      const prevModuleName = prevModule.modname;
      const prevModuleType = prevModule.contents?.[0].mimetype;
      const prevActivityId = prevModule.id;
      const prevQuizId = prevModule.instance;

      // for pdf or video
      if (prevModuleName === 'resource' &&
        (prevModuleType.includes('pdf') || prevModuleType.includes('video'))
      ) {
        // check if course.state == 1         
        this.userId = localStorage.getItem('user-id');
        this.getActivityStatus.getActivityStatus(this.courseId, this.userId).subscribe((res: any) => {
          if (res.exception || res.errorcode) {
            this.authService.signOut();
            this.router.navigate(['login']);
            return;
          }
          
          let enrolledCourses: any[] = res.statuses;
          for (let course of enrolledCourses) {
            if (course.cmid == prevActivityId) {
              if (course.state == 0) {
                console.log("user CANNOT view the module if prev is pdf or video,course.state:",course.state);
                this.openIncompleteActivityModal();
                return;
              } else {
                // user can continue viewing the module
                console.log("user can continue viewing the module if prev is pdf or video,course.state:", course.state);
                this.navigateToModule(module,topicId);
              }
            }
          }
        });
      }
      else if (prevModuleName === 'quiz') {
        // check if this.status == 2
        this.userId = localStorage.getItem('user-id');
        this.getActivityStatus
          .getActivityStatus(this.courseId, this.userId)
          .subscribe((res: any) => {
            if (res.exception || res.errorcode) {
              this.authService.signOut();
              this.router.navigate(['login']);
              return;
            }
            let details, status;
            for (let quiz of res.statuses) {
              if (quiz.cmid == prevActivityId) {
                details = quiz.details;
              }
            }
            if (details.length >= 2) {

              status =
                details[
                  details.length - 1
                ].rulevalue.status;

              if (status == 2) {
                console.log("user can continue viewing the module if prev is quiz,status:", status);
                this.navigateToModule(module,topicId);
              }
              else {
                console.log("user CANNOT view the module if prev is quiz,status:", status);
                this.openIncompleteActivityModal();

                return;
              }
            }

            if (res.exception) {
              return;
            }
          });
      }
      else if (prevModuleName === 'zoom') {
        // check if course.state != 0 
        this.userId = localStorage.getItem('user-id');
        this.getActivityStatus.getActivityStatus(this.courseId, this.userId).subscribe((res: any) => {
          if (res.exception || res.errorcode) {
            this.authService.signOut();
            this.router.navigate(['login']);
            return;
          }
          let enrolledCourses: any[] = res.statuses;
          for (let course of enrolledCourses) {
            if (course.cmid == prevActivityId) {
              if (course.state == 0) {
                console.log("user CANNOT view the module if prev is meeting,course.state:", course.state);
                this.openIncompleteActivityModal();

                return;
              } else {
                // user can continue viewing the module
                console.log("user can continue viewing the module if prev is meeting,course.state:", course.state);
                this.navigateToModule(module,topicId);
              }
            }
          }
        });
      }
      // if prev module is feedback then clicked module MUST be certificate
      else if (prevModuleName === 'coursefeedback'){
        this.getActivityStatus.getActivityStatus(this.courseId, this.userId).subscribe((res: any) => {
          if (res.exception || res.errorcode) {
            this.authService.signOut();
            this.router.navigate(['login']);
            return;
          }
          let enrolledCourses: any[] = res.statuses;
          for (let course of enrolledCourses) {
            if (course.cmid == prevActivityId) {
              if (course.state == 0) {
                console.log("user CANNOT view the module if prev is coursefeedback,course.state:", course.state);
                this.openIncompleteCourseModal();
                return;
              } else {
                console.log("user can continue viewing the module if prev is coursefeedback,course.state:", course.state);
                this.navigateToModule(module,topicId);
              }
            }
          }
        });
      }
      else if (prevModuleName === 'videoplus')
      {
        this.getActivityStatus.getActivityStatus(this.courseId, this.userId).subscribe((res: any) => {
          if (res.exception || res.errorcode) {
            this.authService.signOut();
            this.router.navigate(['login']);
            return;
          }
          let enrolledCourses: any[] = res.statuses;
          for (let course of enrolledCourses) {
            if (course.cmid == prevActivityId) {
              if (course.state == 0) {
                console.log("user CANNOT view the module if prev is coursefeedback,course.state:", course.state);
                this.openIncompleteActivityModal();
                return;
              } else {
                console.log("user can continue viewing the module if prev is coursefeedback,course.state:", course.state);
                this.navigateToModule(module,topicId);
              }
            }
          }
        });
      }
    }
    /* if module clicked has a previous module on its previous topic */
    else if((topicIndex-1 >= 0 && this.allTopics[topicIndex-1]?.modules.length > 0))
    {
      console.log("if module clicked has a previous module on its previous topic");

      const prevTopicModules = this.allTopics[topicIndex-1].modules;
      const prevTopicId = this.allTopics[topicIndex-1].id;
      
      const prevModule = prevTopicModules[prevTopicModules.length-1];
      const prevModuleName = prevModule.modname;
      const prevModuleType = prevModule.contents?.[0].mimetype;
      const prevActivityId = prevModule.id;
      const prevQuizId = prevModule.instance;

      // for pdf or video
      if (prevModuleName === 'resource' &&
        (prevModuleType.includes('pdf') || prevModuleType.includes('video'))
      ) {
        // check if course.state == 1         
        this.userId = localStorage.getItem('user-id');
        this.getActivityStatus.getActivityStatus(this.courseId, this.userId).subscribe((res: any) => {
          if (res.exception || res.errorcode) {
            this.authService.signOut();

            this.router.navigate(['login']);
            return;
          }
          
          let enrolledCourses: any[] = res.statuses;
          for (let course of enrolledCourses) {
            if (course.cmid == prevActivityId) {
              if (course.state == 0) {
                console.log("user CANNOT view the module if prev is pdf or video,course.state:",course.state);
                this.openIncompleteActivityModal();

                return;
              } else {
                // user can continue viewing the module
                console.log("user can continue viewing the module if prev is pdf or video,course.state:", course.state);
                this.navigateToModule(module,topicId);
              }
            }
          }
        });
      }
      else if (prevModuleName === 'quiz') {
        // check if this.status == 2
        this.userId = localStorage.getItem('user-id');
        this.getActivityStatus
          .getActivityStatus(this.courseId, this.userId)
          .subscribe((res: any) => {
            if (res.exception || res.errorcode) {
              this.authService.signOut();

              this.router.navigate(['login']);
              return;
            }
            let details, status;
            for (let quiz of res.statuses) {
              if (quiz.cmid == prevActivityId) {
                details = quiz.details;
              }
            }
            if (details.length >= 2) {

              status =
                details[
                  details.length - 1
                ].rulevalue.status;

              if (status == 2) {
                console.log("user can continue viewing the module if prev is quiz,status:", status);
                this.navigateToModule(module,topicId);
              }
              else {
                console.log("user CANNOT view the module if prev is quiz,status:", status);
                this.openIncompleteActivityModal();

                return;
              }
            }

            if (res.exception) {
              return;
            }
          });
      }
      else if (prevModuleName === 'zoom') {
        // check if course.state != 0 
        this.userId = localStorage.getItem('user-id');
        this.getActivityStatus.getActivityStatus(this.courseId, this.userId).subscribe((res: any) => {
          if (res.exception || res.errorcode) {
            this.authService.signOut();

            this.router.navigate(['login']);
            return;
          }
          let enrolledCourses: any[] = res.statuses;
          for (let course of enrolledCourses) {
            if (course.cmid == prevActivityId) {
              if (course.state == 0) {
                console.log("user CANNOT view the module if prev is meeting,course.state:", course.state);
                this.openIncompleteActivityModal();

                return;
              } else {
                // user can continue viewing the module
                console.log("user can continue viewing the module if prev is meeting,course.state:", course.state);
                this.navigateToModule(module,topicId);
              }
            }
          }
        });
      }
      // if prev module is feedback then clicked module MUST be certificate
      else if (prevModuleName === 'coursefeedback'){
        this.getActivityStatus.getActivityStatus(this.courseId, this.userId).subscribe((res: any) => {
          if (res.exception || res.errorcode) {
            this.authService.signOut();
            this.router.navigate(['login']);
            return;
          }
          let enrolledCourses: any[] = res.statuses;
          for (let course of enrolledCourses) {
            if (course.cmid == prevActivityId) {
              if (course.state == 0) {
                console.log("user CANNOT view the module if prev is coursefeedback,course.state:", course.state);
                this.openIncompleteCourseModal();
                return;
              } else {
                console.log("user can continue viewing the module if prev is coursefeedback,course.state:", course.state);
                this.navigateToModule(module,topicId);
              }
            }
          }
        });
      }
      else if (prevModuleName === 'videoplus')
      {
        this.getActivityStatus.getActivityStatus(this.courseId, this.userId).subscribe((res: any) => {
          if (res.exception || res.errorcode) {
            this.authService.signOut();
            this.router.navigate(['login']);
            return;
          }
          let enrolledCourses: any[] = res.statuses;
          for (let course of enrolledCourses) {
            if (course.cmid == prevActivityId) {
              if (course.state == 0) {
                console.log("user CANNOT view the module if prev is coursefeedback,course.state:", course.state);
                this.openIncompleteActivityModal();
                return;
              } else {
                console.log("user can continue viewing the module if prev is coursefeedback,course.state:", course.state);
                this.navigateToModule(module,topicId);
              }
            }
          }
        });
      }
    }
    else {
      console.log("user can access very 1st module always");
      this.navigateToModule(module, topicId);
    }
  }

  private navigateToModule(module:any,topicId:any){
      const moduleName = module.modname;
      const moduleType = module.contents?.[0].mimetype;
      const activityId = module.id;
      const quizId = module.instance;

      if (moduleName === 'resource') {
        let route = '';
        if (moduleType.includes('pdf')) {
          route = 'pdf';
        } else if (moduleType.includes('video')) {
          route = 'video';
        }

        this.router.navigate([`course-activity/${this.courseId}/${route}`], {
          queryParams: {
            course: this.courseId,
            activity: activityId,
            topic: topicId
          },
        });
      } else if (moduleName === 'quiz') {
        this.router.navigate([`course-activity/${this.courseId}/quiz`], {
          queryParams: {
            id: quizId,
            course: this.courseId,
            activity: activityId,
            topic: topicId
          },
        });
      }
      else if (moduleName == 'coursefeedback') {
        this.router.navigate([`course-activity/${this.courseId}/feedback`], {
            queryParams: {
                course: this.courseId,
                activity: activityId,
                topic: topicId
            },
        });
    } 
      else if (moduleName === 'zoom') {
        this.router.navigate([`course-activity/${this.courseId}/meeting`], {
          queryParams: {
            course: this.courseId,
            activity: activityId,
            topic: topicId
          },
        });
      }
      else if(moduleName === 'customcert')
      {
        this.userId = localStorage.getItem('user-id');
        this.getCertificateApi
          .getCertificate(this.courseId, this.userId)
          .subscribe((response: any) => {

            if (response.url) {
              const cert_url = response.url;
              window.open(cert_url, '_blank');
            } else {
              console.log("certificate url not found");
              this.openIncompleteCourseModal();
            }
          });
      }
      else if(moduleName === 'videoplus')
      {
        this.router.navigate([`course-activity/${this.courseId}/video-pdf`], {
          queryParams: {
            course: this.courseId,
            activity: activityId,
            topic: topicId
          },
        });
      }
  }

  getLastActivity(userId: string, courseId: string) {
    const lastCourseActivity = JSON.parse((localStorage.getItem(
      JSON.stringify({
        'userId': userId,
        'courseId': courseId,
      })
    )));

    return lastCourseActivity;
  }

  getActivityTypeInMenu(module)
  {
    if (module.modname === 'resource') {
      if (module.contents?.[0].mimetype.includes('pdf')) return 'pdf';
      else return 'video';
    }
    else if (module.modname === 'videoplus') {
      return 'video';
    }
    else if(module.modname==='coursefeedback')
    {
      return 'feedback';
    }
    else if(module.modname==='customcert')
    {
      return 'certificate';
    }
    else return module.modname;
  }
}
