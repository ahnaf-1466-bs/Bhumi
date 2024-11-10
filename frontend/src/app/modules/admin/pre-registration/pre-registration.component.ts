import { Component } from '@angular/core';
import {
    FormBuilder,
    FormControl,
    FormGroup,
    UntypedFormGroup,
    Validators,
} from '@angular/forms';
import { ActivatedRoute, Router } from '@angular/router';
import { CourseDetailsService } from 'app/modules/landing/courses/course-details/course-details.service';

@Component({
    selector: 'app-pre-registration',
    templateUrl: './pre-registration.component.html',
    styleUrls: ['./pre-registration.component.scss'],
})
export class PreRegistrationComponent {
    PreRegistrationForm: UntypedFormGroup;
    savedMail: string = '';
    savedName: any = '';
    logInForm: UntypedFormGroup;

    constructor(
        private fb: FormBuilder,
        private _router: Router,
        private route: ActivatedRoute,
        private _getCoursesServiceAPI: CourseDetailsService
    ) {
        this.successCode = 0;
    }

    name;
    email;
    batch;
    result;
    userId;
    courseId;
    courseName;
    successCode;
    showMessage: boolean = false;
    loading: boolean = false;

    ngOnInit() {
        this.logInForm = new FormGroup({
            name: new FormControl('', [Validators.required]),
            mail: new FormControl('', [Validators.required, Validators.email]),
        });

        this.savedMail = localStorage.getItem('user-mail');
        this.logInForm.get('mail').patchValue(this.savedMail);
        this.courseId = this.route.snapshot.queryParamMap.get('course');
        this.batch = this.route.snapshot.queryParamMap.get('batch');
        this.userId = localStorage.getItem('user-id');

        let body = [
            { wsfunction: 'core_course_get_courses_by_field' },
            { field: 'id' },
            { value: this.courseId },
        ];
        this._getCoursesServiceAPI.getDetails(body).subscribe((response) => {
            this.result = response.courses[0];
            this.courseName = this.result.fullname;
        });
    }

    loader() {
        this.loading = true;
    }

    PreRegistration() {
        this.name = this.logInForm.get('name').value;
        this.email = this.logInForm.get('mail').value;

        this.logInForm.get('name').reset('');

        let body2 = [
            { wsfunction: 'local_preregistration_add_user_to_batch' },
            { batchid: this.batch },
            { userid: this.userId },
            { name: this.name },
            { email: this.email },
        ];
        this._getCoursesServiceAPI
            .addUserToNextBatch(body2)
            .subscribe((res: any) => {
                this.successCode = res.statuscode;
                this.showMessage = true;
                this.loading = false;
            });
    }

    navigateToPreviousPage() {
        this._router.navigate(['course', this.courseId]);
    }
}
