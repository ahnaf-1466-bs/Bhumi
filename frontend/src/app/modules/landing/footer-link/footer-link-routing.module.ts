import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { FooterLinkComponent } from './components/footer-link/footer-link.component';
import { FaqComponent } from './components/faq/faq.component';
const routes: Routes = [
  {
    path: '',
    component: FooterLinkComponent
  },
  {
    path: 'faq',
    component: FaqComponent
  }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class FooterLinkRoutingModule { }
