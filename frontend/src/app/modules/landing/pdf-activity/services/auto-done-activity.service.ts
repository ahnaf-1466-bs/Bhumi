import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Router } from '@angular/router';
import { environment } from 'environments/environment';
import { Observable, of, switchMap } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class AutoDoneActivityService{

  constructor(
    private http: HttpClient,
    private router: Router
  ) {}

  autoDone(resourceID):Observable<any>{
    let wstokenVal = localStorage.getItem("auth-token");

    let formData = new FormData();
    formData.append('wsfunction', 'mod_resource_view_resource');
    formData.append('wstoken', wstokenVal);
    formData.append('moodlewsrestformat', 'json');
    formData.append('resourceid', resourceID);     
                       
                     
  

    return this.http.post(`${environment.baseURL}/webservice/rest/server.php`, formData).pipe(
      switchMap((response: any) => {
              return of(response);
      })
  );
  
}

  autoDoneVideoPlus(resourceID):Observable<any>{
    let wstokenVal = localStorage.getItem("auth-token");

    let formData = new FormData();
    formData.append('wsfunction', 'mod_videoplus_view_videoplus');
    formData.append('wstoken', wstokenVal);
    formData.append('moodlewsrestformat', 'json');
    formData.append('videoplusid', resourceID);     
                       
                     
  

    return this.http.post(`${environment.baseURL}/webservice/rest/server.php`, formData).pipe(
      switchMap((response: any) => {
              return of(response);
      })
  );
  
}
}
