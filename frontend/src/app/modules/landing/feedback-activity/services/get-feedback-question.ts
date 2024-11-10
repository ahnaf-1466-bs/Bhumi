import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Router } from '@angular/router';
import { environment } from 'environments/environment';
import { Observable, of, switchMap } from 'rxjs';

@Injectable({
    providedIn: 'root',
})
export class FeedbackQuestionService {
    constructor(private http: HttpClient, private router: Router) {}

    getFeedbackQuestion(feedbackId, cmId): Observable<any> {
        let token = localStorage.getItem('auth-token');
        let userID = localStorage.getItem('user-id');

       
        let formData = new FormData();
        formData.append(
            'wsfunction',
            'mod_coursefeedback_get_coursefeedback_questions'
        );
        formData.append('wstoken', token);
        formData.append('moodlewsrestformat', 'json');
        formData.append('feedbackid', feedbackId);
        formData.append('cmid', cmId);
        formData.append('userid', userID);

        return this.http
            .post(`${environment.baseURL}/webservice/rest/server.php`, formData)
            .pipe(
                switchMap((response: any) => {
                    return of(response);
                })
            );
    }
}
