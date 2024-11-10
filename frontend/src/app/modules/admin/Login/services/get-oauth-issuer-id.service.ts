import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Router } from '@angular/router';
import { environment } from 'environments/environment';
import { Observable, of, switchMap } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class GetOauthIssuerIdService {

  constructor(
    private http: HttpClient,
    private router: Router
  ) {}

  getOAuth2IssuerID(address:string):Observable<any>{
      let wstokenVal = environment.wstoken;

      let formData = new FormData();

      
      formData.append('wsfunction', 'auth_vumi_login_with_issuers');
      formData.append('wstoken', wstokenVal);
      formData.append('moodlewsrestformat', 'json');
      formData.append('oauth2issuer', address);
     
                        

      return this.http.post(`${environment.baseURL}/webservice/rest/server.php`, formData).pipe(
        switchMap((response: any) => {
                return of(response);
        })
    );
  }
       
}
