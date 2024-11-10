import { CommonModule } from '@angular/common';
import { NgModule } from '@angular/core';
import { RouterModule } from '@angular/router';
import { SharedModule } from 'app/shared/shared.module';
import { FeedbackActivityComponent } from './feedback-activity.component';
import { FeedbackActivityRoutes } from './feedback-activity.routing';
import { ReactiveFormsModule } from '@angular/forms';
import { StarComponent } from './components/star/star.component';
import { MatIconModule } from '@angular/material/icon';

@NgModule({
    declarations: [FeedbackActivityComponent, StarComponent],
    imports: [
        CommonModule,
        ReactiveFormsModule,
        RouterModule.forChild(FeedbackActivityRoutes),
        SharedModule,
        MatIconModule
    ],
})
export class FeedbackActivityModule {}
