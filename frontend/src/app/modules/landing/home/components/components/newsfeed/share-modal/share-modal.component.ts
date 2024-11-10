import { Component, Inject, OnInit, ViewEncapsulation } from '@angular/core';
import { MAT_DIALOG_DATA, MatDialogRef } from '@angular/material/dialog';
import { MatSnackBar } from '@angular/material/snack-bar';
import { TranslateService } from '@ngx-translate/core';
import { AllNewsfeedApiService } from 'app/modules/landing/newsfeeds/services/all-newsfeed.service';
import { NewsfeedByIdApiService } from 'app/modules/landing/newsfeeds/services/newsfeed-by-id.service';
declare const ClipboardJS: any;
@Component({
    selector: 'app-share-modal',
    templateUrl: './share-modal.component.html',
    styleUrls: ['./share-modal.component.scss'],
    encapsulation: ViewEncapsulation.Emulated,
})
export class ShareModalComponent implements OnInit {
    constructor(
        private _snackBar: MatSnackBar,
        private _translate: TranslateService,
        private _allNews: AllNewsfeedApiService,
        @Inject(MAT_DIALOG_DATA) public data: any,
        private _newsById: NewsfeedByIdApiService,
        public dialogRef: MatDialogRef<ShareModalComponent>
    ) {
        this.shareableLink = window.location.href;
    }

    id;
    title;
    title_bn;
    shareableLink: string;
    updatedUrl: string;
    isBengali: boolean = false;

    ngOnInit() {
        if (localStorage.getItem('lang') === 'bn') {
            this.isBengali = true;
        } else {
            this.isBengali = false;
        }

        this._allNews.getAllNewsFeedData().subscribe((res: any) => {});
        this._newsById.getNewsFeedById(this.data.id).subscribe((res: any) => {
            this.title = res?.newstitle;
            this.title_bn = res?.newstitle_bn;
            const currentUrl = this.shareableLink; // get the current URL path
            this.updatedUrl = `${currentUrl}newsfeed/${this.data.id}`;
        });

        const clipboard = new ClipboardJS('.btn');

        clipboard.on('success', (event) => {
            const translatedText = this._translate.instant(
                'LINK_COPIED_TO_CLIPBOARD'
            );
            this._snackBar.open(translatedText, 'Close', {
                duration: 3000,
                horizontalPosition: 'left',
                verticalPosition: 'bottom',
            });
        });

        clipboard.on('error', (event) => {
            const translatedFailedText = this._translate.instant(
                'FAILED_TO_COPY_TEXT_TO_CLIPBOARD'
            );
            console.error(translatedFailedText, event.action);
        });
    }

    onNoClick(): void {
        this.dialogRef.close();
    }
}
