import { Route } from '@angular/router';
import { ConfirmationRequiredComponent } from './components/confirmation-required/confirmation-required.component';
import { RegistrationConfirmComponent } from './components/registration-confirm/registration-confirm.component';
import { RegistrationCreateComponent } from './components/registration-create/registration-create.component';


export const routes: Route[] = [
    {
        path: '',
        component: RegistrationCreateComponent 
    },
    {
        path: 'confirm',
        component: RegistrationConfirmComponent
    },
    {
        path: 'confirmation-required',
        component: ConfirmationRequiredComponent
    }
];