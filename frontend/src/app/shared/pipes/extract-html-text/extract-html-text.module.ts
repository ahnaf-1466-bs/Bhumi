import { NgModule } from '@angular/core';
import { ExtractHtmlTextPipe } from './extract-html-text.pipe';

@NgModule({
  declarations: [ExtractHtmlTextPipe],
  exports:[ExtractHtmlTextPipe]
})
export class ExtractHtmlTextPipeModule { }
