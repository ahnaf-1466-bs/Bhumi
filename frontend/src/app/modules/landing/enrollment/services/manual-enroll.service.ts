import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Router } from '@angular/router';
import { environment } from 'environments/environment';
import { Observable, of, switchMap } from 'rxjs';


@Injectable({
  providedIn: 'root'
})

@Injectable({
  providedIn: 'root'
})
export class ManualEnrollService {

  constructor(
    private http: HttpClient,
    private _router: Router,
   
  ) {}

  enrolManual(courseID):Observable<any>{
      
    let wstokenFlobal:string = environment.wstoken;
    let formData = new FormData();
    formData.append('wsfunction', 'enrol_manual_enrol_users');
    formData.append('wstoken', wstokenFlobal);
    formData.append('moodlewsrestformat', 'json');
    formData.append('enrolments[0][roleid]', '5');
    formData.append('enrolments[0][userid]', localStorage.getItem("user-id"));
    formData.append('enrolments[0][courseid]', courseID);
                        
    return this.http.post(`${environment.baseURL}/webservice/rest/server.php`, formData).pipe(
      switchMap((response: any) => {
              return of(response);
      })
    );
  }
}
