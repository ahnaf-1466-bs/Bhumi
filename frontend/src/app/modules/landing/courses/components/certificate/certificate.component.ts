import { Component, Input } from '@angular/core';
import { DomSanitizer } from '@angular/platform-browser';
import { ActivatedRoute, ParamMap, Router } from '@angular/router';
import { ActivityApiService } from 'app/modules/landing/pdf-activity/services/activity-api.service';
import { CourseDetailsService } from '../../course-details/course-details.service';
declare const pdfjsLib: any;

@Component({
    selector: 'app-certificate',
    templateUrl: './certificate.component.html',
    styleUrls: ['./certificate.component.scss'],
})
export class CertificateComponent {
    @Input() mobile: boolean;
    @Input() enrolled: boolean;
    @Input() dataFromParent: any;

    constructor(
        private sanitize: DomSanitizer,
        private _getCoursesService: CourseDetailsService,
        private route: ActivatedRoute,
        private _pdf: ActivityApiService,
        private _router: Router
    ) {}

    result;
    id;
    courseName;
    activityId;
    response;
    modules;
    moduleName;
    contents;
    moduleType;
    pdf;
    imageSrc;

    ngOnInit() {
        if (window.screen.width <= 820) {
            this.mobile = true;
        } else {
            this.mobile = false;
        }

        this.route.paramMap.subscribe((params: ParamMap) => {
            this.id = +params.get('id');
        });

        let body2 = [
            { wsfunction: 'vumi_webservicesuit_sample_customcert' },
            { userid: '0' },
            { courseid: this.id },
        ];

        this._getCoursesService.getCertificate(body2).subscribe((response) => {
            this.arrayBufferToPdf(response).then((pdfBlob: Blob) => {
                const pdfUrl = URL.createObjectURL(pdfBlob);

                this.pdf = this.sanitize.bypassSecurityTrustResourceUrl(pdfUrl);

                const loadingTask = pdfjsLib.getDocument(pdfUrl);
                loadingTask.promise
                    .then((pdf) => {
                        pdf.getPage(1).then((page) => {
                            const canvas = document.createElement('canvas');
                            const context = canvas.getContext('2d');
                            const viewport = page.getViewport({ scale: 1 });
                            canvas.width = viewport.width;
                            canvas.height = viewport.height;
                            const renderContext = {
                                canvasContext: context,
                                viewport: viewport,
                            };
                            page.render(renderContext).promise.then(() => {
                                this.imageSrc = canvas.toDataURL('image/png');
                            });
                        });
                    })
                    .catch((error) => {
                        // Error occurred while loading the PDF
                        console.log(error);
                    });
            });
            this.pdf = new Blob([response], { type: 'application/pdf' });
        });

        this._pdf.getActivities(this.id).subscribe(
            (response) => {
                this.response = response[1];
                this.modules = this.response.modules;
                if (this.modules && this.modules.length > 0) {
                    const firstModule = this.modules[0];
                    if (
                        firstModule &&
                        firstModule.contents &&
                        firstModule.contents.length > 0
                    ) {
                        this.activityId = firstModule.id;
                        this.moduleName = firstModule.modname;
                        this.contents = firstModule.contents[0];
                        this.moduleType = this.contents.mimetype;
                    }
                }
            },
            (error) => {
                console.error('Error fetching activities', error);
            }
        );
    }

    ngOnChanges() {
        this.result = this.dataFromParent;
        this.courseName = this.result?.fullname;
        this.courseName = this.titleCase(this.courseName)?.replace('&amp;','&');
    }

    titleCase(str) {
        str = str?.toLowerCase().split(' ');
        for (let i = 0; i < str?.length; i++) {
            str[i] = str[i].charAt(0).toUpperCase() + str[i].slice(1);
        }
        return str?.join(' ');
    }

    arrayBufferToPdf(arrayBuffer: ArrayBuffer): Promise<Blob> {
        return new Promise((resolve, reject) => {
            try {
                const blob = new Blob([arrayBuffer], {
                    type: 'application/pdf',
                });
                resolve(blob);
            } catch (error) {
                reject(error);
            }
        });
    }

    goto() {
        this._router.navigate(['/enrollment', this.id]);
    }
}
