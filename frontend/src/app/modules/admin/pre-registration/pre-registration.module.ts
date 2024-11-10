import { CommonModule } from '@angular/common';
import { NgModule } from '@angular/core';
import { RouterModule } from '@angular/router';
import { SharedModule } from 'app/shared/shared.module';
import { PreRegistrationComponent } from './pre-registration.component';
import { PreRegistrationRoutes } from './pre-registration.routing';

@NgModule({
    declarations: [PreRegistrationComponent],
    imports: [
        CommonModule,
        RouterModule.forChild(PreRegistrationRoutes),
        SharedModule,
    ],
})
export class PreRegistrationModule {}
