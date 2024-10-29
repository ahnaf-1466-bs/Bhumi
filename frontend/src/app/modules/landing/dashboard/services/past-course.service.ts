import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { environment } from 'environments/environment';
import { Observable, of, switchMap } from 'rxjs';

@Injectable({
    providedIn: 'root',
})
export class PastCourseService {
    constructor(private http: HttpClient) {}

    getDetails(body): Observable<any> {
        let wstoken = environment.wstoken;
        let formData = new FormData();
        formData.append('wstoken', wstoken);
        formData.append('moodlewsrestformat', 'json');

        for (let i = 0; i < body.length; i++) {
            for (var j in body[i]) {
                formData.append(j, body[i][j]);
            }
        }

        // console.log('FORM DATA', formData);

        return this.http
            .post(`${environment.baseURL}/webservice/rest/server.php`, formData)
            .pipe(
                switchMap((response: any) => {
                    return of(response);
                })
            );
    }
}
