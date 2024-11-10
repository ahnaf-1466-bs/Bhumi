import { Component, OnInit } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { UserService } from 'app/core/user/user.service';
import { AuthMockApi } from 'app/mock-api/common/auth/api';
import { GetUserInfoService } from '../services/get-user-info.service';
import { User } from 'app/modules/landing/profile/models/user';
import { UserProfileService } from 'app/modules/landing/profile/services/user-profile.service';


@Component({
    selector: 'app-oauth-login',
    templateUrl: './oauth-login.component.html',
    styleUrls: ['./oauth-login.component.scss'],
})
export class OauthLoginComponent implements OnInit {

    user:User = {} as User;
    
    constructor(
        private acr: ActivatedRoute,
        private _router: Router,
        private _api: AuthMockApi,
        private _user: UserService,
        private getUserInfo: GetUserInfoService,
        private profileApi: UserProfileService
    ) {}
    ngOnInit() {
        this.acr.queryParams.subscribe((res) => {
            if (res.token) {
                localStorage.setItem('userToken', res.token);
                localStorage.setItem('auth-token', res.token);
                localStorage.setItem('accessToken', this._api._generateJWT());
                this._user.setLogin(true);
                this.getUserInfo.getUserInfo().subscribe((successRes: any) => {
                    let userID = successRes.id;
                    let userMail = successRes.email;
                    let userName =
                        successRes.firstname + ' ' + successRes.lastname;
                    let firstName = successRes.firstname;
                    localStorage.setItem('user-id', userID);
                    localStorage.setItem('user-mail', userMail);
                    localStorage.setItem('user-fullname', userName);
                    localStorage.setItem('user-firstname', firstName);

                    this.profileApi.getUserProfileData().subscribe( (response:any)=>{
                         
                        if( this.isProfileDatacomplete(response) == false){
                              localStorage.setItem('profile-status: ', '0');
                            
                              this._router.navigate(['profile/edit']);
                        }
                        else{
                             
                              localStorage.setItem('profile-status: ', '88924');
                              let prev = localStorage.getItem('prev') || '';
                              if(prev.includes('course') &&  localStorage.getItem('latest-visited-course'))
                              {
                                  this._router.navigate(['course', localStorage.getItem('latest-visited-course')]);

                              }
                          
                              else{
                                  this._router.navigate(['dashboard']);
                              }
                        }
                    })
                    //this._router.navigate(['dashboard']);
                })
            }
        });
    }

    isProfileDatacomplete(res:any){
        if(res[0]){
            let userData:any = res[0];

            if(userData.username)this.user.username = userData.username;
            else this.user.username = '';

            if(userData.firstname)this.user.firstName = userData.firstname;
            else this.user.firstName = '';

            if(userData.lastname)this.user.lastName = userData.lastname;
            else this.user.lastName = '';

            if(userData.address)this.user.address = userData.address;
            else this.user.address = '';

            if(userData.phone1)this.user.phone1 = userData.phone1;
            if(userData.phone2)this.user.phone2 = userData.phone2;

            if(userData.country)this.user.country = userData.country;
            else this.user.country = '';

            if(userData.city)this.user.city = userData.city;
            else this.user.city = '';

            if(userData.timezone)this.user.timezone = userData.timezone;
            else this.user.timezone = '';

            if(userData.customfields){
               
                let customeFields:any[] = userData.customfields;
                
                
                let organization:any = customeFields.find( (prop)=>prop.name == "Organization");
                if(organization){
                    this.user.organization = organization.value;
                }
                else this.user.organization = "";
                

                let designation:any = customeFields.find( (prop)=>prop.name == "Designation");
                if(designation){
                    this.user.designation = designation.value;
                }
                else this.user.designation = "";
               

                let phone:any = customeFields.find( (prop)=>prop.name == "Phone number");
                if(phone){
                    this.user.phone = phone.value;
                }
                else this.user.phone = "";
               
            }
            for (const key in this.user) {
                if (Object.prototype.hasOwnProperty.call(this.user, key)) {
                  
                  if(this.user[key] == ""){
                     return false;
                  }
                }
               
            }
            return true;       
        }
        else return false;
    }
}
