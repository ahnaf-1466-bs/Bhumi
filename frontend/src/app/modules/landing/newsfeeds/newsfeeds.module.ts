import { CommonModule } from '@angular/common';
import { NgModule } from '@angular/core';
import { RouterModule } from '@angular/router';
import { SharedModule } from 'app/shared/shared.module';
import { NewsfeedsComponent } from './newsfeeds.component';
import { NewsfeedsRoutes } from './newsfeeds.routing';

@NgModule({
    declarations: [NewsfeedsComponent],
    imports: [
        CommonModule,
        RouterModule.forChild(NewsfeedsRoutes),
        SharedModule,
    ],
})
export class NewsfeedsModule {}
