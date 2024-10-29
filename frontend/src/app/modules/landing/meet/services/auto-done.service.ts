import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Router } from '@angular/router';
import { environment } from 'environments/environment';
import { Observable, of, switchMap } from 'rxjs';


@Injectable({
  providedIn: 'root'
})
export class AutoDoneService {

  constructor(
    private http: HttpClient,
    private router: Router
  ) {}

  autoDone(cmId):Observable<any>{
        let formData = new FormData();
        formData.append('wsfunction', 'core_completion_update_activity_completion_status_manually');
        formData.append('wstoken', localStorage.getItem('auth-token'));
        formData.append('moodlewsrestformat', 'json');
        formData.append('cmid', cmId);
        formData.append('completed', "1");                    
        return this.http.post(`${environment.baseURL}/webservice/rest/server.php`, formData).pipe(
          switchMap((response: any) => {
                  return of(response);
          })
        );
     
  }

}
