import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { HelperService } from 'app/services/helper';
import { environment } from 'environments/environment';
import { Observable } from 'rxjs';

@Injectable({
    providedIn: 'root',
})
export class VumiService {
    constructor(
        private _httpClient: HttpClient,
        private _help: HelperService
    ) {}

    getCourses(): Observable<any> {
        let wstoken = environment.wstoken;

        let data: any = {
            wstoken: wstoken,
            wsfunction: 'core_course_get_courses_by_field',
            moodlewsrestformat: 'json',
        };
        let form = this._help.formMaker(data);
        return this._httpClient.post(
            `${environment.baseURL}/webservice/rest/server.php`,
            form
        );
    }

    getNotifications(userid: any): Observable<any> {
        let wstoken = environment.wstoken;
        const lang = localStorage.getItem('lang') || 'en';
        let data: any = {
            wstoken: wstoken,
            wsfunction: 'message_popup_get_popup_notifications',
            moodlewsrestformat: 'json',
            useridto: userid,
            newestfirst: '1',
            moodlewssettinglang: lang,
        };
        let form = this._help.formMaker(data);
        return this._httpClient.post(
            `${environment.baseURL}/webservice/rest/server.php`,
            form
        );
    }

    markAsRead(userid: any): Observable<any> {
        let wstoken = environment.wstoken;
        let data: any = {
            wstoken: wstoken,
            wsfunction: 'core_message_mark_all_notifications_as_read',
            moodlewsrestformat: 'json',
            useridto: userid,
        };
        let form = this._help.formMaker(data);
        return this._httpClient.post(
            `${environment.baseURL}/webservice/rest/server.php`,
            form
        );
    }
}
