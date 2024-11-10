import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { SharedModule } from 'app/shared/shared.module';
import { FooterLinkRoutingModule } from './footer-link-routing.module';
import { FooterLinkComponent } from './components/footer-link/footer-link.component';
import { FaqComponent } from './components/faq/faq.component';


@NgModule({
  declarations: [
    FooterLinkComponent,
    FaqComponent
  ],
  imports: [
    CommonModule,
    SharedModule,
    FooterLinkRoutingModule
  ]
})
export class FooterLinkModule { }
