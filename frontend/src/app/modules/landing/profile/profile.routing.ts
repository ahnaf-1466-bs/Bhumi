import { Route } from '@angular/router';
import { EditProfileComponent } from './components/edit-profile/edit-profile.component';

export const ProfileRoutes: Route[] = [
    {
        path: '',
        component: EditProfileComponent,
    },
];
