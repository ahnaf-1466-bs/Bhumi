import { Component } from '@angular/core';
import { MatDialogRef } from '@angular/material/dialog';
import { Router } from '@angular/router';

@Component({
    selector: 'app-update-successful-modal',
    templateUrl: './update-successful-modal.component.html',
    styleUrls: ['./update-successful-modal.component.scss'],
})
export class UpdateSuccessfulModalComponent {
    constructor(
        private _router: Router,
        private dialogRef: MatDialogRef<UpdateSuccessfulModalComponent>
    ) {}
    goTo() {
        this.dialogRef.close();
        this._router.navigate(['courses']).then(() => {
            window.location.reload();
        });
    }
}
