import { Component, Input } from '@angular/core';
import { ActivatedRoute, ParamMap, Router } from '@angular/router';
import { ActivityApiService } from 'app/modules/landing/pdf-activity/services/activity-api.service';
import { QuizService } from 'app/modules/landing/quiz-activity/services/quiz.service';
import { CourseDetailsService } from '../../course-details/course-details.service';

@Component({
    selector: 'app-slide-content2',
    templateUrl: './slide-content.component.html',
    styleUrls: ['./slide-content.component.scss'],
})
export class SlideContentComponent {
    @Input() title: string = '';
    @Input() subtitle: string = '';
    @Input() para: string = '';
    @Input() enrolled: boolean;
    @Input() dataFromParent: any;

    constructor(
        private _pdf: ActivityApiService,
        private route: ActivatedRoute,
        private _router: Router,
        private _quiz: QuizService,
        private _getNextBatchAPI: CourseDetailsService,
    ) {}

    id: any;
    activityId: any;
    response: any;
    modules: any;
    moduleName: any;
    contents: any;
    moduleType: any;
    quiz: any = {};
    quizId: string = '';
    token: any;

    endDate;
    result;
    endIn;
    batch;
    nextbatch: boolean = false;

    ngOnInit() {
        this.route.paramMap.subscribe((params: ParamMap) => {
            this.id = +params.get('id');
            if (localStorage.getItem('user-id') == null) {
                localStorage.setItem('latest-visited-course', this.id);
            } else {
                localStorage.removeItem('latest-visited-course');
            }
        });

        this._pdf.getActivities(this.id).subscribe((response) => {
            if (response[1]) {
                this.response = response[1];
                this.modules = this.response.modules;
                if (this.modules && this.modules.length > 0) {
                    this.activityId = this.modules[0].id;
                    this.moduleName = this.modules[0].modname;
                    this.contents = this.modules[0]?.contents?.[0];
                    this.moduleType = this.contents?.mimetype;
                }
            }
        });

        let token: any = localStorage.getItem('auth-token');
        this.token = token;

        this._quiz.getQuiz(token, this.id).subscribe((res: any) => {
            this.quiz = res;
            this.quizId =
                res && res.quizzes && res.quizzes.length > 0
                    ? res.quizzes[0].id
                    : undefined;
        });

        let body3 = [
            { wsfunction: 'local_preregistration_get_batch_data_by_courseid' },
            { courseid: this.id },
        ];
        this._getNextBatchAPI.getNextBatch(body3).subscribe((res: any) => {
            if (res.statuscode == 200) {
                this.nextbatch = true;
            }
            this.batch = res.batchid;
        });
    }

    ngOnChanges() {
        this.result = this.dataFromParent;
        this.endDate = this.result?.enddate;
        this.endIn = this.endDate * 1000 - Date.now();
    }

    goToFirstActivity() {
        if(this.id)
        {
            this._router.navigate(['course-activity/'+this.id]);
        }
        // if (this.moduleName === 'resource') {
        //     let route = '';
        //     if (this.moduleType.includes('pdf')) {
        //         route = 'course-activity/pdf';
        //     } else if (this.moduleType.includes('video')) {
        //         route = 'video';
        //     }

        //     this._router.navigate([`/${route}`], {
        //         queryParams: {
        //             course: this.id,
        //             activity: this.activityId,
        //         },
        //     });
        // } else if (this.moduleName === 'quiz') {
        //     this._router.navigate([`/quiz`], {
        //         queryParams: {
        //             id: this.quizId,
        //             course: this.id,
        //             activity: this.activityId,
        //         },
        //     });
        // } else if (this.moduleName === 'zoom') {
        //     this._router.navigate(['meeting'], {
        //         queryParams: {
        //             course: this.id,
        //             activity: this.activityId,
        //         },
        //     });
        // }
    }

    goToEnrollment() {
        this._router.navigate(['enrollment', this.id]);
    }

    goToPreRegistration() {
        this._router.navigate([`/pre-registration`], {
            queryParams: {
                course: this.id,
                batch: this.batch,
            },
        });
    }
}
