import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Router } from '@angular/router';
import { TranslateService } from '@ngx-translate/core';
import { environment } from 'environments/environment';
import { Observable, of, switchMap } from 'rxjs';

@Injectable({
  providedIn: 'root'
})

export class GetCourseDetailsService {

  constructor(
    private http: HttpClient,
    private router: Router,
    private translateService:TranslateService
  ) {}

  getCourseList(lang:any):Observable<any>{
    
        let wstokenVal = environment.globalToken;

        let formData = new FormData();
        formData.append('wsfunction', 'bs_webservicesuite_search_courses_by_lang');
        formData.append('wstoken', wstokenVal);
        formData.append('moodlewsrestformat', 'json');
        formData.append('criterianame', 'search');
        formData.append('criteriavalue', '');
        formData.append('lang', lang);
                            
        return this.http.post(`${environment.baseURL}/webservice/rest/server.php`, formData).pipe(
          switchMap((response: any) => {
                  return of(response);
          })
        );
     
  }

  getAllCoursesList():Observable<any>{
        let wstokenVal = environment.globalToken;

        let formData = new FormData();
        formData.append('wsfunction', 'core_course_get_courses');
        formData.append('wstoken', wstokenVal);
        formData.append('moodlewsrestformat', 'json');
                            
        return this.http.post(`${environment.baseURL}/webservice/rest/server.php`, formData).pipe(
          switchMap((response: any) => {
                  return of(response);
          })
        );
  }

  searchCourses(lang:any, searchedItem:any):Observable<any>{
        let wstokenVal = environment.globalToken;

        let formData = new FormData();
        formData.append('wsfunction', 'bs_webservicesuite_search_courses_by_lang');
        formData.append('wstoken', wstokenVal);
        formData.append('moodlewsrestformat', 'json');
        formData.append('criterianame', 'search');
        // if language is english
        if(this.translateService.getDefaultLang()!=='bn')
        {
          formData.append('criteriavalue', searchedItem);
          formData.append('lang', lang);
        }
        // if language is Bengali
        else{
          formData.append('criteriavalue', '');
          formData.append('cname', searchedItem);
        }
        
        return this.http.post(`${environment.baseURL}/webservice/rest/server.php`, formData).pipe(
          switchMap((response: any) => {
                  return of(response);
          })
        );
  }

  getCourseDetails():Observable<any>{
    
    let wstokenVal = environment.wstoken;

    let formData = new FormData();
    formData.append('wsfunction', 'bs_webservicesuite_get_course_details_with_instructor');
    formData.append('wstoken', wstokenVal);
    formData.append('moodlewsrestformat', 'json');
                        
    return this.http.post(`${environment.baseURL}/webservice/rest/server.php`, formData).pipe(
      switchMap((response: any) => {
              return of(response);
      })
  );
     
  }
}
