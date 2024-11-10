import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Router } from '@angular/router';
import { environment } from 'environments/environment';
import { Observable, of, switchMap } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class UpdateActivityStatusService {

  constructor(
    private http: HttpClient,
    private router: Router
  ) {}

  updateActivityStatus(reqBody):Observable<any>{
    
    let wstokenVal = localStorage.getItem("auth-token");

    let formData = new FormData();
    formData.append('wsfunction', 'core_completion_update_activity_completion_status_manually');
    formData.append('wstoken', wstokenVal);
    formData.append('moodlewsrestformat', 'json');
    formData.append('cmid', reqBody.cmid);      
    formData.append('completed', reqBody.completed);                    
                     

    return this.http.post(`${environment.baseURL}/webservice/rest/server.php`, formData).pipe(
      switchMap((response: any) => {
              return of(response);
      })
  );
     
}
}
