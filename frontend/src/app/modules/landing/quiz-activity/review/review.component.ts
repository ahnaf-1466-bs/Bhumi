import { Component, OnInit, ViewEncapsulation } from '@angular/core';
import { DomSanitizer } from '@angular/platform-browser';
import { ActivatedRoute } from '@angular/router';
import { QuizService } from '../services/quiz.service';
import { CompleteCourseService } from '../../feedback-activity/services/complete-course.service';
import { AuthService } from 'app/core/auth/auth.service';
import { Router } from '@angular/router';
import { GetActivityStatusService } from '../../video-activity/services/get-activity-status.service';

@Component({
    selector: 'app-review',
    templateUrl: './review.component.html',
    styleUrls: ['./review.component.scss'],
    encapsulation: ViewEncapsulation.None,
})
export class ReviewComponent implements OnInit {
    questionsHtml: any[] = [];
    attemptResponse: any = {};

    constructor(
        private _quiz: QuizService,
        private sanitize: DomSanitizer,
        private acr: ActivatedRoute,
        private _authService: AuthService,
        private _router:Router,
        private completeCourseService:CompleteCourseService,
        private getActivityStatus: GetActivityStatusService,
    ) {}

    token;
    quizId: string = '';
    courseId: string = '';
    attemptId: string = '';
    activityId:string = '';
    userId:any;
    loading: boolean = true;
    isQuizPassed:boolean = false;

    ngOnInit() {
        this.loading = true;
        let token = localStorage.getItem('auth-token');
        this.token = token;
        this.acr.queryParams.subscribe((res) => {
            this.quizId = res['id'];
            this.courseId = res['course'];
            this.attemptId = res['attempt'];
            this.activityId = res['activity']
        });
        this.userId = localStorage.getItem('user-id');
        this.getActivityStatus
            .getActivityStatus(this.courseId, this.userId)
            .subscribe((res: any) => {
                let details;
                let status3;
                for (let quiz of res.statuses) {
                    if (quiz.cmid == this.activityId) {
                        details = quiz.details;
                    }
                }
                if (details.length >= 2) {
                    status3 =
                        details[
                            details.length - 1
                        ].rulevalue.status;
                    if (status3 == 2) {
                        this.isQuizPassed = true;
                    }
                }
                this._quiz
                    .getAttemptReview(this.token, this.attemptId)
                    .subscribe((res: any) => {
                        if (res.exception || res.errorcode) {
                            this._authService.signOut();
                            this._router.navigate(['login']);
                            return;
                        }
                        this.attemptResponse = res.attempt;
                        let temp = res.questions;
                        for (let ques of temp) {
                            if (!this.isQuizPassed) 
                            {
                                var htmlString = ques.html; //"<div class='example'>...</div><div class='example'>...</div><div class='other'>...</div>";

                                // Create a temporary div element
                                var tempDiv = document.createElement('div');
                                
                                // Set the inner HTML of the temporary div to your HTML string
                                tempDiv.innerHTML = htmlString;
                                
                                // Get all div elements with the class name 'rightanswer'
                                var divsWithClassName = tempDiv.querySelectorAll('div.rightanswer');
                                
                                // Remove divs with the class name 'rightanswer' from the temporary div
                                divsWithClassName.forEach(function(div) {
                                    div.parentNode.removeChild(div);
                                });
                                
                                // Get the updated HTML string from the temporary div
                                var cleanedHtmlString = tempDiv.innerHTML;
                                this.questionsHtml.push(
                                    this.sanitize.bypassSecurityTrustHtml(cleanedHtmlString)
                                );
                            }
                            else {
                                this.questionsHtml.push(
                                    this.sanitize.bypassSecurityTrustHtml(ques.html)
                                );
                            }
                        }
                        this.loading = false;
                    });
            })
    }

    goBack() {
        window.history.go(-2);
        this.completeCourseService.submit();
    }
}
