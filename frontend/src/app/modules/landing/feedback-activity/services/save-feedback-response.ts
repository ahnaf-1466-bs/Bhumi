import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Router } from '@angular/router';
import { environment } from 'environments/environment';
import { Observable, of, switchMap } from 'rxjs';
import { Feedback } from '../models/feedback';
import { CourseComment } from '../models/course-comment';
import { Course } from '../../dashboard/models/course';
@Injectable({
    providedIn: 'root',
})
export class SaveFeedbackService {
    feedBacks:Feedback[] = [];
    courseComment: CourseComment = {} as CourseComment;

    constructor(private http: HttpClient, private router: Router) {}

    submitFeedback(fBacks:any[], cComment:any, feedBackId, activityId, courseId): Observable<any> {
        this.feedBacks = fBacks;
        this.courseComment = cComment;

       
        let token = localStorage.getItem('auth-token');
        let userID = localStorage.getItem('user-id');

        let formData = new FormData();
        formData.append(
            'wsfunction',
            'mod_coursefeedback_save_feedback_responses'
        );
        formData.append('wstoken', token);
        formData.append('moodlewsrestformat', 'json');
        formData.append('feedbackid', feedBackId);
        formData.append('cmid', activityId);
        formData.append('courseid', courseId);
        formData.append('userid', userID);

        formData.append('responses[0][questionid]', '0');
        formData.append('responses[0][response]', this.feedBacks[0].rating);
        formData.append('responses[0][inputtype]', 'int');

        formData.append('responses[1][questionid]', '0');
        formData.append('responses[1][response]', this.courseComment.answer);
        formData.append('responses[1][inputtype]', 'text');

        for(let indx:number=2; indx < this.feedBacks.length; indx++){
           
            let key1:string= "responses[" + indx + "][questionid]";
            let val1:any = this.feedBacks[indx].questionid;
            formData.append(key1, val1);

            let key2:string= "responses[" + indx + "][response]";
            let val2:any = this.feedBacks[indx].rating;
            formData.append(key2, val2);

            let key3:string= "responses[" + indx + "][inputtype]";
            let val3:any = "int";
            formData.append(key3, val3);
        }

       
        return this.http
            .post(`${environment.baseURL}/webservice/rest/server.php`, formData)
            .pipe(
                switchMap((response: any) => {
                    return of(response);
                })
            );
    }
}
