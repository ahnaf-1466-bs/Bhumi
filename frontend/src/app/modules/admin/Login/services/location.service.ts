import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { environment } from 'environments/environment';
import { Observable, of, switchMap } from 'rxjs';

@Injectable({ providedIn: 'root' })
export class LocationService {
    constructor(private http: HttpClient) {}
    getLocation(userID: any, timeZone: string): Observable<any> {
        let wstokenVal = environment.wstoken;

        let formData = new FormData();

        formData.append('wsfunction', 'core_user_update_users');
        formData.append('wstoken', wstokenVal);
        formData.append('moodlewsrestformat', 'json');
        formData.append('users[0][id]', userID);
        formData.append('users[0][timezone]', timeZone);

        return this.http
            .post(`${environment.baseURL}/webservice/rest/server.php`, formData)
            .pipe(
                switchMap((response: any) => {
                    return of(response);
                })
            );
    }
}
