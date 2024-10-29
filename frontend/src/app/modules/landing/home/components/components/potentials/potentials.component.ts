import { Component } from '@angular/core';
import { DomSanitizer, SafeResourceUrl } from '@angular/platform-browser';

@Component({
    selector: 'app-potentials',
    templateUrl: './potentials.component.html',
    styleUrls: ['./potentials.component.scss'],
})
export class PotentialsComponent {
    backgroundImgSrc: SafeResourceUrl;

    constructor(private sanitizer: DomSanitizer) {
        this.setBackgroundImage();
    }

    setBackgroundImage(): void {
        const imageUrl = 'assets/images/home/spark.webp';
        this.backgroundImgSrc =
            this.sanitizer.bypassSecurityTrustResourceUrl(imageUrl);
    }
}
