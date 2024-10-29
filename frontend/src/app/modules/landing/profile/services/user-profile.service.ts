import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { environment } from 'environments/environment';
import { Observable, of, switchMap } from 'rxjs';
import { User } from '../models/user';

@Injectable({
  providedIn: 'root'
})
export class UserProfileService {

  constructor(
    private http: HttpClient,

  ) {}

  getUserProfileData():Observable<any>{
      
    let wstokenVal = environment.globalToken;

    let formData = new FormData();
    formData.append('wsfunction', 'core_user_get_users_by_field');
    formData.append('wstoken', wstokenVal);
    formData.append('moodlewsrestformat', 'json');
    formData.append('field', 'email');
    formData.append('values[0]', localStorage.getItem('user-mail'));
                        
    return this.http.post(`${environment.baseURL}/webservice/rest/server.php`, formData).pipe(
      switchMap((response: any) => {
              return of(response);
      })
    );
  }

  updateUserProfileData(user:User):Observable<any>{
    let wstokenVal = environment.wstoken;
    let fullName = user.firstName +" " + user.lastName;

    let formData = new FormData();
    formData.append('wsfunction', 'core_user_update_users');
    formData.append('wstoken', wstokenVal);
    formData.append('moodlewsrestformat', 'json');
    formData.append('users[0][id]', localStorage.getItem('user-id') );
    formData.append('users[0][firstname]', user.firstName );
    formData.append('users[0][lastname]', user.lastName );
    formData.append('users[0][phone1]', user.phone1 );
    formData.append('users[0][phone2]', user.phone2 );
    formData.append('users[0][country]', user.country );
    formData.append('users[0][city]', user.city );
    formData.append('users[0][address]', user.address );
    formData.append('users[0][timezone]', user.timezone );
    
    formData.append('users[0][customfields][0][type]', 'designation' );
    formData.append('users[0][customfields][0][value]', user.designation );

    formData.append('users[0][customfields][1][type]', 'organization' );
    formData.append('users[0][customfields][1][value]', user.organization );

    formData.append('users[0][customfields][2][type]', 'phone_number' );
    formData.append('users[0][customfields][2][value]', user.phone );


    
                        
    return this.http.post(`${environment.baseURL}/webservice/rest/server.php`, formData).pipe(
      switchMap((response: any) => {
              return of(response);
      })
    );
  }


}
