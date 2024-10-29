import { DOCUMENT } from '@angular/common';
import { Component, Inject, ViewEncapsulation } from '@angular/core';
import { FormControl, FormGroup, Validators } from '@angular/forms';
import { ActivatedRoute } from '@angular/router';
import { CertificateVerificationService } from '../../services/certificate-verification.service';
declare const pdfjsLib: any;

@Component({
    selector: 'app-certificateVerification',
    templateUrl: './certificate-verification.component.html',
    styleUrls: ['./certificate-verification.component.scss'],
    encapsulation: ViewEncapsulation.None,
})
export class CertificateVerificationComponent {
    url;
    code;
    massage;
    imageSrc;
    showIt: boolean = false;
    loading: boolean = true;

    constructor(
        private route: ActivatedRoute,
        private _certificateVerification: CertificateVerificationService,
        @Inject(DOCUMENT) private document: Document
    ) {}

    ngOnInit() {
        this.document.body.scrollTop = 0;
        this.document.documentElement.scrollTop = 0;

        this.loading = true;
        this.code = this.route.snapshot.queryParamMap.get('code');
        if (this.code) {
            this.loginForm.controls.code.patchValue(this.code);
        }
        this.showIt = false;
    }

    loginForm = new FormGroup({
        code: new FormControl('', [Validators.required]),
    });

    verifyCode() {
        this.code = this.loginForm.value.code;
        this._certificateVerification
            .getVelidation(this.code)
            .subscribe((response) => {
                this.massage = response.message;
                this.url = response.url;

                // if (this.url) {
                //     // window.location.href = this.url;
                //     window.open(this.url, '_blank');
                // }

                if (this.url) {
                    const loadingTask = pdfjsLib.getDocument(this.url);
                    loadingTask.promise
                        .then((pdf) => {
                            // PDF loaded successfully, do something with it
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
                                    this.loading = false;
                                    this.imageSrc =
                                        canvas.toDataURL('image/png');
                                });
                            });
                        })
                        .catch((error) => {
                            // Error occurred while loading the PDF
                            console.log(error);
                        });
                }
            });
        this.showIt = true;
    }

    resetCode() {
        this.loginForm.reset();
    }
}
