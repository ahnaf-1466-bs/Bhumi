import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Router } from '@angular/router';
import { environment } from 'environments/environment';
import { Observable, of, switchMap } from 'rxjs';

@Injectable({
    providedIn: 'root',
})
export class AboutPageApiService {
    constructor(private http: HttpClient, private _router: Router) {}

    getAboutPageData(): Observable<any> {
        let wstokenVal = environment.wstoken;

        let formData = new FormData();
        formData.append('wsfunction', 'local_dcms_get_aboutpage_contents');
        formData.append('wstoken', wstokenVal);
        formData.append('moodlewsrestformat', 'json');

        return this.http
            .post(`${environment.baseURL}/webservice/rest/server.php`, formData)
            .pipe(
                switchMap((response: any) => {
                    return of(response);
                })
            );
    }
}
