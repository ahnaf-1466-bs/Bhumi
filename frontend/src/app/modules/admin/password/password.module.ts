import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ForgetPasswordComponent } from './forget-password/forget-password.component';
import { RouterModule } from '@angular/router';
import { routes } from './password.routing';
import { FormsModule } from '@angular/forms';
import { RetrieveActionComponent } from './retrieve-action/retrieve-action.component';
import { UpdateSuccessComponent } from './update-success/update-success.component';
import { SharedModule } from 'app/shared/shared.module';

@NgModule({
  declarations: [
    ForgetPasswordComponent,
    RetrieveActionComponent,
    UpdateSuccessComponent,
    
  ],
  imports: [
    CommonModule,
    FormsModule,
    SharedModule,
    RouterModule.forChild(routes)
  ]
})
export class PasswordModule { }
