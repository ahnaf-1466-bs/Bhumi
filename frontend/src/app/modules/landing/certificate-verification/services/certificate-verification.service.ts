import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { environment } from 'environments/environment';
import { Observable, of, switchMap } from 'rxjs';

@Injectable({ providedIn: 'root' })
export class CertificateVerificationService {
    constructor(private http: HttpClient) {}
    getVelidation(validCode: string): Observable<any> {
        let wstokenVal = environment.wstoken;

        let formData = new FormData();

        formData.append(
            'wsfunction',
            'vumi_webservicesuit_certificate_varify_by_code'
        );
        formData.append('wstoken', wstokenVal);
        formData.append('moodlewsrestformat', 'json');
        formData.append('code', validCode);

        return this.http
            .post(`${environment.baseURL}/webservice/rest/server.php`, formData)
            .pipe(
                switchMap((response: any) => {
                    return of(response);
                })
            );
    }
}
