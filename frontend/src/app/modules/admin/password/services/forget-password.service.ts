import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Router } from '@angular/router';
import { environment } from 'environments/environment';
import { Observable, of, switchMap } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class ForgetPasswordService {

  constructor(
    private http: HttpClient,
    private router: Router
  ) {}

  reqPassReset(username:string):Observable<any>{
    
      let wstokenVal = environment.wstoken;

    

      let formData = new FormData();

      formData.append('username', username);
      formData.append('wsfunction', 'auth_vumi_auth_request_password_reset');
      formData.append('wstoken', wstokenVal);
      formData.append('moodlewsrestformat', 'json');
                         


      return this.http.post(`${environment.baseURL}/webservice/rest/server.php`, formData).pipe(
        switchMap((response: any) => {
                return of(response);
        })
    );
       
  }

}
