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
export class EnrolledCourseService {

  constructor(
    private http: HttpClient,
    private _router: Router
  ) {}

  getEnrolledCourses():Observable<any>{
      
    
    let formData = new FormData();
    formData.append('wsfunction', 'core_enrol_get_users_courses');
    formData.append('wstoken', localStorage.getItem("auth-token"));
    
    formData.append('moodlewsrestformat', 'json');
    formData.append('userid', localStorage.getItem("user-id"));

    return this.http.post(`${environment.baseURL}/webservice/rest/server.php`, formData).pipe(
      switchMap((response: any) => {
              return of(response);
      })
    );
  }
}
