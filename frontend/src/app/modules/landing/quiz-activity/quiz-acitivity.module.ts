import { NgModule } from '@angular/core';
import { MatDialogModule } from '@angular/material/dialog';
import { RouterModule } from '@angular/router';
import { SharedModule } from 'app/shared/shared.module';
import { AttemtsComponent } from './attemts/attemts.component';
import { QuestionComponent } from './question/question.component';
import { QuizActivityComponent } from './quiz-activity.component';
import { quizRouts } from './quiz-activity.routing';
import { ReviewComponent } from './review/review.component';
import { MatIconModule } from '@angular/material/icon';

@NgModule({
    declarations: [
        QuizActivityComponent,
        AttemtsComponent,
        QuestionComponent,
        ReviewComponent,
    ],
    imports: [RouterModule.forChild(quizRouts), SharedModule, MatDialogModule,MatIconModule],
})
export class QuizActivityModule {}
