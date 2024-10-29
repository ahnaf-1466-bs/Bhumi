import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Router } from '@angular/router';
import { environment } from 'environments/environment';
import { Observable, of, switchMap } from 'rxjs';


@Injectable({
  providedIn: 'root'
})
export class GetCertificateService {

  constructor(
    private http: HttpClient,
    private router: Router
  ) {}

  getCertificate(cID, uID):Observable<any>{
    
    let wstokenVal = localStorage.getItem("auth-token");

    let formData = new FormData();
    formData.append('wsfunction', 'vumi_webservicesuit_certificate_url_by_courseid_userid');
    formData.append('wstoken', wstokenVal);
    formData.append('moodlewsrestformat', 'json');
    formData.append('courseid', cID);      
    formData.append('userid',  uID);                    
                     
   

    return this.http.post(`${environment.baseURL}/webservice/rest/server.php`, formData).pipe(
      switchMap((response: any) => {
              return of(response);
      })
  );
 }
}
