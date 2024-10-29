import { NgModule } from '@angular/core';
import { MatIconModule } from '@angular/material/icon';
import { RouterModule } from '@angular/router';
import { FuseLoadingBarModule } from '@fuse/components/loading-bar';
import { SharedModule } from 'app/shared/shared.module';
import { VumiLayoutComponent } from './vumi.component';

import { MatFormFieldModule } from '@angular/material/form-field';
import { MatMenuModule } from '@angular/material/menu';
import { MatSelectModule } from '@angular/material/select';

@NgModule({
    declarations: [VumiLayoutComponent],
    imports: [
        RouterModule,
        FuseLoadingBarModule,
        SharedModule,
        MatIconModule,
        MatSelectModule,
        MatFormFieldModule,
        MatMenuModule,
    ],
    exports: [VumiLayoutComponent],
})
export class VumiLayoutModule {}
