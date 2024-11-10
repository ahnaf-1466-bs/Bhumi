import { NgModule } from '@angular/core';
import { Route, RouterModule, Routes } from '@angular/router';
import { EnrollmentComponent } from './enrollment/enrollment.component';


export const routes: Route[] = [
  {
      path: '',
      component: EnrollmentComponent
  },
  
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class EnrollmentRoutingModule { }
