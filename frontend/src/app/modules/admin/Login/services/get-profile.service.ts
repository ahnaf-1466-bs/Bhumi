import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Router } from '@angular/router';
import { environment } from 'environments/environment';
import { Observable, of, switchMap } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class GetProfileService {

  constructor(
    private http: HttpClient,
    private router: Router
  ) {}

  getProfileData(email:string):Observable<any>{
      let wstokenVal = environment.wstoken;

      let formData = new FormData();

      
      formData.append('wsfunction', 'core_user_get_users_by_field');
      formData.append('wstoken', wstokenVal);
      formData.append('moodlewsrestformat', 'json');
      formData.append('field', 'email');
      formData.append('values[0]', email);
                         
                       
     

      return this.http.post(`${environment.baseURL}/webservice/rest/server.php`, formData).pipe(
        switchMap((response: any) => {
                return of(response);
        })
    );
       
  }

}
