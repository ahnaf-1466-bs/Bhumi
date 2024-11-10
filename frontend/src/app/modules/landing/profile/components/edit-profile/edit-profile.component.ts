import { HttpClient } from '@angular/common/http';
import { Component, ViewChild } from '@angular/core';
import { NgForm, UntypedFormGroup } from '@angular/forms';
import { MatDialog } from '@angular/material/dialog';
import { Router } from '@angular/router';
import { AuthService } from 'app/core/auth/auth.service';
import { User } from '../../models/user';
import { UserProfileService } from '../../services/user-profile.service';
import { UpdateSuccessfulModalComponent } from './../update-successful-modal/update-successful-modal.component';

@Component({
    selector: 'app-edit-profile',
    templateUrl: './edit-profile.component.html',
    styleUrls: ['./edit-profile.component.scss'],
})
export class EditProfileComponent {
    @ViewChild('UpdateProfileForm') UpdateProfileForm: NgForm;
    showPassword: boolean = false;
    showConfirmPassword: boolean = false;
    loading: boolean = true;
    user: User = {} as User;
    formInfo: UntypedFormGroup;
    updateSuccess: boolean = false;
    submitBtnLoader: boolean = false;

    countries: any[] = [];

    constructor(
        private _router: Router,
        private http: HttpClient,
        private dialog: MatDialog,
        private _authService: AuthService,
        private profileApi: UserProfileService
    ) {}

    ngOnInit() {
        this.http.get<any[]>('/assets/countries.json').subscribe((data) => {
            this.countries = data;
        });

        this.profileApi.getUserProfileData().subscribe((res: any) => {
            if (res.errorcode || res.exception || res.length == 0) {
                this._authService.signOut();
                this._router.navigate(['login']);
                return;
            }

            this.loading = false;
            let cnt: any = 0;

            if (res[0]) {
                let userData: any = res[0];
                if (userData.username) {
                    this.user.username = userData.username;
                    cnt++;
                }
                if (userData.firstname) {
                    this.user.firstName = userData.firstname;
                    cnt++;
                }
                if (userData.lastname) {
                    this.user.lastName = userData.lastname;
                    cnt++;
                }

                if (userData.address) {
                    this.user.address = userData.address;
                    cnt++;
                } else this.user.address = '';

                if (userData.phone1) {
                    this.user.phone1 = userData.phone1;
                    cnt++;
                } else this.user.phone1 = '';

                if (userData.phone2) {
                    this.user.phone2 = userData.phone2;
                    cnt++;
                } else this.user.phone2 = '';

                if (userData.country) {
                    this.user.country = userData.country;
                    cnt++;
                } else this.user.country = '';

                if (userData.city) {
                    this.user.city = userData.city;
                    cnt++;
                } else this.user.city = '';

                this.user.timezone = localStorage.getItem('TimeZone');

                if (userData.customfields) {
                    let customeFields: any[] = userData.customfields;

                    let organization: any = customeFields.find(
                        (prop) => prop.name == 'Organization'
                    );
                    if (organization) {
                        this.user.organization = organization.value;
                    } else this.user.organization = '';

                    let designation: any = customeFields.find(
                        (prop) => prop.name == 'Designation'
                    );
                    if (designation) {
                        this.user.designation = designation.value;
                    } else this.user.designation = '';

                    let phone: any = customeFields.find(
                        (prop) => prop.name == 'Phone number'
                    );
                    if (phone) {
                        this.user.phone = phone.value;
                    } else this.user.phone = '';
                }

                if (this.user.phone == null || this.user.phone == undefined)
                    this.user.phone = '';
                if (
                    this.user.designation == null ||
                    this.user.designation == undefined
                )
                    this.user.designation = '';
                if (
                    this.user.organization == null ||
                    this.user.organization == undefined
                )
                    this.user.organization = '';
                if (cnt >= 8) {
                    this.updateSuccess = undefined;
                } else {
                    this.updateSuccess = false;
                }
            }
        });
    }

    updateProfile() {
        this.submitBtnLoader = true;
        this.user.email = this.user.username;
        this.user.phone1 = this.user.phone;
        this.user.phone2 = this.user.phone;

        this.profileApi.updateUserProfileData(this.user).subscribe(
            (res: any) => {
                this.submitBtnLoader = false;

                if (
                    res.warnings &&
                    res.warnings[0] &&
                    res.warnings[0].warningcode == 'usernotupdateddeleted'
                ) {
                    //user deleted
                    this._authService.signOut();
                    this._router.navigate(['login']);
                    return;
                } else if (!res.exception) {
                    //successfully profile updated
                    localStorage.setItem('profile-status: ', '88924');
                    window.scroll(0, 0);
                    this.updateSuccess = true;
                    localStorage.setItem(
                        'user-firstname',
                        this.user?.firstName
                    );
                    const dialogRef = this.dialog.open(
                        UpdateSuccessfulModalComponent
                    );
                } else {
                    localStorage.setItem('profile-status: ', '0');
                    setTimeout(() => {
                        this.updateSuccess = false;
                        window.scroll(0, 0);
                    }, 1000);
                }
            },
            (err: any) => {
                this.submitBtnLoader = false;
                localStorage.setItem('profile-status: ', '0');
                setTimeout(() => {
                    this.updateSuccess = false;
                    window.scroll(0, 0);
                }, 1000);
            }
        );
    }

    isFormValidated(): boolean {
        if (!this.user.firstName || this.user.firstName == '') return false;
        if (!this.user.lastName || this.user.lastName == '') return false;
        if (
            !this.user.phone ||
            this.user.phone == '' ||
            this.user.phone.length < 11
        )
            return false;
        if (!this.user.designation || this.user.designation == '') return false;
        if (!this.user.country || this.user.country == '') return false;
        if (!this.user.city || this.user.city == '') return false;
        if (!this.user.address || this.user.address == '') return false;
        return true;
    }
}
