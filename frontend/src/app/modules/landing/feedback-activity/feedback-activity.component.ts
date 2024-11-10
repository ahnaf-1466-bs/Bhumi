import { Component } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { FeedbackQuestionService } from './services/get-feedback-question';
import { SaveFeedbackService } from './services/save-feedback-response';
import { Feedback } from './models/feedback';
import { CourseComment } from './models/course-comment';
import { ActivatedRoute, Router } from '@angular/router';
import { AutoDoneService } from './services/auto-done.service';
import { CompleteCourseService } from './services/complete-course.service';
import { ActivityApiService } from '../pdf-activity/services/activity-api.service';
import { GetActivityStatusService } from '../video-activity/services/get-activity-status.service';
import { GetCertificateService } from '../video-activity/services/get-certificate.service';
import { AuthService } from 'app/core/auth/auth.service';
import { ActivityCompletionService } from 'app/services/activity-completion.service';
import { TranslateService } from '@ngx-translate/core';
@Component({
    selector: 'app-feedback-activity',
    templateUrl: './feedback-activity.component.html',
    styleUrls: ['./feedback-activity.component.scss'],
})
export class FeedbackActivityComponent {
    public forms: any[] = [];
    feedbacks: Feedback[] = [];
    activityID:any;
    courseID:any;
    userID:any;
    enrolled:boolean = false;
    title:string;
    cert_url: string = '';
    commentFillded:boolean = false;
    certificateAvailable: boolean = false;
    activityList:any[];
    prevActivityID:any;
    prevInstanceID:any;
    prevActivityType:string;
    prevActivityFormat:string;
    instanceID:string; 
    isCommentRequired:boolean = false;
    completeDone:boolean = false;
    loading:boolean=true;
    ratingRequired:number = 0;
    ratingSubmitted:number = 0;
    submitSectionLoader:boolean = false;
    courseComment:CourseComment = {} as CourseComment;
    topicId: any;
    prevTopicId: any;

    constructor(
        private _question: FeedbackQuestionService,
        private _saveFedback: SaveFeedbackService,
        private fb: FormBuilder,
        private route:ActivatedRoute,
        private _router:Router,
        private _authService: AuthService,
        private getCertificateApi: GetCertificateService,
        private autoDoneApi: AutoDoneService,
        private getActivityStatus: GetActivityStatusService,
        private activityApi: ActivityApiService,
        private completeCourseService: CompleteCourseService,
        private activityCompletion: ActivityCompletionService,
        private translateService: TranslateService
    ) {
      
    }

    ngOnInit() {
        this.route.queryParams.subscribe( (params) => { 
            this.topicId = params.topic;
            this.activityID = params.activity;
            this.courseID = params.course;
            this.userID = localStorage.getItem('user-id');

            this.saveCurrentActivity(this.userID,this.courseID,this.activityID,this.topicId);


            this.getActivityStatus.getActivityStatus(this.courseID, this.userID).subscribe((res: any) => {
                        if (res.exception || res.errorcode) {
                            this.enrolled = false;  
                            this.loading = false; 
                            this._authService.signOut();
                            this._router.navigate(['login']);
                            return;
                        }
                        let enrolledCourses: any[] = res.statuses;
                        this.enrolled = false;
                        for (let course of enrolledCourses) {
                            if (course.cmid == this.activityID) {
                                this.enrolled = true;
                                if (course.state == 0) {
                                    this.completeDone = false;
                                } else {
                                    this.completeDone = true;
                                    this.commentFillded = true;
                                    this.testCertificate(); 
                                    this.activityCompletion.updateActivityStatus(Number(this.activityID));

                                }
                              
                            }
                        }
                        
            });

            this.activityApi.getActivities(this.courseID).subscribe((successRes: any) => {
                // topics should not start from General
                const allTopics = successRes.slice(1);
                const topic = allTopics?.find(
                    topic => topic.id == this.topicId
                )
                const currTopicIndex = allTopics.findIndex(
                    topic => topic.id == this.topicId
                )
                this.activityList = topic.modules;


                this.activityList.forEach((act, index) => {
                    if (act.id == this.activityID) {
                        if(this.translateService.getDefaultLang()==='bn')
                        {
                            this.activityApi
                            .getBengaliDetailsActivity(act.id)
                            .subscribe((res: any) => {
                                this.title = res.results[0]
                                    .category_details[0].field_value;
                            })
                        }
                        else{
                            this.title = act.name;
                        }
                        this.instanceID = act.instance;
    
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
                        else {
                            if (currTopicIndex - 1 >= 0 &&
                                allTopics[currTopicIndex - 1].modules?.length > 0) {
                                const prevTopicModules = allTopics[currTopicIndex - 1].modules;

                                this.prevTopicId = allTopics[currTopicIndex - 1].id;

                                this.prevActivityID =
                                    prevTopicModules[prevTopicModules?.length - 1]['id'];

                                this.prevInstanceID =
                                    prevTopicModules[prevTopicModules?.length - 1]['instance'];

                                this.prevActivityType =
                                    prevTopicModules[prevTopicModules?.length - 1]['modname'];

                                if (this.prevActivityType == 'resource') {
                                    this.prevActivityFormat =
                                        prevTopicModules[prevTopicModules?.length - 1]
                                        ['contentsinfo']['mimetypes'];
                                }
                            }
                        }
                       
                    }
                });

                this._question.getFeedbackQuestion(this.instanceID, this.activityID).subscribe((res: any) => {
                    this.feedbacks = res?.questions;
                    if(res.iscommentrequired == 0)this.isCommentRequired = false;
                    else this.isCommentRequired = true;

                    for(let indx = 0; indx < res.questions?.length; indx++){
                        if( indx != 1){
                            this.ratingRequired++;
                            if(res.questions[indx].response==null){
                                this.feedbacks[indx].rating = 0;
                            }
                            else{
                                this.feedbacks[indx].rating = res.questions[indx].response;
                            }
                            if(this.feedbacks[indx].rating > 0){
                              
                                this.ratingSubmitted++;
                            }
                 
                        }
                        if(indx == 1){
                           this.courseComment.answer = res.questions[indx].response;
                           if(this.courseComment.answer?.length > 0)this.commentFillded = true;
                        }
                    }
                    this.loading = false;
                    
                    this.courseComment.question = this.feedbacks?.find((item) => item.questionid==0 && item.inputtype=="text").question;
                    if(this.isSubmissionComplete() == true){
                        this.completeDone = true;
                    }
                    else this.completeDone = false; 
                });
            });

            
        })
    }

    setRating(id:number, newRating:number){ 
        if(this.feedbacks[id].rating == 0)this.ratingSubmitted++; //yet never pressed, so incerement
        this.feedbacks[id].rating = newRating;
    }

    onSubmit(){
        
        if(this.isSubmissionComplete() == true){
            this.completeDone = true;
            this.activityCompletion.updateActivityStatus(Number(this.activityID));
            this.submitSectionLoader = true;
            this._saveFedback.submitFeedback(this.feedbacks, this.courseComment, this.instanceID, this.activityID, this.courseID).subscribe((res: any) => {

                this.autoDoneApi.autoDone(this.instanceID).subscribe( (res:any)=>{
                    this.testCertificate();
                    if(this.certificateAvailable == true)this.submitSectionLoader=false;
                    this.completeCourseService.submit();
                })
               
              
                this._question.getFeedbackQuestion(this.instanceID, this.activityID).subscribe((res: any) => {
                    if (res.exception || res.errorcode) {
                        this.enrolled = false;  
                        this.loading = false; 
                        this._authService.signOut();
                        this._router.navigate(['login']);
                        return;
                    }
                    
                    this.feedbacks = res.questions;
                    for(let indx = 0; indx < res.questions?.length; indx++){
                           if( indx != 1){
                               if(res.questions[indx].response==null){
                                   this.feedbacks[indx].rating = 0;
                               }
                               else{
                                   this.feedbacks[indx].rating = res.questions[indx].response;
                               }
                               
                           }
                           if(indx == 1){
                              this.courseComment.answer = res.questions[indx].response;
                              if(this.courseComment.answer?.length > 0)this.commentFillded = true;
                           }
                    }
                    this.loading = false;
                    this.courseComment.question = this.feedbacks?.find((item) => item.questionid==0 && item.inputtype=="text").question;       
                });
              
            }); 
        }
        else this.commentFillded = false;
        
    }

    goToCourse(){
        this._router.navigate(['course', this.courseID]);
    }

    getCertificate() {
        window.open(this.cert_url, '_blank');
    }

    goPrevActivity() {
        // if prevActivityID is not in current Topic's modules then topic: prevTopicId
        if(this.activityList.findIndex(module=>module.id == this.prevActivityID) === -1)
        {
            this.topicId = this.prevTopicId;
        }

        if (this.prevActivityFormat == 'video/mp4' ||
        this.prevActivityFormat == 'video/webm' ||
        this.prevActivityFormat == 'video/mov') {
            this._router.navigate([`course-activity/${this.courseID}/video`], {
                queryParams: {
                    course: this.courseID,
                    activity: this.prevActivityID,
                    topic:this.topicId
                },
            });
        }

        else if (this.prevActivityFormat == 'application/pdf') {
            this._router.navigate([`course-activity/${this.courseID}/pdf`], {
                queryParams: {
                    course: this.courseID,
                    activity: this.prevActivityID,
                    topic:this.topicId
                },
            });
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

    testCertificate() {
        this.getCertificateApi
            .getCertificate(this.courseID, this.userID)
            .subscribe((response: any) => {
                
                if (response.url) {
                    this.certificateAvailable = true;
                    this.submitSectionLoader = false;
                    this.cert_url = response.url;
                } else {
                    this.certificateAvailable = false;
                }
            });
    }

    isSubmissionComplete(){
        if(this.isCommentRequired){
             if(this.ratingRequired == this.ratingSubmitted   && this.courseComment.answer?.length > 0)
                  return true;
             else return false;   
        }
        else{
            if(this.ratingRequired == this.ratingSubmitted )return true;
            else return false;
        }
    }

    saveCurrentActivity(userId,courseId,activityId,topicId) {
        // save current course activity in local storage
        const currCourseActivity = {
            'type': 'feedback',
            'activityId': activityId,
            'topicId': topicId
        };

        localStorage.setItem(JSON.stringify({
            'userId': userId,
            'courseId': courseId,
        }), JSON.stringify(currCourseActivity));
    }
  
}
