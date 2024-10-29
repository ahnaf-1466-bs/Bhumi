import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Router } from '@angular/router';
import { environment } from 'environments/environment';
import { Observable, of, switchMap } from 'rxjs';


@Injectable({
  providedIn: 'root'
})
export class MeetingInfoService {

  constructor(
    private http: HttpClient,
    private router: Router
  ) {}

  meetingInfo(zoomID):Observable<any>{
    
  
    let formData = new FormData();
    formData.append('wsfunction', 'vumi_webservicesuit_zoom_get_state');
    formData.append('wstoken', localStorage.getItem('auth-token'));
    formData.append('moodlewsrestformat', 'json');
    formData.append('zoomid', zoomID);
                        
    return this.http.post(`${environment.baseURL}/webservice/rest/server.php`, formData).pipe(
      switchMap((response: any) => {
              return of(response);
      })
  );
     
  }

}
