import { Component, ViewChild, ViewEncapsulation } from '@angular/core';
import {
    FormGroup,
    NgForm,
    UntypedFormBuilder,
    UntypedFormGroup,
    Validators,
} from '@angular/forms';
import { Router } from '@angular/router';
import { fuseAnimations } from '@fuse/animations';
import { GetOauthIssuerIdService } from 'app/modules/admin/Login/services/get-oauth-issuer-id.service';
import { environment } from 'environments/environment';
import { User } from '../../models/user';
import { SignUpService } from '../../services/sign-up.service';

function matchPassword(control: FormGroup) {
    const password = control.get('password');
    const confirmPassword = control.get('confirmPassword');

    if (password.value !== confirmPassword.value) {
        confirmPassword.setErrors({ matchPassword: true });
    } else {
        confirmPassword.setErrors(null);
    }
}

@Component({
    selector: 'app-registration-create',
    templateUrl: './registration-create.component.html',
    styleUrls: ['./registration-create.component.scss'],
    encapsulation: ViewEncapsulation.None,
    animations: fuseAnimations,
})
export class RegistrationCreateComponent {
    showPassword: boolean = false;
    showConfirmPassword: boolean = false;

    user: User = {} as User;
    passMatched: boolean = true;
    duplicateMail: boolean = false;
    givenPassword: string = '';
    givenConfirmpassword: string = '';
    loading: boolean = false;
    passwordIssue: boolean = false; //server response checker for password

    @ViewChild('regForm') regForm: NgForm;
    formInfo: UntypedFormGroup;

    constructor(
        private _formBuilder: UntypedFormBuilder,
        private _router: Router,
        private _register: SignUpService,
        private _oAuthIssuerID: GetOauthIssuerIdService
    ) {}

    ngOnInit() {
        this.passMatched = true;
        this.loading = false;
        this.duplicateMail = false;
        this.passwordIssue = false; //server response checker for password

        this.formInfo = this._formBuilder.group(
            {
                firstName: ['', [Validators.required]],
                lastName: ['', [Validators.required]],
                username: [
                    '',
                    Validators.compose([
                        Validators.required,
                        Validators.pattern(
                            '^[a-z0-9._%+-]+@[a-z0-9.-]+.[a-z]{2,4}$'
                        ),
                    ]),
                ],
                phone: [
                    '',
                    Validators.compose([
                        Validators.required,
                        Validators.minLength(11),
                        Validators.maxLength(11),
                    ]),
                ],
                organization: [''],
                designation: ['', [Validators.required]],
                password: [
                    '',
                    Validators.compose([
                        Validators.required,
                        Validators.minLength(8),
                        Validators.pattern(
                            '^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[$@$!%*?&])[A-Za-z\\d$@$!%*?&].{7,}$'
                        ),
                    ]),
                ],
                confirmPassword: [
                    '',
                    Validators.compose([
                        Validators.required,
                        Validators.minLength(8),
                    ]),
                ],
            },
            {
                validator: matchPassword,
            }
        );
    }

    getIssuerID(address: string) {
        this._oAuthIssuerID.getOAuth2IssuerID(address).subscribe((res: any) => {
            let oAuthURL =
                `${environment.baseURL}/auth/vumi_auth/issuerauth.php?issuerid=` +
                res.issuerid +
                '&wantsurl=/';
            window.location.replace(oAuthURL);
        });
    }

    onMouseDown(x: any) {
        this.duplicateMail = false;
    }

    register(): void {
        this.loading = true;
        if (this.formInfo.invalid) {
            return;
        }

        let userData = this.formInfo.value;

        this.duplicateMail = false;
        this.passwordIssue = false; //server response checker for password

        this.user.email = userData.username;
        this.user.password = userData.password;
        this.user.fName = userData.firstName;
        this.user.lName = userData.lastName;
        this.user.designation = userData.designation;
        this.user.organization = userData.organization;
        this.user.phone = userData.phone;

        this._register.register(this.user).subscribe(
            (res: any) => {
                this.loading = false;
                if (res.exception == 'moodle_exception') {
                    this.formInfo.reset();
                    alert('Internal Server Error!');
                    return;
                } 
                else if (res.success == false) {
                    if (res.warnings) {
                        if (
                            res.warnings[0].item == 'username' ||
                            res.warnings[0].item == 'email'
                        ) {
                            this.loading = false;
                            this.duplicateMail = true;
                        } else {
                            this.passwordIssue = true;
                            this.loading = true;
                        }
                    }
                } 
                else {
                    this.loading = true;
                    this._router.navigate(['signup/confirmation-required']);
                }
            },
            (err: any) => {
                this.loading = false;
                if (err.warnings) {
                    if (err.warnings) {
                        if (
                            err.warnings[0].item == 'username' ||
                            err.warning[0].item == 'email'
                        ) {
                            this.duplicateMail = true;
                        } else this.passwordIssue = true;
                    }
                }
            }
        );
    }
}
