import { NgModule } from '@angular/core';
import { MatIconModule } from '@angular/material/icon';
import { Route, RouterModule } from '@angular/router';
import { LoginComponent } from './Login.component';
import { FormsModule } from '@angular/forms';
import { SharedModule } from 'app/shared/shared.module';
import { MatButtonModule } from '@angular/material/button';
import { MatCheckboxModule } from '@angular/material/checkbox';
import { MatFormFieldModule } from '@angular/material/form-field';
import { TranslateModule } from '@ngx-translate/core';
import { MatInputModule } from '@angular/material/input';
import { FuseAlertModule } from '@fuse/components/alert';
import { ResetPasswordComponent } from './reset-password/reset-password.component';
import { OauthLoginComponent } from './oauth-login/oauth-login.component';



const exampleRoutes: Route[] = [
    {
        path     : '',
        component: LoginComponent
    },
    {
        path: 'forgot_password',
        component: ResetPasswordComponent
    }
];

@NgModule({
    declarations: [
        LoginComponent,
        ResetPasswordComponent,
        OauthLoginComponent
    ],
    imports     : [
        RouterModule.forChild(exampleRoutes),
        FormsModule,
        MatIconModule,
        MatButtonModule,
        MatCheckboxModule,
        MatFormFieldModule,
        MatInputModule,
        FuseAlertModule,
        SharedModule,
        // TranslateModule
    ]
})
export class LoginModule
{
}
