import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { BehaviorSubject, Observable } from 'rxjs';

import { environment } from 'environments/environment';

@Injectable({
    providedIn: 'root',
})
export class QuizService {
    quizzes: any = new BehaviorSubject<any>({});

    currentQuizzes = this.quizzes.asObservable();

    constructor(private http: HttpClient) {}

    getQuiz(token: string, courseId: string): Observable<any> {
        let form = new FormData();
        form.append('wstoken', token);
        form.append('wsfunction', 'mod_quiz_get_quizzes_by_courses');
        form.append('moodlewsrestformat', 'json');
        form.append('courseids[0]', courseId);
        this.http
            .post(`${environment.baseURL}/webservice/rest/server.php`, form)
            .subscribe((res) => this.quizzes.next(res));
        return this.http.post(
            `${environment.baseURL}/webservice/rest/server.php`,
            form
        );
    }

    /**
     *
     * @param token
     * @param quizId
     * @returns observable of user attempts data
     */

    getUserAttempts(token: string, quizId: string) {
        // console.log(' user attempt quizId ', quizId);

        let form = new FormData();
        form.append('wstoken', token);
        form.append('wsfunction', 'mod_quiz_get_user_attempts');
        form.append('moodlewsrestformat', 'json');
        form.append('quizid', quizId);
        form.append('status', 'all');

        return this.http.post(
            `${environment.baseURL}/webservice/rest/server.php`,
            form
        );
    }

    /**
     *
     * @param token
     * @param attemptId
     * @param page
     * @returns observable of attempted question
     */
    getAttemptData(token: string, attemptId: string, page: string = '0') {
        let form = new FormData();
        form.append('wstoken', token);
        form.append('wsfunction', 'mod_quiz_get_attempt_data');
        form.append('moodlewsrestformat', 'json');
        form.append('attemptid', attemptId);
        form.append('page', page);

        return this.http.post(
            `${environment.baseURL}/webservice/rest/server.php`,
            form
        );
    }
    /**
     *
     * @param token
     * @param attemptId
     * @param data
     * @param finsihAttempt takes 0 or 1 default is 0 e.i attempt is not finished yet
     * @param timeup takes 0 or 1 default parameter values is 0 e.i time is not up yet
     * @returns observable response for the current question
     */
    processAttempt(
        token: string,
        attemptId: string,
        data: any[],
        finsihAttempt: string = '0',
        timeup: string = '0'
    ): Observable<any> {
        let form = new FormData();

        form.append('wstoken', token);
        form.append('wsfunction', 'mod_quiz_process_attempt');
        form.append('moodlewsrestformat', 'json');
        form.append('attemptid', attemptId);
        for (let i = 0; i < data.length; ++i) {
            form.append(`data[${i}][name]`, data[i].name);
            form.append(`data[${i}][value]`, data[i].value);
        }
        // form.append('data[0][name]', data[0].name);
        // form.append('data[0][value]', data[0].value);
        // form.append('data[1][name]', data[1].name);
        // form.append('data[1][value]', data[1].value);
        form.append('finishattempt', finsihAttempt);
        form.append('timeup', timeup);

        return this.http.post(
            `${environment.baseURL}/webservice/rest/server.php`,
            form,
            {
                responseType: 'json',
            }
        );
    }
    getAttemptReview(token: string, attemptId: string) {
        let form = new FormData();
        form.append('wstoken', token);
        form.append('wsfunction', 'mod_quiz_get_attempt_review');
        form.append('moodlewsrestformat', 'json');
        form.append('attemptid', attemptId);

        return this.http.post(
            `${environment.baseURL}/webservice/rest/server.php`,
            form,
            {
                responseType: 'json',
            }
        );
    }
    newAttempt(token: string, quizId: string) {
        let form = new FormData();
        form.append('wstoken', token);
        form.append('wsfunction', 'mod_quiz_start_attempt');
        form.append('moodlewsrestformat', 'json');
        form.append('quizid', quizId);

        return this.http.post(
            `${environment.baseURL}/webservice/rest/server.php`,
            form,
            { responseType: 'json' }
        );
    }
}
