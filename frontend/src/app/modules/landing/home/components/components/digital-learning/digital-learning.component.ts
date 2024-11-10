import { Component } from '@angular/core';
import { MatDialog } from '@angular/material/dialog';
import { DomSanitizer } from '@angular/platform-browser';
import { ComingSoonModalComponent } from './coming-soon-modal/coming-soon-modal/coming-soon-modal.component';

@Component({
    selector: 'app-digital-learning',
    templateUrl: './digital-learning.component.html',
    styleUrls: ['./digital-learning.component.scss'],
})
export class DigitalLearningComponent {
    digital_learning_photo: any = this.sanitize.bypassSecurityTrustResourceUrl(
        'assets/images/home/Digital_Learning.webp'
    );

    digital_learningTab_photo: any =
        this.sanitize.bypassSecurityTrustResourceUrl(
            'assets/images/home/Digital Learning_tab.webp'
        );

    digital_learningPhone_photo: any =
        this.sanitize.bypassSecurityTrustResourceUrl(
            'assets/images/home/Digital Learning_Phone.webp'
        );

    download_icon: any = this.sanitize.bypassSecurityTrustResourceUrl(
        'assets/images/home/download-icon.webp'
    );

    playStore_icon: any = this.sanitize.bypassSecurityTrustResourceUrl(
        'assets/images/home/play-store-icon.webp'
    );

    playStore_icon2: any = this.sanitize.bypassSecurityTrustResourceUrl(
        'assets/images/home/play-store-icon2.webp'
    );

    appStore_icon: any = this.sanitize.bypassSecurityTrustResourceUrl(
        'assets/images/home/app-store-icon.webp'
    );

    appStore_icon2: any = this.sanitize.bypassSecurityTrustResourceUrl(
        'assets/images/home/app-store-icon2.webp'
    );

    constructor(private sanitize: DomSanitizer, private dialog: MatDialog) {}

    openCommingSoonModal() {
        const dialogRef = this.dialog.open(ComingSoonModalComponent);

        setTimeout(() => {
            dialogRef.close();
        }, 5000);
    }
}
