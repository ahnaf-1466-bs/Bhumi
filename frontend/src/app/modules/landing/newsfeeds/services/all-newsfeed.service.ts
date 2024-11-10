import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { environment } from 'environments/environment';
import { Observable, of, switchMap } from 'rxjs';

@Injectable({
    providedIn: 'root',
})
export class AllNewsfeedApiService {
    constructor(private http: HttpClient) {}

    getAllNewsFeedData(): Observable<any> {
        let wstokenVal = environment.wstoken;
        let formData = new FormData();

        formData.append('wsfunction', 'local_newsfeed_view_full_newsfeed');
        formData.append('wstoken', wstokenVal);
        formData.append('moodlewsrestformat', 'json');
        formData.append('startdate', '2021-02-04');
        formData.append('enddate', '');

        return this.http
            .post(`${environment.baseURL}/webservice/rest/server.php`, formData)
            .pipe(
                switchMap((response: any) => {
                    return of(response);
                })
            );
    }
}
