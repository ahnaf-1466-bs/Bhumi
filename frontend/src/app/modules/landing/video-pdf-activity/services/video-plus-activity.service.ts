import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { environment } from 'environments/environment';
import { of, switchMap } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class VideoPlusActivityService {

  constructor(
    private http: HttpClient,
  ) { }

  getVideoPlusDetails(courseId:string,cmid:string)
  {
    const wstokenVal = localStorage.getItem("auth-token");

    let formData = new FormData();
    formData.append('wstoken', wstokenVal);
    formData.append('wsfunction', 'mod_videoplus_get_details');
    formData.append('moodlewsrestformat', 'json');
    formData.append('courseid', courseId);      
    formData.append('cmid',  cmid);                    
                     
   

    return this.http.post(`${environment.baseURL}/webservice/rest/server.php`, formData).pipe(
      switchMap((response: any) => {
              return of(response);
      })
  );
  }
}
