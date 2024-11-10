import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { WishlistRoutingModule } from './wishlist-routing.module';
import { FavouriteCourseComponent } from './components/favourite-course/favourite-course.component';
import { WishlistedCourseComponent } from './components/wishlisted-course/wishlisted-course.component';
import { SharedModule } from 'app/shared/shared.module';

@NgModule({
  declarations: [
    FavouriteCourseComponent,
    WishlistedCourseComponent,
  ],
  imports: [
    CommonModule,
    WishlistRoutingModule,
    SharedModule
  ]
})
export class WishlistModule { }
