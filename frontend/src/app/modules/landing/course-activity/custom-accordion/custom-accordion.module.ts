import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { CustomAccordionComponent } from './custom-accordion.component';
import {CdkAccordionModule} from '@angular/cdk/accordion';
import { MatIconModule } from '@angular/material/icon';
import { SharedModule } from 'app/shared/shared.module';


@NgModule({
  declarations: [
    CustomAccordionComponent
  ],
  imports: [
    CommonModule,
    CdkAccordionModule,
    MatIconModule,
    SharedModule
  ],
  exports:[
    CustomAccordionComponent
  ]
})
export class CustomAccordionModule { }
