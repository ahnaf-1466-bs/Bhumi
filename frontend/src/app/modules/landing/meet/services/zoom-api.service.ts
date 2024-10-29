import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Router } from '@angular/router';
import { environment } from 'environments/environment';
import { Observable, of, switchMap } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class ZoomApiService {

  constructor(
    private http: HttpClient,
    private router: Router
  ) {}

  joinMeet(zoomID):Observable<any>{
    
   

    let formData = new FormData();
    formData.append('wsfunction', 'mod_zoom_grade_item_update');
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
