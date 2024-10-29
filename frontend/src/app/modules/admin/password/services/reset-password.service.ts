import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Router } from '@angular/router';
import { environment } from 'environments/environment';
import { Observable, of, switchMap } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class ResetPasswordService {

  constructor(
    private http: HttpClient,
    private router: Router
  ) {}

  updatePassword(info:any):Observable<any>{
   
      let wstokenVal = environment.wstoken;

      let formData = new FormData();
      formData.append('wsfunction', 'auth_vumi_auth_update_password_after_validation');
      formData.append('wstoken', wstokenVal);
      formData.append('moodlewsrestformat', 'json');
      formData.append("email", info.email);
      formData.append("token", info.token);
      formData.append("password", info.password);
      
     
      return this.http.post(`${environment.baseURL}/webservice/rest/server.php`, formData).pipe(
          switchMap((response: any) => {
              
                  return of(response);
      })
  );
     
}
}
