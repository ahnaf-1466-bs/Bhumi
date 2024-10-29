import { Component } from '@angular/core';
import { Router } from '@angular/router';
import { ActivatedRoute } from '@angular/router';

@Component({
    selector: 'app-payment',
    templateUrl: './payment.component.html',
    styleUrls: ['./payment.component.scss'],
})
export class PaymentComponent {
    status: number = 0;
    courseID: any = 0;
    userID: any = -1;
    activityRoute: string = localStorage.getItem('latest-activity-route');
    countdown: number = 3;
    error: any = '';
    failedMessage: string = '';

    constructor(private route: ActivatedRoute, private router: Router) {}

    ngOnInit() {
        this.activityRoute = localStorage.getItem('latest-activity-route');

        this.route.queryParams.subscribe((params) => {
            this.status = params.status;
            this.userID = params.userid;

            if (params.courseid) {
                this.courseID = params.courseid;
            } else {
                this.courseID = localStorage.getItem('course-id');
            }

            if (params.error) {
                this.error = params.error;
            }

            if (params.statusMessage) {
                let decodedMessage = decodeURI(params.statusMessage).replace(
                    /%2F/g,
                    ' '
                );
                this.failedMessage = decodedMessage.replaceAll('/', ' ');
            } else {
                if (params.errorMessage) {
                    let decodedMessage = decodeURI(params.errorMessage).replace(
                        /%2F/g,
                        ' '
                    );
                    this.failedMessage = decodedMessage.replaceAll('/', ' ');
                } else {
                    this.failedMessage = 'Payment Failed';
                }
            }
        });
    }

    goToCourse() {
        this.router.navigate(['course', this.courseID]);
    }
}
