import { Component, ElementRef, ViewChild } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { AutoDoneActivityService } from '../pdf-activity/services/auto-done-activity.service';
import { GetActivityStatusService } from '../video-activity/services/get-activity-status.service';
import { AuthService } from 'app/core/auth/auth.service';
import { ActivityApiService } from '../pdf-activity/services/activity-api.service';
import { ActivityCompletionService } from 'app/services/activity-completion.service';
import { TranslateService } from '@ngx-translate/core';
import { Activity } from '../video-activity/models/activity';
import { DomSanitizer } from '@angular/platform-browser';
import { GetCertificateService } from '../video-activity/services/get-certificate.service';
import { VideoPlusActivityService } from './services/video-plus-activity.service';

@Component({
  selector: 'app-video-pdf-activity',
  templateUrl: './video-pdf-activity.component.html',
})
export class VideoPdfActivityComponent {
  @ViewChild('myVideo') myVideo: ElementRef;

  instanceID: any;
  userID: any;
  activityList: any[];
  videoActivity: any;
  fileURL: string;
  videoName: string;
  activityID: string;
  courseID: string;
  wsToken: string;
  video_src: any;
  certificateAvailable: boolean = false;
  cert_url: string = '';
  videoFound: boolean;
  isAutomatic: boolean;
  loading: boolean = true;
  prevActivityID: number = -1;
  nextActivityID: number = -1;
  prevActivityType: string = '';
  nextActivityType: string = '';
  prevActivityFormat: string = '';
  nextActivityFormat: string = '';
  activityDone: boolean = false;
  currentActivity: Activity;
  videoDuration: any;
  enrolled: boolean = true;
  currentVideoTime: any = 0;
  marginalVideoTime: any;
  doneBtnDisabled: boolean = true;
  videoFinished: boolean = false;
  activityInfo: any = {} as any;
  prevInstanceID: any;
  topicId: any;
  prevTopicId: any;
  nextTopicId: any;
  pdfURL!: string;
  videoDesc!: any;
  showVideoDesc:boolean = false;

  constructor(
    private domSanitizer: DomSanitizer,
    private route: ActivatedRoute,
    private router: Router,
    private getCertificateApi: GetCertificateService,
    private autoDoneApi: AutoDoneActivityService,
    private getActivityStatus: GetActivityStatusService,
    private authService: AuthService,
    private activityApi: ActivityApiService,
    private activityCompletion: ActivityCompletionService,
    private translateService: TranslateService,
    private videoPlusService: VideoPlusActivityService
  ) { }

  ngOnInit() {
    this.route.queryParams.subscribe((params) => {
      this.enrolled = true;
      this.videoFound = false;
      this.activityDone = false;
      this.currentActivity = {} as Activity;
      this.topicId = params.topic;
      this.activityID = params.activity;
      this.video_src = '';
      this.prevActivityID = -1;
      this.nextActivityID = -1;
      this.loading = true;

      this.courseID = params.course;
      this.userID = localStorage.getItem('user-id');
      this.wsToken = localStorage.getItem('auth-token');

      this.saveCurrentActivity(this.userID,this.courseID,this.activityID,this.topicId);


      this.getActivityStatus
        .getActivityStatus(this.courseID, this.userID)
        .subscribe((res: any) => {
          if (res.exception || res.errorcode) {
            this.enrolled = false;
            this.loading = false;
            this.videoFound = false;
            this.authService.signOut();
            this.router.navigate(['login']);
            return;
          }
          let enrolledCourses: any[] = res.statuses;
          this.enrolled = false;
          for (let course of enrolledCourses) {
            if (course.cmid == this.activityID) {
              this.activityInfo = course;
              this.enrolled = true;

              if (course.isautomatic == true) {
                this.isAutomatic = true;
                if (course.state == 1) {
                  this.activityDone = true;
                  this.doneBtnDisabled = true;
                  this.testCertificate();
                  this.activityCompletion.updateActivityStatus(Number(this.activityID));

                } else {
                  this.activityDone = false;
                }
              } else {
                if (course.state == 1) {
                  this.activityDone = true;
                  this.doneBtnDisabled = false;
                  this.testCertificate();
                  this.activityCompletion.updateActivityStatus(Number(this.activityID));

                } else {
                  this.activityDone = false;
                }
              }
            }
          }
          this.loading = false;
        });

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

          if (this.activityList)
            this.activityList.forEach((act, index) => {
              if (act.id == this.activityID) {
                this.loading = false;
                this.videoFound = true;

                // if default language is bengali
                if (this.translateService.getDefaultLang() === 'bn') {
                  this.activityApi
                    .getBengaliDetailsActivity(act.id)
                    .subscribe((res: any) => {
                      this.videoName = res.results[0].category_details[0].field_value;
                      this.videoDesc = res.results[0].category_details[1].field_value;
                    })
                }
                else {
                  this.videoName = act.name;
                }

                // this.fileURL = act.contents[0].fileurl;          
                this.videoPlusService.getVideoPlusDetails(this.courseID, this.activityID)
                  .subscribe((res: any) => {
                    this.fileURL = res?.details[0].videourl; 
                    this.pdfURL = res?.details[0].pdfurl;
                    if(this.translateService.getDefaultLang() !== 'bn')
                    {
                      this.videoDesc = res?.details[0].intro;
                    }

                    this.video_src = this.domSanitizer.bypassSecurityTrustResourceUrl(
                      this.fileURL
                    );
                    if (this.myVideo) {
                      this.myVideo.nativeElement.load();
                    }
                  })

                if (index > 0) {
                  if (this.activityList[index - 1]['id']) {
                    this.prevActivityID =
                      this.activityList[index - 1]['id'];
                    this.prevInstanceID =
                      this.activityList[index - 1][
                      'instance'
                      ];
                    this.prevActivityType =
                      this.activityList[index - 1][
                      'modname'
                      ];
                    if (
                      this.prevActivityType == 'resource'
                    ) {
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
                      this.activityList[index + 1][
                      'modname'
                      ];

                    if (
                      this.nextActivityType == 'resource'
                    ) {
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
    });
  }

  timer: any;
  ngAfterViewInit() {
    this.timer = setInterval(() => {
      if (
        this.myVideo &&
        this.myVideo.nativeElement &&
        this.myVideo.nativeElement.currentTime
      ) {
        this.currentVideoTime = this.myVideo.nativeElement.currentTime;

        if (this.currentVideoTime >= this.marginalVideoTime) {
          this.activityDone = true;
          this.autoDone();
          this.activityCompletion.updateActivityStatus(Number(this.activityID));
        }
      }
    }, 1000);
  }

  ngOnDestroy(): void {
    clearInterval(this.timer); // clear the interval on component destroy
  }

  autoDone() {
    this.autoDoneApi
      .autoDoneVideoPlus(this.activityInfo.instance)
      .subscribe((res: any) => {
        this.activityDone = true;
        this.testCertificate();
        this.doneBtnDisabled = true;
        this.activityCompletion.updateActivityStatus(Number(this.activityID));
      });
  }

  onVideoMetadataLoaded(event: Event) {
    this.videoDuration = this.myVideo.nativeElement.duration;

    this.marginalVideoTime = (this.videoDuration * 75) / 100.0;
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

  onLoadMetadata(event: any) {
    this.videoDuration = this.myVideo.nativeElement.duration;
  }

  goNextActivity() {
    // if nextActivityId is not in current Topic's modules then topic: nextTopicId
    if (this.activityList.findIndex(module => module.id == this.nextActivityID) === -1) {
      this.topicId = this.nextTopicId;
    }

    if (this.nextActivityType == 'quiz') {
      this.router.navigate([`course-activity/${this.courseID}/quiz`], {
        queryParams: {
          id: this.instanceID,
          course: this.courseID,
          activity: this.nextActivityID,
          topic: this.topicId
        },
      });
    } else if (this.nextActivityType == 'zoom') {
      this.router.navigate([`course-activity/${this.courseID}/meeting`], {
        queryParams: {
          course: this.courseID,
          activity: this.nextActivityID,
          topic: this.topicId
        },
      });
    } 
    else if (this.nextActivityType === 'videoplus') {
      this.router.navigate([`course-activity/${this.courseID}/video-pdf`], {
        queryParams: {
          course: this.courseID,
          activity: this.nextActivityID,
          topic: this.topicId
        },
      });
    } 
    else if (this.nextActivityType == 'coursefeedback') {
      this.router.navigate([`course-activity/${this.courseID}/feedback`], {
        queryParams: {
          course: this.courseID,
          activity: this.nextActivityID,
          topic: this.topicId
        },
      });
    } else if (this.nextActivityFormat == 'application/pdf') {
      this.router.navigate([`course-activity/${this.courseID}/pdf`], {
        queryParams: {
          course: this.courseID,
          activity: this.nextActivityID,
          topic: this.topicId
        },
      });
    } else if (this.nextActivityFormat == 'video/mp4' ||
      this.nextActivityFormat == 'video/webm' ||
      this.nextActivityFormat == 'video/mov') {
      this.router.navigate([`course-activity/${this.courseID}/video`], {
        queryParams: {
          course: this.courseID,
          activity: this.nextActivityID,
          topic: this.topicId
        },
      });
    }
  }

  goPrevActivity() {
    // if prevActivityID is not in current Topic's modules then topic: prevTopicId
    if (this.activityList.findIndex(module => module.id == this.prevActivityID) === -1) {
      this.topicId = this.prevTopicId;
    }

    if (this.prevActivityType == 'quiz') {
      this.router.navigate([`course-activity/${this.courseID}/quiz`], {
        queryParams: {
          id: this.prevInstanceID,
          course: this.courseID,
          activity: this.prevActivityID,
          topic: this.topicId
        },
      });
    } else if (this.prevActivityType == 'zoom') {
      this.router.navigate([`course-activity/${this.courseID}/meeting`], {
        queryParams: {
          course: this.courseID,
          activity: this.prevActivityID,
          topic: this.topicId
        },
      });
    } 
    else if(this.prevActivityType === 'videoplus')
    {
        this.router.navigate([`course-activity/${this.courseID}/video-pdf`], {
            queryParams: {
                course: this.courseID,
                activity: this.prevActivityID,
                topic:this.topicId
            },
        });
    } 
    else if (this.prevActivityFormat == 'application/pdf') {
      this.router.navigate([`course-activity/${this.courseID}/pdf`], {
        queryParams: {
          course: this.courseID,
          activity: this.prevActivityID,
          topic: this.topicId
        },
      });
    } else if (
      this.prevActivityFormat == 'video/mp4' ||
      this.prevActivityFormat == 'video/webm' ||
      this.prevActivityFormat == 'video/mov'
    ) {
      this.router.navigate([`course-activity/${this.courseID}/video`], {
        queryParams: {
          course: this.courseID,
          activity: this.prevActivityID,
          topic: this.topicId
        },
      });
    }
  }
  saveCurrentTime() {
    this.route.queryParams.subscribe((params) => {
      localStorage.setItem(JSON.stringify(params), this.myVideo.nativeElement.currentTime);
    })
  }
  loadPreviousTime() {
    this.route.queryParams.subscribe((params) => {
      const prevDuration: Number = Number(localStorage.getItem(JSON.stringify(params)));
      if (this.myVideo) {
        this.myVideo.nativeElement.currentTime = prevDuration;
      }
    })
  }

  toggleVideoDescription()
  {
    this.showVideoDesc = !this.showVideoDesc;
  }

  downloadPdfFile()
  {
    window.open(this.pdfURL, '_blank');
  }

  saveCurrentActivity(userId,courseId,activityId,topicId) {
    // save current course activity in local storage
    const currCourseActivity = {
        'type': 'video-pdf',
        'activityId': activityId,
        'topicId': topicId
    };

    localStorage.setItem(JSON.stringify({
        'userId': userId,
        'courseId': courseId,
    }), JSON.stringify(currCourseActivity));
}
}
