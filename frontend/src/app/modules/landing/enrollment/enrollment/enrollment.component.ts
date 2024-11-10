import { Component, HostListener } from '@angular/core';
import { MatDialog } from '@angular/material/dialog';
import { ActivatedRoute, Router } from '@angular/router';
import { AuthService } from 'app/core/auth/auth.service';
import { environment } from 'environments/environment';
import { EnrolledCourseService } from '../../courses/services/enrolled-course.service';
import { ActivityApiService } from '../../pdf-activity/services/activity-api.service';
import { PaymentGatewayComponent } from '../modals/payment-gateway/payment-gateway.component';
import { Payment } from '../models/payment';
import { CouponInfoService } from '../services/coupon-info.service';
import { EnrollemntInfoService } from '../services/enrollemnt-info.service';
import { ManualEnrollService } from '../services/manual-enroll.service';

@Component({
    selector: 'app-enrollment',
    templateUrl: './enrollment.component.html',
    styleUrls: ['./enrollment.component.scss'],
})
export class EnrollmentComponent {
    public getScreenWidth: any;
    public getScreenHeight: any;

    payment: Payment = {} as Payment;
    courseID: any;
    firstActivityID: any = -1;
    courseName: string = '';
    courseDescription: string = ''; //get course content api theke ai info ta nibo
    totalPayment: number = 0;
    paymentAfterDiscount: number = 0;
    discountPercentage: number = 0;
    isInvalidCoupon: boolean = false;
    isDiscountFound: boolean = false;
    userGivenCoupon: string = '';
    firstActivityRoute: string = '';
    enrolled: boolean = false;
    visibleStatus: boolean = false;
    couponVerificationStatusDone: boolean = true;
    isChecked: boolean = false;
    isBengali: boolean = false;

    @HostListener('window:resize', ['$event'])
    onWindowResize() {
        this.getScreenWidth = window.innerWidth;
        this.getScreenHeight = window.innerHeight;
    }

    constructor(
        private _router: Router,
        private route: ActivatedRoute,
        private activityApi: ActivityApiService,
        private enrollmentApi: EnrollemntInfoService,
        private manualEnrollApi: ManualEnrollService,
        private discountApi: CouponInfoService,
        private enrolledCourseApi: EnrolledCourseService,
        private dialog: MatDialog,
        private _authService: AuthService
    ) {}

    ngOnInit() {
        this.getScreenWidth = window.innerWidth;
        this.getScreenHeight = window.innerHeight;
        this.couponVerificationStatusDone = true;
        this.courseID = this.route.snapshot.params.id;
        this.payment.courseid = this.route.snapshot.params.id;
        this.payment.userid = localStorage.getItem('user-id');

        if (localStorage.getItem('lang') == 'bn') {
            this.isBengali = true;
        } else {
            this.isBengali = false;
        }

        this.enrolledCourseApi
            .getEnrolledCourses()
            .subscribe((response: any) => {
                if (response.exception || response.errorcode) {
                    this._authService.signOut();
                    this._router.navigate(['login']);
                    return;
                }

                for (let course of response) {
                    if (course.id == this.payment.courseid) {
                        this.enrolled = true;
                    }
                }
                this.visibleStatus = true;
            });

        this.enrollmentApi
            .getEnrolledCourseInfo(this.courseID)
            .subscribe((res: any) => {
                if (res.exception || res.errorcode) {
                    this._authService.signOut();
                    this._router.navigate(['login']);
                    return;
                }

                if (res.component) {
                    this.payment.component = res.component;
                }

                if (res.paymentarea) {
                    this.payment.paymentarea = res.paymentarea;
                }

                if (res.itemid) {
                    this.payment.itemid = res.itemid;
                }

                if (res.description) {
                    this.payment.description = res.description;
                    this.courseName = res.description;
                    if (this.courseName.length > 45) {
                        this.courseName = this.courseName.substring(0, 45);
                        this.courseName += '...';
                    }
                }

                if (res.cost) {
                    this.payment.cost = res.cost;
                    this.totalPayment = res.cost;
                    this.paymentAfterDiscount = res.cost;
                }

                if (res.amount) {
                    //will override final payment  if res.amount is available
                    this.payment.cost = res.amount;
                    this.paymentAfterDiscount = res.amount;
                }

                if (res.status) {
                    if (res.status == true) {
                        if (res.coupon_code) {
                            this.userGivenCoupon = res.coupon_code;
                        }
                        this.isDiscountFound = true;
                        this.isInvalidCoupon = false;
                        if (res.discount_percentage) {
                            this.discountPercentage = res.discount_percentage;
                        }
                    }
                }
            });

        this.activityApi
            .getActivities(this.payment.courseid)
            .subscribe((response: any) => {
                if (response.exception || response.errorcode) {
                    this._authService.signOut();
                    this._router.navigate(['login']);
                    return;
                }
                if (response[1]) {
                    let contents: any;
                    let moduleType: any;
                    let instanceId: any;
                    let actvityList = response[1];

                    if (actvityList.modules) {
                        let firstActivity = actvityList.modules[0];

                        let activityId = firstActivity.id;

                        instanceId = firstActivity.instance;
                        let moduleName = firstActivity.modname;

                        if (firstActivity.contents) {
                            contents = firstActivity.contents[0];
                            if (contents.mimetype)
                                moduleType = contents.mimetype;
                        }

                        if (moduleName == 'resource') {
                            let route = '';
                            if (moduleType.includes('pdf')) {
                                route = 'pdf';
                            } else if (moduleType.includes('video')) {
                                route = 'video';
                            }
                            this.firstActivityRoute =
                                route +
                                '?course=' +
                                this.courseID +
                                '&activity=' +
                                activityId;
                        } else if (moduleName == 'quiz') {
                            this.firstActivityRoute =
                                'quiz' +
                                '?id=' +
                                instanceId +
                                '&course=' +
                                this.courseID +
                                '&activity=' +
                                activityId;
                        } else if (moduleName == 'zoom') {
                            this.firstActivityRoute =
                                'meeting' +
                                '?course=' +
                                this.courseID +
                                '&activity=' +
                                activityId;
                        }

                        localStorage.setItem(
                            'latest-activity-route',
                            this.firstActivityRoute
                        );
                    }
                }
            });
    }

    verifyCoupon() {
        this.isInvalidCoupon = false;

        this.discountApi
            .verifyDiscount(this.userGivenCoupon, this.courseID)
            .subscribe((res: any) => {
                if (res.exception || res.errorcode) {
                    this._authService.signOut();
                    this._router.navigate(['login']);
                    return;
                }

                if (res.status == false) {
                    this.isInvalidCoupon = true;
                } else {
                    //discount found

                    this.isDiscountFound = true;
                    this.isInvalidCoupon = false;
                    if (res.amount != undefined) {
                        this.paymentAfterDiscount = res.amount;
                        this.payment.cost = res.amount;
                    }
                    if (res.discount_percentage) {
                        this.discountPercentage = res.discount_percentage;
                    }
                }
            });
    }

    processPayment(paymentMethod: any) {
        localStorage.setItem('course-id', this.payment.courseid);
        if (!(this.payment.cost == 0 || this.discountPercentage == 100)) {
            let payUrl: string =
                `${environment.baseURL}/payment/gateway/${paymentMethod}/pay.php?component=` +
                this.payment.component;
            payUrl += '&paymentarea=' + this.payment.paymentarea;
            payUrl += '&itemid=' + this.payment.itemid;
            if (paymentMethod != 'bkash')
                payUrl +=
                    '&description=' +
                    encodeURIComponent(this.payment.description);
            payUrl += '&userid=' + this.payment.userid;
            if (this.isDiscountFound) {
                payUrl += '&amount=' + this.payment.cost;
            }

            window.location.href = payUrl;
        } else if (this.payment.cost == 0 || this.discountPercentage == 100) {
            //call manual enroll api
            this.manualEnrollApi
                .enrolManual(this.payment.courseid)
                .subscribe((res: any) => {
                    if (res.exception || res.errorcode) {
                        this._authService.signOut();
                        this._router.navigate(['login']);
                        return;
                    }
                    //this.goToFirstActivity();
                    if (res == null) {
                        this._router.navigate(['course', this.courseID]);
                    }
                });
        }
    }

    openPaymnetGateway() {
        this.enrollmentApi
            .getEnrolledCourseInfo(this.courseID)
            .subscribe((res: any) => {
                this.payment.cost = res.cost;
                this.discountPercentage = res.discount_percentage;
                if (
                    this.payment.cost === 0 ||
                    this.discountPercentage === 100
                ) {
                    this.manualEnrollApi
                        .enrolManual(this.payment.courseid)
                        .subscribe((res: any) => {
                            if (res === null) {
                                this._router.navigate([
                                    'course',
                                    this.courseID,
                                ]);
                            }
                            if (res.exception || res.errorcode) {
                                this._authService.signOut();
                                this._router.navigate(['login']);
                                return;
                            }
                        });
                } else {
                    const dialogRef = this.dialog.open(PaymentGatewayComponent);
                    dialogRef.componentInstance.dataEmitter.subscribe(
                        (data) => {
                            if (data == 'shurjopay') {
                                this.processPayment(data);
                            }
                            if (data == 'bkash') {
                                this.processPayment(data);
                            }
                        }
                    );
                }
            });
    }

    goToFirstActivity() {
        this._router.navigateByUrl(`${this.firstActivityRoute}`);
    }
}
