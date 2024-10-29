import { Component, Input } from '@angular/core';
import { Certificate } from './models/certificate';

@Component({
    selector: 'app-past-certificate',
    templateUrl: './past-certificate.component.html',
    styleUrls: ['./past-certificate.component.scss'],
})
export class PastCertificateComponent {
    @Input() pastCertificateList: Certificate[] = [];

    constructor() {}

    displayCert(certificateUrl) {
        window.open(certificateUrl, '_blank');
    }
}
