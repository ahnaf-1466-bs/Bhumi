import { Component, OnInit, ViewEncapsulation } from '@angular/core';
import { MatDialog } from '@angular/material/dialog';
import { DomSanitizer } from '@angular/platform-browser';
import { ActivatedRoute } from '@angular/router';
import { SubmitModalComponent } from '../modal/submit-modal.component';
import { QuizService } from '../services/quiz.service';
import { AuthService } from 'app/core/auth/auth.service';
import { Router } from '@angular/router';

@Component({
    selector: 'app-question',
    templateUrl: './question.component.html',
    styleUrls: ['./question.component.scss'],
    encapsulation: ViewEncapsulation.None,
})
export class QuestionComponent implements OnInit {
    attemptId: string = '';
    token: string = '';
    constructor(
        private acr: ActivatedRoute,
        private _quiz: QuizService,
        private sanitize: DomSanitizer,
        private _authService: AuthService,
        private _router:Router,
        private dialog: MatDialog
    ) {}

    questionContent: any = {};
    questionCards: any[] = [];
    quizId: string = '';
    nextPage = 0;
    loading: boolean = true;

    ngOnInit() {
        this.loading = true;
        this.token = localStorage.getItem('auth-token');
        this.acr.queryParams.subscribe((res) => {
            this.quizId = res['id'];
            this.attemptId = res['attempt'];
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
                this.loading = false;
            });
    }

    openSubmitModal() {
        const dialogRef = this.dialog.open(SubmitModalComponent);
        dialogRef.afterClosed().subscribe((result) => {
            console.log(`Dialog result: ${result}`);
        });
    }
}
