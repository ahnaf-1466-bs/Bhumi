import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { OurStoryComponent } from './components/our-story/our-story.component';
import { AboutComponent } from './components/about/about.component';
import { RouterModule } from '@angular/router';
import { routes } from './about.routing';
import { VisionComponent } from './components/vision/vision.component';
import { WhyVumiComponent } from './components/why-vumi/why-vumi.component';
import { ForWhomComponent } from './components/for-whom/for-whom.component';
import { OurStrengthsComponent } from './components/our-strengths/our-strengths.component';
import { SharedModule } from 'app/shared/shared.module';



@NgModule({
  declarations: [
           OurStoryComponent,
           AboutComponent,
           VisionComponent,
           WhyVumiComponent,
           ForWhomComponent,
           OurStrengthsComponent
  ],
  imports: [
    CommonModule,
    SharedModule,
    RouterModule.forChild(routes),
  ]
})
export class AboutModule { }
