import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { EnrollmentRoutingModule } from './enrollment-routing.module';
import { EnrollmentComponent } from './enrollment/enrollment.component';
import { RouterModule } from '@angular/router';
import { FormsModule } from '@angular/forms';
import { routes } from '../about/about.routing';
import { MatDialogModule } from '@angular/material/dialog';
import { PaymentGatewayComponent } from './modals/payment-gateway/payment-gateway.component';
import { SharedModule } from 'app/shared/shared.module';


@NgModule({
  declarations: [
    EnrollmentComponent,
    PaymentGatewayComponent,
   
  ],
  imports: [
    CommonModule,
    FormsModule,
    MatDialogModule,
    SharedModule,
    EnrollmentRoutingModule,
    RouterModule.forChild(routes)
  ]
})
export class EnrollmentModule { }
