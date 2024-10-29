import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Router } from '@angular/router';
import { environment } from 'environments/environment';
import { Observable, of, switchMap } from 'rxjs';

@Injectable({
    providedIn: 'root',
})
export class UpcomingCourseService {
    constructor(private http: HttpClient, private _router: Router) {}

    getUpcomingCourses(): Observable<any> {
        let wstokenValue = environment.wstoken;
        let formData = new FormData();

        formData.append('wsfunction', 'bs_webservicesuite_get_future_courses');
        formData.append('wstoken', wstokenValue);
        // formData.append('wstoken', localStorage.getItem("auth-token"));
        formData.append('moodlewsrestformat', 'json');

        return this.http
            .post(`${environment.baseURL}/webservice/rest/server.php`, formData)
            .pipe(
                switchMap((response: any) => {
                    return of(response);
                })
            );
    }

    getRecommendedCourses(): Observable<any> {
        let formData = new FormData();
        formData.append(
            'wsfunction',
            'bs_webservicesuite_get_recommended_courses'
        );
        formData.append('wstoken', localStorage.getItem('auth-token'));
        formData.append('moodlewsrestformat', 'json');
        formData.append('userid', localStorage.getItem('user-id'));

        return this.http
            .post(`${environment.baseURL}/webservice/rest/server.php`, formData)
            .pipe(
                switchMap((response: any) => {
                    return of(response);
                })
            );
    }

    getFavouriteCourses(): Observable<any> {
        let wstokenVal = environment.wstoken;

        let formData = new FormData();
        formData.append(
            'wsfunction',
            'block_starredcourses_get_starred_courses'
        );
        formData.append('wstoken', localStorage.getItem('auth-token'));
        formData.append('moodlewsrestformat', 'json');
        formData.append('limit', '0');
        formData.append('offset', '0');

        return this.http
            .post(`${environment.baseURL}/webservice/rest/server.php`, formData)
            .pipe(
                switchMap((response: any) => {
                    return of(response);
                })
            );
    }

    setFavourite(courseID, favouriteValue): Observable<any> {
        let wstokenVal = environment.wstoken;

        let formData = new FormData();
        formData.append('wsfunction', 'core_course_set_favourite_courses');
        formData.append('wstoken', localStorage.getItem('auth-token'));
        formData.append('moodlewsrestformat', 'json');
        formData.append('courses[0][id]', courseID);
        formData.append('courses[0][favourite]', favouriteValue);

        return this.http
            .post(`${environment.baseURL}/webservice/rest/server.php`, formData)
            .pipe(
                switchMap((response: any) => {
                    return of(response);
                })
            );
    }
}
