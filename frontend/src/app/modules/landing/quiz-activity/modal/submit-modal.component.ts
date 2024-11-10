import { Component, Inject, OnInit } from '@angular/core';
import { MatDialogRef, MAT_DIALOG_DATA } from '@angular/material/dialog';
import { DomSanitizer } from '@angular/platform-browser';
import { ActivatedRoute, Router } from '@angular/router';
import { QuizService } from '../services/quiz.service';
import { AuthService } from 'app/core/auth/auth.service';
import { SharedModule } from 'app/shared/shared.module';

@Component({
    selector: 'app-submit-modal',
    templateUrl: './submit-modal.component.html',
    styleUrls: ['./submit-modal.component.scss'],
    standalone: true,
    imports: [SharedModule]
})
export class SubmitModalComponent implements OnInit {
    constructor(
        private _router: Router,
        private _quiz: QuizService,
        private acr: ActivatedRoute,
        private sanitize: DomSanitizer,
        private _authService: AuthService,
        public dialogRef: MatDialogRef<SubmitModalComponent>,
        @Inject(MAT_DIALOG_DATA) public data: any
    ) {}

    attemptId: string = '';
    token: string = '';
    questionContent: any = {};
    questionCards: any[] = [];
    quizId: string = '';
    courseId: string = '';
    activityID: string = '';
    nextPage = 0;
    ngOnInit() {
        this.token = localStorage.getItem('auth-token');
        // console.log('TOKEN', this.token);

        this.acr.queryParams.subscribe((res) => {
            this.quizId = res['id'];
            this.attemptId = res['attempt'];
            this.courseId = res['course'];
            this.activityID = res['activity']
        });
        this._quiz
            .getAttemptData(this.token, this.attemptId)
            .subscribe((res: any) => {

                if( res.exception || res.errorcode){
                    this._authService.signOut();
                    this._router.navigate(['login']);
                    return;
                }

                this.questionContent = res;
                this.questionCards = res.questions.map((quest: any) =>
                    this.sanitize.bypassSecurityTrustHtml(quest.html)
                );
            });
    }

    onNoClick(): void {
        this.dialogRef.close();
    }

    submitAttempt() {
        let data: any[] = this.prepareSubmission();
        let finishAttempt = '1';
        let timeup = '1';
        this._quiz
            .processAttempt(
                this.token,
                this.attemptId,
                data,
                finishAttempt,
                timeup
            )
            .subscribe((res: any) => {
                if( res.exception || res.errorcode){
                    this._authService.signOut();
                    this._router.navigate(['login']);
                    return;
                }

                if (res.state === 'finished') {
        console.log("this.courseID",this.courseId);

                    this._router.navigate([`course-activity/${this.courseId}/quiz/review`], {
                        queryParams: {
                            id: this.quizId,
                            course: this.courseId,
                            activity:  this.activityID,
                            attempt: this.attemptId,
                        },
                    });
                }
            });
        this.dialogRef.close();
    }

    prepareSubmission(): any[] {
        let allRadioInputs: any[] = [];
        let quids = [];
        let data: any = [];
        let sequenceList: any[] = Array.from(
            document.querySelectorAll('input[name*=sequencecheck]')
        );
        sequenceList = sequenceList.map((node: any) => ({
            name: node.name,
            value: node.value,
        }));
        let answerList: any[] = Array.from(
            document.querySelectorAll('input[name*=answer],input[name*=choice]')
        );

        // checking question type to populate our questions

        let qtype = answerList[0].type;
        // console.log(sequenceList);

        answerList = this.mixedHelper(answerList);

        for (let i = 0; i < answerList.length; ++i) {
            data.push(answerList[i]);
        }
        for (let i = 0; i < sequenceList.length; ++i) {
            data.push(sequenceList[i]);
        }

        // console.log(data);

        return data;
    }

    mixedHelper(answerList: any[]): any[] {
        // console.log(answerList);

        answerList = answerList
            .filter((node: any) => {
                if (node.type === 'text') return true;
                if (node.type === 'radio' && node.checked) return true;
                if (node.type === 'checkbox' && node.checked) return true;
                return false;
            })
            .map((node: any) => {
                let ret: any = {};
                if (node.type === 'radio') {
                    ret = { name: node.name, value: node.value };
                } else if (node.type === 'text') {
                    ret = { name: node.name, value: node.value };
                } else if (node.type === 'checkbox') {
                    ret = { name: node.name, value: node.value };
                }
                return ret;
            });
        return answerList;
    }
}
