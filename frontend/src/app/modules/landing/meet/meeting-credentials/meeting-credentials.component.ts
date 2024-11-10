import { Location } from '@angular/common';
import { Component, OnInit } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import ZoomMtgEmbedded from '@zoomus/websdk/embedded';
import { AuthService } from 'app/core/auth/auth.service';
import { ActivityCompletionService } from 'app/services/activity-completion.service';
import { environment } from 'environments/environment';
import { KJUR } from 'jsrsasign';
import { ActivityApiService } from '../../pdf-activity/services/activity-api.service';
import { Activity } from '../../video-activity/models/activity';
import { GetActivityStatusService } from '../../video-activity/services/get-activity-status.service';
import { AutoDoneService } from '../services/auto-done.service';
import { MeetingInfoService } from '../services/meeting-info.service';
import { ZoomApiService } from '../services/zoom-api.service';
import { TranslateService } from '@ngx-translate/core';


@Component({
  selector: 'app-meeting-credentials',
  templateUrl: './meeting-credentials.component.html',
  styleUrls: ['./meeting-credentials.component.scss']
})



export class MeetingCredentialsComponent implements OnInit{

  zoomID:string = "";
  loading:boolean = false;
  visibilityStatusDone:boolean = false;
  failedJoin:boolean = false;
  name:string = "";
  intro:string = "";
  available:boolean = false;
  startTime:number = 0;
  joinBeforeHost: boolean = false;
  zoomComponentRendered:boolean = false;
  leave:boolean = false;
  sdkKey:string = environment.ZOOM_SDK_KEY;
  meetingID:string = "";
  role:number = 0;
  userPassword:string = "";
  registrantToken:string = "";
  userName:string = localStorage.getItem('user-fullname');
  userEmail:string = localStorage.getItem('user-mail');
  client = ZoomMtgEmbedded.createClient();
  meetingInfo:any = {
      meetingID: "",
      passcode: ""
 }
  Zoom: any;
  joined:boolean = false;
  userID:any;
  activityList: any[];
  pdfActivity: any;
  fileURL: string;
  instanceID:any;
  activityID:string;
  courseID:string;
  wsToken:string;
  enrolled:boolean=true;
  meetingEnded:boolean = false;
  statusBtnHidden:boolean = false;  
  prevActivityID:number=-1;
  nextActivityID:number=-1;
  prevInstanceID:any;
  prevActivityType:string = "";
  nextActivityType:string = "";
  prevActivityFormat:string = "";
  nextActivityFormat:string = "";
  activityDone:boolean = false;
  currentActivity:Activity;
  doneBtnDisabled:boolean=false;
  private timer: any; 
    topicId: any;
    prevTopicId: any;
    nextTopicId: any;


 constructor(
    private _router:Router,
    private zoomApi:ZoomApiService,
    private route: ActivatedRoute,
    private meetingInfoApi:MeetingInfoService,
    private autoDoneApi: AutoDoneService,
    private getActivityStatus: GetActivityStatusService,
    private _authService: AuthService,
    private activityApi: ActivityApiService,
    private activityCompletion:ActivityCompletionService,
    private translateService: TranslateService
 ){
  this.timer = setInterval(() => {
    const meetingSDKElement = document?.getElementById('meetingSDKElement');
    let height = meetingSDKElement?.offsetHeight;
    if(height == 0 && this.joined == true)this.meetingEnded = true;
   
  }, 1000); // check every 1 second
 }

  ngOnInit(): void {

    this.route.queryParams.subscribe(params => {
      this.topicId = params.topic;
      this.activityID = params.activity;
      this.courseID = params.course;
    
      this.userID = localStorage.getItem("user-id");
        
      this.saveCurrentActivity(this.userID,this.courseID,this.activityID,this.topicId);
      
      this.getActivityStatus.getActivityStatus(this.courseID, this.userID).subscribe(  (res:any)=>{
                  
                 if(res.exception || res.errorcode){
                        this.enrolled=false;
                        this.loading=false;
                        this.visibilityStatusDone = true;
                        this._authService.signOut();
                        this._router.navigate(['login']); 
                         return;
                 }
                 let enrolledCourses:any[] = res.statuses;
               
                 for(let course of enrolledCourses){
                  
                     if(course.cmid == this.activityID){
                         this.enrolled=true;
                         if(course.state == 0){
                             this.activityDone = false;
                         }
                         else{
                             this.activityDone = true;
                             this.activityCompletion.updateActivityStatus(Number(this.activityID));
                         }

                     }
                 }
                 
                 this.loading=false;
                 this.visibilityStatusDone = true;
              
      })

      this.activityApi.getActivities(this.courseID).subscribe((successRes: any) => {
            // topics should not start from General
            const allTopics = successRes.slice(1);
            const topic = allTopics.find(
                topic => topic.id == this.topicId
            )
            const currTopicIndex = allTopics.findIndex(
                topic => topic.id == this.topicId
            )
            this.activityList = topic.modules;

          this.activityList.forEach((act, index) => {
              if (act.id == this.activityID) {
                  this.loading = false;
                  if(act.contents){
                      this.fileURL = act.contents[0].fileurl;
                      this.fileURL = this.fileURL + '&token=' + this.wsToken;
                  }
                  
                  if (index > 0) {
                      if (this.activityList[index - 1]['id']) {
                          this.prevActivityID =
                              this.activityList[index - 1]['id'];
                          this.prevInstanceID =
                              this.activityList[index - 1][
                                  'instance'
                              ];
                          this.prevActivityType =
                              this.activityList[index - 1]['modname'];
                          if (this.prevActivityType == 'resource') {
                              this.prevActivityFormat =
                                  this.activityList[index - 1][
                                      'contentsinfo'
                                  ]['mimetypes'];
                          }
                      }
                  }
                  /* 
                    if there isn't an activity before, but there is an activity
                    on immediate previous topic
                    */
                else{
                    if(currTopicIndex-1 >= 0 && 
                        allTopics[currTopicIndex-1].modules.length > 0)
                    {
                        const prevTopicModules = allTopics[currTopicIndex-1].modules;
                        
                        this.prevTopicId = allTopics[currTopicIndex-1].id;
                        
                        this.prevActivityID = 
                            prevTopicModules[prevTopicModules.length-1]['id'];
                        
                        this.prevInstanceID = 
                            prevTopicModules[prevTopicModules.length-1]['instance'];
                        
                        this.prevActivityType = 
                            prevTopicModules[prevTopicModules.length-1]['modname'];
                        
                        if (this.prevActivityType == 'resource') {
                            this.prevActivityFormat = 
                                prevTopicModules[prevTopicModules.length-1]
                                ['contentsinfo']['mimetypes'];
                        }
                    }
                }
                // if there is an activity after current activity
                  if (index + 1 < this.activityList.length) {
                      if (this.activityList[index + 1]['id']) {
                          this.nextActivityID =
                              this.activityList[index + 1]['id'];
                          this.instanceID =
                              this.activityList[index + 1][
                                  'instance'
                              ];
                          this.nextActivityType =
                              this.activityList[index + 1]['modname'];
                          if (this.nextActivityType == 'resource') {
                              this.nextActivityFormat =
                                  this.activityList[index + 1][
                                      'contentsinfo'
                                  ]['mimetypes'];
                          }
                      }
                  }
                  /* 
                    if there isn't an activity after, but there is an activity
                    on immediate next topic
                  */
                  else {
                      if (currTopicIndex + 1 < allTopics.length &&
                          allTopics[currTopicIndex + 1].modules.length > 0) {
                          const nextTopicModules = allTopics[currTopicIndex + 1].modules;

                          this.nextTopicId = allTopics[currTopicIndex + 1].id;

                          this.nextActivityID = nextTopicModules[0]['id'];

                          this.instanceID = nextTopicModules[0]['instance'];

                          this.nextActivityType = nextTopicModules[0]['modname'];

                          if (this.nextActivityType == 'resource') {
                              this.nextActivityFormat =
                                  nextTopicModules[0]['contentsinfo']['mimetypes'];
                          }
                      }
                  }
              }
          });
      
      });

      this.wsToken = localStorage.getItem("auth-token");

      this.activityApi.getActivities(this.courseID).subscribe( (successRes:any)=>{
           
          // topics should not start from General
          const topic = successRes.slice(1).find(
              topic => topic.id == this.topicId
          )
          this.activityList = topic.modules;
 
            this.activityList.forEach((act, index) => {
              
                if( act.id == this.activityID){
                    this.loading = false;
                   
                   
                    if(index > 0){
                       if(this.activityList[index - 1]['id']) {
                           this.prevActivityID = this.activityList[index - 1]['id'];
                           this.prevActivityType = this.activityList[index-1]['modname'];
                           if(this.prevActivityType == "resource"){
                                this.prevActivityFormat = this.activityList[index - 1]['contentsinfo']['mimetypes'];
                           }
                          
                       }
                    }
                    if( (index+1) < this.activityList.length){
                        if(this.activityList[index + 1]['id']) {
                           this.nextActivityID = this.activityList[index + 1]['id'];
                           this.nextActivityType = this.activityList[index+1]['modname'];
                           if(this.nextActivityType == "resource"){
                               this.nextActivityFormat = this.activityList[index + 1]['contentsinfo']['mimetypes'];
                          }
                        }
                    }
                    
                
                }
            });
  
          
      })

      this.zoomID = this.activityID;

      this.meetingInfoApi.meetingInfo(this.zoomID).subscribe(  (res:any)=>{
        
          if (this.translateService.getDefaultLang() === 'bn') {
              this.activityApi
                  .getBengaliDetailsActivity(this.activityID)
                  .subscribe((res: any) => {
                      this.name = res.results[0]
                          .category_details[0].field_value;
                      this.intro = res.results[0]
                          .category_details[1].field_value;
                  })
          }
          else {
              if (res.name) {
                  this.name = res.name;
              }
          }
          if(res.intro){
            if(this.translateService.getDefaultLang()!=="bn")
            {
                this.intro = res.intro;
            }
          }
          if(res.available){
            this.available = res.available
          }
          if(res.joinbeforehost){
            this.joinBeforeHost = res.joinbeforehost
          }
          if(res.start_time){
            this.startTime = res.start_time*1000;
          }
  
         
      })
      
    })
  

   

  }

  onMeetingStatusChange(status: any): void {
    if (status == this.Zoom.MEETING_STATUS_ENDED) {
      this.zoomComponentRendered = false;
    }
  }

 

  goNextActivity(){
        // if nextActivityId is not in current Topic's modules then topic: nextTopicId
        if(this.activityList.findIndex(module=>module.id == this.nextActivityID) === -1)
        {
            this.topicId = this.nextTopicId;
        }
         
        if (this.nextActivityFormat == 'video/mp4' ||
        this.nextActivityFormat == 'video/webm' ||
        this.nextActivityFormat == 'video/mov') {
            this._router.navigate([`course-activity/${this.courseID}/video`], {
                queryParams: {
                    course: this.courseID,
                    activity: this.nextActivityID,
                    topic:this.topicId
                },
            });
        }

        else if (this.nextActivityFormat == 'application/pdf') {
            this._router.navigate([`course-activity/${this.courseID}/pdf`], {
                queryParams: {
                    course: this.courseID,
                    activity: this.nextActivityID,
                    topic:this.topicId
                },
            });
        }

        else if (this.nextActivityType == 'quiz') {
            this._router.navigate([`course-activity/${this.courseID}/quiz`], {
                queryParams: {
                    id: this.instanceID,
                    course: this.courseID,
                    activity: this.nextActivityID,
                    topic:this.topicId
                },
            });
        }

        else if (this.nextActivityType == 'coursefeedback') {
            this._router.navigate([`course-activity/${this.courseID}/feedback`], {
                queryParams: {    
                    course: this.courseID,
                    activity: this.nextActivityID,
                    topic:this.topicId
                },
            });
        }

        else if (this.nextActivityType == 'zoom') {
            this._router.navigate([`course-activity/${this.courseID}/meeting`], {
                queryParams: {
                    course: this.courseID,
                    activity: this.nextActivityID,
                    topic:this.topicId
                },
            });
        }

        else if (this.nextActivityType === 'videoplus') {
            this._router.navigate([`course-activity/${this.courseID}/video-pdf`], {
                queryParams: {
                    course: this.courseID,
                    activity: this.nextActivityID,
                    topic: this.topicId
                },
            });
        }
  }

  goPrevActivity(){
    // if prevActivityID is not in current Topic's modules then topic: prevTopicId
    if(this.activityList.findIndex(module=>module.id == this.prevActivityID) === -1)
    {
        this.topicId = this.prevTopicId;
    }

    if(this.prevActivityFormat == 'video/mp4' ||
    this.prevActivityFormat == 'video/webm' ||
    this.prevActivityFormat == 'video/mov'){
        this._router.navigate(
            [`course-activity/${this.courseID}/video`],
            { queryParams: { course: this.courseID, activity: this.prevActivityID,topic:this.topicId } }
          );
    }

    else if(this.prevActivityFormat == "application/pdf"){
        this._router.navigate(
            [`course-activity/${this.courseID}/pdf`],
            { queryParams: { course: this.courseID, activity: this.prevActivityID,topic:this.topicId } }
          );
    }

    else if (this.prevActivityType == 'quiz') {
      this._router.navigate([`course-activity/${this.courseID}/quiz`], {
          queryParams: {
              id: this.prevInstanceID,
              course: this.courseID,
              activity: this.prevActivityID,
              topic:this.topicId
          },
      });
    }

    else if (this.prevActivityType == 'zoom') {
        this._router.navigate([`course-activity/${this.courseID}/meeting`], {
            queryParams: {
                course: this.courseID,
                activity: this.prevActivityID,
                topic:this.topicId
            },
        });
    }

    else if (this.prevActivityType === 'videoplus') {
        this._router.navigate([`course-activity/${this.courseID}/video-pdf`], {
            queryParams: {
                course: this.courseID,
                activity: this.prevActivityID,
                topic: this.topicId
            },
        });
    }

  }
 
  tryAgain(){
     window.location.reload();
  }

  joinMeeting(){
     
      this.loading = true;
      this.statusBtnHidden = true;
   
      this.failedJoin = false;
      this.zoomApi.joinMeet(this.zoomID).subscribe( (res:any)=>{
       
          this.loading = false;
          setTimeout(() => {
               this.statusBtnHidden = false;
          }, 700);
          
          if( res.joinurl )
          {
              let idMatch =res.joinurl.match(/\/j\/(\d+)/);
              this.meetingID = idMatch ? idMatch[1] : null;
              let pwdMatch = res.joinurl.match(/pwd=([^&]+)/);
              this.userPassword = pwdMatch ? pwdMatch[1] : null;
              this.zoomComponentRendered = true;
              this.initMeeting(); 
              //this._router.navigate(['meeting'], { queryParams:  { meetingid: zoomID, passcode: pwd } });
          }
          else{
            // this.failedJoin = true;
          }
      })
  }

  startMeeting() {

    let signature = this.getSignature();
    
    try{
          this.client.join({
            sdkKey: this.sdkKey,
            signature: signature,
            meetingNumber: this.meetingID,
            password: this.userPassword,
            userName: this.userName,
            userEmail: this.userEmail,
            tk: this.registrantToken
          }).then(() => {
               this.joined = true;
               this.toggleActivityStatus();
          })
          .catch((error) => {
               
                  if( !this.joined )this.failedJoin = true;
              
          });
    }catch (error) {
  
    }
  }

  initMeeting(){
    
     let meetingSDKElement = document.getElementById('meetingSDKElement');
     this.client.init({
         debug: true,
         zoomAppRoot: meetingSDKElement,
         customize: {
           video: {
             isResizable: true,
             viewSizes: {
               default: {
                 width: 1000,
                 height: 600,
               
               },
               ribbon: {
                 width: 300,
                 height: 700
               }
             }
           },
           meetingInfo: ['topic', 'host', 'mn', 'pwd', 'telPwd', 'invite', 'participant', 'dc', 'enctype'],
               toolbar: {
               
               }
           }
           }).then(() => {
                 
                   this.startMeeting();
             })
             .catch((error:any) => {

              });
  }

  getSignature() {   

    const oHeader = { alg: 'HS256', typ: 'JWT' }
    const iat = Math.round(new Date().getTime() / 1000) - 30;
    const exp = iat + 60 * 60 * 2;

    const oPayload = {
        sdkKey: environment.ZOOM_SDK_KEY,   
        mn: this.meetingID,  
        role: 0,
        iat: iat,
        exp: exp,
        appKey: environment.ZOOM_SDK_KEY,
        tokenExp: iat + 60 * 60 * 2
    };

    const sHeader = JSON.stringify(oHeader);
    const sPayload = JSON.stringify(oPayload);
    let signature =  KJUR.jws.JWS.sign('HS256', sHeader, sPayload, environment.ZOOM_SDK_SECRET);
   
    return signature;
  }




  goToCourse(){
    this._router.navigate(['course', this.courseID]);
  }


  ngOnDestroy(): void {
    clearInterval(this.timer); // clear the interval on component destroy
  }




toggleActivityStatus() {
   
        this.autoDoneApi.autoDone(this.activityID).subscribe( (res:any)=>{
          
            this.activityDone = true;
            this.activityCompletion.updateActivityStatus(Number(this.activityID));
        })
    
}


saveCurrentActivity(userId,courseId,activityId,topicId) {
    // save current course activity in local storage
    const currCourseActivity = {
        'type': 'meeting',
        'activityId': activityId,
        'topicId': topicId
    };

    localStorage.setItem(JSON.stringify({
        'userId': userId,
        'courseId': courseId,
    }), JSON.stringify(currCourseActivity));
}

  

}