import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';
import { RegistrationCreateComponent } from './components/registration-create/registration-create.component';
import { RegistrationConfirmComponent } from './components/registration-confirm/registration-confirm.component';
import { ConfirmationRequiredComponent } from './components/confirmation-required/confirmation-required.component';
import { routes } from './registration.routing';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { MatIconModule } from '@angular/material/icon';
import { SharedModule } from 'app/shared/shared.module';

@NgModule({
  declarations: [
    RegistrationConfirmComponent,
    ConfirmationRequiredComponent,
    RegistrationCreateComponent
  ],
  imports: [
    CommonModule,
    FormsModule,
    MatIconModule,  
    ReactiveFormsModule,
    SharedModule,
    RouterModule.forChild(routes),
  ]
})
export class RegistrationModule { }
