import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Router } from '@angular/router';
import { environment } from 'environments/environment';
import { Observable, of, switchMap } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class ConfirmUserService {

  constructor(
    private http: HttpClient,
    private router: Router
  ) {}

  confirmUser(info:any):Observable<any>{
    
      let wstokenVal = environment.wstoken;

      let formData = new FormData();

      formData.append('username', info.username);
      formData.append('secret' , info.secret);
      formData.append('wsfunction', 'core_auth_confirm_user');
      formData.append('wstoken', wstokenVal);
      formData.append('moodlewsrestformat', 'json');

      return this.http.post(`${environment.baseURL}/webservice/rest/server.php`, formData).pipe(
        switchMap((response: any) => {
                return of(response);
        })
      );
     
}
}
