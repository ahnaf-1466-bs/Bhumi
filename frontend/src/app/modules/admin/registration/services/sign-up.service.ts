import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Router } from '@angular/router';
import { environment } from 'environments/environment';
import { Observable, of, switchMap } from 'rxjs';
import { User } from '../models/user';

@Injectable({
  providedIn: 'root'
})
export class SignUpService {

  constructor(
    private http: HttpClient,
    private router: Router
  ) {}

  register(user:User):Observable<any>{
    
      let wstokenVal = environment.wstoken;

      let reqPayload = {} as any;
      let cf0type:string = "customprofilefields[0][type]";

      let formData = new FormData();

                          formData.append('username', user.email)
                          formData.append('password' , user.password)
                          formData.append('firstname', user.fName)
                          formData.append('lastname', user.lName)
                          formData.append('email', user.email)
                          formData.append('wsfunction', 'auth_vumi_auth_signup_user')
                          formData.append('wstoken', wstokenVal)
                          formData.append('moodlewsrestformat', 'json'),
                          formData.append('customprofilefields[0][type]', 'text'),
                          formData.append('customprofilefields[0][name]', 'profile_field_designation')
                          formData.append('customprofilefields[0][value]', user.designation)
                          formData.append('customprofilefields[1][type]', 'text')
                          formData.append('customprofilefields[1][name]', 'profile_field_organization')
                          formData.append('customprofilefields[1][value]', user.organization)
                          formData.append('customprofilefields[2][type]', 'text')
                          formData.append('customprofilefields[2][name]', 'profile_field_phone_number')
                          formData.append('customprofilefields[2][value]', user.phone);
                       
     

      return this.http.post(`${environment.baseURL}/webservice/rest/server.php`, formData).pipe(
        switchMap((response: any) => {
                return of(response);
        })
      );
       
  }
}
