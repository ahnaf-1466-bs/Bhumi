import { Route } from '@angular/router';
import { ForgetPasswordComponent } from './forget-password/forget-password.component';
import { RetrieveActionComponent } from './retrieve-action/retrieve-action.component';
import { UpdateSuccessComponent } from './update-success/update-success.component';

export const routes: Route[] = [
    {
        path: 'forget',
        component: ForgetPasswordComponent
    },
    {
        path: 'retrieve/action',
        component: RetrieveActionComponent
    },
    {
        path: 'update',
        component: UpdateSuccessComponent
    }
];