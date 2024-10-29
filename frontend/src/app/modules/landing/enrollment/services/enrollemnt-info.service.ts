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
export class EnrollemntInfoService {

  constructor(
    private http: HttpClient,
    private _router: Router,
   
  ) {}

  getEnrolledCourseInfo(courseID):Observable<any>{
      
    
    let formData = new FormData();
    formData.append('wsfunction', 'local_discount_find_coupon_by_userid');
    formData.append('wstoken', localStorage.getItem("auth-token"));
    formData.append('moodlewsrestformat', 'json');
    formData.append('userid', localStorage.getItem("user-id"));
    formData.append('courseid', courseID);
                        
    return this.http.post(`${environment.baseURL}/webservice/rest/server.php`, formData).pipe(
      switchMap((response: any) => {
              return of(response);
      })
    );
  }
}
