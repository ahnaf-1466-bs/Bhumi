import { CommonModule } from '@angular/common';
import { NgModule } from '@angular/core';
import { RouterModule } from '@angular/router';
import { SharedModule } from 'app/shared/shared.module';
import { PdfActivityComponent } from './pdf-activity.component';
import { PdfActivityRoutes } from './pdf-activity.routing';
import { MatIconModule } from '@angular/material/icon';


@NgModule({
    declarations: [PdfActivityComponent],
    imports: [
        CommonModule,
        RouterModule.forChild(PdfActivityRoutes),
        SharedModule,
        MatIconModule
    ],
})
export class PdfActivityModule {}
