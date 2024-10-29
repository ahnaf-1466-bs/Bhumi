import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { environment } from 'environments/environment';
import { Observable, of, switchMap } from 'rxjs';

@Injectable({
    providedIn: 'root',
})
export class NewsfeedByIdApiService {
    constructor(private http: HttpClient) {}

    getNewsFeedById(id): Observable<any> {
        let wstokenVal = environment.wstoken;
        let formData = new FormData();

        formData.append('wsfunction', 'local_newsfeed_view_newsfeed_by_newsid');
        formData.append('wstoken', wstokenVal);
        formData.append('moodlewsrestformat', 'json');
        formData.append('newsid', id);

        return this.http
            .post(`${environment.baseURL}/webservice/rest/server.php`, formData)
            .pipe(
                switchMap((response: any) => {
                    return of(response);
                })
            );
    }
}
