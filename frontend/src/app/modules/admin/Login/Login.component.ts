import { Component, ViewChild, ViewEncapsulation } from '@angular/core';
import {
    NgForm,
    UntypedFormBuilder,
    UntypedFormGroup,
    Validators,
} from '@angular/forms';
import { ActivatedRoute, Router, RoutesRecognized } from '@angular/router';
import { fuseAnimations } from '@fuse/animations';
import { FuseAlertType } from '@fuse/components/alert';
import { AuthService } from 'app/core/auth/auth.service';
import { User } from 'app/modules/landing/profile/models/user';
import { UserProfileService } from 'app/modules/landing/profile/services/user-profile.service';
import { GetOauthIssuerIdService } from './services/get-oauth-issuer-id.service';
import { GetUserInfoService } from './services/get-user-info.service';

import { QuizService } from 'app/modules/landing/quiz-activity/services/quiz.service';
import { environment } from 'environments/environment';
import { filter, pairwise } from 'rxjs';
import { LocationService } from './services/location.service';
import { TranslateService } from '@ngx-translate/core';

@Component({
    selector: 'Login',
    templateUrl: './Login.component.html',
    styleUrls: ['./Login.component.scss'],
    encapsulation: ViewEncapsulation.None,
    animations: fuseAnimations,
})
export class LoginComponent {
    @ViewChild('LogInNgForm') LogInNgForm: NgForm;
    user: User = {} as User;

    alert: { type: FuseAlertType; message: string } = {
        type: 'success',
        message: 'failed',
    };

    logInForm: UntypedFormGroup;
    showAlert: boolean = false;
    savedMail: string = '';
    savedPassword: string = '';
    prev: string = '';
    userID: string;
    quizId: string = '';
    token;

    constructor(
        private _formBuilder: UntypedFormBuilder,
        private _authService: AuthService,
        private router: ActivatedRoute,
        private _router: Router,
        private getUserInfo: GetUserInfoService,
        private _oAuthIssuerID: GetOauthIssuerIdService,
        private _quiz: QuizService,
        private _location: LocationService,
        private profileApi: UserProfileService,
        private translateService: TranslateService
    ) {}

    ngOnInit() {
        this.logInForm = this._formBuilder.group({
            username: [
                '',
                Validators.compose([
                    Validators.required,
                    Validators.pattern(
                        '^[a-z0-9._%+-]+@[a-z0-9.-]+.[a-z]{2,4}$'
                    ),
                ]),
            ],
            password: ['', Validators.required],
            service: 'moodle_mobile_app',
        });
        if (localStorage.hasOwnProperty('vumi-mail')) {
            this.savedMail = localStorage.getItem('vumi-mail');
        }
        if (localStorage.hasOwnProperty('tmp-session')) {
            this.savedPassword = this.decryptPassword(
                localStorage.getItem('tmp-session')
            );
        }
        this.logInForm.patchValue({
            username: this.savedMail,
            password: this.savedPassword,
        });

        this._router.events
            .pipe(
                filter((evt: any) => evt instanceof RoutesRecognized),
                pairwise()
            )
            .subscribe((events: RoutesRecognized[]) => {
                this.prev = events[0].urlAfterRedirects;
                localStorage.setItem('prev', this.prev);
            });

        let wstoken = environment.wstoken;

        let token: any = wstoken;
        this.token = token;
        let id = localStorage.getItem('prev') || '';
        let id2 = id.split('/').pop();

        this._quiz.getQuiz(token, id2).subscribe((res: any) => {
            if (res.quizzed) {
                if (res.quizzes[0]) {
                    if (res.quizzes[0].id) {
                        this.quizId = res.quizzes[0].id;
                    }
                }
            }
        });
    }

    showOptions(event: any) {
        if (event.checked) {
            let tmpMail = this.logInForm.get('username').value;
            let tmpPassword = this.logInForm.get('password').value;
            localStorage.setItem('vumi-mail', tmpMail);

            let encryptedPass = this.encryptPassword(tmpPassword);
            localStorage.setItem('tmp-session', encryptedPass);
        }
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

    LogIn(): void {
        if (this.logInForm.invalid) {
            return;
        }

        this.logInForm.disable();
        this.showAlert = false;
        this._authService.logIn(this.logInForm.value).subscribe(
            (successRes: any) => {
                localStorage.setItem('auth-token', successRes.token);
                localStorage.removeItem('ID');
                localStorage.removeItem('activityId');
                localStorage.removeItem('moduleName');
                localStorage.removeItem('moduleType');
                this.getUserInfo.getUserInfo().subscribe((successRes: any) => {
                    this.userID = successRes.id;
                    let userMail = successRes.email;
                    let userName =
                        successRes.firstname + ' ' + successRes.lastname;
                    let firstName = successRes.firstname;
                    localStorage.setItem('user-id', this.userID);
                    localStorage.setItem('user-mail', userMail);
                    localStorage.setItem('user-fullname', userName);
                    localStorage.setItem('user-firstname', firstName);

                    const userTimezone =
                        Intl.DateTimeFormat().resolvedOptions().timeZone;
                    localStorage.setItem('TimeZone', userTimezone);

                    let usersID = localStorage.getItem('user-id');

                    this._location
                        .getLocation(usersID, userTimezone)
                        .subscribe((response) => {});

                    this.profileApi
                        .getUserProfileData()
                        .subscribe((response: any) => {
                            if (this.isProfileDatacomplete(response) == false) {
                                localStorage.setItem('profile-status: ', '0');

                                this._router.navigate(['profile']);
                            } else {
                                localStorage.setItem(
                                    'profile-status: ',
                                    '88924'
                                );
                                let prev = localStorage.getItem('prev') || '';
                                if (
                                    prev.includes('course') &&
                                    localStorage.getItem(
                                        'latest-visited-course'
                                    )
                                ) {
                                    this._router.navigate([
                                        'course',
                                        localStorage.getItem(
                                            'latest-visited-course'
                                        ),
                                    ]);
                                } else {
                                    this._router.navigate(['dashboard']);
                                }
                            }
                        });
                });
            },
            (errorRes: any) => {
                this.logInForm.enable();

                this.alert = {
                    type: 'error',
                    message:  this.translateService.instant("*Wrong Email or Password")
                };

                this.showAlert = true;
                setTimeout(()=>{ this.showAlert = false; }, 2500);
            }
        );
    }

    navigateToSignUp() {
        this._router.navigate(['signup']);
    }

    isProfileDatacomplete(res: any) {
        if (res[0]) {
            let userData: any = res[0];

            if (userData.username) this.user.username = userData.username;
            else this.user.username = '';

            if (userData.firstname) this.user.firstName = userData.firstname;
            else this.user.firstName = '';

            if (userData.lastname) this.user.lastName = userData.lastname;
            else this.user.lastName = '';

            if (userData.address) this.user.address = userData.address;
            else this.user.address = '';

            if (userData.phone1) this.user.phone1 = userData.phone1;
            if (userData.phone2) this.user.phone2 = userData.phone2;

            if (userData.country) this.user.country = userData.country;
            else this.user.country = '';

            if (userData.city) this.user.city = userData.city;
            else this.user.city = '';

            if (userData.timezone) this.user.timezone = userData.timezone;
            else this.user.timezone = '';

            if (userData.customfields) {
                let customeFields: any[] = userData.customfields;
                if (this.user.organization) {
                    this.user.organization = customeFields.find(
                        (prop) => prop.name == 'Organization'
                    ).value;
                }
                this.user.designation = customeFields.find(
                    (prop) => prop.name == 'Designation'
                ).value;
                this.user.phone = customeFields.find(
                    (prop) => prop.name == 'Phone number'
                ).value;
            }
            for (const key in this.user) {
                if (Object.prototype.hasOwnProperty.call(this.user, key)) {
                    if (this.user[key].length == 0) {
                        return false;
                    }
                }
                // return false;
            }
            return true;
        } else return false;
    }

    encryptPassword(password: string) {
        let newPass: string =
            'c7$b?*d8@gdx!!d@z8g6ddz5y' + password + 'zHd2c?e^ns@g#*9h';
        return newPass;
    }

    decryptPassword(password: string) {
        let newPass: string = password.substring(25); //remove first few
        newPass = newPass.slice(0, -16); //remove last few
        return newPass;
    }
}
