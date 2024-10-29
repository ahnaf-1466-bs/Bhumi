import { ChangeDetectorRef, Component, OnInit } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { AuthService } from 'app/core/auth/auth.service';
import { ActivityApiService } from 'app/modules/landing/pdf-activity/services/activity-api.service';
import { ActivityCompletionService } from 'app/services/activity-completion.service';
import { AutoDoneActivityService } from '../../pdf-activity/services/auto-done-activity.service';
import { Activity } from '../../video-activity/models/activity';
import { GetActivityStatusService } from '../../video-activity/services/get-activity-status.service';
import { GetCertificateService } from '../../video-activity/services/get-certificate.service';
import { QuizService } from '../services/quiz.service';
import { TranslateService } from '@ngx-translate/core';

@Component({
    selector: 'app-attemts',
    templateUrl: './attemts.component.html',
    styleUrls: ['./attemts.component.scss'],
})
export class AttemtsComponent implements OnInit {
    attempts: any[] = [];
    prevTopicId: any;
    nextTopicId: any;

    constructor(
        private _quiz: QuizService,
        private acr: ActivatedRoute,
        private _router: Router,
        private route: ActivatedRoute,
        private getCertificateApi: GetCertificateService,
        private getActivityStatus: GetActivityStatusService,
        private _authService: AuthService,
        private autoDoneApi: AutoDoneActivityService,
        private activityApi: ActivityApiService,
        private activityCompletion:ActivityCompletionService,
        private changeDetector:ChangeDetectorRef,
        private translateService:TranslateService
    ) {}
    quizId: string = '';
    buttonText: string = 'Re-Attempt';
    quizSummary: any;
    error: string = '';
    attemptId: string = '';
    token: string = '';
    enrolled: boolean = true;

    name: string = '';
    intro: string = '';
    id;
    activityId;
    response;
    modules;
    moduleName;
    contents;
    moduleType;
    quiz: any = {};

    userID: any;
    activityList: any[];
    pdfActivity: any;
    fileURL: string;
    pdfName: string;
    activityID: string;
    courseID: string;
    wsToken: string;
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
    details: any[] = [];
    status3;
    activityStatus3;
    topicId:any;

    ngOnInit(){
        this.route.queryParams.subscribe(params=>{
            // if query parameters are changed then restart entire component
            this.resetStateVariables();
            this.changeDetector.detectChanges();
            this.loadData();
        })
    }
    
    private resetStateVariables() {
        this.attempts = [];
        this.quizId = '';
        this.buttonText = 'Re-Attempt';
        this.quizSummary = null;
        this.error = '';
        this.attemptId = '';
        this.token = '';
        this.enrolled = true;

        this.name = '';
        this.intro = '';
        this.id = null;
        this.activityId = null;
        this.response = null;
        this.modules = null;
        this.moduleName = '';
        this.contents = null;
        this.moduleType = null;
        this.quiz = {};

        this.userID = '';
        this.activityList = null;
        this.pdfActivity = null;
        this.fileURL = '';
        this.pdfName = '';
        this.activityID = '';
        this.courseID = '';
        this.wsToken = '';
        this.pdf_src = null;
        this.pdfFound = false;
        this.certificateAvailable = false;
        this.loading = true;
        this.prevActivityID = -1;
        this.nextActivityID = -1;
        this.cert_url = '';
        this.prevActivityType = '';
        this.nextActivityType = '';
        this.prevActivityFormat = '';
        this.nextActivityFormat = '';
        this.activityDone = false;
        this.currentActivity = null;
        this.doneBtnDisabled = false;
        this.instanceID = '';
        this.prevInstanceID = '';
        this.details = [];
        this.status3 = false;
        this.activityStatus3 = '';
        
    }

    private loadData()
    {
        this.activityDone = false;
        this.enrolled = true;
        this.currentActivity = {} as Activity;
        this.prevActivityID = -1;
        this.nextActivityID = -1;
        this.pdfFound = false;

        

        // why use timeout of 2seconds to  fetch activity, course, user ids and quiz information?
        setTimeout(() => {
            this.route.queryParams.subscribe((params) => {
                this.topicId = params.topic;
                this.activityID = params.activity;
                this.courseID = params.course;
                console.log("this.courseID",this.courseID);
                
                this.userID = localStorage.getItem('user-id');

                this.saveCurrentActivity(this.userID,this.courseID,this.activityID,params.id,this.topicId);

                // if language is bengali then load up bengali details of this activity
                if(this.translateService.getDefaultLang()==='bn')
                {
                    this.activityApi
                    .getBengaliDetailsActivity(this.activityID)
                    .subscribe((res: any) => {
                        this.name = res.results[0].category_details[0].field_value;
                        this.intro = res.results[0].category_details[1].field_value;
                    })
                }


                this.getActivityStatus
                    .getActivityStatus(this.courseID, this.userID)
                    .subscribe((res: any) => {
                        if (res.exception || res.errorcode) {
                            this._authService.signOut();
                            this._router.navigate(['login']);
                            return;
                        }

                        for (let quiz of res.statuses) {
                            if (quiz.cmid == this.activityID) {
                                this.details = quiz.details;
                            }
                        }
                        if (this.details.length >= 2) {
                            this.status3 =
                                this.details[
                                    this.details.length - 1
                                ].rulevalue.status;

                            if (this.status3 == 0) {
                                this.activityStatus3 = 'To do';
                            }
                            if (this.status3 == 1) {
                                this.activityStatus3 = 'Done';
                            }
                            if (this.status3 == 2) {
                                this.activityDone = true;
                                this.activityStatus3 = 'Passed';
                                this.activityCompletion.updateActivityStatus(Number(this.activityID));
                            }
                            if (this.status3 == 3) {
                                this.activityStatus3 = 'Failed';
                            }
                        }

                        if (res.exception) {
                            this.enrolled = false;
                            this.loading = false;
                            this.pdfFound = false;

                            return;
                        }
                        let enrolledCourses: any[] = res.statuses;
                        this.enrolled = false;
                        for (let course of enrolledCourses) {
                            if (course.cmid == this.activityID) {
                                this.enrolled = true;
                                if (course.state == 0) {
                                } else {
                                    this.testCertificate();
                                }

                                if (course.isautomatic) {
                                    this.autoDoneApi
                                        .autoDone(course.instance)
                                        .subscribe((res) => {
                                            this.testCertificate();
                                            this.doneBtnDisabled = true;
                                        });
                                }
                            }
                        }
                        this.loading = false;
                    });
            });
            this.wsToken = localStorage.getItem('auth-token');

            this.activityApi
                .getActivities(this.courseID)
                .subscribe((successRes: any) => {
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
                                    allTopics[currTopicIndex - 1].modules.length > 0) {
                                    const prevTopicModules = allTopics[currTopicIndex - 1].modules;

                                    this.prevTopicId = allTopics[currTopicIndex - 1].id;

                                    this.prevActivityID =
                                        prevTopicModules[prevTopicModules.length - 1]['id'];

                                    this.prevInstanceID =
                                        prevTopicModules[prevTopicModules.length - 1]['instance'];

                                    this.prevActivityType =
                                        prevTopicModules[prevTopicModules.length - 1]['modname'];

                                    if (this.prevActivityType == 'resource') {
                                        this.prevActivityFormat =
                                            prevTopicModules[prevTopicModules.length - 1]
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
        }, 1000);

        this.acr.queryParams.subscribe((res) => {
            this.quizId = res['id'];
            if (this.quizId === '') {
                this.enrolled = false;
            }
            this.courseID = res['course'];
        });
        let token: any = localStorage.getItem('auth-token');
        this.token = token;
        
        if (this.translateService.getDefaultLang()==='en') {
            this._quiz.getQuiz(token, this.courseID).subscribe((res: any) => {
                for (let quiz of res?.quizzes) {
                    if (quiz.id == this.quizId) {
                        this.name = quiz?.name;
                        this.intro = quiz?.intro;
                    }
                }
            });
        }

        // why use timeout of 0second to fetch user attempts?
        setTimeout(() => {
            // console.log("timeout of 0ms to fetch user attempts");
            this._quiz
                .getUserAttempts(token, this.quizId)
                .subscribe((res: any) => {
                    this.quizSummary = res;
                    if (res?.errorcode === 'requireloginerror') {
                        this.error = res?.message;
                        return;
                    }
                    this.attempts = res?.attempts;
                    for (const attempt of this.attempts) {
                        if (attempt.state === 'inprogress') {
                            this.buttonText = 'Continue Attempt';
                            this.attemptId = attempt.id;
                        }
                    }
                    this.attempts = this.attempts.reverse();
                });
        }, 0);
    }
    saveCurrentActivity(userId,courseID,activityId,quizId,topicId) {
        // save current course activity in local storage
        const currCourseActivity = {
            'type': 'quiz',
            'activityId': activityId,
            'quizId': quizId,
            'topicId': topicId
        };

        localStorage.setItem(JSON.stringify({
            'userId': userId,
            'courseId': courseID,
        }), JSON.stringify(currCourseActivity));
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

    goto(attempt: any) {
        console.log("this.courseID",this.courseID);
        
        if (attempt.state === 'finished')
        {   
            this._router.navigate([`course-activity/${this.courseID}/quiz/review`], {
                queryParams: {
                    id: this.quizId,
                    attempt: this.attemptId,
                    topic:this.topicId
                },
            });
        }
        else {
            this._router.navigate([`course-activity/${this.courseID}/quiz/question`], {
                queryParams: {
                    id: this.quizId,
                    attempt: this.attemptId,
                    topic:this.topicId
                },
            });
        }
    }

    attempt() {
        console.log("this.courseID",this.courseID);

        if (this.error !== '') {
            alert(this.error);
            return;
        }
        if (this.buttonText === 'Continue Attempt') {
            this._router.navigate([`course-activity/${this.courseID}/quiz/question`], {
                queryParams: {
                    id: this.quizId,
                    activity:  this.activityID,
                    attempt: this.attemptId,
                    topic:this.topicId
                },
            });
        } else {
            this._quiz
                .newAttempt(this.token, this.quizId)
                .subscribe((res: any) => {
                    this._router.navigate([`course-activity/${this.courseID}/quiz/question`], {
                        queryParams: {
                            id: this.quizId,
                            course:  this.courseID,
                            activity:  this.activityID,
                            attempt: res.attempt.id,
                            topic:   this.topicId
                        },
                    });
                });
        }
    }
    
    goNextActivity() {
        console.log("this.courseID",this.courseID);

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
                    topic:   this.topicId
                },
            });
        } else if (this.nextActivityType == 'coursefeedback') {
            this._router.navigate([`course-activity/${this.courseID}/feedback`], {
                queryParams: {
                    course: this.courseID,
                    activity: this.nextActivityID,
                    topic:   this.topicId
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
                    topic:   this.topicId
                },
            });
        } else if (this.nextActivityFormat == 'application/pdf') {
            this._router.navigate([`course-activity/${this.courseID}/pdf`], {
                queryParams: {
                    course: this.courseID,
                    activity: this.nextActivityID,
                    topic:   this.topicId
                },
            });
        } else if (this.moduleName == 'application/pdf') {
            this._router.navigate([`course-activity/${this.courseID}/pdf`], {
                queryParams: {
                    course: this.courseID,
                    activity: this.nextActivityID,
                    topic:   this.topicId
                },
            });
        } else if (this.nextActivityType == 'zoom') {
            this._router.navigate([`course-activity/${this.courseID}/meeting`], {
                queryParams: {
                    course: this.courseID,
                    activity: this.nextActivityID,
                    topic:   this.topicId
                },
            });
        }
    }

    goPrevActivity() {
        console.log("this.courseID",this.courseID);

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
                    topic:   this.topicId
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
                    topic:   this.topicId
                },
            });
        } else if (this.prevActivityFormat == 'application/pdf') {
            this._router.navigate([`course-activity/${this.courseID}/pdf`], {
                queryParams: {
                    course: this.courseID,
                    activity: this.prevActivityID,
                    topic:   this.topicId
                },
            });
        } else if (this.prevActivityType == 'zoom') {
            this._router.navigate([`course-activity/${this.courseID}/meeting`], {
                queryParams: {
                    course: this.courseID,
                    activity: this.prevActivityID,
                    topic:   this.topicId
                },
            });
        }
    }
}
