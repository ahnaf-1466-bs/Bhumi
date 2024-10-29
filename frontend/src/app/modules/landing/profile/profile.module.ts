import { CommonModule } from '@angular/common';
import { NgModule } from '@angular/core';
import { RouterModule } from '@angular/router';
import { SharedModule } from 'app/shared/shared.module';

import { EditProfileComponent } from './components/edit-profile/edit-profile.component';
import { ProfileRoutes } from './profile.routing';
import { UpdateSuccessfulModalComponent } from './components/update-successful-modal/update-successful-modal.component';

@NgModule({
    declarations: [EditProfileComponent, UpdateSuccessfulModalComponent],
    imports: [CommonModule, RouterModule.forChild(ProfileRoutes), SharedModule],
})
export class ProfileModule {}
