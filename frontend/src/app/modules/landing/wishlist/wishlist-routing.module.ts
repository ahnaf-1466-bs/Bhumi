import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { FavouriteCourseComponent } from './components/favourite-course/favourite-course.component';


const routes: Routes = [
  {
    path: '',
    component: FavouriteCourseComponent
 },
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class WishlistRoutingModule { }
