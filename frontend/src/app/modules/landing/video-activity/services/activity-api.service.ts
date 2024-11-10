import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Router } from '@angular/router';
import { environment } from 'environments/environment';
import { Observable, of, switchMap } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class ActivityApiService{

  constructor(
    private http: HttpClient,
    private router: Router
  ) {}

  getActivities(courseID):Observable<any>{
    
    let wstokenVal = localStorage.getItem("auth-token");

    let formData = new FormData();
    formData.append('wsfunction', 'core_course_get_contents');
    formData.append('wstoken', wstokenVal);
    formData.append('moodlewsrestformat', 'json');
    formData.append('courseid', courseID);      //currently static later it will be passed from course details page
                       
                     
   

    return this.http.post(`${environment.baseURL}/webservice/rest/server.php`, formData).pipe(
      switchMap((response: any) => {
              return of(response);
      })
  );
}

getBengaliDetailsActivity(activityID): Observable<any> {
  let wstokenVal = localStorage.getItem("auth-token");

  let formData = new FormData();
  formData.append('wsfunction', 'modcustomfields_details_by_courseid_and_cmid');
  formData.append('wstoken', wstokenVal);
  formData.append('moodlewsrestformat', 'json');
  formData.append('cmid', activityID);

  return this.http
      .post(`${environment.baseURL}/webservice/rest/server.php`, formData)
      .pipe(
          switchMap((response: any) => {
              return of(response);
          })
      );
}
}
