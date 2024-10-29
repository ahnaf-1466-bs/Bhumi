import { Location } from '@angular/common';
import { Component } from '@angular/core';
import { DomSanitizer } from '@angular/platform-browser';
import { ActivatedRoute, Router } from '@angular/router';
import { AuthService } from 'app/core/auth/auth.service';
import { ActivityCompletionService } from 'app/services/activity-completion.service';
import { Activity } from '../video-activity/models/activity';
import { GetActivityStatusService } from '../video-activity/services/get-activity-status.service';
import { GetCertificateService } from '../video-activity/services/get-certificate.service';
import { ActivityApiService } from './services/activity-api.service';
import { UpdateActivityStatusService } from './services/update-activity-status.service';
import { TranslateService } from '@ngx-translate/core';

@Component({
    selector: 'app-pdf-activity',
    templateUrl: './pdf-activity.component.html',
    styleUrls: ['./pdf-activity.component.scss'],
})
export class PdfActivityComponent {
    userID: any;
    activityList: any[];
    pdfActivity: any;
    fileURL: string;
    pdfName: string;
    activityID: string;
    courseID: string;
    wsToken: string;
    enrolled: boolean = true;
    pdf_src: any;
    pdfFound: boolean;
    certificateAvailable: boolean = false;
    loading: boolean = true;
    prevActivityID: number = -1;
    nextActivityID: number = -1;
    cert_url: string = '';
    prevActivityType: string = '';
    nextActivityType: string = '';
    prevActivityFormat: string = '';
    nextActivityFormat: string = '';
    activityDone: boolean = false;
    currentActivity: Activity;
    doneBtnDisabled: boolean = false;
    instanceID;
    prevInstanceID;
    topicId: any;
    nextTopicId:any;
    prevTopicId: any;

    constructor(
        private sanitize: DomSanitizer,
        private route: ActivatedRoute,
        private _router: Router,
        private getCertificateApi: GetCertificateService,
        private updateActivityStatus: UpdateActivityStatusService,
        private getActivityStatus: GetActivityStatusService,
        private _authService: AuthService,
        private activityApi: ActivityApiService,
        private activityCompletion: ActivityCompletionService,
        private translateService:TranslateService
    ) {}

    ngOnInit() {
               
                this.route.queryParams.subscribe((params) => {
                        this.topicId = params.topic
                        this.activityDone = false;
                        this.enrolled = true;
                        this.currentActivity = {} as Activity;
                        this.prevActivityID = -1;
                        this.nextActivityID = -1;
                        this.pdfFound = false;
                        this.loading = true;
 

                        this.activityID = params.activity;
                        this.courseID = params.course;
                        this.userID = localStorage.getItem('user-id');

                        this.saveCurrentActivity(this.userID,this.courseID,this.activityID,this.topicId);

                        this.getActivityStatus.getActivityStatus(this.courseID, this.userID).subscribe((res: any) => {
                                if (res.exception || res.errorcode) {
                                        this.enrolled = false;
                                        this.loading = false;
                                        this.pdfFound = false;
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
                                            this.activityDone = false;
                                        } else {
                                            this.activityDone = true;
                                            this.testCertificate();
                                            this.activityCompletion.updateActivityStatus(Number(this.activityID));
                                        }
                                    }
                                }
                                this.loading = false;
                        });

                        this.wsToken = localStorage.getItem('auth-token');

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
                                        this.pdfFound = true;
                                        // if default language is bengali
                                        if(this.translateService.getDefaultLang()==='bn')
                                        {
                                            this.activityApi
                                                .getBengaliDetailsActivity(act.id)
                                                .subscribe((res: any) => {
                                                    this.pdfName = res.results[0].category_details[0].field_value;
                                                    // this.pdfName = res.results[0].category_details[1].field_value;
                                                })
                                        }
                                        else{
                                            this.pdfName = act.name;
                                        }
            
                                        this.fileURL = act.contents[0].fileurl;
                                        this.fileURL =
                                            this.fileURL + '&token=' + this.wsToken;
                                        this.pdf_src =
                                            this.sanitize.bypassSecurityTrustResourceUrl(
                                                this.fileURL
                                            );
            
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
                                            if(currTopicIndex+1 < allTopics.length && 
                                                allTopics[currTopicIndex+1].modules.length > 0)
                                            {
                                                const nextTopicModules = allTopics[currTopicIndex+1].modules;

                                                this.nextTopicId = allTopics[currTopicIndex+1].id;

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
                });
           
        
    }

   

    getCertificate() {
        window.open(this.cert_url, '_blank');
    }

    testCertificate() {
        this.getCertificateApi
            .getCertificate(this.courseID, this.userID)
            .subscribe((response: any) => {
               
                if (response.url) {
                    this.certificateAvailable = true;
                    this.cert_url = response.url;
                } else {
                    this.certificateAvailable = false;
                }
            });
    }

    toggleActivityStatus() {
      
            this.currentActivity.cmid = this.activityID;
            this.currentActivity.completed = 1;
            this.updateActivityStatus.updateActivityStatus(this.currentActivity).subscribe((successRes: any) => {
                  
                    if (successRes.status) {
                        if (successRes.status == true) {
                            this.activityDone = true;
                            this.testCertificate();
                            this.activityCompletion.updateActivityStatus(Number(this.activityID));
                        }
                    }
                });
        
    }

    goNextActivity() {
        // if nextActivityId is not in current Topic's modules then topic: nextTopicId
        if(this.activityList.findIndex(module=>module.id == this.nextActivityID) === -1)
        {
            this.topicId = this.nextTopicId;
        }

        if (this.nextActivityType == 'quiz') {
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

        else if (this.nextActivityFormat == 'video/mp4' ||
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

       
    }

    goPrevActivity() {
        // if prevActivityID is not in current Topic's modules then topic: prevTopicId
        if(this.activityList.findIndex(module=>module.id == this.prevActivityID) === -1)
        {
            this.topicId = this.prevTopicId;
        }

        if (this.prevActivityType == 'quiz') {
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

        else if (this.prevActivityFormat == 'video/mp4' ||
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

        
    }
    saveCurrentActivity(userId,courseId,activityId,topicId) {
        // save current course activity in local storage
        const currCourseActivity = {
            'type': 'pdf',
            'activityId': activityId,
            'topicId': topicId
        };

        localStorage.setItem(JSON.stringify({
            'userId': userId,
            'courseId': courseId,
        }), JSON.stringify(currCourseActivity));
    }
}