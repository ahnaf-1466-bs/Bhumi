import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Router } from '@angular/router';
import { environment } from 'environments/environment';
import { Observable, of, switchMap } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class SearchCourseService {

  constructor(
    private http: HttpClient,
    private router: Router
  ) {}

  filterCourses(searchedCourse:string):Observable<any>{
    
    

    let formData = new FormData();
    formData.append('wsfunction', 'core_course_search_courses');
    formData.append('wstoken', environment.wstoken );
    formData.append('moodlewsrestformat', 'json');
    formData.append('criterianame', 'search');                   
    formData.append('criteriavalue', searchedCourse);                  
    

    return this.http.post(`${environment.baseURL}/webservice/rest/server.php`, formData).pipe(
      switchMap((response: any) => {
              return of(response);
      })
  );
     
  }
}
