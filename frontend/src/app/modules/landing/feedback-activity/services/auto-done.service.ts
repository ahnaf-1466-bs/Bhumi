import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Router } from '@angular/router';
import { environment } from 'environments/environment';
import { Observable, of, switchMap } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class AutoDoneService{

  constructor(
    private http: HttpClient,
    private router: Router
  ) {}

  autoDone(resourceID):Observable<any>{
    
    let wstokenVal = localStorage.getItem("auth-token");

    let formData = new FormData();
    formData.append('wsfunction', 'mod_coursefeedback_view_coursefeedback');
    formData.append('wstoken', wstokenVal);
    formData.append('moodlewsrestformat', 'json');
    formData.append('coursefeedbackid', resourceID);     
                       
                     
  

    return this.http.post(`${environment.baseURL}/webservice/rest/server.php`, formData).pipe(
      switchMap((response: any) => {
              return of(response);
      })
  );
     
}
}
