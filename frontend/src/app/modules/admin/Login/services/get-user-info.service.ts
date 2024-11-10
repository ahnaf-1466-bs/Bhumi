import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Router } from '@angular/router';
import { environment } from 'environments/environment';
import { Observable, of, switchMap } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class GetUserInfoService {

  constructor(
    private http: HttpClient,
    private router: Router
  ) {}

  getUserInfo(){
    let wstokenVal = localStorage.getItem("auth-token");

    let formData = new FormData();
    formData.append('wsfunction', 'auth_vumi_user_info_by_token');
    formData.append('wstoken', wstokenVal);
    formData.append('token', wstokenVal);
    formData.append('moodlewsrestformat', 'json');
                    
                     
  

    return this.http.post(`${environment.baseURL}/webservice/rest/server.php`, formData).pipe(
      switchMap((response: any) => {
              return of(response);
      })
  );
  }
}
