import { NgModule } from '@angular/core';
import { Route, RouterModule, Routes } from '@angular/router';
import { MeetingCredentialsComponent } from './meeting-credentials/meeting-credentials.component';

export const routes: Route[] = [
    {
        path: '',
        component: MeetingCredentialsComponent
    },
   
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class MeetRoutingModule { }
