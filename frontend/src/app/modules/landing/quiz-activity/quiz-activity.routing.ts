import { Route } from '@angular/router';
import { AttemtsComponent } from './attemts/attemts.component';
import { QuestionComponent } from './question/question.component';
import { QuizActivityComponent } from './quiz-activity.component';
import { ReviewComponent } from './review/review.component';

export const quizRouts: Route[] = [
    {
        path: '',
        component: QuizActivityComponent,
        children: [
            { path: '', component: AttemtsComponent },
            { path: 'question', component: QuestionComponent },
            { path: 'review', component: ReviewComponent },
        ],
    },
];
